<?php
  $web_path = "/var/www" ;
  
  include "class.bargraph.php";
  
  $g = new BarGraph();
  $data = array();
  for ($i = 0; $i < 7; $i++) {
    $data[chr(ord('a') + $i)] = 1 + $i;
  }
  
  for ($i = 0; $i < 6; $i++) {
    $data[chr(ord('h') + $i)] = 6 - $i;
  }
  $g->SetGraphPadding(20, 30, 20, 15);
  $g->SetBarData($data);
  $g->SetGraphTitle("BarGraph Test");

  $g->DrawGraph($web_path."/graficos/bargraph/img/1.png");
?>
