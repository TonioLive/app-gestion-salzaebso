<?php 
$url_webcam="http://santpau.dyndns.info:".$puerto_camara."/";
$url_camara="santpau.dyndns.info";
$usuario_camara=(getenv('ZEIM_CAMERA_USER_03') ?: '');
$password_camara=(getenv('ZEIM_CAMERA_PASSWORD_03') ?: '');
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
        <link rel="stylesheet" href="<?=$url_webcam?>style.css?r=20120327" type="text/css" media="screen, projection" />
        <link rel="stylesheet" href="<?=$url_webcam?>ipcam.css?r=20120327" type="text/css" media="screen, projection" />
        <script language=javascript src='<?=$url_webcam?>javascript.js?r=20120327'></script>
        <script language=javascript src='<?=$url_webcam?>language.js?r=20120327'></script>
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
                        document.ipcamForm.liveFitToWindow.src = "<?=$url_webcam?>images/on-1_25.png";
                        document.ipcamForm.liveFitToWindow.title = showText(lang_Fit_To_Window_Stop);
                        isFitToWindow = 1;
                }
                else {
                        document.ipcamForm.liveFitToWindow.src = "<?=$url_webcam?>images/off-1_25.png";
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
         *   FullScreen & DigitalZoom functions
         *****************************************************************************************************************************/
        var isFullWindow = 0;
        var isDigitalZoom = 0;

        function fullWindow()
        {
                /*if (isFullWindow == 0) {
                        document.ipcamForm.liveFullScreen.src = "<?=$url_webcam?>images/off-1_26.png";
                        document.ipcamForm.liveFullScreen.title = "Full Screen";
                        isFullWindow = 1;
                }
                else {
                        document.ipcamForm.liveFullScreen.src = "<?=$url_webcam?>images/off-1_26.png";
                        document.ipcamForm.liveFullScreen.title = "Fit To Window Start";
                        isFullWindow = 0;
                }*/
                document.IPCam_Plugin.IsFullWindow();
        }

        function digitalZoom()
        {
                /*if (isDigitalZoom == 0) {
                        document.ipcamForm.liveDigitalZoom.src = "<?=$url_webcam?>images/off-1_27.png";
                        document.ipcamForm.liveDigitalZoom.title = "Digital Zoom";
                        isDigitalZoom = 1;
                }
                else {
                        document.ipcamForm.liveDigitalZoom.src = "<?=$url_webcam?>images/off-1_27.png";
                        document.ipcamForm.liveDigitalZoom.title = "Digital Zoom";
                        isDigitalZoom = 0;
                }*/
                document.IPCam_Plugin.IsZoom();
        }


        /*****************************************************************************************************************************
         *   Cookie functions
         *****************************************************************************************************************************/
        function getCookieVal (offset)
        {
                var endstr = document.cookie.indexOf (";", offset);
                if (endstr == -1)
                        endstr = document.cookie.length;
                return unescape(document.cookie.substring(offset, endstr));
        }

        function GetCookie (name) {
                var arg = name + "=";
                var alen = arg.length;
                var clen = document.cookie.length;
                var i = 0;
                while (i < clen) {
                        var j = i + alen;
                        if (document.cookie.substring(i, j) == arg)
                                return getCookieVal (j);

                        i = document.cookie.indexOf(" ", i) + 1;
                        if (i == 0)
                                break;
                }
                return null;
        }

        function get_snapshotPath_cookie()
        {
                var path = GetCookie("IPCAM_IC3015_SNAPSHOT_PATH");
                if (path == null) {
                        path = "C:\\";
                }
                return path;
        }

        function get_recordPath_cookie()
        {
                var path = GetCookie("IPCAM_IC3015_RECORD_PATH");
                if (path == null) {
                        path = "C:\\";
                }
                return path;
        }

        function set_snapshotPath_cookie(path)
        {
                var expires = new Date();
                expires.setTime(expires.getTime() + 100 * 24 * 60 * 60 * 1000); //expires: 100 days
                document.cookie = "IPCAM_IC3015_SNAPSHOT_PATH=" + path + "; expires=" + expires.toGMTString();
        }

        function set_recordPath_cookie(path)
        {
                var expires = new Date();
                expires.setTime(expires.getTime() + 100 * 24 * 60 * 60 * 1000); //expires: 100 days
                document.cookie = "IPCAM_IC3015_RECORD_PATH=" + path + "; expires=" + expires.toGMTString();
        }


        /*****************************************************************************************************************************
         *   Record & Snapshot functions
         *****************************************************************************************************************************/
        var recordStatus = 0;

        function openDirDialog(type)
        {
                var path = document.IPCam_Plugin.openDirDialog();
                if (path != null && path != "") {
                        if (type == "snapshot") {
                                document.IPCam_Plugin.setSnapshotPath(path);
                                document.ipcamForm.text_snapshotPath.value = path;
                                set_snapshotPath_cookie(path);
                        }
                        else if (type == "record") {
                                document.IPCam_Plugin.setRecordPath(path);
                                document.ipcamForm.text_recordPath.value = path;
                                set_recordPath_cookie(path);
                        }
                }
                return path;
        }

        function saveSnapshot()
        {
                var date = new Date();
                var fileExt = ".jpg";
                var filename = "Snapshot" + "_" + date.getYear() + "_" + (date.getMonth() + 1) + "_" + date.getDate()
                + "_" + date.getHours() + "_" + date.getMinutes() + "_" + date.getSeconds() + fileExt;
                document.IPCam_Plugin.saveSnapshot(filename);
        }

        function recordAVI()
        {
                var date = new Date();
                var filename = "Record" + "_" + date.getYear() + "_" + (date.getMonth() + 1) + "_" + date.getDate()
                + "_" + date.getHours() + "_" + date.getMinutes() + "_" + date.getSeconds() + ".avi";

                var ReturnValue;
                ReturnValue = document.IPCam_Plugin.recordAVI(filename);

                if(ReturnValue == 0) {
                        if (recordStatus == 0) {
                                document.ipcamForm.recBtn.value= showText(lang_Stop_Recording);
                                recordStatus = 1;
                        }
                        else {
                                document.ipcamForm.recBtn.value= showText(lang_Start_Recording);
                                recordStatus = 0;
                        }
                }
                return ReturnValue;
        }

        function take_snapshot()
        {
                var rval;
                document.ipcamForm.liveSnapshot.src = "<?=$url_webcam?>images/on-1_21.png";
                if (document.IPCam_Plugin.IsProtectMode() != "1") {
                        rval = openDirDialog('snapshot');
                        if (rval == null || rval =="") {
                                document.ipcamForm.liveSnapshot.src = "images/off-1_21.png";
                                return;
                        }
                }
                saveSnapshot();
                document.ipcamForm.liveSnapshot.src = "<?=$url_webcam?>images/off-1_21.png";
        }

        function start_record()
        {
                var rval;
                if (recordStatus == 0) {
                        document.ipcamForm.liveRecord.src = "<?=$url_webcam?>images/on-2_23.png";
                        document.ipcamForm.liveRecord.title = showText(lang_Stop_Recording);
                        if (document.IPCam_Plugin.IsProtectMode() != "1") {
                                rval = openDirDialog('record');
                                if (rval == null || rval =="") {
                                        document.ipcamForm.liveRecord.src = "<?=$url_webcam?>images/off-1_23.png";
                                        document.ipcamForm.liveRecord.title = showText(lang_Start_Recording);
                                        return;
                                }
                        }
                        if(recordAVI() == -1) {
                                document.ipcamForm.liveRecord.src = "<?=$url_webcam?>images/off-1_23.png";
                                document.ipcamForm.liveRecord.title = showText(lang_Start_Recording);
                         }
                }else {
                         if(recordAVI() == 0) {
                                document.ipcamForm.liveRecord.src = "<?=$url_webcam?>images/off-1_23.png";
                                document.ipcamForm.liveRecord.title = showText(lang_Start_Recording);
                         }
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
                document.IPCam_Plugin.Code = "A194C4975E35F8BE541DB27114F97F06";
                document.IPCam_Plugin.Login = <?php echo json_encode((string)getenv('ZEIM_CAMERA_USER_04'), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
                document.IPCam_Plugin.Passwd = <?php echo json_encode((string)getenv('ZEIM_CAMERA_PASSWORD_04'), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
                document.IPCam_Plugin.Encryption = "1";
                document.IPCam_Plugin.WebPort = "8051";
                document.IPCam_Plugin.MDMode = "0";
                document.IPCam_Plugin.Lang = languageItem;
        }

        function initWeb() {
                if (browser.msie) {
                        document.getElementById("ctlIcon").style.display = "block";
                        //document.getElementById("ctlText").style.display = "block";

                        var snapshotPath = get_snapshotPath_cookie();
                        document.ipcamForm.text_snapshotPath.value = snapshotPath;
                        set_snapshotPath_cookie(snapshotPath); //extend cookie lifetime

                        var recordPath = get_recordPath_cookie();
                        document.ipcamForm.text_recordPath.value = recordPath;
                        set_recordPath_cookie(recordPath); //extend cookie lifetime

                        try {
                                if (document.IPCam_Plugin) {
                                        if (Width == 1280) {
                                                //document.IPCam_Plugin.width = 1024;
                                                document.IPCam_Plugin.height = 768;
                                        }
                                        SendActiveXParm();
                                        document.IPCam_Plugin.setSnapshotPath(snapshotPath);
                                        document.IPCam_Plugin.setRecordPath(recordPath);
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
                document.ipcamForm.liveDigitalZoom.src = "<?=$url_webcam?>images/on-1_27.png";
        else
                document.ipcamForm.liveDigitalZoom.src = "<?=$url_webcam?>images/off-1_27.png";
-->
</SCRIPT>


<body onload="initWeb();">
<div id="wrapper">

        <div id="header">
                <div id="header_banner"><img src="<?=$url_webcam?>images/banner.png" width="1024" height="93" border="0" alt=""></div>
                <div id="header_link_live" class="header_link_class_selected"><a href="<?=$url_webcam?>/live.asp?r=20120327"><SCRIPT LANGUAGE="JavaScript">dw(lang_Live_View);</SCRIPT>
</a></div>
                <div id="header_link_setup" class="header_link_class"><a href="<?=$url_webcam?>/setup.asp?r=20120327"><SCRIPT LANGUAGE="JavaScript">dw(lang_Setup);</SCRIPT>
</a></div>

        </div><!-- #header-->

        <div id="middle">

                <div id="container">
                        <div id="content_live" ALIGN="CENTER">
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
                                                        document.write('<PARAM NAME="ARCHIVE" VALUE ="IPcamTest.jar">');
                                                        document.write('</applet>');
                                                }

                                                if (browser.msie) {
                                                        document.write('<OBJECT ID="IPCam_Plugin" CLASSID=clsid:3A5EF8E2-34B2-4B01-962B-FF430245CCA4 ALIGN="CENTER" WIDTH="'+320+'" HEIGHT="'+240+'" Codebase="IPCamPluginDM.cab#version=1,0,1,0">');
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
                                                        document.write('<PARAM name="IP" value="<?=$url_camara?>">');
                                                        document.write('<PARAM name="Port" value="8051">');
                                                        document.write('<PARAM name="Login" value="73B12A1457A096A4">');
                                                        document.write(<?php echo json_encode('<PARAM name="Password" value="'.htmlspecialchars((string)getenv('ZEIM_CAMERA_PASSWORD_04'),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'">',JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>);
                                                        document.write('<PARAM NAME="ARCHIVE" VALUE ="MjpegIPCam.jar">');
                                                        document.write('<PARAM NAME="Encryption" VALUE ="1">');
                                                        document.write('<PARAM NAME="Decode" VALUE ="A194C4975E35F8BE541DB27114F97F06">');
                                                        if (Width == 1280) {
                                                                document.write('<PARAM NAME="Width" VALUE ="1024">');
                                                                document.write('<PARAM NAME="Height" VALUE ="768">');
                                                        }
                                                        else {
                                                                document.write('<PARAM NAME="Width" VALUE ="'+320+'">');
                                                                document.write('<PARAM NAME="Height" VALUE ="'+240+'">');
                                                        }
                                                        document.write('<PARAM NAME="VideoMode" VALUE ="0">');
                                                        document.write('<PARAM NAME="AnonymMode" VALUE ="0">');
                                                        document.write('</applet>');
                                                }
                                                else {
                                                        if (Width == 1280) {
                                                                document.write('<img id="MJPEG_streaming" src="http://<?=$url_camara?>:'+8051+'/snapshot.cgi" ALIGN="CENTER" WIDTH="1024" HEIGHT="768">');
                                                        }
                                                        else {
                                                                document.write('<img id="MJPEG_streaming" src="http://<?=$url_camara?>:'+8051+'/snapshot.cgi" ALIGN="CENTER" WIDTH="'+320+'" HEIGHT="'+240+'">');
                                                        }
                                                }
                                        //-->
                                        </SCRIPT>
                                </div>

                                <!--
                                <div id="activeXDL"  class="tdClass3" style="display: none;">
                                        <br>Please install ActiveX first, <script type="text/javascript">document.write('<a href="<?=$url_webcam?>/Setup_v' + activeX_Version + '.exe">Download ActiveX</a>');</script>
                                </div>
                                -->

                                <FORM NAME="ipcamForm">
                                <div id="ctlIcon" style=" display:none;">
                                <br>
                                <div class="liveViewCtlBox">
                                        <a class="liveViewIcon"  href="javascript://" onclick="take_snapshot();"><SCRIPT LANGUAGE="JavaScript">document.write('<img name="liveSnapshot" src="images/off-1_21.png" width="37" height="30" title="'+showText(lang_Take_Snapshot)+'">');</SCRIPT></a>
                                        <a class="liveViewIcon"  href="javascript://" onclick="start_record();"><SCRIPT LANGUAGE="JavaScript">document.write('<img name="liveRecord" src="images/off-1_23.png" width="37" height="30" title="'+showText(lang_Start_Recording)+'">');</SCRIPT></a>
                                        <a class="liveViewIcon"  href="javascript://" onclick="StartFitToWindow();"><SCRIPT LANGUAGE="JavaScript">document.write('<img name="liveFitToWindow" src="images/off-1_25.png" width="37" height="30" title="'+showText(lang_Fit_To_Window_Start)+'">');</SCRIPT></a>
                                        <a class="liveViewIcon"  href="javascript://" onclick="fullWindow();"><SCRIPT LANGUAGE="JavaScript">document.write('<img name="liveFullScreen" src="images/off-1_26.png" width="37" height="30" title="'+showText(lang_Full_Screen)+'">');</SCRIPT></a>
                                        <a class="liveViewIcon"  href="javascript://" onclick="digitalZoom();"><SCRIPT LANGUAGE="JavaScript">document.write('<img name="liveDigitalZoom" src="images/off-1_27.png" width="37" height="30" title="'+showText(lang_Digital_Zoom)+'">');</SCRIPT></a>
                                </div>
                                <br>
                                <div>

                                <div id="ctlText" style="display: none;">
                                <TABLE class="tableClass">
                                        <TR class="trClass1" ALIGN="LEFT">
                                               <TD class="tdClass1">
                                                        <img src="images/point.gif" border="0" hspace=2>Snapshot : </TD>
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="text" NAME="text_snapshotPath" size="36" maxlength="256" onclick="openDirDialog('snapshot');" readonly></TD>
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="button" value="Get snapshot" onclick="saveSnapshot();"></TD>
                                        </TR>

                                        <TR class="trClass1" ALIGN="LEFT">
                                                <TD class="tdClass1">
                                                        <img src="images/point.gif" border="0" hspace=2>Record : </TD>
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="text" NAME="text_recordPath" size="36" maxlength="256" onclick="openDirDialog('record');" readonly></TD>
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="button" name="recBtn" value="Start recording" onclick="recordAVI();"></TD>
                                        </TR>
                                </TABLE>
                                <TABLE>
                                        <TR class="trClass1" ALIGN="LEFT">
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="button" NAME="FitToWindow" value="Fit to window" onclick="StartFitToWindow();"></TD>
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="button" NAME="FullScreen" value="Full Screen" onclick="document.IPCam_Plugin.IsFullWindow();"></TD>
                                                <TD class="tdClass1">
                                                        <INPUT TYPE="button" NAME="DigitalZoom" value="Digital Zoom" onclick="document.IPCam_Plugin.IsZoom();"></TD>
                                </TABLE>
                                </div>
                                </FORM>
                        </div><!-- #content-->
                </div><!-- #container-->
        </div><!-- #middle-->

</div><!-- #wrapper -->

</body>
</html>
