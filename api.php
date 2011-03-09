<?php
  /*
   * API para Fundar
   * @author Mauro Parra <mauropm@gmail.com>
   * (c) 2011 Mauro Parra mauropm@gmail.com
   * (c) 2011 Edgar Macias edgar@laboratoriocitrico.com
   */ 

require_once('config.php');
require_once('utilities.php');
require_once ('Classes/jpgraph/jpgraph.php');
require_once ('Classes/jpgraph/jpgraph_bar.php');  
require_once ('Classes/jpgraph/jpgraph_canvas.php');
require_once ('Classes/jpgraph/jpgraph_table.php');

function do_graph(){ 
  // This is an example as Huevom Asked
  // Arreglos con los datos; se pueden rellenar con las salidas
  // de los queries. Habra que hacer diversas de acuerdo a 
  // Cada tipo de grafica que deseemos
  $data1y=array(47,80,40,116);
  $data2y=array(61,30,82,105);
  $data3y=array(115,50,70,93);
  
  // Crear la grafica. Se requieren estas dos llamadas forzosamente
  $graph = new Graph(350,200,'auto');
  $graph->SetScale("textlin");
  
  // En el futuro podemo poner otro theme
  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);

  // Posiciones textuales en el eje Y
  $graph->yaxis->SetTickPositions(array(0,30,60,90,120,150), array(15,45,75,105,135));
  $graph->SetBox(false); //Esta en cajita o no

  // Como llenamos y, las etiquetas en X (modificadas por el query)
  $graph->ygrid->SetFill(false);
  $graph->xaxis->SetTickLabels(array('2008','2009','2010','2011'));
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);
  
  // Crear las barras. Note que enviamos arreglos con los datos ya "digeridos"
  // en este caso, mandariamos dos, por ejemplo el de gasto ejercido contra
  // gasto presupuestado
  $b1plot = new BarPlot($data1y);
  $b2plot = new BarPlot($data2y);
  $b3plot = new BarPlot($data3y);

  // Agrupamos el graficado de barras
  $gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot));
  
  // Anexamos la grafica a la grafica en si
  $graph->Add($gbplot);

  // Colores por barra

  $b1plot->SetColor("white");
  $b1plot->SetFillColor("#cc1111");
  
  $b2plot->SetColor("white");
  $b2plot->SetFillColor("#11cccc");
  
  $b3plot->SetColor("white");
  $b3plot->SetFillColor("#1111cc");
  
  // Titulo de la grafica. Puede ser vacio. 
  $graph->title->Set("Secretaria de Economia");
 

  // La magia de graficar finalmente
  $graph->Stroke();

  // End of example
}

/* 
 * Generador de informacion tabular 
 */
function do_tabs(){
  // Necesitamos decir cuantas columnas y filas tendra esto
  $cols = 4;
  $rows = 3;

  // Ponemos la respuesta del query sql en un arreglo de arreglos:
  $data = array( array('','Jan','Feb','Mar','Apr'),
	       array('Min','15.2', '12.5', '9.9', '70.0'),
	       array('Max','23.9', '14.2', '18.6', '71.3'));
  
  // Creamos el contexto de una grafica
  $graph = new CanvasGraph(300,200);

  // Creamos una tabla basica
  $table = new GTextTable($cols,$rows);
  $table->Set($data);
  
  // Anexamos la tabla a la grafica 
  $graph->Add($table);

  // Dibujamos la grafica
  $graph->Stroke();
}

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