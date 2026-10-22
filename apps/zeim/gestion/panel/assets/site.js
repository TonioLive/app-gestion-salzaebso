(function(){'use strict';
 var shell=document.querySelector('[data-app-shell]'),toggle=document.querySelector('[data-menu-toggle]'),side=document.querySelector('[data-sidebar]');
 function menu(open){if(!shell||!toggle)return;shell.classList.toggle('app-shell--menu-open',open);toggle.setAttribute('aria-expanded',String(open));document.body.classList.toggle('menu-open',open);if(open){var first=side.querySelector('a');if(first)first.focus();}else toggle.focus();}
 if(toggle)toggle.addEventListener('click',function(){menu(toggle.getAttribute('aria-expanded')!=='true');});
 var backdrop=document.querySelector('[data-sidebar-backdrop]');if(backdrop)backdrop.addEventListener('click',function(){menu(false);});
 document.addEventListener('keydown',function(e){if(e.key==='Escape'&&toggle&&toggle.getAttribute('aria-expanded')==='true')menu(false);});
 if(side)side.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){if(toggle.getAttribute('aria-expanded')==='true')menu(false);});});
 var password=document.getElementById('password'),button=document.querySelector('[data-password-toggle]');
 if(button)button.addEventListener('click',function(){var show=password.type==='password';password.type=show?'text':'password';button.textContent=show?'Ocultar':'Mostrar';button.setAttribute('aria-label',show?'Ocultar contraseña':'Mostrar contraseña');button.setAttribute('aria-pressed',String(show));});
 var search=document.querySelector('[data-tool-search]');
 function normalize(s){return s.toLocaleLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
 if(search)search.addEventListener('input',function(){var count=0,q=normalize(search.value.trim());document.querySelectorAll('[data-module]').forEach(function(card){card.hidden=normalize(card.textContent).indexOf(q)<0;if(!card.hidden)count++;});document.querySelector('[data-search-empty]').hidden=count>0;});
}());
