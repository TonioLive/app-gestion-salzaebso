<?php
// New monitor screens enforce group ownership. Do not leave legacy write routes as a bypass.
if(!empty($permitido)&&(int)$nivel_acceso!==4&&!((int)$nivel_acceso===3&&app_can('zona_aulas'))){
 $root=str_replace('\\','/',realpath(dirname(__DIR__)));
 $script=str_replace('\\','/',(string)realpath(app_value($_SERVER,'SCRIPT_FILENAME')));
 $route=strtolower(strpos($script,$root.'/')===0?substr($script,strlen($root)):(string)parse_url(app_value($_SERVER,'REQUEST_URI'),PHP_URL_PATH));
 $table=strtolower(trim((string)app_value($_POST,'tabla',app_value($_GET,'tabla'))));
 if(preg_match('~^/gestion/grupos[^/]*/~',$route)||preg_match('~/(?:guarda_dato_programacion|guarda_dato_evaluacion)\.php$~',$route)||strpos($table,'agenda_programacion')===0){
  http_response_code(403);header('Content-Type: text/html; charset=UTF-8');exit('Esta operación no está disponible para monitores. <a href="/gestion/piscina/grupos.php">Abrir mis grupos y programaciones</a>');
 }
}
