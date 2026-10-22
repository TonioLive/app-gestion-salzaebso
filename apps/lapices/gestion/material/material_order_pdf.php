<?php

class MaterialSimplePdf
{
    private $pageWidth = 297.0;
    private $pageHeight = 210.0;
    private $pointsPerMillimetre = 72 / 25.4;
    private $pages = array();
    private $currentPage = -1;

    public function addPage()
    {
        $this->pages[] = '';
        $this->currentPage = count($this->pages) - 1;
        return $this->currentPage;
    }

    public function pageCount()
    {
        return count($this->pages);
    }

    public function colour($hex)
    {
        $hex = ltrim((string) $hex, '#');
        if (strlen($hex) !== 6) {
            return array(0, 0, 0);
        }
        return array(
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        );
    }

    private function number($value)
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    }

    private function x($millimetres)
    {
        return $this->number($millimetres * $this->pointsPerMillimetre);
    }

    private function y($millimetres)
    {
        return $this->number(
            ($this->pageHeight - $millimetres) * $this->pointsPerMillimetre
        );
    }

    private function append($command, $page)
    {
        if ($page === null) {
            $page = $this->currentPage;
        }
        $this->pages[$page] .= $command . "\n";
    }

    private function setFill($colour)
    {
        return $this->number($colour[0]) . ' '
            . $this->number($colour[1]) . ' '
            . $this->number($colour[2]) . ' rg';
    }

    private function setStroke($colour)
    {
        return $this->number($colour[0]) . ' '
            . $this->number($colour[1]) . ' '
            . $this->number($colour[2]) . ' RG';
    }

    public function rect($x, $y, $width, $height, $fill, $stroke, $page)
    {
        $bottom = $this->pageHeight - $y - $height;
        $command = '';
        if ($fill !== null) {
            $command .= $this->setFill($fill) . "\n";
        }
        if ($stroke !== null) {
            $command .= $this->setStroke($stroke) . "\n0.7 w\n";
        }
        $command .= $this->x($x) . ' ' . $this->x($bottom) . ' '
            . $this->x($width) . ' ' . $this->x($height) . ' re ';
        if ($fill !== null && $stroke !== null) {
            $command .= 'B';
        } elseif ($fill !== null) {
            $command .= 'f';
        } else {
            $command .= 'S';
        }
        $this->append($command, $page);
    }

    public function line($x1, $y1, $x2, $y2, $colour, $page)
    {
        $this->append(
            $this->setStroke($colour) . "\n0.7 w\n"
            . $this->x($x1) . ' ' . $this->y($y1) . ' m '
            . $this->x($x2) . ' ' . $this->y($y2) . ' l S',
            $page
        );
    }

    public function circle($centreX, $centreY, $radius, $fill, $stroke, $page)
    {
        $factor = 0.5522847498;
        $cx = $centreX;
        $cy = $this->pageHeight - $centreY;
        $r = $radius;
        $k = $radius * $factor;
        $command = $this->setFill($fill) . "\n"
            . $this->setStroke($stroke) . "\n0.7 w\n"
            . $this->x($cx + $r) . ' ' . $this->x($cy) . ' m '
            . $this->x($cx + $r) . ' ' . $this->x($cy + $k) . ' '
            . $this->x($cx + $k) . ' ' . $this->x($cy + $r) . ' '
            . $this->x($cx) . ' ' . $this->x($cy + $r) . ' c '
            . $this->x($cx - $k) . ' ' . $this->x($cy + $r) . ' '
            . $this->x($cx - $r) . ' ' . $this->x($cy + $k) . ' '
            . $this->x($cx - $r) . ' ' . $this->x($cy) . ' c '
            . $this->x($cx - $r) . ' ' . $this->x($cy - $k) . ' '
            . $this->x($cx - $k) . ' ' . $this->x($cy - $r) . ' '
            . $this->x($cx) . ' ' . $this->x($cy - $r) . ' c '
            . $this->x($cx + $k) . ' ' . $this->x($cy - $r) . ' '
            . $this->x($cx + $r) . ' ' . $this->x($cy - $k) . ' '
            . $this->x($cx + $r) . ' ' . $this->x($cy) . ' c B';
        $this->append($command, $page);
    }

    private function utf8($value)
    {
        $value = (string) $value;
        if ($value === '' || @preg_match('//u', $value)) {
            return $value;
        }
        if (function_exists('iconv')) {
            $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);
            if ($converted !== false) {
                return $converted;
            }
        }
        return $value;
    }

    private function encode($value)
    {
        $value = $this->utf8($value);
        if (function_exists('iconv')) {
            $converted = @iconv(
                'UTF-8',
                'Windows-1252//TRANSLIT//IGNORE',
                $value
            );
            if ($converted !== false) {
                return $converted;
            }
        }
        return preg_replace('/[^\x20-\x7E]/', '', $value);
    }

    private function escape($value)
    {
        return str_replace(
            array('\\', '(', ')', "\r", "\n"),
            array('\\\\', '\\(', '\\)', ' ', ' '),
            $this->encode($value)
        );
    }

    public function textWidth($text, $size, $bold)
    {
        $factor = $bold ? 0.54 : 0.50;
        return strlen($this->encode($text))
            * (float) $size
            * $factor
            / $this->pointsPerMillimetre;
    }

    public function text($x, $y, $text, $size, $bold, $colour, $align, $width, $page)
    {
        $textWidth = $this->textWidth($text, $size, $bold);
        if ($align === 'R') {
            $x += max(0, $width - $textWidth);
        } elseif ($align === 'C') {
            $x += max(0, ($width - $textWidth) / 2);
        }
        $font = $bold ? 'F2' : 'F1';
        $command = 'BT /' . $font . ' ' . $this->number($size)
            . ' Tf ' . $this->setFill($colour) . ' '
            . '1 0 0 1 ' . $this->x($x) . ' ' . $this->y($y)
            . ' Tm (' . $this->escape($text) . ') Tj ET';
        $this->append($command, $page);
    }

    public function wrap($text, $size, $bold, $width)
    {
        $text = trim($this->utf8($text));
        if ($text === '') {
            return array('');
        }
        $words = preg_split('/\s+/u', $text);
        $lines = array();
        $current = '';
        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if (
                $current !== ''
                && $this->textWidth($candidate, $size, $bold) > $width
            ) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }
        return $lines;
    }

    public function output($path, $destination, $filename)
    {
        $objects = array(
            1 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica '
                . '/Encoding /WinAnsiEncoding >>',
            2 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold '
                . '/Encoding /WinAnsiEncoding >>',
            3 => '',
            4 => '<< /Type /Catalog /Pages 3 0 R >>',
        );
        $pageReferences = array();
        foreach ($this->pages as $content) {
            $contentId = count($objects) + 1;
            $objects[$contentId] = '<< /Length ' . strlen($content)
                . " >>\nstream\n" . $content . "endstream";
            $pageId = count($objects) + 1;
            $objects[$pageId] = '<< /Type /Page /Parent 3 0 R '
                . '/MediaBox [0 0 '
                . $this->number($this->pageWidth * $this->pointsPerMillimetre)
                . ' '
                . $this->number($this->pageHeight * $this->pointsPerMillimetre)
                . '] /Resources << /Font << /F1 1 0 R /F2 2 0 R >> >> '
                . '/Contents ' . $contentId . ' 0 R >>';
            $pageReferences[] = $pageId . ' 0 R';
        }
        $objects[3] = '<< /Type /Pages /Count ' . count($pageReferences)
            . ' /Kids [' . implode(' ', $pageReferences) . '] >>';

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = array(0);
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        $pdf .= 'trailer << /Size ' . (count($objects) + 1)
            . " /Root 4 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

        if ($destination === 'F') {
            file_put_contents($path, $pdf);
            return $path;
        }

        if (!headers_sent()) {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($pdf));
            header(
                'Content-Disposition: attachment; filename="'
                . str_replace('"', '', $filename) . '"'
            );
            header('Cache-Control: private, max-age=0, must-revalidate');
        }
        echo $pdf;
        return $filename;
    }
}

