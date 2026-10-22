<font size=3 face="<?=$fuente_general;?>">Haz click sobre tu nombre para iniciar la sesión... (¡¡PROVISIONAL!!)</font><br><br>
<?php 
$listado_usuarios=db_query("SELECT * FROM usuarios WHERE nivel_acceso>=4");

while($usuarios=db_fetch_array($listado_usuarios)) {
?>
<input type="button" value="<?=$usuarios["usuario"];?>" style="font-size: 16px" onclick="window.location.href='<?=$url_web;?>/configuracion/inicio_sesion_sp.php?usuario=<?=$usuarios["usuario"];?>'">
<?php 
}
?>
