<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_admin.php");

$id_webcam=$_GET["id_webcam"];

if ($id_webcam!=0) {

$busca_datos_camara=db_query_params('SELECT * FROM webcam WHERE id_webcam=?', 's', array($id_webcam));

$datos_camara=db_fetch_array($busca_datos_camara);

$usuario_camara=$datos_camara["usuario_visor"];
$password_camara=$datos_camara["password_visor"];
$url_camara=$datos_camara["url_webcam"];
$puerto_camara=$datos_camara["puerto_webcam"];
$nombre_camara=$datos_camara["nombre_webcam"];
$hora_inicio_camara=strtotime($datos_camara["hora_conexion"]);
$hora_fin_camara=strtotime($datos_camara["hora_desconexion"]);
$modelo_camara=$datos_camara["modelo"];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title></title>
        <meta name="title" content="" />
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta http-equiv=pragma content=no-cache>
        <link rel="stylesheet" href="http://<?=$url_camara?>:<?=$puerto_camara?>/style.css?r=20120327" type="text/css" media="screen, projection" />
        <link rel="stylesheet" href="http://<?=$url_camara?>:<?=$puerto_camara?>/ipcam.css?r=20120327" type="text/css" media="screen, projection" />
        <script language=javascript src='http://<?=$url_camara?>:<?=$puerto_camara?>/javascript.js?r=20120327'></script>
        <script language=javascript src='http://<?=$url_camara?>:<?=$puerto_camara?>/language.js?r=20120327'></script>
</head>

<SCRIPT LANGUAGE="JavaScript">

        var userAgent = navigator.userAgent.toLowerCase();
        var browser = {
                version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [])[1],
                chrome: /chrome/.test( userAgent ),
                safari: /webkit/.test( userAgent ) && !/chrome/.test( userAgent ),
                opera: /opera/.test( userAgent ),
                msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
                mozilla: /mozilla/.test(userAgent)&&!/(compatible|webkit)/.test(userAgent)
        };


        /****************************************************************************************************************************
         *Fit-to-Window functions
         ****************************************************************************************************************************/
        var isFitToWindow = 0;
        var borderWidth   = 100, borderHeight = 170;
        var Width = 320;
        var Height = 240;
        var currentClientHeight = 0;
        var currentClientWidth  = 0;
        var select, i;

        window.onresize = resize;
        function resize()
        {
                if ((currentClientHeight != document.body.clientHeight || currentClientWidth != document.body.clientWidth) && isFitToWindow == 1 ) {
                        document.IPCam_Plugin.FitWindow("w="+document.body.clientWidth+" h="+document.body.clientHeight);
                        if(document.body.clientWidth - borderWidth < 160 || document.body.clientHeight - borderHeight < 120)
                        {
                                document.IPCam_Plugin.width  = 160;
                                document.IPCam_Plugin.height = 120;
                        }else if(document.body.clientWidth - borderWidth < Width && document.body.clientHeight - borderHeight < Height){
                                select = Math.round((document.body.clientWidth - borderWidth)/4) > Math.round((document.body.clientHeight - borderHeight)/3) ? 1 : 0;
                                if (select == 1)
                                {
                                        document.IPCam_Plugin.width  = 4*(document.body.clientHeight  - borderHeight)/3;
                                        document.IPCam_Plugin.height = document.body.clientHeight  - borderHeight;
                                }else{
                                        document.IPCam_Plugin.width  = document.body.clientWidth  - borderWidth;
                                        document.IPCam_Plugin.height = 3*(document.body.clientWidth  - borderWidth)/4;
                                }
                        }else if(document.body.clientWidth - borderWidth < Width ){
                                document.IPCam_Plugin.width  = document.body.clientWidth  - borderWidth;
                                document.IPCam_Plugin.height = 3*(document.body.clientWidth  - borderWidth)/4;
                        }else if (document.body.clientHeight - borderHeight < Height){
                                document.IPCam_Plugin.width  = 4*(document.body.clientHeight  - borderHeight)/3;
                                document.IPCam_Plugin.height = document.body.clientHeight  - borderHeight;
                        }else{
                                document.IPCam_Plugin.width  = Width;
                                document.IPCam_Plugin.height = Height;
                        }
                        scrl(-10);
                }
                currentClientHeight = document.body.clientHeight;
                currentClientWidth  = document.body.clientWidth;
        }

        function StartFitToWindow()
        {
                if (isFitToWindow == 0) {
                        document.ipcamForm.liveFitToWindow.src = "http://<?=$url_camara?>:<?=$puerto_camara?>/images/on-1_25.png";
                        document.ipcamForm.liveFitToWindow.title = showText(lang_Fit_To_Window_Stop);
                        isFitToWindow = 1;
                }
                else {
                        document.ipcamForm.liveFitToWindow.src = "http://<?=$url_camara?>:<?=$puerto_camara?>/images/off-1_25.png";
                        document.ipcamForm.liveFitToWindow.title = showText(lang_Fit_To_Window_Start);
                        isFitToWindow = 0;
                }

                document.IPCam_Plugin.IsFitWindow(isFitToWindow);

                if (isFitToWindow == 1 ) {
                        document.IPCam_Plugin.FitWindow("w="+document.body.clientWidth+" h="+document.body.clientHeight);
                        if(document.body.clientWidth - borderWidth < 160 || document.body.clientHeight - borderHeight < 120)
                        {
                                document.IPCam_Plugin.width  = 160;
                                document.IPCam_Plugin.height = 120;
                        }else if(document.body.clientWidth - borderWidth < Width && document.body.clientHeight - borderHeight < Height){
                                select = Math.round((document.body.clientWidth - borderWidth)/4) > Math.round((document.body.clientHeight - borderHeight)/3) ? 1 : 0;
                                if (select == 1){
                                        document.IPCam_Plugin.width  = 4*(document.body.clientHeight  - borderHeight)/3;
                                        document.IPCam_Plugin.height = document.body.clientHeight  - borderHeight;
                                }else{
                                        document.IPCam_Plugin.width  = document.body.clientWidth  - borderWidth;
                                        document.IPCam_Plugin.height = 3*(document.body.clientWidth  - borderWidth)/4;
                                }
                        }else if(document.body.clientWidth - borderWidth < Width ){
                                document.IPCam_Plugin.width  = document.body.clientWidth  - borderWidth;
                                document.IPCam_Plugin.height = 3*(document.body.clientWidth  - borderWidth)/4;
                        }else if (document.body.clientHeight - borderHeight < Height){
                                document.IPCam_Plugin.width  = 4*(document.body.clientHeight  - borderHeight)/3;
                                document.IPCam_Plugin.height = document.body.clientHeight  - borderHeight;
                        }else{
                                document.IPCam_Plugin.width = Width;
                                document.IPCam_Plugin.height = Height;
                        }
                }
                else{
                        if (Width == 1280) {
                                document.IPCam_Plugin.width  = 1024;
                                document.IPCam_Plugin.height = 768;
                        }
                        else {
                                document.IPCam_Plugin.width  = Width;
                                document.IPCam_Plugin.height = Height;
                        }
                }
                resize();
        }

        function scrl(step)
        {
                isize = 500;
                count = Math.abs(isize / step);
                while (count > 0)
                {
                        scrollBy(step, 0);
                        count--;
                }
        }

        /*****************************************************************************************************************************
         *   Init set ActiveX
         *****************************************************************************************************************************/
        var activeX_Version = "1,0,1,0";

        function getIntVersion(a) {
                var strArray = a.split(".");
                return parseInt(strArray[0])*100*100*100 + parseInt(strArray[1])*100*100 + parseInt(strArray[2])*100 + parseInt(strArray[3]);
        }

          function checkActiveXVersion()
        {
                var myversion = document.IPCam_Plugin.getVersion();
                if (getIntVersion(activeX_Version) > getIntVersion(myversion)) {
                        document.getElementById("liveView").style.display = "none";
                }
        }

        function SendActiveXParm() {
                document.IPCam_Plugin.IP = "<?=$url_camara?>";
                //document.IPCam_Plugin.Port = "4321";
                document.IPCam_Plugin.Code = "";
                document.IPCam_Plugin.Login = "";
                document.IPCam_Plugin.Passwd = "";
                document.IPCam_Plugin.Encryption = "1";
                document.IPCam_Plugin.WebPort = "<?=$puerto_camara?>";
                document.IPCam_Plugin.MDMode = "0";
                document.IPCam_Plugin.Lang = languageItem;
        }

        function initWeb() {
                if (browser.msie) {

                        try {
                                if (document.IPCam_Plugin) {
                                        if (Width == 1280) {
                                                //document.IPCam_Plugin.width = 1024;
                                                document.IPCam_Plugin.height = 768;
                                        }
                                        SendActiveXParm();
                                        checkActiveXVersion();
                                        document.IPCam_Plugin.StartRun();
                                }
                        }
                        catch (e) {
                                document.getElementById("liveView").style.display = "none";
                                //document.getElementById("activeXDL").style.display = "block";
                        }
                }
        }

