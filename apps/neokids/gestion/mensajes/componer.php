<?php
require_once __DIR__.'/common.php';
$child=(int)app_value($_GET,'id_alumno');$student=$child?fm_child($child):null;
$q=mb_substr(trim((string)app_value($_GET,'q')),0,120);$choices=[];
if(!$child&&$q!==''){
 $types='s';$values=['%'.$q.'%'];$scope=fm_child_scope('u.id_usuario',$types,$values);
 $choices=app_rows('SELECT u.id_usuario,CONCAT_WS(" ",u.Nombre,u.Apellido1,u.Apellido2) AS alumno FROM usuarios u WHERE CONCAT_WS(" ",u.Nombre,u.Apellido1,u.Apellido2) LIKE ? AND EXISTS(SELECT 1 FROM padres p WHERE p.padre_de=u.id_usuario) AND '.$scope.' ORDER BY u.Apellido1,u.Apellido2,u.Nombre LIMIT 30',$types,$values);
}
fm_header('Nuevo mensaje a una familia');
if(!$child){?>
<form class="fm-toolbar" method="get"><label class="fm-search">Busca al alumno<input type="search" name="q" value="<?=app_escape($q)?>" placeholder="Nombre y apellidos del alumno" maxlength="120" required></label><button class="primary">Buscar familia</button></form><div class="fm-student-options"><?php foreach($choices as $choice){?><a href="nuevo_mensaje.php?id_alumno=<?=(int)$choice['id_usuario']?>"><?=app_escape($choice['alumno'])?> →</a><?php }?></div><?php if($q!==''&&!$choices){?><p class="fm-empty">No se han encontrado familias con ese nombre dentro de tu ámbito de acceso.</p><?php }if(count($choices)===30){?><p>Mostrando los primeros 30 resultados. Añade un apellido para concretar la búsqueda.</p><?php }
}else{?>
<div class="fm-conversation"><p class="fm-student">Para la familia de <?=app_escape($student['alumno'])?></p><form class="fm-compose" method="post" action="acciones.php"><?php fm_post_fields('new');fm_input('id_alumno',$child);?><label>Asunto<input name="subject" maxlength="150" required></label><label>Mensaje<textarea name="text" maxlength="12000" rows="7" required></textarea></label><p class="fm-muted">Lo podrán leer los familiares vinculados a este alumno en su app y responder en la misma conversación.</p><button class="primary">Enviar mensaje</button></form></div>
<?php }fm_footer(); ?>
