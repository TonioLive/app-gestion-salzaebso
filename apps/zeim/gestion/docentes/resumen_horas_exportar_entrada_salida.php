<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php"); 

$id_aula=$_GET["id_aula"];
$mes_sel=$_GET["mes"];
$ano_sel=$_GET["ano"];

// Busca datos del aula
$consulta_datos_educadora=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_educadora);


$nombre_trabajador=utf8_encode($datos_aula["Nombre"]." ".$datos_aula["Apellido1"]." ".$datos_aula["Apellido2"]);
$nif_trabajador=utf8_encode($datos_aula["DNI"]);
$nss_trabajador=utf8_encode($datos_aula["nss"]);
$razon_social_empresa=utf8_encode("ZAEBSAL, s.l.");
$nif_empresa=utf8_encode("B97324123");
$ccc_empresa=utf8_encode("46118861376");

$periodo_liquidacion=utf8_encode($nombre_mes[$mes_sel]." ".$ano_sel);

$numero_dias_mes=date("t",strtotime($ano_sel."-".$mes_sel."-1"));

$nombre_archivo=utf8_encode($datos_aula["Nombre"]."_".$datos_aula["Apellido1"]."_".$datos_aula["Apellido2"]."-".$nombre_mes[$mes_sel]."_".$ano_sel);

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Registro_Diario_".$nombre_archivo.".xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);

echo('<'.chr(63).'xml version="1.0"'.chr(63).'>');

