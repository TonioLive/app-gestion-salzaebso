(() => {
  'use strict';
  const tables=[];
  document.querySelectorAll('table').forEach(table=>{
    if(table.querySelector('table')||table.rows.length<2)return;
    const heading=table.tHead?.rows[0]||table.rows[0];
    if(!heading||heading.cells.length<3)return;
    const explicit=table.matches('.neo-data-table,[data-neo-table]') || (document.body.classList.contains('caja-app') && !!table.closest('.caja-table-wrap'));
    const legacy=document.body.classList.contains('legacy-admin');
    if(!explicit&&!legacy)return;
    table.classList.add('neo-card-table');
    if(legacy&&!explicit)table.classList.add('neo-legacy-table');
    let wrap=table.closest('.neo-table-wrap,.caja-table-wrap');
    if(!wrap){wrap=document.createElement('div');wrap.className='neo-table-wrap';table.before(wrap);wrap.append(table);}
    wrap.tabIndex=0;wrap.setAttribute('role','region');wrap.setAttribute('aria-label',table.caption?.textContent.trim()||'Tabla de datos; desplaza para ver todas las columnas');
    const labels=Array.from(heading.cells).map(c=>c.textContent.trim());
    heading.classList.add('neo-card-heading');
    Array.from(table.rows).forEach(row=>{if(row===heading)return;Array.from(row.cells).forEach((cell,index)=>cell.dataset.neoLabel=labels[index]||'Acciones');});
    tables.push({table,wrap,cols:heading.cells.length});
  });
  document.querySelectorAll('[data-neo-filter]').forEach(input=>{
    input.addEventListener('input',()=>{
      const target=document.getElementById(input.dataset.neoFilter);if(!target)return;
      const fold=s=>s.toLocaleLowerCase('es').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
      const query=fold(input.value.trim());
      target.querySelectorAll('tbody>tr:not(.group-row)').forEach(row=>row.hidden=query!==''&&!fold(row.textContent).includes(query));
    });
  });
  document.querySelectorAll('body.neo-page form[data-confirm]').forEach(form=>form.addEventListener('submit',event=>{if(!window.confirm(form.dataset.confirm))event.preventDefault();}));
  if(!tables.length)return;
  const dialog=document.createElement('section');dialog.className='neo-orientation';dialog.hidden=true;dialog.setAttribute('role','dialog');dialog.setAttribute('aria-modal','true');dialog.setAttribute('aria-labelledby','neo-turn-title');
  dialog.innerHTML='<div><span class="phone" aria-hidden="true">↻ ▯</span><h2 id="neo-turn-title">Gira el móvil</h2><p>Esta tabla necesita más ancho para mostrar todos los datos con claridad. Pon el dispositivo en horizontal.</p><button type="button" data-landscape>Ver en horizontal</button><button type="button" data-cards>Ver como fichas en vertical</button><p class="neo-help" data-turn-help>Si no gira, desactiva el bloqueo de orientación del móvil.</p></div>';
  document.body.append(dialog);
  let cards=false,lastFocus=null;
  const update=()=>{
    const portrait=window.matchMedia('(max-width:600px) and (orientation:portrait)').matches;
    const dense=tables.some(t=>t.table.getClientRects().length&&t.table.scrollWidth>t.wrap.clientWidth+24);
    const shown=portrait&&dense&&!cards;
    if(shown&&dialog.hidden){lastFocus=document.activeElement;dialog.hidden=false;dialog.querySelector('button').focus();}
    if(!shown&&!dialog.hidden){dialog.hidden=true;if(lastFocus?.isConnected)lastFocus.focus();}
    document.documentElement.classList.toggle('neo-orientation-active',shown);
  };
  dialog.querySelector('[data-cards]').addEventListener('click',()=>{cards=true;document.body.classList.add('neo-card-view');update();});
  dialog.querySelector('[data-landscape]').addEventListener('click',async()=>{
    try {if(!screen.orientation?.lock)throw new Error();if(!document.fullscreenElement)await document.documentElement.requestFullscreen();await screen.orientation.lock('landscape');}
    catch(_){dialog.querySelector('[data-turn-help]').textContent='Gira el dispositivo manualmente o utiliza la vista de fichas.';}
    update();
  });
  dialog.addEventListener('keydown',e=>{if(e.key!=='Tab')return;const buttons=dialog.querySelectorAll('button');if(e.shiftKey&&document.activeElement===buttons[0]){e.preventDefault();buttons[1].focus();}else if(!e.shiftKey&&document.activeElement===buttons[1]){e.preventDefault();buttons[0].focus();}});
  let queued=false;window.addEventListener('resize',()=>{if(!queued){queued=true;requestAnimationFrame(()=>{queued=false;update();});}});window.addEventListener('orientationchange',update);update();
})();
