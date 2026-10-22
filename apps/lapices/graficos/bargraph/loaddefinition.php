<?php
  include "class.bargraph.php";
  
  $bg = new BarGraph();
  
  $bg->LoadGraph(realpath("./graph.def"));
  
  $bg->DrawGraph();
?>
