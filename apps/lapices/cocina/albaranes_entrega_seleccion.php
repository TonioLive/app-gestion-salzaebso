<?php
require("../configuracion/config.php");
require("../configuracion/parte_superior_sin_login_ancho_bootstrap.php");

$inicio=$_GET["dia"];

if (strlen($inicio)==0) {
    $inicio=time();  
    $fecha_time_stamp=time();
} else {
    $fecha_time_stamp=$inicio;
}
  		
$tipo_lista=1;

$busca_centros=db_query("SELECT * FROM centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0) AND lista_compra=$tipo_lista ORDER BY nombre_centro;");               
    
?>
<div class="container">
    <h1>Albaranes para el  día <?=date("d/m/Y",$inicio)?></h1>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php
                // Busca centros
                while($centros=db_fetch_array($busca_centros)) {
                ?>
                    <a class="btn btn-lg btn-primary" style="margin-bottom: 2em;margin-right: 2em;" href="/cocina/albaranes_entrega_platos_firma.php?dia=<?=$fecha_time_stamp?>&id_centro=<?=$centros["id_centro"]?>"><?=$centros["nombre_centro"]?></a>
                <?php
                }
            ?>
        </div>
    </div>
    <a class="btn btn-lg btn-success" style="margin-bottom: 2em;margin-right: 2em;" href="/cocina/calendario_mes_comedor.php?dia=<?=date("Y/m/d",$fecha_time_stamp)?>">Salir</a>
</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>