function material_pdf_quantity($value)
{
    $number = (float) $value;
    return abs($number - round($number)) < 0.001
        ? number_format($number, 0, ',', '.')
        : number_format($number, 2, ',', '.');
}

function material_pdf_header($pdf, $page, $order)
{
    $green = $pdf->colour('#114d41');
    $white = $pdf->colour('#ffffff');
    $pdf->rect(0, 0, 297, 28, $green, null, $page);
    $pdf->circle(18, 14, 8, $white, $white, $page);
    $pdf->text(12, 17, 'L', 14, true, $green, 'C', 12, $page);
    $pdf->text(
        32,
        11,
        'Pedido de uniformes y material',
        16,
        true,
        $white,
        'L',
        160,
        $page
    );
    $pdf->text(
        32,
        19,
        $order['centro_nombre'],
        8,
        false,
        $white,
        'L',
        160,
        $page
    );
    $pdf->text(
        210,
        11,
        $order['codigo'],
        12,
        true,
        $white,
        'R',
        76,
        $page
    );
    $pdf->text(
        210,
        19,
        'Documento listo para enviar al proveedor',
        7,
        false,
        $white,
        'R',
        76,
        $page
    );
}

function material_pdf_table_header($pdf, $page, $y)
{
    $green = $pdf->colour('#114d41');
    $white = $pdf->colour('#ffffff');
    $pdf->rect(10, $y, 277, 8, $green, null, $page);
    $columns = array(
        array(10, 13, 'N.º', 'C'),
        array(23, 55, 'Familia', 'L'),
        array(78, 102, 'Artículo / modelo', 'L'),
        array(180, 27, 'Destino', 'C'),
        array(207, 20, 'Talla', 'C'),
        array(227, 30, 'Cantidad', 'R'),
        array(257, 30, 'Revisado', 'C'),
    );
    foreach ($columns as $column) {
        $pdf->text(
            $column[0] + 2,
            $y + 5.3,
            $column[2],
            7,
            true,
            $white,
            $column[3],
            $column[1] - 4,
            $page
        );
    }
    return $y + 8;
}

