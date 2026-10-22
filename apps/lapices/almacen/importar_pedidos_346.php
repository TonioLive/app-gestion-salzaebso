<?php 
die();
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
echo("Si");
	// Copia
	
	// 1. Copia pedido centro
	// $inserta=mysql_query("INSERT INTO pedidos_material_centros (id_pedido_centro,id_centro,id_usuario,fecha_pedido,fecha_entrega,observaciones,exportado) SELECT id_pedido_centro,id_centro,id_usuario,fecha_pedido,fecha_entrega,observaciones,exportado FROM escuelamunicipal.pedidos_material_centros WHERE escuelamunicipal.pedidos_material_centros.id_pedido_centro=$proximo_pc") or die(mysql_error());	;

	// 2. Copia detalles pedidos aulas
	
	$busca_detalles_pedido=db_query("SELECT * FROM escuelamunicipal.detalles_pedido_material WHERE escuelamunicipal.detalles_pedido_material.id_pedido_centro=346") or die (db_error()."-A");
	
	while($detalles_pedido=db_fetch_array($busca_detalles_pedido)) {
	
	echo("INSERT INTO gestionguarderias.detalles_pedido_material (id_detalle,id_pedido,id_pedido_centro,id_pedido_empresa,generico_solicitado,id_material,cantidad,cantidad_original,entregado,observaciones,id_albaran,confirmado) values ('".$detalles_pedido["id_detalle"]."','".$detalles_pedido["id_pedido"]."','".$detalles_pedido["id_pedido_centro"]."','".$detalles_pedido["id_pedido_empresa"]."','".$detalles_pedido["generico_solicitado"]."','".$detalles_pedido["id_material"]."','".$detalles_pedido["cantidad"]."','".$detalles_pedido["cantidad_original"]."','".$detalles_pedido["entregado"]."','".$detalles_pedido["observaciones"]."','".$detalles_pedido["id_albaran"]."','".$detalles_pedido["confirmado"]."');<br>");
	$inserta=db_query("INSERT INTO gestionguarderias.detalles_pedido_material (id_detalle,id_pedido,id_pedido_centro,id_pedido_empresa,generico_solicitado,id_material,cantidad,cantidad_original,entregado,observaciones,id_albaran,confirmado) values ('".$detalles_pedido["id_detalle"]."','".$detalles_pedido["id_pedido"]."','".$detalles_pedido["id_pedido_centro"]."','".$detalles_pedido["id_pedido_empresa"]."','".$detalles_pedido["generico_solicitado"]."','".$detalles_pedido["id_material"]."','".$detalles_pedido["cantidad"]."','".$detalles_pedido["cantidad_original"]."','".$detalles_pedido["entregado"]."','".$detalles_pedido["observaciones"]."','".$detalles_pedido["id_albaran"]."','".$detalles_pedido["confirmado"]."')") or die(db_error()."-1");	
		
	}
	

	// 3. Copia pedidos aula
	$busca_pa=db_query("SELECT id_pedido FROM escuelamunicipal.detalles_pedido_material WHERE id_pedido_centro=346 GROUP BY id_pedido ORDER BY id_pedido") or die(db_error()."-2");
	
	while($lista_pa=db_fetch_array($busca_pa)) {	
	
		$busca_datos_pedidos=db_query("SELECT id_pedido,id_aula,id_usuario,fecha_pedido,fecha_entrega,observaciones,validado_coordinador FROM escuelamunicipal.pedidos_material WHERE escuelamunicipal.pedidos_material.id_pedido=".$lista_pa["id_pedido"]) or die(db_error()."-B");
		
		$detalles_pedido=db_fetch_array($busca_datos_pedidos);
	
		$inserta=db_query("INSERT INTO gestionguarderias.pedidos_material (id_pedido,id_aula,id_usuario,fecha_pedido,fecha_entrega,observaciones,validado_coordinador) VALUES ('".$detalles_pedido["id_pedido"]."','".$detalles_pedido["id_aula"]."','".$detalles_pedido["id_usuario"]."','".$detalles_pedido["fecha_pedido"]."','".$detalles_pedido["fecha_entrega"]."','".$detalles_pedido["observaciones"]."','".$detalles_pedido["validado_coordinador"]."')") or die(db_error()."-3");
		echo("INSERT INTO gestionguarderias.pedidos_material (id_pedido,id_aula,id_usuario,fecha_pedido,fecha_entrega,observaciones,validado_coordinador) VALUES ('".$detalles_pedido["id_pedido"]."','".$detalles_pedido["id_aula"]."','".$detalles_pedido["id_usuario"]."','".$detalles_pedido["fecha_pedido"]."','".$detalles_pedido["fecha_entrega"]."','".$detalles_pedido["observaciones"]."','".$detalles_pedido["validado_coordinador"]."');<br>");
	}

// header("Location: pedidos_escuelas.php");
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