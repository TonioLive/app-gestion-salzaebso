'use strict';
document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) { event.preventDefault(); return; }
    const button=form.querySelector('button[type="submit"]'); if(button){button.disabled=true;button.textContent='Guardando...';}
}));
function updateTableHints(){document.querySelectorAll('.caja-table-wrap').forEach(wrap=>{const hint=wrap.previousElementSibling;if(hint&&hint.classList.contains('caja-rotate'))hint.classList.toggle('needed',wrap.scrollWidth>wrap.clientWidth+5);});}
window.addEventListener('resize',updateTableHints);updateTableHints();
document.querySelectorAll('[data-landscape]').forEach(button=>button.addEventListener('click',async()=>{try{if(document.documentElement.requestFullscreen)await document.documentElement.requestFullscreen();if(screen.orientation&&screen.orientation.lock)await screen.orientation.lock('landscape');}catch(e){button.textContent='Gira el dispositivo';}}));
const counted=document.querySelector('[name="importe_contado"]'),withdrawn=document.querySelector('[name="importe_retirado"]'),preview=document.querySelector('[data-close-preview]');
function previewClose(){if(!preview)return;const parse=v=>Number(v.trim().replace(/\.(?=\d{3}(?:\.|,))/g,'').replace(',','.'));const c=parse(counted.value),w=parse(withdrawn.value);if(counted.value!==''&&Number.isFinite(c)&&Number.isFinite(w))preview.textContent='Se dejar\u00e1n en caja '+(c-w).toLocaleString('es-ES',{style:'currency',currency:'EUR'})+'. Diferencia del recuento: '+(c-Number(preview.dataset.expected)).toLocaleString('es-ES',{style:'currency',currency:'EUR'})+'.';}
if(counted&&withdrawn){counted.addEventListener('input',previewClose);withdrawn.addEventListener('input',previewClose);}
const centreSelect=document.querySelector('select[name="id_centro_caja"]');if(centreSelect)centreSelect.addEventListener('change',()=>{const periodSelect=document.querySelector('select[name="id_cierre_caja"]');if(periodSelect)periodSelect.value='0';});
