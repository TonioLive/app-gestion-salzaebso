import * as pdfjs from './vendor/pdfjs/pdf.min.mjs';
pdfjs.GlobalWorkerOptions.workerSrc = new URL('./vendor/pdfjs/pdf.worker.min.mjs', import.meta.url).href;
const form = document.querySelector('[data-contract-upload]');
const status = document.querySelector('[data-contract-status]');
let working = false;
function loadOcr() {
  return new Promise((resolve,reject) => {
    if (window.Tesseract) return resolve();
    const script=document.createElement('script');script.src=new URL('./vendor/tesseract/tesseract.min.js',import.meta.url).href;
    script.onload=resolve;script.onerror=()=>reject(new Error('No se ha podido cargar el lector de documentos escaneados.'));document.head.append(script);
  });
}
function pageText(content) {
  // Preserve line breaks and separate columns so labels stay next to their data.
  const lines=[];
  for(const item of content.items){if(!('str' in item)||!item.str.trim())continue;const y=item.transform[5];let line=lines.find(l=>Math.abs(l.y-y)<3);if(!line){line={y,items:[]};lines.push(line);}line.items.push(item);}
  return lines.sort((a,b)=>b.y-a.y).map(line=>{
    let right=null;return line.items.sort((a,b)=>a.transform[4]-b.transform[4]).map(item=>{const x=item.transform[4];const gap=right===null?'':(x-right>18?'\t':' ');right=x+item.width;return gap+item.str;}).join('');
  }).join('\n');
}
async function scannedText(worker,canvas) {
  // Automatic layout handles clause paragraphs and ignores ruling lines better.
  await worker.setParameters({tessedit_pageseg_mode:'3'});
  let text=(await worker.recognize(canvas)).data.text;
  const employee=/DATOS[ \t]+(?:DEL(?:[ \t]*\/[ \t]*DE)?|DE)[ \t]+(?:LA[ \t]+)?TRABAJADOR(?:A|\/A)?[^\n]*\n[\s\S]*?(?=\bDECLARAN\b|\bCL[ÁA]USULAS\b)/iu;
  const block=text.match(employee);
  if(block&&/NIF[^\n]*FECHA[^\n]*(?:AFILIACI|SEGURIDAD)/iu.test(block[0])) {
    // A second, row-preserving pass keeps the name, ID and SS number together in SEPE tables.
    await worker.setParameters({tessedit_pageseg_mode:'6'});
    const rows=(await worker.recognize(canvas)).data.text.match(employee);
    if(rows)text=text.replace(employee,()=>rows[0]);
  }
  return text;
}
form?.addEventListener('submit',async event=>{
  event.preventDefault();if(working||!form.reportValidity())return;
  const file=form.querySelector('[type=file]').files[0];
  if(!file||file.size>8*1024*1024){status.textContent='Selecciona un PDF de hasta 8 MB.';return;}
  working=true;const button=form.querySelector('[type=submit]');button.disabled=true;
  let pdf,worker;const progress=form.querySelector('progress');progress.hidden=false;
  try{
    status.textContent='Leyendo el contrato…';
    pdf=await pdfjs.getDocument({data:await file.arrayBuffer(),isEvalSupported:false,useSystemFonts:true,standardFontDataUrl:new URL('./vendor/pdfjs/standard_fonts/',import.meta.url).href,cMapUrl:new URL('./vendor/pdfjs/cmaps/',import.meta.url).href,cMapPacked:true,wasmUrl:new URL('./vendor/pdfjs/wasm/',import.meta.url).href}).promise;
    if(pdf.numPages>30)throw new Error('El contrato supera las 30 páginas. Sube un PDF más corto.');
    const parts=[];progress.max=pdf.numPages;
    for(let n=1;n<=pdf.numPages;n++){
      const page=await pdf.getPage(n);let text=pageText(await page.getTextContent());
      if(text.replace(/\s/g,'').length<65){
        status.textContent=`Reconociendo página escaneada ${n} de ${pdf.numPages}…`;
        if(!worker){await loadOcr();const root=new URL('./vendor/tesseract/',import.meta.url).href;worker=await window.Tesseract.createWorker('spa',1,{workerPath:root+'worker.min.js',corePath:root+'core',langPath:root+'lang',gzip:false,workerBlobURL:false,cacheMethod:'none'});await worker.setParameters({preserve_interword_spaces:'1'});}
        const natural=page.getViewport({scale:1});const scale=Math.min(3.2,3000/Math.max(natural.width,natural.height));const viewport=page.getViewport({scale});const canvas=document.createElement('canvas');canvas.width=Math.ceil(viewport.width);canvas.height=Math.ceil(viewport.height);
        await page.render({canvasContext:canvas.getContext('2d'),viewport}).promise;
        text=await scannedText(worker,canvas);canvas.width=canvas.height=0;
      }
      parts.push(text);progress.value=n;status.textContent=`Leída la página ${n} de ${pdf.numPages}.`;
      if(parts.join('\n').length>300000)throw new Error('El contrato contiene demasiado texto. Sube un PDF más corto.');page.cleanup();
    }
    const text=parts.join('\n\n');if(text.trim().length<20)throw new Error('No se ha podido leer el contrato. Prueba un PDF más nítido o continúa con el alta manual.');
    form.querySelector('[name=contract_text]').value=text;
    status.textContent='Preparando los datos para que los revises…';
    HTMLFormElement.prototype.submit.call(form);
  }catch(error){status.textContent=error.name==='PasswordException'?'El PDF está protegido con contraseña. Sube una copia desbloqueada.':(error.message||'No se ha podido leer el PDF. Puedes continuar con el alta manual.');button.disabled=false;working=false;progress.hidden=true;}
  finally{if(worker)await worker.terminate();if(pdf)await pdf.destroy();}
});
