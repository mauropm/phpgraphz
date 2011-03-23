<?php

require_once('api.php');

switch($_GET['method']){
 case "graph":
   api($_GET['api'], $_GET['method'], $_GET['type'], $_GET['val']);
   break;
 case "excel":
   api($_GET['api'], $_GET['method'], $_GET['type'], $_GET['val']);
   break;
 case "tabs":
   api($_GET['api'], $_GET['method'], $_GET['type'], $_GET['val']);
   break;
 default:
   print("No value provided");
 }


?>