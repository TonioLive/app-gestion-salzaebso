<script language="javascript1.2">
var viewMode = 1; // WYSIWYG
var statusBold = 0;
var statusItalic = 0;
var statusUnderline = 0;
var statusStrike = 0;
var statusBul = 0;
var statusRule = 0;

function Init() {
        sz  =  ""
        wysiwyg.document.designMode = 'On';
        if (document.edicion_mensaje.texto_editor.value!="") {
                wysiwyg.document.write(document.edicion_mensaje.texto_editor.value);
                } else {
                wysiwyg.document.write(" ");
                }
        wysiwyg.document.body.style.fontFamily="Arial";
}

function buttonOver(ctrl) {
        ctrl.style.cursor = 'Hand';
}

function buttonOut(ctrl) {
        ctrl.style.cursor = 'Default';
}

function doBold(ctrl) {
        if(statusBold) {
                ctrl.className = 'button';
                statusBold = 0;
        }
        else {
                ctrl.className = 'buttonOn';
                statusBold = 1;
        }
        wysiwyg.document.execCommand('bold', false, null);
        wysiwyg.focus();
}

function doItalic(ctrl) {
        if(statusItalic) {
                ctrl.className = 'button';
                statusItalic = 0;
        }
        else {
                ctrl.className = 'buttonOn';
                statusItalic = 1;
        }
        wysiwyg.document.execCommand('italic', false, null);
        wysiwyg.focus();
}

function doUnderline(ctrl) {
        if(statusUnderline) {
                ctrl.className = 'button';
                statusUnderline = 0;
        }
        else {
                ctrl.className = 'buttonOn';
                statusUnderline = 1;
        }
        wysiwyg.document.execCommand('underline', false, null);
        wysiwyg.focus();
}

function doStrike(ctrl) {
        if(statusStrike) {
                ctrl.className = 'button';
                statusStrike = 0;
        }
        else {
                ctrl.className = 'buttonOn';
                statusStrike = 1;
        }
        wysiwyg.document.execCommand('strikethrough', false, null);
        wysiwyg.focus();
}

function doLeft() {
    wysiwyg.document.execCommand('justifyleft', false, null);
}

function doCenter() {
    wysiwyg.document.execCommand('justifycenter', false, null);
}

function doRight() {
    wysiwyg.document.execCommand('justifyright', false, null);
}

function doBulList(ctrl) {
        /*if(statusBul) {
                ctrl.className = 'button';
                statusBul = 0;
        }
        else {
                ctrl.className = 'buttonOn';
                statusBul = 1;
        }*/
        wysiwyg.document.execCommand('insertunorderedlist', false, null);
        wysiwyg.focus();
}

function doCopiar(ctrl) {
        wysiwyg.document.execCommand('copy', false, null);
        wysiwyg.focus();
}

function doPegar(ctrl) {
        wysiwyg.document.execCommand('paste', false, null);
        wysiwyg.focus();
}

function doCortar(ctrl) {
        wysiwyg.document.execCommand('cut', false, null);
        wysiwyg.focus();
}

function doSangriaMenos(ctrl) {
        wysiwyg.document.execCommand('outdent', false, null);
        wysiwyg.focus();
}

function doSangriaMas(ctrl) {
        wysiwyg.document.execCommand('indent', false, null);
        wysiwyg.focus();
}

function doListaOrd(ctrl) {
        wysiwyg.document.execCommand('insertorderedlist', false, null);
        wysiwyg.focus();
}

function doRule(ctrl) {
        /*if(statusRule) {
                ctrl.className = 'button';
                statusRule = 0;
        }
        else {
                ctrl.className = 'buttonOn';
                statusRule = 1;
        }*/
        wysiwyg.document.execCommand('inserthorizontalrule', false, null);
        wysiwyg.focus();
}

function doFont(fName) {
        if(fName != '')
        wysiwyg.document.execCommand('fontname', false, fName);
}

function doHead(hType) {
        if(hType != '') {
                wysiwyg.document.execCommand('formatblock', false, hType);
        }
        wysiwyg.focus();
}

function doUndo() {
        wysiwyg.document.execCommand('undo', false, null);
        wysiwyg.focus();
}

function doRedo() {
        wysiwyg.document.execCommand('redo', false, null);
        wysiwyg.focus();
}

function doToggleView() {
        if(viewMode == 1) {
                iHTML = wysiwyg.document.body.innerHTML;
                wysiwyg.document.body.innerText = iHTML;

                // Hide all controls
                controls.style.display = 'none';
                wysiwyg.focus();

                viewMode = 2; // Code
        }
        else {
                wysiwygText = wysiwyg.document.body.innerText;
                wysiwyg.document.body.innerHTML = wysiwygText;

                // Show all controls
                controls.style.display = 'inline';
                wysiwyg.focus();

                viewMode = 1; // WYSIWYG
        }
}

function PostForm() {
        var rtf = wysiwyg.document.body.innerHTML;
        document.form.texto_editor.value = rtf;
        return true;
}
</script>
