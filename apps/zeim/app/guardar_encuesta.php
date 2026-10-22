<?php
require_once __DIR__.'/../configuracion/config.php';
require_once __DIR__.'/../configuracion/autentifica_padre.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);exit;}
$survey=(int)app_value($_POST,'id_encuesta');
$surveys=app_rows('SELECT * FROM consultas_padres WHERE id_consulta=? AND fecha_inicio<=CURDATE() AND fecha_fin>=CURDATE()','i',array($survey));
if (!$surveys) family_forbidden();$surveyRow=$surveys[0];$answers=array();
foreach ($_POST as $key=>$value) {
    if (!preg_match('/^id_hijo_\d+$/D',$key)) continue;
    if (!is_scalar($value)||!ctype_digit((string)$value)||count($answers)>=100) family_forbidden();
    $child=(int)$value;family_child($familyChildren,$child);
    $valid=app_rows('SELECT id_alumno FROM alumnos WHERE id_usuario=? AND id_centro=? AND (?=0 OR id_curso=?) LIMIT 1','iiii',array($child,(int)$surveyRow['id_centro'],(int)$surveyRow['id_curso'],(int)$surveyRow['id_curso']));
    if (!$valid) family_forbidden();
    $option=app_value($_POST,'asiste_'.$survey.'_'.$child);
    $text=app_value($_POST,'horario_'.$survey.'_'.$child);
    $max=(int)$surveyRow['numero_respuestas_posibles']===3?3:2;
    if (!is_scalar($option)||!ctype_digit((string)$option)||(int)$option<1||(int)$option>$max||!is_string($text)||strlen($text)>255) {http_response_code(422);exit('Revisa las respuestas y el horario indicado.');}
    $answers[$child]=array((int)$option,$text);
}
if (!$answers) {http_response_code(422);exit('Selecciona al menos una respuesta.');}
$lock='family_survey_'.$survey.'_'.$id_usuario;
$locked=app_rows('SELECT GET_LOCK(?,3) AS acquired','s',array($lock));
if (empty($locked[0]['acquired'])) {http_response_code(409);exit('Inténtalo de nuevo.');}
try {
    foreach ($answers as $child=>$answer) {
        // Repeated submission must not create duplicate responses for this child.
        $exists=app_rows('SELECT id_respuesta FROM consultas_padres_respuestas WHERE id_consulta=? AND id_alumno=? LIMIT 1','ii',array($survey,$child));
        if (!$exists) {
            $stmt=app_db()->prepare('INSERT INTO consultas_padres_respuestas (id_consulta,id_alumno,id_usuario,asistira,horario_deseado) VALUES (?,?,?,?,?)');
            $stmt->bind_param('iiiis',$survey,$child,$id_usuario,$answer[0],$answer[1]);
            if (!$stmt->execute()) {http_response_code(503);exit('No se ha podido guardar la respuesta.');}
            $stmt->close();
        }
    }
} finally {app_rows('SELECT RELEASE_LOCK(?)','s',array($lock));}
header('Location: /app/index.php?mensajeOK=1',true,303);exit;
