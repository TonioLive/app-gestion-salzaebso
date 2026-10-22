<?php
require __DIR__.'/common.php';pool_require_administration();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);header('Allow: POST');exit;}
app_check_csrf();$source=app_value($_POST,'source');if($source!=='water'&&!($source==='air'&&app_value($_POST,'action')==='close')){http_response_code(422);exit('Equipo no válido.');}
require __DIR__.'/controller-session.php';header('Cache-Control: no-store');header('Referrer-Policy: no-referrer');header('X-Frame-Options: DENY');
$closed=true;if(isset($_SESSION['pool_controller_sessions'][$source])){$closed=pool_controller_session_close($_SESSION['pool_controller_sessions'][$source]);if($closed)unset($_SESSION['pool_controller_sessions'][$source]);}
if(app_value($_POST,'action')==='close'){$_SESSION['pool_access_notice']=$closed?'Acceso al controlador cerrado.':'El equipo no responde y no se ha podido confirmar el cierre. Vuelve a intentarlo cuando se recupere la conexión.';header('Location: estado.php',true,303);exit;}
if(!$closed){http_response_code(503);exit('No se ha podido cerrar la sesión anterior del controlador. Vuelve al estado de la piscina e inténtalo de nuevo.');}
try{$s=pool_controller_session($source);$_SESSION['pool_controller_sessions'][$source]=array('base'=>$s['base'],'sid'=>$s['sid']);}catch(Exception $e){http_response_code(503);pool_header('No se ha podido abrir el equipo','estado');echo '<p class="alert">El controlador no está disponible o no ha aceptado la conexión. Puedes volver a intentarlo desde el estado de la piscina.</p><a class="quiet" href="estado.php">Volver al estado</a>';pool_footer();exit;}
$nonce=bin2hex(openssl_random_pseudo_bytes(16));header("Content-Security-Policy: default-src 'none'; script-src 'nonce-".$nonce."'; form-action ".$s['base']."; frame-ancestors 'none'; base-uri 'none'");header('Content-Type: text/html; charset=UTF-8');session_write_close();
// Only the authenticated temporary session crosses to the device.
// Never expose the password or its reusable strong hash.
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="referrer" content="no-referrer"><title>Abriendo controlador · Neo Kids</title></head><body><p>Abriendo el controlador…</p><form id="launch" method="post" action="<?php echo app_escape($s['base']); ?>/standard/mainframe.php"><input type="hidden" name="SessionID" value="<?php echo app_escape($s['sid']); ?>"><input type="hidden" name="LocaleID" value="1034"><button>Entrar al controlador</button></form><script nonce="<?php echo $nonce; ?>">document.getElementById('launch').submit();</script></body></html>
