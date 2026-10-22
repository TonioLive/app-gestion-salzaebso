'use strict';
const test=require('node:test'),assert=require('node:assert/strict');
const {allowedEndpoint,deliver}=require('./worker.cjs');
const job={id:1,subscription:{endpoint:'https://fcm.googleapis.com/fcm/send/local-test',keys:{}},payload:{title:'Neo Kids',body:'Aviso',url:'/app/',tag:'neo-'+ 'a'.repeat(64)},ttl:300};
test('rejects SSRF and credentials in URLs',()=>{
 for(const url of ['http://fcm.googleapis.com/x','https://127.0.0.1/x','https://fcm.googleapis.com.evil.test/x','https://user@fcm.googleapis.com/x','https://fcm.googleapis.com:8080/x','https://fcm.googleapis.com/x#fragment'])assert.equal(allowedEndpoint(url),false,url);
 for(const url of ['https://fcm.googleapis.com/fcm/send/x','https://updates.push.services.mozilla.com/wpush/v2/x','https://web.push.apple.com/x'])assert.equal(allowedEndpoint(url),true,url);
});
test('success acknowledges delivery to push service',async()=>{
 let receipt;const result=await deliver(job,async(s,p,o)=>{assert.equal(o.TTL,300);assert.equal(o.timeout,12000);assert.equal(JSON.parse(p).url,'/app/');return {statusCode:201};},async a=>{receipt=a;});assert.equal(result,'accepted');assert.equal(receipt.code,201);
});
test('expired subscription passes 410 for deactivation',async()=>{
 let receipt;await deliver(job,async()=>{throw {statusCode:410};},async a=>{receipt=a;});assert.equal(receipt.code,410);
});
test('transient error is retryable',async()=>{
 let receipt;await deliver(job,async()=>{throw {statusCode:503};},async a=>{receipt=a;});assert.equal(receipt.code,503);
});
test('network timeout becomes bounded retry',async()=>{
 let receipt;await deliver(job,async()=>{throw Error('timeout');},async a=>{receipt=a;});assert.equal(receipt.code,0);
});
test('forbidden endpoint never triggers network',async()=>{
 let sent=false;await deliver({...job,subscription:{endpoint:'http://127.0.0.1/'}},async()=>{sent=true;},async()=>{});assert.equal(sent,false);
});
