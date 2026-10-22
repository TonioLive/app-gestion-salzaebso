<?php
require_once __DIR__.'/../configuracion/family-assets.php';
$file=family_asset_path(app_value($_GET,'file'),'image');
family_send_asset($file,true,false);
