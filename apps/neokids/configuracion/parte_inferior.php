	</div>
    </td>
    <td width="15"><img border="0" src="<?php $onload_script = isset($onload_script) ? $onload_script : "";  echo $url_root ; ?>/imagen/nada.gif" width=10 height=600></td>
  </tr>
<tr>
<td colspan="3">
<br><div align="center"><font size=1 face="<?=$fuente_general?>"><b><?=$texto_pie_pagina;?></b></font></div>
</td>
</tr>
</table>
</div>
<?php  require("inc/capas.php"); ?>
<?php
	
if ($onload_script!="") {
?>
<script>
	
$( document ).ready(function() {

<?=$onload_script;?>

});	
	
</script>	
<?php
}
?>
</body>
</html>

