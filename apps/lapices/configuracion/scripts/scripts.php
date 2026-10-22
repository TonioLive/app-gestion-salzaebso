<script language="JavaScript">
<!-- hide from JavaScript-challenged browsers
// Ventanas
function openWindow(url,w,h,n) {
  popupWin = window.open(url, 'new_page' + n, 'width=' + w + ',height=' + h)
  if (parseInt(navigator.appVersion) >= 4) { popupWin.window.focus();}
}

function openOferta(url,w,h,n) {
  popupWin = window.open(url, 'new_page' + n, 'width=' + w + ',height=' + h + ', scrollbars=yes')
  if (parseInt(navigator.appVersion) >= 4) { popupWin.window.focus();}
}

function openWindowScroll(url,w,h,n) {
  popupWin = window.open(url, 'new_page' + n, 'width=' + w + ',height=' + h + ', scrollbars=yes, toolbar=yes')
  if (parseInt(navigator.appVersion) >= 4) { popupWin.window.focus();}
}

// Cookies

function setCookie(name, value, expire) {
        document.cookie = name + "=" + escape(value)
      + ((expire == null) ? "" : ("; expires=" + expire.toGMTString()))
        }

function registrar_cookie(Ventana) {
   var hoy = new Date()
   var caducidad = new Date()
   //La cookie dura un año
   caducidad.setTime(hoy.getTime() + 1000*60*60*24*365)
   setCookie("<?=$nombre_cookie;?>", Ventana, caducidad)
   }

function getCookie(Name) {
   var search = Name + "="
   if (document.cookie.length > 0) {
        // if there are any cookies
                offset = document.cookie.indexOf(search)
                if (offset != -1) {
                // if cookie exists
                        offset += search.length
                        // set index of beginning of value
                        end = document.cookie.indexOf(";", offset)
                        // set index of end of cookie value
                        if (end == -1)
                                 end = document.cookie.length
                        return unescape(document.cookie.substring(offset, end))
      }
    }
}

function clickea() {
if (window.document.casilla.no_deseo.checked==true) {
                registrar_cookie(1);
                }
                else {
                registrar_cookie(0);
                }
history.go(0);
}

// Capas

function mostrar(capa, altura, texto, posicion_y, posicion_x)
{
        var Autoayuda = getCookie("<?=$nombre_cookie;?>")
        Visibilidad_capa="visible";
        if (Autoayuda==1) {
                Visibilidad_capa="hidden"
                }
        if(document.all)
                document.all[capa].innerHTML='<table border="1" width="100%" height="100%" cellpadding=3 cellspacing=0 bgcolor=<?=$Fondo_web;?> bordercolor=#111111><tr><td align="center" valign="top"><font face="<?=$Fuente_general;?>" size=1 color="<?=$Color_fuente;?>">' + texto + '</font></td></tr></table>';
                document.all[capa].style.height=altura;
                document.all[capa].style.width="200px";
                document.all[capa].style.left=String(posicion_x+"px");
                document.all[capa].style.top=String(posicion_y+"px");
                document.all[capa].style.clip="rect(0px,200px," + altura + "px,0px)"
                document.all[capa].style.visibility=Visibilidad_capa;

        if(document.layers)
                //document[capa].document.write(texto);
                //document[capa].document.close();
                document[capa].visibility=Visibilidad_capa;

}

function seguir(capa, altura, texto)
{
        var Autoayuda = getCookie("<?=$nombre_cookie;?>")
        Visibilidad_capa="visible";
        if (Autoayuda==1) {
                Visibilidad_capa="hidden"
                }
        if(document.all)
                posicion_x = (document.layers) ? e.pageX : document.body.scrollLeft+event.clientX-100
                posicion_y = (document.layers) ? e.pageY : document.body.scrollTop+event.clientY+10

                document.all[capa].innerHTML='<table border="1" width="100%" height="100%" cellpadding=3 cellspacing=0 bgcolor=<?=$fondo_web;?> bordercolor=#111111><tr><td align="center" valign="top"><font face="<?=$fuente_general;?>" size=1 color="<?=$color_fuente;?>">' + texto + '</font></td></tr></table>';
                document.all[capa].style.height=altura;
                document.all[capa].style.width="200px";
                document.all[capa].style.left=String(posicion_x+"px");
                document.all[capa].style.top=String(posicion_y+"px");
                document.all[capa].style.clip="rect(0px,200px," + altura + "px,0px)"
                document.all[capa].style.visibility=Visibilidad_capa;

        if(document.layers)
                //document[capa].document.write(texto);
                //document[capa].document.close();
                document[capa].visibility=Visibilidad_capa;

}

function seguir_xy(capa, altura, texto, desfase_y, desfase_x)
{
        var Autoayuda = getCookie("<?=$nombre_cookie;?>")
        Visibilidad_capa="visible"
        if (Autoayuda==1) {
                Visibilidad_capa="hidden"
                }
        if(document.all)
                posicion_x = (document.layers) ? e.pageX : document.body.scrollLeft+event.clientX-100+desfase_x
                posicion_y = (document.layers) ? e.pageY : document.body.scrollTop+event.clientY+desfase_y

                document.all[capa].innerHTML='<table border="1" width="100%" height="100%" cellpadding=3 cellspacing=0 bgcolor=<?=$fondo_web;?> bordercolor=#111111><tr><td align="center" valign="top"><font face="<?=$fuente_general;?>" size=1 color="<?=$color_fuente;?>">' + texto + '</font></td></tr></table>';
                document.all[capa].style.height=altura;
                document.all[capa].style.width="200px";
                document.all[capa].style.left=String(posicion_x+"px");
                document.all[capa].style.top=String(posicion_y+"px");
                document.all[capa].style.clip="rect(0px,200px," + altura + "px,0px)"
                document.all[capa].style.visibility=Visibilidad_capa;

        if(document.layers)
                //document[capa].document.write(texto);
                //document[capa].document.close();
                document[capa].visibility=Visibilidad_capa;

}

function ocultar(capa)
{
        if(document.all)
                document.all[capa].style.visibility="hidden";

        if(document.layers)
                document[capa].visibility="hidden";
}

// done hiding -->
</script>
