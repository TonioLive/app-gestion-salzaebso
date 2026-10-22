<?php
/** Read-only collection status for the enrolments already visible in the timetable. */
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(404);
    exit;
}

/**
 * Amounts are integer cents. An enrolment without an issued invoice/receipt is
 * "uninvoiced", never overdue. Credits on a different invoice are not silently
 * used to settle an unpaid invoice. The caller must supply only visible rosters.
 *
 * @return array<int,array{status:string,pending_cents:int,pending_invoices:int}>
 */
function pool_student_payment_status(array $enrolmentIds): array
{
    // A timetable assignment alone does not grant access to family finances.
    if (!app_can('zona_facturacion')) {
        return [];
    }

    $ids = [];
    foreach ($enrolmentIds as $value) {
        if ((is_int($value) || is_string($value)) && ctype_digit((string)$value)
            && (int)$value > 0 && (int)$value <= 2147483647) {
            $ids[(int)$value] = (int)$value;
        }
    }
    if (!$ids) {
        return [];
    }

    $allCentres = (int)($_SESSION['nivel_acceso'] ?? 0) === 4
        || !empty($_SESSION['usuario_multicentro']);
    $centre = (int)($_SESSION['id_centro_usuario'] ?? 0);
    if (!$allCentres && $centre < 1) {
        return [];
    }

    $statuses = [];
    // Bounded batches, with one query per batch rather than one per student.
    foreach (array_chunk(array_values($ids), 500) as $batch) {
        $marks = implode(',', array_fill(0, count($batch), '?'));
        $types = str_repeat('i', count($batch));
        $values = $batch;
        $centreFilter = '';
        if (!$allCentres) {
            $centreFilter = ' AND a.id_centro=?';
            $types .= 'i';
            $values[] = $centre;
        }

        // facturada=0 is an issued receipt, not a preview. Preview/copy tables
        // are deliberately not read. Deleted invoices have no row in this table.
        // Aggregating lines and payments separately prevents join multiplication.
        $sql = "WITH selected_students AS (
            SELECT a.id_alumno FROM alumnos a
            WHERE a.id_alumno IN ($marks)$centreFilter
        ), selected_invoices AS (
            SELECT f.id_factura,f.id_cliente,f.total_factura
            FROM facturas_emitidas f
            JOIN selected_students s ON s.id_alumno=f.id_cliente
            WHERE COALESCE(f.tipo_cliente,0)=0
              AND (f.fecha_emision IS NULL OR f.fecha_emision<=CURDATE())
        ), line_totals AS (
            SELECT d.id_factura,COUNT(*) AS line_count,
                COALESCE(SUM(d.cantidad*d.precio*(1-COALESCE(d.dto,0)/100)
                    *(1+COALESCE(d.tipo_iva,0))),0) AS amount
            FROM detalles_factura d
            JOIN selected_invoices f ON f.id_factura=d.id_factura
            GROUP BY d.id_factura
        ), payment_totals AS (
            SELECT p.id_factura,COALESCE(SUM(ROUND(p.importe_pago*100)*p.signo),0) AS paid_cents
            FROM pagos_a_cuenta p
            JOIN selected_invoices f ON f.id_factura=p.id_factura
            GROUP BY p.id_factura
        )
        SELECT s.id_alumno,f.id_factura,
            CASE WHEN l.line_count>0 THEN l.amount ELSE f.total_factura END AS amount,
            COALESCE(p.paid_cents,0) AS paid_cents
        FROM selected_students s
        LEFT JOIN selected_invoices f ON f.id_cliente=s.id_alumno
        LEFT JOIN line_totals l ON l.id_factura=f.id_factura
        LEFT JOIN payment_totals p ON p.id_factura=f.id_factura";

        $result = db_query_params($sql, $types, $values, app_db());
        if (!$result instanceof mysqli_result) {
            // Never label missing query results as a settled account.
            throw new RuntimeException('No se ha podido comprobar el estado de los cobros.');
        }
        while ($row = $result->fetch_assoc()) {
            $id = (int)$row['id_alumno'];
            if (!isset($statuses[$id])) {
                $statuses[$id] = ['status' => 'uninvoiced', 'pending_cents' => 0, 'pending_invoices' => 0];
            }
            if ($row['id_factura'] === null) {
                continue;
            }

            // Match the collection screen: round invoice totals once, subtract
            // signed payments in cents, including historic negative payments.
            $remaining = (int)round((float)$row['amount'] * 100) - (int)$row['paid_cents'];
            if ($remaining > 0) {
                $statuses[$id]['pending_cents'] += $remaining;
                $statuses[$id]['pending_invoices']++;
                $statuses[$id]['status'] = 'pending';
            } elseif ($statuses[$id]['status'] !== 'pending') {
                $statuses[$id]['status'] = 'paid';
            }
        }
        $result->free();
    }

    return $statuses;
}
