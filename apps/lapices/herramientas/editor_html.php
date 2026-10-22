<table id="editor" width="<?=$anchura_editor;?>" cellpadding="2" cellspacing="0" border="0">
        <tr>
                <td>
                <table id="controls" width="100%" cellpadding="2" cellspacing="0" border="0">
                        <tr>
                                <td>
                                <!--
                                <select onchange="wysiwyg.document.execCommand('formatBlock', false, this[this.selectedIndex].value); wysiwyg.focus();" id=select1 name=select1>
                    <option selected>Estilo</option>
                    <option value='Normal'>Normal</option>
                    <option value='Heading 1'>Heading 1</option>
                    <option value='Heading 2'>Heading 2</option>
                    <option value='Heading 3'>Heading 3</option>
                    <option value='Heading 4'>Heading 4</option>
                    <option value='Heading 5'>Heading 5</option>
                    <option value='Address'>Address</option>
                    <option value='Formatted'>Formatted</option>
                    <option value='Definition Term'>Definition Term</option>
                  </select>
                                <select name="selFont" onChange="doFont(this.options[this.selectedIndex].value)">
                                        <option value="">Fuente</option>
                                        <option value="Arial">Arial</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                </select>
                    <select onchange="wysiwyg.document.execCommand('fontsize', false, this[this.selectedIndex].value); wysiwyg.focus();" id=select1 name=select1>
                    <option selected>Tam.</option>
                    <option value='1'>1</option>
                    <option value='2'>2</option>
                    <option value='3'>3</option>
                    <option value='4'>4</option>
                    <option value='5'>5</option>
                    <option value='6'>6</option>
                    <option value='7'>7</option>
                    <option value='8'>8</option>
                    <option value='10'>10</option>
                    <option value='12'>12</option>
                    <option value='14'>14</option>
                  </select>
                                -->
                                <img src="/imagen/editor/separator.gif" hspace="2" width="2" height="22" />
                                <img class="button" id="Bold" alt="Negrita" src="/imagen/editor/bold.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doBold(this)" />
                                <img class="button" id="Italic" alt="Cursiva" src="/imagen/editor/italic.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doItalic(this)" />
                                <img class="button" id="Underline" alt="Subrayado" src="/imagen/editor/underline.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doUnderline(this)" />
                                <!--
                                <img class="button" id="Strike" alt="Tachado" src="/imagen/editor/strike.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doStrike(this)" />
                                -->
                                <img src="/imagen/editor/separator.gif" hspace="2" width="2" height="22" />
                                <img class="button" id="Bul" alt="Cortar" src="/imagen/editor/cut.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doCortar(this)" />
                                <img class="button" id="Bul" alt="Copiar" src="/imagen/editor/copy.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doCopiar(this)" />
                                <img class="button" id="Rule" alt="Pegar" src="/imagen/editor/paste.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doPegar(this)" />
                                <!--
                                <img src="/imagen/editor/separator.gif" hspace="2" width="2" height="22" />
                                <img class="button" id="Bul" alt="Reducir sangría" src="/imagen/editor/outdent.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doSangriaMenos(this)" />
                                <img class="button" id="Bul" alt="Aumentar sangría" src="/imagen/editor/indent.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doSangriaMas(this)" />
                                <img src="/imagen/editor/separator.gif" hspace="2" width="2" height="22" />
                                <img class="button" id="Bul" alt="Lista de viñetas" src="/imagen/editor/bullets.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doBulList(this)" />
                                <img class="button" id="Bul" alt="Lista Ordenada" src="/imagen/editor/orderedlist.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doListaOrd(this)" />
                                <img class="button" id="Rule" alt="Regla Horizontal" src="/imagen/editor/hr.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doRule(this)" />
                                <img src="/imagen/editor/separator.gif" hspace="2" width="2" height="22" />
                                <img class="button" id="Undo" alt="Deshacer" src="/imagen/editor/undo.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doUndo(this)" />
                                <img class="button" id="Redo" alt="Rehacer" src="/imagen/editor/redo.gif" onMouseOut="buttonOut(this)" onMouseOver="buttonOver(this)" onClick="doRedo(this)" />
                                -->
                                </td>
                        </tr>
                </table>
                </td>
        </tr>
        <tr>
                <td>
                <iframe id="wysiwyg" name="wysiwyg" style="width: <?=$anchura_editor;?>; height:<?=$altura_editor;?>"></iframe>
                </td>
        </tr>
</table>
