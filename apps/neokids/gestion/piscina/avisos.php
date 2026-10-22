<?php
require __DIR__.'/common.php';pool_require('zona_mensajes');
if(!$centro_actual){header('Location: /');exit;}
app_db()->set_charset('utf8mb4');$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 app_check_csrf();
 if(app_value($_POST,'action')==='archive') {
  $ok=db_query_params('UPDATE piscina_avisos SET archivado=1 WHERE id_aviso=? AND id_centro=?','ii',array((int)app_value($_POST,'id'),$centro_actual),app_db());
 } else {
  $title=trim((string)app_value($_POST,'title'));$body=trim((string)app_value($_POST,'body'));$expires=(string)app_value($_POST,'expires');
  if($title===''||strlen($title)>180||$body===''||strlen($body)>12000||($expires!==''&&(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$expires)||date('Y-m-d',strtotime($expires))!==$expires||$expires<date('Y-m-d'))))$error='Revisa el título, el texto y la fecha de caducidad.';
  else $ok=db_query_params('INSERT INTO piscina_avisos (id_centro,titulo,texto,publicado,caduca,autor) VALUES (?,?,?,NOW(),?,?)','isssi',array($centro_actual,$title,$body,$expires===''?null:$expires,$idUsuario),app_db());
 }
 if(!$error && !empty($ok)){header('Location: avisos.php?guardado=1',true,303);exit;}
 if(!$error)$error='No se ha podido guardar el aviso.';
}
$rows=app_rows('SELECT * FROM piscina_avisos WHERE id_centro=? AND archivado=0 ORDER BY publicado DESC LIMIT 100','i',array($centro_actual));
pool_header('Avisos a familias','avisos');
if($error)echo '<p class="alert" role="alert">'.app_escape($error).'</p>';if(isset($_GET['guardado']))echo '<p class="notice" role="status">Aviso guardado.</p>';
?>
<p>Publica novedades, cambios de horario y recordatorios para las familias de <?php echo app_escape($nombre_centro_actual); ?>.</p><details class="card" <?php if($error)echo 'open'; ?>><summary>+ Publicar un aviso</summary><form method="post" class="stack"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><label for="title">Título</label><input id="title" name="title" maxlength="180" required><label for="body">Aviso</label><textarea id="body" name="body" rows="6" maxlength="12000" required></textarea><label for="expires">Visible hasta (opcional)</label><input type="date" id="expires" name="expires" min="<?php echo date('Y-m-d'); ?>"><button class="primary">Publicar para las familias</button></form></details>
<?php foreach($rows as $n){ ?><article class="card"><span class="eyebrow"><?php echo app_escape($n['publicado']); ?><?php if($n['caduca'])echo ' · Hasta '.app_escape($n['caduca']); ?></span><h2><?php echo app_escape($n['titulo']); ?></h2><p class="message-text"><?php echo nl2br(app_escape($n['texto'])); ?></p><form method="post"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="archive"><input type="hidden" name="id" value="<?php echo (int)$n['id_aviso']; ?>"><button class="quiet">Retirar aviso</button></form></article><?php } if(!$rows)echo '<section class="card empty"><h2>No hay avisos publicados</h2><p>Los avisos que publiques aparecerán en la app de familias.</p></section>';pool_footer(); ?>
