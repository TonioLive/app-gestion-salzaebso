<?php  require("../configuracion/config.php"); ?>
<table border=1 style="border-collapse: collapse">
        <tr>
        <td>Email</td>
        <td>Resultado</td>
        <td>Nombre Alumno en contactos</td>
        <td>Tabla Alumnos</td>
        </tr>
<?php 

$tipo_resultado=array("","Abierto","No abierto","Rebotado");

$busca_resultado_mailing=db_query("SELECT * FROM resultado_emails ORDER BY resultado");

while($resul=db_fetch_array($busca_resultado_mailing)) {


        $busca_en_contactos=db_query_params('SELECT * FROM contactos WHERE email like ?', 's', array('%' . (string)($resul["email"]) . '%'));
        
        $lista_contactos="";
        
        while($en_contactos=db_fetch_array($busca_en_contactos)) {
                $lista_contactos.=$en_contactos["Nombre"]." ".$en_contactos["Apellido1"]." ".$en_contactos["Apellido2"]." - ".$en_contactos["telefono"]." ESTADO: <b>".$en_contactos["proceso_decision"]."</b><br>";
                }


        $busca_en_alumnos=db_query_params('SELECT * FROM usuarios WHERE email like ?', 's', array('%' . (string)($resul["email"]) . '%'));

        $lista_alumnos="";

        while($en_alumnos=db_fetch_array($busca_en_alumnos)) {
                $lista_alumnos.=$en_alumnos["Nombre"]." ".$en_alumnos["Apellido1"]." ".$en_alumnos["Apellido2"]." - ".$en_alumnos["telefono_contacto1"]." ".$en_alumnos["telefono_contacto2"]." ".$en_alumnos["movil"]."<br>";
                }
        ?>
        <tr>
        <td><?=$resul["email"]?></td>
        <td><?=$tipo_resultado[$resul["resultado"]]?></td>
        <td><?=$lista_contactos?></td>
        <td><?=$lista_alumnos?></td>
        </tr>
        <?php 

        }


?>
</table>
