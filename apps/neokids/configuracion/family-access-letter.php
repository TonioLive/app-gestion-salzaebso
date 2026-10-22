<?php
/** Shared, permission-checked family access letters. No credential is renewed here. */
function family_access_letter_text($value) {
    return iconv('UTF-8','Windows-1252//TRANSLIT',app_text((string)$value));
}
function family_access_letters($single=false) {
    if(!security_can_view_family_passwords())security_stop();
    $centre=app_value($_GET,'id_escuela');
    if(!ctype_digit((string)$centre)||(int)$centre<1)security_stop('Centro no válido.',422);
    $centre=(int)$centre;
    if(!security_owned_centre($centre))security_stop();
    $centres=app_rows('SELECT nombre_centro FROM centros WHERE id_centro=?','i',array($centre));
    if(!$centres)security_stop('Centro no disponible.',404);
    $where='a.id_centro=?';$types='i';$values=array($centre);
    if($single) {
        $child=app_value($_GET,'id_alumno');$parent=app_value($_GET,'id_padre');
        if(!ctype_digit((string)$child)||!ctype_digit((string)$parent)||(int)$child<1||(int)$parent<1)security_stop('Familia no válida.',422);
        $where.=' AND p.padre_de=? AND p.id_padre=?';$types.='ii';$values[]=(int)$child;$values[]=(int)$parent;
    } else {
        $courses=app_rows('SELECT id_curso FROM cursos_academicos WHERE fecha_fin>=CURDATE() ORDER BY (fecha_inicio<=CURDATE()) DESC,fecha_inicio LIMIT 1');
        $where.=' AND a.id_curso=?';$types.='i';$values[]=$courses?(int)$courses[0]['id_curso']:0;
    }
    $sql='SELECT p.*,f.Nombre AS family_name,f.Apellido1 AS family_surname1,f.Apellido2 AS family_surname2,u.Nombre AS child_name,u.Apellido1 AS child_surname1,u.Apellido2 AS child_surname2,ca.nombre AS classroom FROM padres p JOIN usuarios f ON f.id_usuario=p.id_usuario JOIN usuarios u ON u.id_usuario=p.padre_de JOIN alumnos a ON a.id_usuario=p.padre_de LEFT JOIN centro_aulas ca ON ca.id_aula=a.aula WHERE '.$where.' ORDER BY ca.nombre,u.Apellido1,u.Apellido2,u.Nombre,p.id_padre,a.id_curso DESC';
    $rows=app_rows($sql,$types,$values);
    if($single&&!$rows)security_stop('La relación familiar no pertenece a este alumno y centro.',403);
    require_once __DIR__.'/dependencies.php';
    $pdf=new FPDF('P','mm','A4');$pdf->SetMargins(22,20,22);$pdf->SetAutoPageBreak(true,22);
    $neo=true;$brand=$neo?'Neo Kids':'ZEIM · Zaebsal';
    $url=$neo?'https://nueva.neo-kids.es/app/':'https://santpau.escuelas-infantiles.com/app/';
    $logo=dirname(__DIR__).'/pdf/'.($neo?'logo_redondo.jpg':'logo_factura'.$centre.'.jpg');
    $seen=array();
    foreach($rows as $row) {
        if(isset($seen[$row['id_padre']]))continue;$seen[$row['id_padre']]=true;
        $password=security_family_password($row);
        $pdf->AddPage();
        if(is_file($logo))$pdf->Image($logo,22,18,$neo?37:52);
        $pdf->SetXY(82,23);$pdf->SetTextColor(7,89,133);$pdf->SetFont('Helvetica','B',20);$pdf->Cell(106,10,family_access_letter_text($brand),0,1,'R');
        $pdf->SetX(82);$pdf->SetFont('Helvetica','',11);$pdf->Cell(106,7,family_access_letter_text('Acceso de las familias'),0,1,'R');
        $pdf->SetX(82);$pdf->SetTextColor(90,105,120);$pdf->SetFont('Helvetica','',9);$pdf->Cell(106,7,date('d/m/Y'),0,1,'R');
        $pdf->SetDrawColor(186,220,237);$pdf->Line(22,65,188,65);$pdf->SetY(73);
        $pdf->SetTextColor(35,50,65);$pdf->SetFont('Helvetica','B',11);
        $childName=trim($row['child_name'].' '.$row['child_surname1'].' '.$row['child_surname2']);
        $familyName=trim($row['family_name'].' '.$row['family_surname1'].' '.$row['family_surname2']);
        $pdf->MultiCell(166,6,family_access_letter_text('Alumno/a: '.$childName));
        $pdf->SetFont('Helvetica','',10);$pdf->MultiCell(166,6,family_access_letter_text($centres[0]['nombre_centro'].($row['classroom']?' · '.$row['classroom']:'')));
        $pdf->Ln(5);$pdf->MultiCell(166,6,family_access_letter_text('Para: '.$familyName));$pdf->Ln(8);
        $pdf->MultiCell(166,6,family_access_letter_text('Estos son tus datos de acceso al área de familias. Puedes utilizar la misma contraseña que ya tenías.'));
        $pdf->Ln(5);$pdf->MultiCell(166,6,family_access_letter_text('1. Abre la aplicación de familias o entra en:'));
        $pdf->SetTextColor(7,89,133);$pdf->Cell(166,8,$url,0,1,'L',false,$url);$pdf->SetTextColor(35,50,65);
        $pdf->MultiCell(166,6,family_access_letter_text('2. Introduce tu usuario y contraseña y pulsa Entrar.'));$pdf->Ln(7);
        $pdf->SetFillColor(237,246,251);$pdf->SetFont('Helvetica','B',12);
        $pdf->MultiCell(166,10,family_access_letter_text('Usuario: '.$row['usuario_webcam']),0,'L',true);
        $pdf->MultiCell(166,10,family_access_letter_text('Contraseña: '.($password!==''?$password:'Consulta con administración')),0,'L',true);
        $pdf->Ln(9);$pdf->SetFont('Helvetica','',10);
        $pdf->MultiCell(166,6,family_access_letter_text($neo?'Desde la aplicación puedes consultar las clases, los mensajes, los avisos y la facturación.':'Desde la aplicación puedes consultar la información del centro, la agenda, los mensajes y los recibos.'));
        $pdf->Ln(6);$pdf->MultiCell(166,6,family_access_letter_text('Guarda estos datos para tu uso personal. Si necesitas ayuda para entrar, contacta con el centro.'));
        $pdf->Ln(9);$pdf->SetFont('Helvetica','B',10);$pdf->Cell(166,7,family_access_letter_text($brand),0,1,'R');
        if($single)break;
    }
    if(!$seen){$pdf->AddPage();$pdf->SetFont('Helvetica','',12);$pdf->MultiCell(166,7,family_access_letter_text('No hay familias para este centro en el curso seleccionado.'));}
    header('Cache-Control: no-store, private');header('Pragma: no-cache');
    $pdf->Output('I',$single?'acceso-familia.pdf':'accesos-familias.pdf');
}