</SCRIPT>

<SCRIPT FOR=IPCam_Plugin EVENT=ZoomEnable(Enable)>
<!--
        if (Enable == 1)
                document.ipcamForm.liveDigitalZoom.src = "http://<?=$url_camara?>:<?=$puerto_camara?>/images/on-1_27.png";
        else
                document.ipcamForm.liveDigitalZoom.src = "http://<?=$url_camara?>:<?=$puerto_camara?>/images/off-1_27.png";
-->
</SCRIPT>


<body onload="initWeb();" margin=0 topmargin=0 leftmargin=0>
                                <div id="liveView" style="display: block;">
                                        <SCRIPT LANGUAGE="JavaScript">
                                        <!--
                                                function sleep( seconds ) {
                                                        var timer = new Date();
                                                        var time = timer.getTime();
                                                        do
                                                                timer = new Date();
                                                        while( (timer.getTime() - time) < (seconds * 1000) );
                                                }

                                                if (browser.opera) {
                                                        document.write('<applet name="AppletTest" id="AppletTest" code="IPcamTest.class" WIDTH="0" HEIGHT="0">');
                                                        document.write('<PARAM NAME="ARCHIVE" VALUE ="http://<?=$url_camara?>:<?=$puerto_camara?>/IPcamTest.jar">');
                                                        document.write('</applet>');
                                                }

                                                if (browser.msie) {
                                                        document.write('<OBJECT ID="IPCam_Plugin" CLASSID=clsid:3A5EF8E2-34B2-4B01-962B-FF430245CCA4 ALIGN="CENTER" WIDTH="'+320+'" HEIGHT="'+240+'" Codebase="http://<?=$url_camara?>:<?=$puerto_camara?>/IPCamPluginDM.cab#version=1,0,1,0">');
                                                        document.write('</OBJECT>');
                                                }
                                                else if(browser.opera) {
                                                        sleep(1); //wait for loading applet
                                                        try {
                                                                if(document.AppletTest.isActive()) {}
                                                        }
                                                        catch(error) {
                                                                document.write('The browser doesn\'t support java plug-in.<br>');
                                                                document.write('<a href="http://www.java.com/en/">Please install the lastest java plug-in first.</a>');
                                                        }
                                                        if (Width == 1280) {
                                                                document.write('<applet name="IPCamApplet" id="IPCamApplet" code="MjpegIPCam.class"  ALIGN="CENTER" WIDTH="1024" HEIGHT="768">');
                                                        }
                                                        else {
                                                                document.write('<applet name="IPCamApplet" id="IPCamApplet" code="MjpegIPCam.class"  ALIGN="CENTER" WIDTH="'+320+'" HEIGHT="'+240+'">');
                                                        }
                                                        document.write('<PARAM name="IP" value="'+window.location.hostname+'">');
                                                        document.write('<PARAM name="Port" value="<?=$puerto_camara?>">');
                                                        document.write('<PARAM name="Login" value="">');
                                                        document.write('<PARAM name="Password" value="">');
                                                        document.write('<PARAM NAME="ARCHIVE" VALUE ="MjpegIPCam.jar">');
                                                        document.write('<PARAM NAME="Encryption" VALUE ="1">');
                                                        document.write('<PARAM NAME="Decode" VALUE ="">');
                                                        if (Width == 1280) {
                                                                document.write('<PARAM NAME="Width" VALUE ="1024">');
                                                                document.write('<PARAM NAME="Height" VALUE ="768">');
                                                        }
                                                        else {
                                                                document.write('<PARAM NAME="Width" VALUE ="'+320+'">');
                                                                document.write('<PARAM NAME="Height" VALUE ="'+240+'">');
                                                        }
                                                        document.write('<PARAM NAME="VideoMode" VALUE ="0">');
                                                        document.write('<PARAM NAME="AnonymMode" VALUE ="1">');
                                                        document.write('</applet>');
                                                }
                                                else {
                                                        if (Width == 1280) {
                                                                document.write('<img id="MJPEG_streaming" src="http://<?=$url_camara?>:'+<?=$puerto_camara?>+'/snapshot.cgi" ALIGN="CENTER" WIDTH="1024" HEIGHT="768">');
                                                        }
                                                        else {
                                                                document.write('<img id="MJPEG_streaming" src="http://<?=$url_camara?>:'+<?=$puerto_camara?>+'/snapshot.cgi" ALIGN="CENTER" WIDTH="'+320+'" HEIGHT="'+240+'">');
                                                        }
                                                }
                                        //-->
                                        </SCRIPT>
                                </div>


</body>
</html>
