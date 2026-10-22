<?php
if(isset($_SERVER['REQUEST_METHOD'])&&$_SERVER['REQUEST_METHOD']==='POST'){require dirname(__DIR__).'/app/salir.php';exit;}
header('Location: /app/login.php');exit;
