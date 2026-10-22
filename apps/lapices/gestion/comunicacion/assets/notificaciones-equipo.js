(function(){
    'use strict';
    if(window.LapicesTeamNotifications)return;
    var widget=document.querySelector('[data-team-notifications]');if(!widget)return;
    window.LapicesTeamNotifications=true;
    var centre=widget.dataset.centre,storageKey='lapices-team-notified-'+widget.dataset.user+'-'+centre;
    var cursor=-1,loading=false,stopped=false,toastTimer=null;
    function saved(){try{var v=localStorage.getItem(storageKey);return v===null?-1:Number(v);}catch(e){return cursor;}}
    cursor=saved();if(!Number.isFinite(cursor))cursor=-1;
    function save(value){cursor=Math.max(cursor,Number(value));try{localStorage.setItem(storageKey,String(cursor));}catch(e){}}
    function toast(events){
        if(!events.length)return;
        var activeChat=document.querySelector('[data-comms][data-mode="chat"]');
        if(activeChat && document.visibilityState==='visible' && document.hasFocus())return;
        var old=document.querySelector('.team-toast');if(old)old.remove();
        var box=document.createElement('aside');box.className='team-toast';box.setAttribute('role','status');
        var close=document.createElement('button');close.type='button';close.textContent='×';close.setAttribute('aria-label','Cerrar aviso');close.onclick=function(){box.remove();};box.appendChild(close);
        var title=document.createElement('strong');title.textContent=events.length===1?'Nuevo mensaje del equipo':events.length+' mensajes nuevos del equipo';box.appendChild(title);
        var text=document.createElement('span'),last=events[events.length-1];text.textContent=last.autor+': '+last.texto.slice(0,160);box.appendChild(text);
        var link=document.createElement('a');link.href=widget.href;link.textContent='Abrir chat';box.appendChild(link);document.body.appendChild(box);
        clearTimeout(toastTimer);toastTimer=setTimeout(function(){box.remove();},9000);
    }
    function desktop(event){
        if(!('Notification' in window) || Notification.permission!=='granted')return;
        if(document.querySelector('[data-comms][data-mode="chat"]') && document.visibilityState==='visible' && document.hasFocus())return;
        try{
            var notification=new Notification(event.autor+' · '+widget.dataset.centreName,{body:event.texto.slice(0,200),tag:'lapices-team-'+centre+'-'+event.id,icon:'/imagen/logo_redondo.png'});
            notification.onclick=function(){window.focus();window.location.href=widget.href;notification.close();};
        }catch(e){/* El contador y el aviso del programa permanecen disponibles. */}
    }
    async function poll(){
        if(loading || stopped)return;loading=true;
        try{
            var after=Math.max(cursor,saved());
            var response=await fetch('/gestion/comunicacion/api.php?action=notifications&centro_id='+encodeURIComponent(centre)+'&after='+after,{credentials:'same-origin',cache:'no-store'});
            if([403,409,419].indexOf(response.status)!==-1){stopped=true;return;}
            var data=await response.json();if(!response.ok||!data.ok)return;
            var badge=widget.querySelector('[data-team-count]');badge.hidden=data.unread<=0;badge.textContent=data.unread>99?'99+':String(data.unread);
            widget.setAttribute('aria-label','Chat del equipo'+(data.unread?', '+data.unread+' mensajes sin leer':''));
            var fresh=[];
            (data.events||[]).forEach(function(event){if(Number(event.id)>Math.max(cursor,saved())){save(event.id);fresh.push(event);desktop(event);}});
            toast(fresh);if(!data.more)save(data.latest);
            loading=false;if(data.more)setTimeout(poll,100);
        }catch(e){/* Se reintentará en la siguiente actualización. */}finally{loading=false;}
    }
    var enable=document.querySelector('[data-enable-notifications]'),status=document.querySelector('[data-notification-status]');
    function permissionState(){
        if(!enable)return;
        if(!('Notification' in window)||!window.isSecureContext){enable.hidden=true;status.textContent='Los avisos dentro del programa están activos. Este navegador no permite notificaciones de escritorio en esta conexión.';return;}
        if(Notification.permission==='granted'){enable.textContent='Notificaciones activadas';enable.disabled=true;status.textContent='Recibirás avisos del navegador mientras tengas el programa abierto, incluso en otra pestaña.';}
        else if(Notification.permission==='denied'){enable.textContent='Notificaciones bloqueadas';enable.disabled=true;status.textContent='Puedes permitir las notificaciones desde los permisos de este sitio en tu navegador. Los avisos del programa siguen activos.';}
        else{enable.textContent='Activar notificaciones';enable.disabled=false;status.textContent='Activa los avisos del navegador para enterarte cuando estés en otra pestaña.';}
    }
    if(enable)enable.addEventListener('click',async function(){try{await Notification.requestPermission();}catch(e){}permissionState();});
    permissionState();poll();setInterval(poll,10000);
    document.addEventListener('lapices:team-read',poll);
    document.addEventListener('visibilitychange',function(){if(document.visibilityState==='visible'){permissionState();poll();}});
}());