?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Delatorre</Author>
  <LastAuthor>Usuario de Microsoft Office</LastAuthor>
  <LastPrinted>2016-02-10T09:53:35Z</LastPrinted>
  <Created>2014-02-13T12:44:13Z</Created>
  <LastSaved>2016-11-15T11:13:39Z</LastSaved>
  <Version>15.0</Version>
 </DocumentProperties>
 <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
  <AllowPNG/>
  <PixelsPerInch>96</PixelsPerInch>
 </OfficeDocumentSettings>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>15460</WindowHeight>
  <WindowWidth>25600</WindowWidth>
  <WindowTopX>0</WindowTopX>
  <WindowTopY>460</WindowTopY>
  <TabRatio>600</TabRatio>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="m2106752416">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="m2106752436">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="m2106752456">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="m2106752476">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="m2106752496">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="m2106752516">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="m2106752536">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="m2106752032">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="m2106752052">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="16" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="m2106752072">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="m2106752092">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s16">
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s17">
   <Borders>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s18">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s19">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s20">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s21">
   <Alignment ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s22">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s23">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders/>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s24">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s25">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
   <NumberFormat ss:Format="@"/>
  </Style>
  <Style ss:ID="s26">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="s27">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="s28">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
   <NumberFormat ss:Format="@"/>
  </Style>
  <Style ss:ID="s29">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="s30">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="s31">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#808080" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s32">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000" ss:Bold="1"/>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s33">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
  </Style>
  <Style ss:ID="s34">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s35">
   <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s36">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s37">
   <Borders/>
  </Style>
  <Style ss:ID="s38">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s39">
   <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s40">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s41">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s42">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s43">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s44">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s45">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s46">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s47">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s48">
   <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
   <Borders/>
  </Style>
  <Style ss:ID="s49">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
  </Style>
  <Style ss:ID="s50">
   <Alignment ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="s80">
   <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s81">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders/>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s82">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s83">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s84">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s85">
   <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s86">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s87">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s88">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s89">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s90">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s91">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s92">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s93">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>  
  <Style ss:ID="s110">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
   <NumberFormat ss:Format="@"/>
  </Style>
  <Style ss:ID="s111">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font x:Family="Swiss" ss:Color="#000000"/>
  </Style> 
  <Style ss:ID="s143">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Interior ss:Color="#969696" ss:Pattern="Solid"/>
  </Style>   
 </Styles>
 <Worksheet ss:Name="Hoja1">
  <Table ss:ExpandedColumnCount="8" ss:ExpandedRowCount="49" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="65" ss:DefaultRowHeight="15">
   <Column ss:AutoFitWidth="0" ss:Width="90"/>
   <Column ss:AutoFitWidth="0" ss:Width="76" ss:Span="6"/>
   <Row ss:AutoFitHeight="0" ss:Height="20">
    <Cell ss:MergeAcross="7" ss:StyleID="m2106752052"><Data ss:Type="String">REGISTRO DIARIO DE JORNADA EN TRABAJADORES A TIEMPO PARCIAL</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="3" ss:StyleID="m2106752072"><Data ss:Type="String">EMPRESA</Data></Cell>
    <Cell ss:MergeAcross="3" ss:StyleID="m2106752092"><Data ss:Type="String">TRABAJADOR</Data></Cell>
   </Row>
   <Row ss:AutoFitHeight="0">
    <Cell ss:StyleID="s80"><Data ss:Type="String">Nombre o Razón Social:</Data></Cell>
    <Cell ss:StyleID="s81"/>
    <Cell ss:StyleID="s81"><Data ss:Type="String"><?=$razon_social_empresa;?></Data></Cell>
    <Cell ss:StyleID="s82"/>
    <Cell ss:StyleID="s80"><Data ss:Type="String">Nombre:</Data></Cell>
    <Cell ss:StyleID="s81"><Data ss:Type="String"><?=$nombre_trabajador;?></Data></Cell>
    <Cell ss:StyleID="s81"/>
    <Cell ss:StyleID="s83"/>
   </Row>
   <Row ss:AutoFitHeight="0">
    <Cell ss:StyleID="s84"><Data ss:Type="String">CIF:</Data></Cell>
    <Cell ss:StyleID="s82"/>
    <Cell ss:StyleID="s82"><Data ss:Type="String"><?=$nif_empresa?></Data></Cell>
    <Cell ss:StyleID="s82"/>
    <Cell ss:StyleID="s85"><Data ss:Type="String">NIF:</Data></Cell>
    <Cell ss:StyleID="s86"><Data ss:Type="String"><?=$nif_trabajador?></Data></Cell>
    <Cell ss:StyleID="s86"/>
    <Cell ss:StyleID="s83"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="16">
    <Cell ss:StyleID="s84"><Data ss:Type="String">C.C.C.:</Data></Cell>
    <Cell ss:StyleID="s82"/>
    <Cell ss:StyleID="s82"><Data ss:Type="String"><?=$ccc_empresa?></Data></Cell>
    <Cell ss:StyleID="s82"/>
    <Cell ss:StyleID="s85"><Data ss:Type="String">NAF:</Data></Cell>
    <Cell ss:StyleID="s87"><Data ss:Type="String"><?=$nss_trabajador?></Data></Cell>
    <Cell ss:StyleID="s87"/>
    <Cell ss:StyleID="s83"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="16">
    <Cell ss:StyleID="s88"><Data ss:Type="String">Período de liquidación:</Data></Cell>
    <Cell ss:StyleID="s89"/>
    <Cell ss:StyleID="s89"><Data ss:Type="String"><?=$periodo_liquidacion?></Data></Cell>
    <Cell ss:StyleID="s90"/>
    <Cell ss:StyleID="s91"><Data ss:Type="String">Fecha:</Data></Cell>
    <Cell ss:StyleID="s143"><Data ss:Type="String"><?=date("d/m/Y");?></Data></Cell>
    <Cell ss:StyleID="s92"/>
    <Cell ss:StyleID="s93"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="25">
    <Cell ss:MergeDown="1" ss:StyleID="m2106752032"><Data ss:Type="String">Día del mes</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="m2106752516"><Data ss:Type="String">Hora Entrada</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="m2106752536"><Data ss:Type="String">Hora de Salida</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="m2106752456"><Data ss:Type="String">Total Horas&#13;Jornada</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="m2106752476"><Data ss:Type="String">Horas&#13;Ordinarias</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="m2106752496"><Data ss:Type="String">Horas&#13;Complem.</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="2" ss:StyleID="s22"><Data ss:Type="String">Mañana</Data></Cell>
    <Cell ss:StyleID="s22"><Data ss:Type="String">Tarde</Data></Cell>
    <Cell ss:StyleID="s22"><Data ss:Type="String">Mañana</Data></Cell>
    <Cell ss:StyleID="s22"><Data ss:Type="String">Tarde</Data></Cell>
   </Row>
<?php 

