document.write('<OBJECT ID="IPCam_Plugin" CLASSID=clsid:b015b944-7316-49ae-ac84-acca9379ea32 ALIGN="CENTER" WIDTH="'+ ipcam_width +'" HEIGHT="'+ ipcam_height +'" Codebase="/IPCamPluginMJPEG.cab#version=1,1,0,0">');
document.write('<PARAM name="IP" value="'+ ipaddr +'">');
document.write('<PARAM name="Port" value="'+ port +'">');
document.write('<PARAM name="Login" value="'+ login +'">');
document.write('<PARAM name="Password" value="'+ password +'">');
document.write('<PARAM name="Code" value="'+ code +'">');
document.write('<PARAM name="Debug" value="0">');
document.write('</OBJECT>');
