<?php
  /*
   * API para Fundar
   * @author Mauro Parra <mauropm@gmail.com>
   * (c) 2011 Mauro Parra mauropm@gmail.com
   * (c) 2011 Edgar Macias edgar@laboratoriocitrico.com
   */ 

require_once('config.php');
require_once('utilities.php');


/* 
 * En este caso, no tenemos "autenticacion" formalmente. 
 * Por seguridad, proporcionaremos un apikey, de 
 * tal forma que autentiquemos que la llamada proviene de 
 * un sitio confiable. 
 */


/* 
 * Habra una sola llamada, con tres opciones:
 * - Grafica
 * - Tabulacion
 * - Excel
 * Las opciones seran: graph, tabs, excel
 */ 

function api ($api, $method, $type) { 
  if($api!=API_KEY){
    printerr("API KEY Invalida, intente de nuevo");
  } else { 
    switch($method){
    case "graph":
      do_graph($type);
      break;
    case "excel":
      do_excel($type);
      break; 
    case "tabs":
      do_tabs($type);
      break;
    default:
      do_tabs($type);
    }
  }
}
?>