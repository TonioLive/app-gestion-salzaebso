'use strict';
const fs=require('node:fs'),path=require('node:path'),{execFileSync}=require('node:child_process');
const webpush=require('../notifications/node_modules/web-push');
const {deliver}=require('../notifications/worker.cjs');
const privateDir=path.resolve(__dirname,'../../.private/pool');
const configFile=path.resolve(__dirname,'../../.private/notifications/config.json');
function bridge(config,input){return JSON.parse(execFileSync(config.php,[...(config.phpArgs||[]),path.join(__dirname,'admin-bridge.php')],{input:JSON.stringify(input),encoding:'utf8',timeout:30000,windowsHide:true}));}
async function main(){
 fs.mkdirSync(privateDir,{recursive:true,mode:0o700});
 const lock=path.join(privateDir,'admin-worker.lock');let fd;
 try{fd=fs.openSync(lock,'wx');}catch(e){if(e.code!=='EEXIST')throw e;try{process.kill(Number(fs.readFileSync(lock,'utf8')),0);return;}catch(err){if(err.code!=='ESRCH')return;}fs.unlinkSync(lock);fd=fs.openSync(lock,'wx');}
 fs.writeFileSync(fd,String(process.pid));const status={at:Math.floor(Date.now()/1000),ok:false,accepted:0,other:0};
 try{
  const config=JSON.parse(fs.readFileSync(configFile,'utf8'));webpush.setVapidDetails(config.subject,config.publicKey,config.privateKey);
  const jobs=bridge(config,{action:'jobs'});
  for(let i=0;i<jobs.length;i+=5){const results=await Promise.all(jobs.slice(i,i+5).map(job=>deliver(job,webpush.sendNotification.bind(webpush),input=>bridge(config,input))));for(const r of results)r==='accepted'?status.accepted++:status.other++;}
  status.ok=true;
 }catch(e){status.error='No se ha podido completar el envío de avisos de los equipos.';process.exitCode=1;}
 finally{fs.writeFileSync(path.join(privateDir,'admin-worker-status.json'),JSON.stringify(status));fs.closeSync(fd);fs.unlinkSync(lock);process.stdout.write(JSON.stringify(status)+'\n');}
}
main().catch(()=>{process.stderr.write('No se ha podido iniciar el proceso de avisos.\n');process.exitCode=1;});
