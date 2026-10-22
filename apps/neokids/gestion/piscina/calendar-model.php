<?php
function pool_calendar_period($time){return $time<'14:00:00'?'manana':'tarde';}
function pool_calendar_minutes($time){return (int)substr($time,0,2)*60+(int)substr($time,3,2);}
// Arrange overlapping time slots in separate lanes, keeping simultaneous subgroups together.
function pool_calendar_layout($slots){
 usort($slots,function($a,$b){return $a['start']===$b['start']?$a['end']-$b['end']:$a['start']-$b['start'];});
 $out=array();$cluster=array();$ends=array();$until=0;
 foreach($slots as $slot){
  if($cluster&&$slot['start']>=$until){foreach($cluster as $s){$s['lanes']=count($ends);$out[]=$s;}$cluster=array();$ends=array();$until=0;}
  $lane=0;while(isset($ends[$lane])&&$ends[$lane]>$slot['start'])$lane++;
  $ends[$lane]=$slot['end'];$slot['lane']=$lane;$cluster[]=$slot;$until=max($until,$slot['end']);
 }
 foreach($cluster as $s){$s['lanes']=count($ends);$out[]=$s;}return $out;
}
