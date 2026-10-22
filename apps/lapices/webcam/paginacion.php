<?php 
$num_paginas=ceil($resultado_total/$numero_resultados);
$ultima_pagina=($num_paginas-1)*$numero_resultados;

if ($inicio_listado>$resultado_total) {
	$inicio_listado=$ultima_pagina;	
	}
?>
<font size="2"><h4>&nbsp;Se han encontrado <?=$resultado_total?> mensajes. Mostrando del <?=$inicio_listado+1?> al <?php  if ($total_resultado_busqueda!=$numero_resultados) { echo($inicio_listado+$total_resultado_busqueda); } else { echo($inicio_listado+$numero_resultados); } ?></h4>
&nbsp;Página: <?php 

if ($inicio_listado!=0) {
$pagina_actual=ceil($inicio_listado/$numero_resultados)+1;
} else {
$pagina_actual=1;
}

if ($pagina_actual<5) {
        $inicio_bucle=1;
        } else {
        $inicio_bucle=$pagina_actual-4;
        }

if ($inicio_bucle!=1) {
?>...<?php 
}

$fin_bucle=($inicio_bucle+9);

$mostrar_puntossus=false;

if ($fin_bucle>$num_paginas) {
        $fin_bucle=$num_paginas;
        } else {
        $mostrar_puntossus=true;
        }

for ($cpag=$inicio_bucle;$cpag<=$fin_bucle;$cpag++) {

if ($cpag!=$pagina_actual) {
?>[<a href="<?=$nombe_script?>.php?q=<?=$busqueda?>&i=<?=(($cpag*$numero_resultados)-$numero_resultados)?>"><?php 
} else {
?><font color=red>&nbsp;<?php 
}
?><?=$cpag?><?php 
if ($cpag!=$pagina_actual) {
?></a>]<?php 
} else {
?>&nbsp;</font><?php 
}

}

if ($mostrar_puntossus==true) {
?>...<?php 
        }
?> de <?=$num_paginas?>.&nbsp;&nbsp;&nbsp;&nbsp;<?php 

if ($inicio_listado!=0) {
?> <a href="<?=$nombe_script?>.php?q=<?=$busqueda?>&i=0"><< Primera</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$nombe_script?>.php?q=<?=$busqueda?>&i=<?=($inicio_listado-$numero_resultados)?>">< Anterior</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
}

if ($total_resultado_busqueda==$numero_resultados) {
?>
 <a href="<?=$nombe_script?>.php?q=<?=$busqueda?>&i=<?=($inicio_listado+$numero_resultados)?>">Siguiente></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$nombe_script?>.php?q=<?=$busqueda?>&i=<?=$ultima_pagina?>">Útima >></a>
<?php 
}
?></font>