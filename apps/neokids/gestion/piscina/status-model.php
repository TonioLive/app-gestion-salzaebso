<?php
// Display targets supplied by the pool operator. This file never changes device settings.
function pool_metric_definitions(){
 return array(
  'water_temperature'=>array('title'=>'Temperatura del agua','unit'=>'°C','source'=>'water','detail'=>'Retorno del vaso','min'=>29,'max'=>30.5,'scale_min'=>25,'scale_max'=>35),
  'humidity'=>array('title'=>'Humedad ambiente','unit'=>'%','source'=>'air','detail'=>'Retorno de aire','min'=>35,'max'=>65,'scale_min'=>0,'scale_max'=>100),
  'air_temperature'=>array('title'=>'Temperatura ambiente','unit'=>'°C','source'=>'air','detail'=>'Retorno de aire','min'=>28,'max'=>34,'scale_min'=>20,'scale_max'=>40),
  'ph'=>array('title'=>'pH del agua','unit'=>'pH','source'=>'vista','detail'=>'Sonda de VistaPool','min'=>7.2,'max'=>8,'scale_min'=>6,'scale_max'=>9),
  'rx'=>array('title'=>'Desinfección · Redox','unit'=>'mV','source'=>'vista','detail'=>'Potencial de oxidación · VistaPool','min'=>680,'max'=>780,'scale_min'=>500,'scale_max'=>900),
  'water_supply'=>array('title'=>'Impulsión de agua','unit'=>'°C','source'=>'water','detail'=>'Sonda de impulsión del vaso','min'=>null,'max'=>null,'scale_min'=>20,'scale_max'=>40)
 );
}
function pool_metric_number($value){return is_numeric($value)&&is_finite((float)$value)?(float)$value:null;}
function pool_source_health($source,$data,$now){
 if(!$data||empty($data['updated_at']))return array('state'=>'missing','label'=>'Sin lecturas','time'=>null);
 $time=$source==='vista'?(isset($data['source_time'])?(int)$data['source_time']:0):(int)$data['updated_at'];
 if(empty($data['ok']))return array('state'=>'offline','label'=>'No se ha podido conectar','time'=>$time?:null);
 if($source==='vista'&&isset($data['device_online'])&&$data['device_online']===false)return array('state'=>'offline','label'=>'Equipo desconectado','time'=>$time?:null);
 // A ten-minute cycle has five minutes of tolerance. An old cloud record is not a live reading.
 if(!$time||$time>$now+120||$now-$time>900||empty($data['checked_at'])||$now-(int)$data['checked_at']>900)return array('state'=>'stale','label'=>'Lectura sin actualizar','time'=>$time?:null);
 return array('state'=>'current','label'=>'Lecturas actualizadas','time'=>$time);
}
function pool_metric_evaluate($key,$definition,$data,$now){
 $health=pool_source_health($definition['source'],$data,$now);
 $value=isset($data['values'][$key])?pool_metric_number($data['values'][$key]):null;
 $range='info';if($definition['min']!==null&&$value!==null)$range=$value<$definition['min']?'low':($value>$definition['max']?'high':'ok');
 $state=$value===null?'missing':($health['state']==='current'?$range:$health['state']);
 $labels=array('ok'=>'En rango','low'=>'Por debajo','high'=>'Por encima','info'=>'Informativo','missing'=>'Sin lectura','offline'=>'Sin conexión','stale'=>'Dato antiguo');
 return array_merge($definition,array('key'=>$key,'value'=>$value,'state'=>$state,'range_state'=>$range,'label'=>$labels[$state],'time'=>$health['time'],'checked_at'=>isset($data['checked_at'])?(int)$data['checked_at']:null,'monitored'=>$definition['min']!==null));
}
function pool_dashboard($sources,$run=array(),$now=null){
 $now=$now===null?time():$now;$metrics=array();$counts=array('ok'=>0,'outside'=>0,'unknown'=>0);
 foreach(pool_metric_definitions() as $key=>$definition){
  $data=isset($sources[$definition['source']])?$sources[$definition['source']]:array();$m=pool_metric_evaluate($key,$definition,$data,$now);$metrics[$key]=$m;
  if($m['monitored']){if($m['state']==='ok')$counts['ok']++;elseif(in_array($m['state'],array('low','high'),true))$counts['outside']++;else $counts['unknown']++;}
 }
 $state=$counts['outside']?'attention':($counts['unknown']?'unknown':'ok');
 $title=$state==='ok'?'Los 5 parámetros están en rango':($state==='attention'?'Hay valores que revisar':'Faltan lecturas actuales');
 $health=array();foreach(array('water','air','vista') as $source)$health[$source]=pool_source_health($source,isset($sources[$source])?$sources[$source]:array(),$now);
 $v=isset($sources['vista'])?$sources['vista']:array();$filtration=isset($v['values']['filtration'])?$v['values']['filtration']:'Sin lectura';
 return array('server_time'=>$now,'interval_seconds'=>600,'state'=>$state,'title'=>$title,'counts'=>$counts,'metrics'=>$metrics,'sources'=>$health,'filtration'=>array('value'=>$filtration,'state'=>$health['vista']['state'],'time'=>$health['vista']['time']),'monitor'=>array('last_run'=>isset($run['finished_at'])?(int)$run['finished_at']:null,'last_automatic'=>isset($run['last_automatic'])?(int)$run['last_automatic']:null,'running'=>!empty($run['running'])&&!empty($run['started_at'])&&$now-(int)$run['started_at']<180,'active'=>!empty($run['last_automatic'])&&$now-(int)$run['last_automatic']<=900));
}
function pool_status_format($value){return $value===null?'—':rtrim(rtrim(number_format($value,2,',',''),'0'),',');}
function pool_gauge_point($fraction,$radius=92){$angle=M_PI+max(0,min(1,$fraction))*M_PI;return array(120+$radius*cos($angle),118+$radius*sin($angle));}
function pool_gauge_arc($from,$to){$a=pool_gauge_point($from);$b=pool_gauge_point($to);return sprintf('M %.3F %.3F A 92 92 0 0 1 %.3F %.3F',$a[0],$a[1],$b[0],$b[1]);}
function pool_gauge_svg($m){
 $span=$m['scale_max']-$m['scale_min'];$fraction=$m['value']===null?0.5:max(0,min(1,($m['value']-$m['scale_min'])/$span));
 $svg='<svg class="pool-gauge" viewBox="0 0 240 156" role="img" aria-label="'.htmlspecialchars($m['title'].': '.pool_status_format($m['value']).' '.$m['unit'].' · '.$m['label'],ENT_QUOTES,'UTF-8').'">';
 if($m['min']===null)$segments=array(array(0,1,'info'));else{$lo=($m['min']-$m['scale_min'])/$span;$hi=($m['max']-$m['scale_min'])/$span;$segments=array(array(0,$lo,'low'),array($lo,$hi,'target'),array($hi,1,'high'));}
 foreach($segments as $s)$svg.='<path class="gauge-band gauge-band-'.$s[2].'" d="'.pool_gauge_arc($s[0],$s[1]).'"/>';
 for($i=0;$i<=10;$i++){$a=pool_gauge_point($i/10,76);$b=pool_gauge_point($i/10,81);$svg.=sprintf('<line class="gauge-tick" x1="%.3F" y1="%.3F" x2="%.3F" y2="%.3F"/>',$a[0],$a[1],$b[0],$b[1]);}
 $svg.='<g class="gauge-needle" transform="rotate('.round(-90+180*$fraction,2).' 120 118)"'.($m['value']===null?' visibility="hidden"':'').'><path d="M 116 118 L 120 51 L 124 118 Z"/><circle cx="120" cy="118" r="7"/></g>';
 $svg.='<text class="gauge-scale" x="28" y="146" text-anchor="middle">'.pool_status_format($m['scale_min']).'</text><text class="gauge-scale" x="212" y="146" text-anchor="middle">'.pool_status_format($m['scale_max']).'</text></svg>';return $svg;
}
