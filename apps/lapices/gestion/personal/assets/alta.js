(() => {
 const form=document.querySelector('[data-staff-review]');
 if(form){
  const centre=form.elements.id_centro, course=form.elements.id_curso, aula=form.elements.id_aula, role=form.elements.nivel_acceso;
  const allClassrooms=Array.from(aula.options);
  function classrooms(){
   const isTeacher=['2','3'].includes(role.value), selected=aula.value;
   document.querySelector('[data-class-section]').hidden=!isTeacher;aula.required=role.value==='2';
   aula.replaceChildren(...allClassrooms.filter(option=>option.value==='0'||(option.dataset.centre===centre.value&&option.dataset.course===course.value)));
   aula.value=isTeacher&&Array.from(aula.options).some(option=>option.value===selected)?selected:'0';
  }
  centre.addEventListener('change',classrooms);course.addEventListener('change',classrooms);role.addEventListener('change',classrooms);classrooms();
  form.addEventListener('submit',()=>{if(form.checkValidity())form.querySelector('[type=submit]').disabled=true;});
 }
 document.querySelector('[data-copy-credentials]')?.addEventListener('click',async()=>{
  const field=document.getElementById('credentials-message'),status=document.querySelector('[data-copy-status]');
  try{await navigator.clipboard.writeText(field.value);status.textContent='Mensaje copiado. Ya puedes compartirlo con el trabajador.';}
  catch(_){field.focus();field.select();status.textContent='Mensaje seleccionado. Pulsa Ctrl+C para copiarlo.';}
 });
})();