for ($dia_mes=1;$dia_mes<=31;$dia_mes++) {

	$hora_entrada_man="";
	$hora_entrada_tar="";
	$hora_salida_man="";
	$hora_salida_tar="";
	$horas_diferencia="0";

	if ($dia_mes<=$numero_dias_mes) {

	$busca_asistencia=db_query_params('SELECT * FROM asistencia_docentes WHERE id_usuario=? AND fecha=? ORDER BY fecha,turno', 'ss', array($id_aula, (string)(date("Y-m-d", strtotime($ano_sel . "-" . $mes_sel . "-" . $dia_mes)))));		
	
	while($datos_asistencia=db_fetch_array($busca_asistencia)){
		
                if ((strlen($datos_asistencia["hora_salida"])!=0) and (strlen($datos_asistencia["hora_entrada"])!=0)) {
                        $diferencia_entrada=intval(strtotime($datos_asistencia["hora_salida"]))-intval(strtotime($datos_asistencia["hora_entrada"]));
                        } else {
                        $diferencia_entrada=0;
                        }
                
                $horas_diferencia=($diferencia_entrada/60)/60;
                if ($horas_diferencia<round((($diferencia_entrada/60)/60),0)) {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0)-1;
                        } else {
                        $horas_diferencia1=round((($diferencia_entrada/60)/60),0);
                        }

                $minutos_diferencia=$horas_diferencia-$horas_diferencia1;
                $minutos_diferencia=round(($minutos_diferencia*60),0);
                
                $total_horas=intval($total_horas)+intval($horas_diferencia1);
                $total_minutos=intval($total_minutos)+intval($minutos_diferencia);
                
                $horas_diferencia1="0".$horas_diferencia1;
                $minutos_diferencia="0".$minutos_diferencia;
                
                $horas_diferencia1=substr($horas_diferencia1,strlen($horas_diferencia1)-2,2);
                $minutos_diferencia=substr($minutos_diferencia,strlen($minutos_diferencia)-2,2);
                
                $hora_entrada_texto=date("H:i:s",strtotime($datos_asistencia["hora_entrada"]));
                $hora_salida_texto=date("H:i:s",strtotime($datos_asistencia["hora_salida"]));
                
				if ($hora_entrada_texto=="01:00:00") {
                	$hora_entrada_texto="No ficha entrada";
                }
                
                if ($hora_salida_texto=="01:00:00") {
                	$hora_salida_texto="No ficha salida";
                }                
                
                if ($diferencia_entrada!=0) {
                        $diferencia_horas=$horas_diferencia1.":".$minutos_diferencia;
                        } else {
                        $diferencia_horas="00:00";
                        }
                        
                if (date("G",strtotime($datos_asistencia["hora_entrada"]))<15) {
                	$turno=0;
                } else {
                	$turno=1;
                } 
                        
                if ($turno==0) {
	 	                $hora_entrada_man=$hora_entrada_texto;
	 	                $hora_salida_man=$hora_salida_texto;
                        } else {
                        $hora_entrada_tar=$hora_entrada_texto;
                        $hora_salida_tar=$hora_salida_texto;
                        }                                      	
		}
	
	}
	
        ?>
   <Row ss:AutoFitHeight="0" ss:Height="17">
    <Cell ss:StyleID="s110"><Data ss:Type="Number"><?=$dia_mes?></Data></Cell>
    <Cell ss:StyleID="s110"><Data ss:Type="String"><?=$hora_entrada_man?></Data></Cell>
    <Cell ss:StyleID="s110"><Data ss:Type="String"><?=$hora_entrada_tar?></Data></Cell>
    <Cell ss:StyleID="s110"><Data ss:Type="String"><?=$hora_salida_man?></Data></Cell>
    <Cell ss:StyleID="s110"><Data ss:Type="String"><?=$hora_salida_tar?></Data></Cell>
    <Cell ss:StyleID="s111"><Data ss:Type="Number"><?=round($horas_diferencia,2);?></Data></Cell>
    <Cell ss:StyleID="s111"><Data ss:Type="Number"><?=round($horas_diferencia,2);?></Data></Cell>
    <Cell ss:StyleID="s111"><Data ss:Type="Number">0</Data></Cell>
   </Row><?php  	
	
}

	// Totales

	$total_minutos=$total_minutos/60;
	if ($total_minutos<round($total_minutos,0)) {
			$total_minutos1=round($total_minutos,0)-1;
		} else {
			$total_minutos1=round($total_minutos,0);
		}

	$minutos_restantes=round((($total_minutos-$total_minutos1)*60),0);
        
	$total_horas=$total_horas+$total_minutos1;
        
	$total_horas="0".$total_horas;
	$minutos_restantes="0".$minutos_restantes;

	$total_horas=substr($total_horas,strlen($total_horas)-2,2);
	$minutos_restantes=substr($minutos_restantes,strlen($minutos_restantes)-2,2);

