(function(){
    'use strict';
    var root=document.querySelector('[data-family-notices]');
    var badges=Array.prototype.slice.call(document.querySelectorAll('[data-family-notice-count]'));
    if(!root&&!badges.length)return;
    var board=root&&root.dataset.familyNotices==='board',list=root&&root.querySelector('[data-family-notice-list]'),status=root&&root.querySelector('[data-family-notice-status]'),more=root&&root.querySelector('[data-family-notice-more]');
    var before=0,csrf='',loading=false,unread=0;
    function node(tag,cls,text){var n=document.createElement(tag);if(cls)n.className=cls;if(text!==undefined)n.textContent=text;return n;}
    function counts(){badges.forEach(function(b){b.hidden=unread<=0;b.textContent='('+unread+')';});}
    async function markRead(n,button,tag){
        button.disabled=true;
        try{var body=new URLSearchParams({_csrf:csrf,id:n.id});var response=await fetch('/app/avisos_api.php',{method:'POST',credentials:'same-origin',body:body});var data=await response.json();if(!response.ok||!data.ok)throw new Error(data.message||'No se ha podido marcar como leído.');button.remove();tag.textContent='Leído';unread=Math.max(0,unread-1);counts();}
        catch(e){status.textContent=e.message;button.disabled=false;}
    }
    function card(n){
        var article=node('article','notice-card');article.id='aviso-'+n.id;
        var meta=node('div','notice-card-meta'),tag=node('span','notice-tag',Number(n.unread)?'Nuevo aviso':'Aviso del centro');meta.appendChild(tag);
        var date=new Date(String(n.creado).replace(' ','T'));meta.appendChild(node('time','',new Intl.DateTimeFormat('es-ES',{day:'numeric',month:'long',year:'numeric'}).format(date)));article.appendChild(meta);
        article.appendChild(node('h3','',n.titulo));article.appendChild(node('p','notice-card-body',n.texto));
        var footer=node('footer','notice-card-footer');footer.appendChild(node('span','',n.centro));
        if(board&&Number(n.unread)){var read=node('button','','Marcar como leído');read.type='button';read.addEventListener('click',function(){markRead(n,read,tag);});footer.appendChild(read);}
        if(!board){var link=node('a','','Leer aviso →');link.href='/app/avisos.php#aviso-'+n.id;footer.appendChild(link);}
        article.appendChild(footer);return article;
    }
    async function load(append){
        if(loading)return;loading=true;if(more)more.disabled=true;
        try{
            var response=await fetch('/app/avisos_api.php?before='+(append?before:0),{credentials:'same-origin',cache:'no-store'}),data=await response.json();
            if(!response.ok||!data.ok)throw new Error(data.message||'No se pueden cargar los avisos.');csrf=data.csrf;unread=data.unread;counts();
            if(list){if(!append)list.textContent='';var notices=board?data.notices:data.notices.slice(0,3);notices.forEach(function(n){list.appendChild(card(n));before=n.id;});status.textContent=list.children.length?'':'No hay avisos publicados en este momento.';if(more)more.hidden=!data.more;}
            if(board&&location.hash){var target=document.getElementById(location.hash.slice(1));if(target)target.scrollIntoView({block:'start'});}
        }catch(e){if(status)status.textContent=e.message;}finally{loading=false;if(more)more.disabled=false;}
    }
    if(more)more.addEventListener('click',function(){load(true);});
    load(false);
    if(!board)setInterval(function(){if(document.visibilityState==='visible')load(false);},60000);
}());