function material_order_pdf_output($order, $lines, $destination, $target)
{
    $pdf = new MaterialSimplePdf();
    $page = $pdf->addPage();
    material_pdf_header($pdf, $page, $order);

    $boys = 0;
    $girls = 0;
    $neutral = 0;
    foreach ($lines as $line) {
        $recipient = material_normalized_text($line['destinatario']);
        if ($recipient === 'nino') {
            $boys += (float) $line['cantidad'];
        } elseif ($recipient === 'nina') {
            $girls += (float) $line['cantidad'];
        } else {
            $neutral += (float) $line['cantidad'];
        }
    }

    $created = strtotime($order['fecha_creacion']);
    $creator = trim((string) $order['creado_por_nombre']);
    if ($creator === '') {
        $creator = 'Usuario de gestión';
    }
    $summary = array(
        array('CURSO', $order['curso_nombre']),
        array(
            'FECHA Y RESPONSABLE',
            ($created ? date('d/m/Y H:i', $created) : '') . ' · ' . $creator,
        ),
        array(
            'ARTÍCULOS / UNIDADES',
            (int) $order['total_lineas'] . ' / '
                . material_pdf_quantity($order['total_unidades']),
        ),
        array(
            'NIÑO · NIÑA · UNISEX',
            material_pdf_quantity($boys) . ' · '
                . material_pdf_quantity($girls) . ' · '
                . material_pdf_quantity($neutral),
        ),
    );
    $boxWidth = 67;
    foreach ($summary as $index => $box) {
        $x = 10 + ($index * 70);
        $pdf->rect(
            $x,
            34,
            $boxWidth,
            21,
            $pdf->colour('#eef6f3'),
            $pdf->colour('#d7e3df'),
            $page
        );
        $pdf->text(
            $x + 4,
            41,
            $box[0],
            6.5,
            true,
            $pdf->colour('#48665d'),
            'L',
            $boxWidth - 8,
            $page
        );
        $valueSize = $index === 1 ? 8 : 11;
        $pdf->text(
            $x + 4,
            50,
            $box[1],
            $valueSize,
            true,
            $pdf->colour('#102f29'),
            'L',
            $boxWidth - 8,
            $page
        );
    }

    $y = material_pdf_table_header($pdf, $page, 61);
    foreach ($lines as $index => $line) {
        $categoryLines = $pdf->wrap($line['categoria'], 7, false, 49);
        $articleLines = $pdf->wrap($line['articulo'], 7.5, true, 96);
        $lineCount = max(count($categoryLines), count($articleLines), 1);
        $rowHeight = max(9, 4.2 * $lineCount + 3.4);
        if ($y + $rowHeight > 190) {
            $page = $pdf->addPage();
            material_pdf_header($pdf, $page, $order);
            $y = material_pdf_table_header($pdf, $page, 34);
        }

        $background = $index % 2 === 0 ? '#ffffff' : '#f5f8f7';
        $pdf->rect(
            10,
            $y,
            277,
            $rowHeight,
            $pdf->colour($background),
            $pdf->colour('#d7e3df'),
            $page
        );
        $pdf->text(
            12,
            $y + 5.4,
            (string) ($index + 1),
            7,
            false,
            $pdf->colour('#163630'),
            'C',
            9,
            $page
        );
        foreach ($categoryLines as $lineIndex => $categoryLine) {
            $pdf->text(
                25,
                $y + 5.2 + ($lineIndex * 4.2),
                $categoryLine,
                7,
                false,
                $pdf->colour('#163630'),
                'L',
                51,
                $page
            );
        }
        foreach ($articleLines as $lineIndex => $articleLine) {
            $pdf->text(
                80,
                $y + 5.2 + ($lineIndex * 4.2),
                $articleLine,
                7.5,
                true,
                $pdf->colour('#163630'),
                'L',
                98,
                $page
            );
        }
        $recipient = material_normalized_text($line['destinatario']);
        $recipientLabel = $recipient === 'nino'
            ? 'Niño'
            : ($recipient === 'nina' ? 'Niña' : 'Unisex');
        $pdf->text(
            182,
            $y + 5.4,
            $recipientLabel,
            7,
            false,
            $pdf->colour('#163630'),
            'C',
            23,
            $page
        );
        $pdf->text(
            209,
            $y + 5.4,
            (int) $line['talla'] > 0 ? (string) (int) $line['talla'] : '—',
            7,
            false,
            $pdf->colour('#163630'),
            'C',
            16,
            $page
        );
        $pdf->text(
            229,
            $y + 5.4,
            material_pdf_quantity($line['cantidad']),
            9,
            true,
            $pdf->colour('#163630'),
            'R',
            26,
            $page
        );
        $pdf->rect(
            269,
            $y + 2.6,
            5,
            5,
            null,
            $pdf->colour('#78918a'),
            $page
        );
        $y += $rowHeight;
    }

    if ($y + 9 > 190) {
        $page = $pdf->addPage();
        material_pdf_header($pdf, $page, $order);
        $y = 34;
    }
    $pdf->rect(
        10,
        $y,
        277,
        9,
        $pdf->colour('#dcece6'),
        null,
        $page
    );
    $pdf->text(
        150,
        $y + 6,
        'TOTAL DE UNIDADES',
        8,
        true,
        $pdf->colour('#102f29'),
        'R',
        105,
        $page
    );
    $pdf->text(
        259,
        $y + 6,
        material_pdf_quantity($order['total_unidades']),
        10,
        true,
        $pdf->colour('#102f29'),
        'R',
        26,
        $page
    );
    $y += 14;

    if ($y + 32 > 194) {
        $page = $pdf->addPage();
        material_pdf_header($pdf, $page, $order);
        $y = 34;
    }
    $pdf->rect(
        10,
        $y,
        183,
        30,
        $pdf->colour('#ffffff'),
        $pdf->colour('#d7e3df'),
        $page
    );
    $pdf->text(
        14,
        $y + 7,
        'Observaciones para el proveedor',
        8,
        true,
        $pdf->colour('#163630'),
        'L',
        175,
        $page
    );
    $pdf->rect(
        198,
        $y,
        89,
        30,
        $pdf->colour('#ffffff'),
        $pdf->colour('#d7e3df'),
        $page
    );
    $pdf->text(
        202,
        $y + 7,
        'Confirmación del pedido',
        8,
        true,
        $pdf->colour('#163630'),
        'L',
        81,
        $page
    );
    $pdf->text(
        202,
        $y + 16,
        'Fecha: ____________________',
        7,
        false,
        $pdf->colour('#48665d'),
        'L',
        81,
        $page
    );
    $pdf->text(
        202,
        $y + 24,
        'Firma: ____________________',
        7,
        false,
        $pdf->colour('#48665d'),
        'L',
        81,
        $page
    );

    $totalPages = $pdf->pageCount();
    for ($pageIndex = 0; $pageIndex < $totalPages; $pageIndex++) {
        $pdf->line(
            10,
            200,
            287,
            200,
            $pdf->colour('#d2e0db'),
            $pageIndex
        );
        $pdf->text(
            10,
            205,
            'Lápices · Pedido generado desde Gestión de material',
            6.5,
            false,
            $pdf->colour('#5b6f68'),
            'L',
            180,
            $pageIndex
        );
        $pdf->text(
            220,
            205,
            'Página ' . ($pageIndex + 1) . ' de ' . $totalPages,
            6.5,
            false,
            $pdf->colour('#5b6f68'),
            'R',
            67,
            $pageIndex
        );
    }

    $filename = 'pedido-material-' . preg_replace(
        '/[^A-Za-z0-9_-]+/',
        '-',
        (string) $order['codigo']
    ) . '.pdf';
    return $pdf->output($target, $destination, $filename);
}