?>
   <Row ss:Height="16">
    <Cell ss:StyleID="s31"><Data ss:Type="String">Total Mes</Data></Cell>
    <Cell ss:StyleID="s32"/>
    <Cell ss:StyleID="s32"/>
    <Cell ss:StyleID="s32"/>
    <Cell ss:StyleID="s32"/>
    <Cell ss:StyleID="s32" ss:Formula="=SUM(R[-31]C[0]:R[-1]C[0])"><Data
      ss:Type="Number">0</Data></Cell>
    <Cell ss:StyleID="s32" ss:Formula="=SUM(R[-31]C[0]:R[-1]C[0])"><Data
      ss:Type="Number">0</Data></Cell>
    <Cell ss:StyleID="s32" ss:Formula="=SUM(R[-31]C[0]:R[-1]C[0])"><Data
      ss:Type="Number">0</Data></Cell>
   </Row>
   <Row ss:AutoFitHeight="0">
    <Cell ss:MergeAcross="7" ss:MergeDown="1" ss:StyleID="s86"><Data
      ss:Type="String">En cumplimiento de la obligación establecida en el Art. 12.4 c)  del Real Decreto Legislativo 2/2015 de 23 de Octubre, por el que se aprueba el texto refundido del la Ley del Estatuto de los Trabajadores</Data></Cell>
   </Row>
   <Row ss:Index="44">
    <Cell ss:StyleID="s49"/>
    <Cell ss:MergeAcross="2" ss:StyleID="m2106752436"><Data ss:Type="String">Firma de la Empresa</Data></Cell>
    <Cell ss:StyleID="s37"/>
    <Cell ss:MergeAcross="2" ss:StyleID="m2106752416"><Data ss:Type="String">Firma del Trabajador</Data></Cell>
   </Row>
   <Row>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s16"/>
    <Cell ss:StyleID="s48"/>
    <Cell ss:StyleID="s17"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s16"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s17"/>
   </Row>
   <Row>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s21"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s50"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s18"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s17"/>
   </Row>
   <Row>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s16"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s17"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s16"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s17"/>
   </Row>
   <Row>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s16"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s17"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s16"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s17"/>
   </Row>
   <Row>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s19"/>
    <Cell ss:StyleID="s38"/>
    <Cell ss:StyleID="s20"/>
    <Cell ss:StyleID="s37"/>
    <Cell ss:StyleID="s19"/>
    <Cell ss:StyleID="s38"/>
    <Cell ss:StyleID="s20"/>
   </Row>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Layout x:CenterHorizontal="1" x:CenterVertical="1"/>
    <Header x:Margin="0.31496062992125984"/>
    <Footer x:Margin="0.31496062992125984"/>
    <PageMargins x:Bottom="0.55118110236220474" x:Left="0.39370078740157483"
     x:Right="0.39370078740157483" x:Top="0.55118110236220474"/>
   </PageSetup>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <Scale>85</Scale>
    <HorizontalResolution>-1</HorizontalResolution>
    <VerticalResolution>600</VerticalResolution>
   </Print>
   <PageLayoutZoom>0</PageLayoutZoom>
   <Selected/>
   <DoNotDisplayZeros/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <RangeSelection>R1C1:R1C8</RangeSelection>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Hoja2">
  <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="65" ss:DefaultRowHeight="15">
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Print>
   </Print>
   <PageLayoutZoom>0</PageLayoutZoom>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Hoja3">
  <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="65" ss:DefaultRowHeight="15">
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Print>
   </Print>
   <PageLayoutZoom>0</PageLayoutZoom>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>