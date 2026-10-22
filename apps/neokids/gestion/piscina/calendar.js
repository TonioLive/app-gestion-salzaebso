(() => {
 'use strict';
 const search=document.getElementById('search-groups');
 if(!search)return;
 const normalize=value=>value.toLocaleLowerCase('es').normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/\s+/g,' ').trim();
 const events=Array.from(document.querySelectorAll('.calendar-event[data-open]'));
 const dialogs=Array.from(document.querySelectorAll('.calendar-dialog'));
 function highlightName(element,name,query) {
  // Create text nodes and marks; names and search input never become HTML.
  element.replaceChildren();
  let folded='',offset=0;const positions=[];
  for(const character of name){
   let part=character.toLocaleLowerCase('es').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
   if(/\s/.test(part))part=' ';
   if(part===' '&&folded.endsWith(' ')){positions[positions.length-1].end=offset+character.length;}
   else{folded+=part;for(let j=0;j<part.length;j++)positions.push({start:offset,end:offset+character.length});}
   offset+=character.length;
  }
  const start=query?folded.indexOf(query):-1;
  if(start<0){element.textContent=name;return;}
  const from=positions[start].start,to=positions[start+query.length-1].end;
  element.append(document.createTextNode(name.slice(0,from)));
  const mark=document.createElement('mark');mark.textContent=name.slice(from,to);element.append(mark,document.createTextNode(name.slice(to)));
 }
 function highlightRoster(dialog) {
  const query=normalize(search.value);let matches=0,first=null;
  dialog.querySelectorAll('.calendar-roster').forEach(details=>{
   let matching=false;
   details.querySelectorAll('.calendar-student').forEach(row=>{
    const name=row.dataset.studentName,match=!!query&&normalize(name).includes(query);
    row.classList.toggle('is-search-match',match);row.querySelector('.calendar-search-badge').hidden=!match;
    highlightName(row.querySelector('.calendar-student-name'),name,match?query:'');
    if(match){matching=true;matches++;if(!first)first=row;}
   });
   if(matching){if(!details.open)details.dataset.openedBySearch='true';details.open=true;}
   else if(details.dataset.openedBySearch){details.open=false;delete details.dataset.openedBySearch;}
  });
  const summary=dialog.querySelector('.calendar-match-summary');summary.hidden=!matches;
  summary.textContent=matches===1?'El alumno buscado está resaltado en su grupo.':matches+' alumnos coinciden con la búsqueda y están resaltados.';
  return first;
 }
 events.forEach(button=>button.addEventListener('click',()=>{
  const dialog=document.getElementById(button.dataset.open);const first=highlightRoster(dialog);
  dialog.showModal();
  if(first)requestAnimationFrame(()=>first.scrollIntoView({block:'nearest',behavior:'auto'}));
 }));
 dialogs.forEach(dialog=>{
  dialog.querySelector('[data-close]').addEventListener('click',()=>dialog.close());
  dialog.addEventListener('click',event=>{if(event.target===dialog){const r=dialog.getBoundingClientRect();if(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom)dialog.close();}});
 });
 function filterGroups(){
  const query=normalize(search.value);let count=0;
  events.forEach(event=>{event.hidden=!normalize(event.dataset.search).includes(query);if(!event.hidden)count++;});
  document.getElementById('no-groups').hidden=count>0;
  document.getElementById('calendar-search-count').textContent=query?(count===1?'1 clase coincide con la búsqueda.':count+' clases coinciden con la búsqueda.'):'';
  dialogs.forEach(dialog=>{if(dialog.open)highlightRoster(dialog);});
  document.querySelectorAll('.calendar-periods a').forEach(link=>{const url=new URL(link.href,location.href);if(search.value.trim())url.searchParams.set('q',search.value.trim());else url.searchParams.delete('q');link.href=url.href;});
 }
 search.addEventListener('input',filterGroups);filterGroups();
})();
