function selecciona_fecha(nombre_campo_presel) {

                // Comprueba fecha
                nombre_formulario="datos_articulo";
                
                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/dmz/herramientas/calendario_mes.php?nombre_formulario='+nombre_formulario+'&dia='+fecha_preseleccionada+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
        
        
