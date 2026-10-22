<?php 
// Recibimos datos del formulario
$usuario_alergias=$_POST["alergias"];
$usuario_vacunaciones=$_POST["vacunaciones"];
$usuario_observaciones=$_POST["observaciones"];
$contacto1=$_POST["contacto1"];
$telefono_contacto1=$_POST["telefono_contacto1"];
$contacto2=$_POST["contacto2"];
$telefono_contacto2=$_POST["telefono_contacto2"];
$contacto_medico=$_POST["contacto_medico"];
$telefono_medico=$_POST["telefono_medico"];


$array_valores_alimentos=array();
$string_alimentos="";


$array_valores_alergias=array();
$string_alergias="";


for ($c=0;$c<count($array_alimentos);$c++) {
                        $array_valores_alimentos[$c]=$_POST[$array_alimentos[$c]];
                        if ($array_valores_alimentos[$c]==1) {
                                $array_valores_alimentos[$c]=1;
                                } else {
                                $array_valores_alimentos[$c]=0;
                                }
                        $string_alimentos=$string_alimentos.$array_alimentos[$c]."=".$array_valores_alimentos[$c].",";
                        }

for ($c=0;$c<count($array_alergias);$c++) {
                        $array_valores_alergias[$c]=$_POST[$array_alergias[$c]];
                        if ($array_valores_alergias[$c]==1) {
                                $array_valores_alergias[$c]=1;
                                } else {
                                $array_valores_alergias[$c]=0;
                                }
                        $string_alergias=$string_alergias.$array_alergias[$c]."=".$array_valores_alergias[$c].",";
                        }


If ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query("UPDATE usuarios SET $string_alimentos $string_alergias contacto1='$contacto1', telefono_contacto1='$telefono_contacto1', contacto2='$contacto2', telefono_contacto2='$telefono_contacto2', contacto_medico='$contacto_medico', telefono_medico='$telefono_medico', alergias='".$usuario_alergias."', vacunaciones='".$usuario_vacunaciones."', observaciones='".$usuario_observaciones."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-2");
        }
?>
