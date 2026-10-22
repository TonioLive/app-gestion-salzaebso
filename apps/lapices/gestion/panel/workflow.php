<?php
// Shared by the personnel and supplier forms; compatible with PHP 5.6.
function lap_wf_rows($sql,$types='',$values=array()) {
    $r=$types===''?db_query($sql):db_query_params($sql,$types,$values);
    if(!($r instanceof mysqli_result))throw new RuntimeException('No se han podido consultar los datos. Inténtalo de nuevo.');
    $rows=array();while($row=db_fetch_assoc($r))$rows[]=$row;db_free_result($r);return $rows;
}
function lap_wf_exec($sql,$types='',$values=array()) {
    if(!($types===''?db_query($sql):db_query_params($sql,$types,$values)))throw new RuntimeException('No se ha podido guardar el cambio. Inténtalo de nuevo.');
    return db_insert_id();
}
function lap_wf_post() {
    if(app_request_value($_SERVER,'REQUEST_METHOD','GET')!=='POST'||!app_verify_csrf(app_request_value($_POST,'_csrf','')))throw new DomainException('La solicitud ha caducado. Recarga la pantalla e inténtalo de nuevo.');
}
function lap_wf_token() {echo '<input type="hidden" name="_csrf" value="'.app_escape(app_csrf_token()).'">';}
function lap_wf_start($title,$intro,$workflowSection='personal') {
    $wfGlobals=$GLOBALS;extract($wfGlobals,EXTR_SKIP);unset($wfGlobals);
    require __DIR__.'/bootstrap.php';
    $panel_active_section=$workflowSection;
    $panel_extra_styles=array('/gestion/panel/assets/workflow.css?v=20261016.1');
    require __DIR__.'/views/header.php';
    echo '<section class="wf"><div class="wf-heading"><span class="eyebrow">'.app_escape($workflowSection==='almacen'?'Proveedores':'Personal').'</span><h1>'.app_escape($title).'</h1><p>'.app_escape($intro).'</p></div>';
}
function lap_wf_end(){$wfGlobals=$GLOBALS;extract($wfGlobals,EXTR_SKIP);unset($wfGlobals);echo '</section>';require __DIR__.'/views/footer.php';}
function lap_wf_error($error){if($error!=='')echo '<div class="wf-alert" role="alert">'.app_escape($error).'</div>';}
function lap_wf_input($label,$name,$value,$type='text',$required=false,$max=255){
    echo '<label>'.app_escape($label).'<input type="'.app_escape($type).'" name="'.app_escape($name).'" value="'.app_escape($value).'" maxlength="'.(int)$max.'"'.($required?' required':'').'></label>';
}
