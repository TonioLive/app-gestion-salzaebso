(function () {
    'use strict';
    var root = document.querySelector('[data-comms]');
    if (!root) return;
    var centre = root.dataset.centre, csrf = root.dataset.csrf;
    var error = root.querySelector('[data-error]'), stopped = false;
    function node(tag, cls, text) { var n=document.createElement(tag); if(cls)n.className=cls; if(text!==undefined)n.textContent=text; return n; }
    function key() { return window.crypto && crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36)+'-'+Math.random().toString(36).slice(2)+'-'+Math.random().toString(36).slice(2); }
    function time(date) { var d=new Date(String(date).replace(' ','T')); return Number.isNaN(d.getTime()) ? '' : new Intl.DateTimeFormat('es-ES',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}).format(d); }
    function failure(e) { error.textContent=e.message; error.hidden=false; }
    async function request(action, values, post) {
        var params=new URLSearchParams(values || {}); params.set('centro_id',centre);
        var url='/gestion/comunicacion/api.php?action='+action;
        var opts={credentials:'same-origin',cache:'no-store'};
        if(post){params.set('_csrf',csrf);opts.method='POST';opts.body=params;} else url+='&'+params.toString();
        var response=await fetch(url,opts), data;
        try {data=await response.json();} catch(e){throw new Error('No se puede conectar. Comprueba la conexión e inténtalo de nuevo.');}
        if(!response.ok || !data.ok){ if([403,409,419].indexOf(response.status)!==-1)stopped=true; throw new Error(data.message || 'No se ha podido completar la operación.'); }
        error.hidden=true; return data;
    }
    function busy(form,value){Array.prototype.forEach.call(form.querySelectorAll('input,textarea,button'),function(el){el.disabled=value;});}

    if(root.dataset.mode==='chat'){
        var stream=root.querySelector('[data-chat-stream]'), form=root.querySelector('[data-chat-form]');
        var textarea=form.querySelector('textarea'), older=root.querySelector('[data-older]'), newMessages=root.querySelector('[data-new-messages]');
        var status=root.querySelector('[data-chat-status]');
        var first=0,last=0,lastRead=0,loaded=false,loading=false,sending=false,pendingKey='';
        function atBottom(){return stream.scrollHeight-stream.scrollTop-stream.clientHeight<70;}
        function toBottom(){stream.scrollTop=stream.scrollHeight;newMessages.hidden=true;read();}
        async function read(){
            if(!last || last<=lastRead || document.visibilityState!=='visible' || !document.hasFocus() || !atBottom() || stopped)return;
            var reading=last; lastRead=reading;
            try{await request('read',{last_id:reading},true);document.dispatchEvent(new Event('lapices:team-read'));}catch(e){lastRead=0;failure(e);}
        }
        function message(m){
            var article=node('article','team-message'+(m.own?' team-message--own':'')); article.dataset.messageId=m.id;
            var initials=String(m.autor).trim().split(/\s+/).slice(0,2).map(function(s){return s.charAt(0);}).join('').toUpperCase();
            article.appendChild(node('span','team-message-avatar',initials));
            var bubble=node('div','team-message-bubble');bubble.appendChild(node('strong','team-message-author',m.own?'Tú':m.autor));bubble.appendChild(node('p','team-message-text',m.texto));
            var stamp=node('time','',time(m.creado));stamp.dateTime=String(m.creado).replace(' ','T');bubble.appendChild(stamp);article.appendChild(bubble);return article;
        }
        async function load(before){
            if(loading || stopped)return;loading=true;
            var wasBottom=atBottom(), oldHeight=stream.scrollHeight, initial=!loaded;
            try{
                var data=await request('chat',before?{before:before}:{after:last});
                if(!loaded){stream.textContent='';loaded=true;}
                var fragment=document.createDocumentFragment();
                data.messages.forEach(function(m){if(!stream.querySelector('[data-message-id="'+m.id+'"]'))fragment.appendChild(message(m));if(!first || m.id<first)first=m.id;last=Math.max(last,m.id);});
                var empty=stream.querySelector('.comms-empty');if(empty && data.messages.length)empty.remove();
                if(before){stream.insertBefore(fragment,stream.firstChild);stream.scrollTop+=stream.scrollHeight-oldHeight;older.hidden=!data.more;}
                else{stream.appendChild(fragment);if(!first && !stream.querySelector('.comms-empty'))stream.appendChild(node('p','comms-empty','Todavía no hay mensajes. Abre la conversación con el equipo.'));if(wasBottom)toBottom();else if(data.messages.length)newMessages.hidden=false;}
                if(initial){older.hidden=!data.more;}
                status.textContent='Se actualiza automáticamente · Solo el equipo de este centro';
                read();
                loading=false;
                if(!initial && !before && data.more && data.messages.length)await load();
            }catch(e){status.textContent='Sin conexión. Reintentando…';failure(e);}finally{loading=false;}
        }
        older.addEventListener('click',function(){load(first);});
        newMessages.addEventListener('click',toBottom);
        stream.addEventListener('scroll',function(){if(atBottom()){newMessages.hidden=true;read();}},{passive:true});
        textarea.addEventListener('input',function(){pendingKey='';});
        form.addEventListener('submit',async function(event){
            event.preventDefault();var text=textarea.value.trim();if(!text || sending || stopped)return;
            sending=true;busy(form,true);pendingKey=pendingKey||key();
            try{await request('send',{text:text,key:pendingKey},true);textarea.value='';pendingKey='';await load();toBottom();document.dispatchEvent(new Event('lapices:team-read'));}
            catch(e){failure(e);}finally{sending=false;busy(form,false);textarea.focus();}
        });
        textarea.addEventListener('keydown',function(event){if((event.ctrlKey||event.metaKey)&&event.key==='Enter'){event.preventDefault();form.requestSubmit();}});
        document.addEventListener('visibilitychange',function(){if(document.visibilityState==='visible'){load();read();}});
        window.addEventListener('focus',function(){load();read();});
        load();window.setInterval(function(){if(document.visibilityState==='visible')load();},5000);
    }else{
        var list=root.querySelector('[data-notice-list]'), filter=root.querySelector('[data-notice-filter]'), more=root.querySelector('[data-notices-more]');
        var noticeForm=root.querySelector('[data-notice-form]'), noticeBefore=0, noticeKey='', noticeLoading=false, noticeReload=false, publishing=false;
        function card(n){
            var article=node('article','notice-card'), meta=node('div','notice-card-meta');
            meta.appendChild(node('span','notice-tag',n.archivado?'Archivado':'Para todas las familias'));meta.appendChild(node('time','',time(n.creado)));article.appendChild(meta);
            article.appendChild(node('h3','',n.titulo));article.appendChild(node('p','notice-card-body',n.texto));
            var footer=node('footer','notice-card-footer');footer.appendChild(node('span','','Publicado por '+n.autor));
            if(root.dataset.canPublish==='1'){
                var action=node('button','',n.archivado?'Volver a publicar':'Archivar');action.type='button';
                action.addEventListener('click',async function(){action.disabled=true;try{await request('archive',{id:n.id,archived:n.archivado?0:1},true);await notices(false);}catch(e){failure(e);action.disabled=false;}});footer.appendChild(action);
            }
            article.appendChild(footer);return article;
        }
        async function notices(append){
            if(stopped)return;if(noticeLoading){if(!append)noticeReload=true;return;}noticeLoading=true;more.disabled=true;
            var selectedFilter=filter.value;
            try{var data=await request('notices',{archived:selectedFilter,before:append?noticeBefore:0});if(selectedFilter!==filter.value){noticeReload=true;return;}if(!append){list.textContent='';noticeBefore=0;}data.notices.forEach(function(n){list.appendChild(card(n));noticeBefore=n.id;});more.hidden=!data.more;if(!list.children.length)list.appendChild(node('p','comms-empty',selectedFilter==='1'?'No hay avisos archivados.':'Todavía no hay avisos publicados. Los nuevos avisos aparecerán aquí y en la app de las familias.'));}
            catch(e){failure(e);}finally{noticeLoading=false;more.disabled=false;if(noticeReload){noticeReload=false;notices(false);}}
        }
        filter.addEventListener('change',function(){notices(false);});more.addEventListener('click',function(){notices(true);});
        if(noticeForm){
            noticeForm.addEventListener('input',function(){noticeKey='';});
            noticeForm.addEventListener('submit',async function(event){
                event.preventDefault();if(stopped||publishing)return;var title=noticeForm.elements.title.value.trim(),text=noticeForm.elements.text.value.trim();if(!title||!text)return;
                publishing=true;busy(noticeForm,true);noticeKey=noticeKey||key();
                try{await request('publish',{title:title,text:text,key:noticeKey},true);noticeForm.reset();noticeKey='';noticeForm.querySelector('[data-publish-status]').textContent='Aviso publicado. Las familias del centro ya pueden verlo en su app.';filter.value='0';await notices(false);}
                catch(e){failure(e);}finally{publishing=false;busy(noticeForm,false);}
            });
        }
        notices(false);
    }
}());
