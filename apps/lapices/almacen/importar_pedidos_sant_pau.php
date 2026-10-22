<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Busca aulas Sant Pau nuevas

$busca_nuevas=db_query("SELECT * FROM escuelamunicipal.centro_aulas WHERE id_aula_pedidos=0 AND id_aula>=10") or die(db_error());	;

while($nuevas_aulas=db_fetch_array($busca_nuevas)) {

	// Inserta registro
	$inserta=db_query("INSERT INTO centro_aulas (id_centro,nombre,capacidad,ocupacion_minima,descripcion,equipamiento,edad_minima,edad_maxima,id_curso,tipo,docente_titular,id_menu_predefinido,mes_minimo,anio_minimo,mes_maximo,anio_maximo,ciclo,edad_minima_prog,edad_maxima_prog,id_aula_pedidos)SELECT id_centro,nombre,capacidad,ocupacion_minima,descripcion,equipamiento,edad_minima,edad_maxima,id_curso,tipo,docente_titular,id_menu_predefinido,mes_minimo,anio_minimo,mes_maximo,anio_maximo,ciclo,edad_minima_prog,edad_maxima_prog,id_aula_pedidos FROM escuelamunicipal.centro_aulas WHERE id_aula=".$nuevas_aulas["id_aula"]) or die(db_error());	;

	$nuevo_id=db_insert_id();
	
	// Actualiza
	db_query("UPDATE escuelamunicipal.centro_aulas SET id_aula_pedidos=$nuevo_id WHERE id_aula=".$nuevas_aulas["id_aula"]) or die(db_error());	;

}


// Busca pedidos Sant Pau sin exportar
$busca_pedidos=db_query("SELECT * FROM escuelamunicipal.pedidos_material_centros WHERE exportado=0") or die(db_error());

while($pedidos=db_fetch_array($busca_pedidos)) {

	// Busca ultimo pedido centro en lapices
	$busca_ultimo_pc=db_query("SELECT * FROM pedidos_material_centros ORDER BY id_pedido_centro DESC LIMIT 0,1") or die(db_error());
	if (db_num_rows($busca_ultimo_pc)!=0) {
	
		$datos_ultimo_pc=db_fetch_array($busca_ultimo_pc);
		$proximo_pc=$datos_ultimo_pc["id_pedido_centro"]+1;		
	
		} else {
		
		$proximo_pc=1;
		
		}
	$id_pc_actual=$pedidos["id_pedido_centro"];
	$cambia_pc=db_query("UPDATE escuelamunicipal.pedidos_material_centros SET id_pedido_centro=$proximo_pc WHERE id_pedido_centro=$id_pc_actual");
		
	$cambia_pc_en_dp=db_query("UPDATE escuelamunicipal.detalles_pedido_material SET id_pedido_centro=$proximo_pc WHERE id_pedido_centro=$id_pc_actual");
		
	// Busca ultimo dp
	$busca_ultimo_dp=db_query("SELECT * FROM detalles_pedido_material ORDER BY id_detalle DESC LIMIT 0,1") or die(db_error());
	if (db_num_rows($busca_ultimo_dp)!=0) {
	
		$datos_ultimo_dp=db_fetch_array($busca_ultimo_dp);
		$proximo_dp=$datos_ultimo_dp["id_detalle"]+1;	
	
		} else {
		
		$proximo_dp=1;
		
		}
		
	$busca_ultimo_dp_em=db_query("SELECT * FROM escuelamunicipal.detalles_pedido_material ORDER BY id_detalle LIMIT 0,1") or die(db_error());	
	
	$datos_ultimo_dp_em=db_fetch_array($busca_ultimo_dp_em);
	$ultimo_dp_em=$datos_ultimo_dp_em["id_detalle"];
	
	// Diferencia
	if ($ultimo_dp_em<$proximo_dp) {
		$diferencia_dp=$proximo_dp-$ultimo_dp_em;
		} else {
		$diferencia_dp=0;
		}
				 	
	// Actualiza	
	db_query("UPDATE escuelamunicipal.detalles_pedido_material SET id_detalle=id_detalle+$diferencia_dp WHERE id_pedido_centro=$proximo_pc") or die(db_error());	


	// Copia
	
	// 1. Copia pedido centro
	$inserta=db_query("INSERT INTO pedidos_material_centros (id_pedido_centro,id_centro,id_usuario,fecha_pedido,fecha_entrega,observaciones,exportado) SELECT id_pedido_centro,id_centro,id_usuario,fecha_pedido,fecha_entrega,observaciones,exportado FROM escuelamunicipal.pedidos_material_centros WHERE escuelamunicipal.pedidos_material_centros.id_pedido_centro=$proximo_pc") or die(db_error());	;

	// 2. Copia detalles pedidos aulas
	$inserta=db_query("INSERT INTO detalles_pedido_material (id_detalle,id_pedido,id_pedido_centro,id_pedido_empresa,generico_solicitado,id_material,cantidad,cantidad_original,entregado,observaciones,id_albaran,confirmado) SELECT id_detalle,id_pedido,id_pedido_centro,id_pedido_empresa,generico_solicitado,id_material,cantidad,cantidad_original,entregado,observaciones,id_albaran,confirmado FROM escuelamunicipal.pedidos_material_centros WHERE escuelamunicipal.detalles_pedido_material.id_pedido_centro=$proximo_pc") or die(db_error());	;

	// 3. Copia pedidos aula
	$busca_pa=db_query("SELECT id_pedido FROM escuelamunicipal.detalles_pedido_material WHERE id_pedido_centro=$proximo_pc GROUP BY id_pedido ORDER BY id_pedido") or die(db_error());
	
	while($lista_pa=db_fetch_array($busca_pa)) {
	
		$inserta=db_query("INSERT INTO pedidos_material (id_pedido,id_aula,id_usuario,fecha_pedido,fecha_entrega,observaciones,validado_coordinador) SELECT id_pedido,id_aula,id_usuario,fecha_pedido,fecha_entrega,observaciones,validado_coordinador FROM escuelamunicipal.pedidos_material WHERE escuelamunicipal.pedidos_material.id_pedido=".$lista_pa["id_pedido"]) or die(db_error());	;
	
	}
	
	// Marca como exportado
	$cierra_pedido=db_query("UDPATE escuelamunicipal.pedidos_material_centros SET exportado=1 WHERE id_pedido_centro=$proximo_pc") or die(db_error());	;

}

header("Location: pedidos_escuelas.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>