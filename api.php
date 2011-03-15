<?php
  /*
   * API para Fundar
   * @author Mauro Parra <mauropm@gmail.com>
   * @author Edgar Macias <edgar@laboratoriocitrico.com>
   * (c) 2011 Mauro Parra mauropm@gmail.com
   * (c) 2011 Edgar Macias edgar@laboratoriocitrico.com
   */ 

  // La APIKEY se define en config.php
  // La configuracion de la base de datos tales como:
  // host, user, password, dbname esta en config.php.

require_once('config.php');
require_once('utilities.php');
require_once ('Classes/jpgraph/jpgraph.php');
require_once ('Classes/jpgraph/jpgraph_bar.php');  
require_once ('Classes/jpgraph/jpgraph_canvas.php');
require_once ('Classes/jpgraph/jpgraph_table.php');
include_once ('Classes/PHPExcel.php');
include_once ('Classes/PHPExcel/Writer/Excel2007.php');


function do_graph($type,$idd=0){ 
  // Aqui hay una cosa interesante. Necesitamos calcular el 
  // numero de barras que mostraremos por anios. 
  
  // Es decir, hacemos la busqueda y tomamos el numero de 'rows'
  // de salida como el hint de cuantos elementos habra en cada arreglo
  // De la misma forma que en tabs o excel, calculamos el numero de datos
  // que mostraremos, por la cuestion de saber el numero  de 'columns'
  // que manejaremos por anio. 

  // Aqui va el codigo de conexion a la base y el respectivo query
  $mysql = my_connect();
  my_use(DB_NAME,$mysql);
  if($type=="federal"){
    $query= sprintf("SELECT anio, original, ejercido FROM presupuestos group by anio order by anio");
    $cols = 2; 
  } else if ($type=="dependencia"){ 
    $query= sprintf("SELECT anio, original, ejercido from presupuestos WHERE dependencia='%s' order by anio",
		    mysql_real_escape_string($idd)
		    );
    $cols = 2; 
  } else if ($type=="gasto"){
    $query= sprintf("SELECT anio, sum(tv)'tv',sum(Radio) 'Radio', sum(DiariosDF) 'DiariosDF', sum(DiariosEdos) 'DiariosEdos', sum(Revistas) 'Revistas', sum(Complementos) 'Complementos', sum(Internacionales) 'Internacionales', sum(Estudios) 'Estudios', sum(Produccion) 'Produccion' FROM campanas GROUP BY anio");
    $cols = 9; 
  } else { 
    // Aqui va el otro query respectivo, son hasta 4 diferentes queries.
    // Falta lo de Número de  campañas por secretaria por mes
    die("Error do_tabs: no existe el type=".$type." for query"); 
  }

  // Corremos el query, que no puede ser vacio, porque de otra forma
  // hubieramos entrado al "die" y no llegariamos aca.                                                                                                               

  $result = mysql_query($query,$mysql);
  my_check_result($result,$query,$mysql);

  // Aqui sabemos que fue un $type dependencia, gasto o federal. 
  // Lo ideal seria meter eso en un arreglo, como iremos metiendo
  // cada row.                                                                                                                                                        
  // Aqui termina el codigo de conexion a la base de datos y el query
  // Establecemos el row como el numero de resultados del query.                                                                                                       
  $rows =  mysql_num_rows($result);
  for($i=0;$i<$rows;$i++){
    $data= mysql_fetch_array($result, MYSQL_NUM);
    $label[$i]= $data[0];
    unset($data[0]); 
    $data = array_values($data);
    for($j=0;$j<count($data);$j++){
      $dataar[$j][$i] = $data[$j];
    }
  }

  //print_r($dataar);a
  //print_r($label);
  // _DEBUG
  //print_r($dataar);

  // Crear la grafica. Se requieren estas dos llamadas forzosamente
  $graph = new Graph(700,400,'auto');
  $graph->SetScale("textlin");
  
  // _DEBUG
  //print("new graph\n");
  
  // En el futuro podemo poner otro theme
  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);

  // _DEBUG
  //  print("theme\n");

  // Posiciones textuales en el eje Y
  //  $graph->yaxis->SetTickPositions(array(0,500000,1000000,1500000,2000000,2500000,3000000,3500000,4000000), array(0,500000,1000000,1500000,2000000,2500000,3000000,3500000,4000000));
  //  $graph->SetBox(false); //Esta en cajita o no

  // _DEBUG
  //  print("TickPositions\n");
  //  print_r($label);

  // Como llenamos y, las etiquetas en X (modificadas por el query)
  $graph->ygrid->SetFill(false);
  $graph->xaxis->SetTickLabels($label);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);
  
  // _DEBUG
  //  print("new labels\n");

  // Crear las barras. Note que enviamos arreglos con los datos ya "digeridos"
  // en este caso, mandariamos dos, por ejemplo el de gasto ejercido contra
  // gasto presupuestado
  for($i=0;$i<$cols;$i++){
    $group[$i]=new BarPlot($dataar[$i]);
  }
  
  //print_r($dataar); 
  
  // _DEBUG
  //  print("group of bar plot\n");

  // _DEBUG
  //  print_r($group);

  // Agrupamos el graficado de barras
  $gbplot = new GroupBarPlot($group);
  
  // Anexamos la grafica a la grafica en si
  $graph->Add($gbplot);
  
  //  for($i=0;$i<$cols;$i++){
  //  $group[$i]->SetColor("blue");
  //  $group[$i]->SetFillColor("#cc1111");
  //}
  
  // Cambiar el titulo
  // Titulo de la grafica. Puede ser vacio. 
  if($type=='federal'){
    $graph->title->Set("Gasto Federal");
  } else if($type=='dependencia'){
    $graph->title->Set("Gasto Dependencia");
  } else { 
    $graph->title->Set("Gasto en medios");
  }

 

  // La magia de graficar finalmente
  $graph->Stroke();

  // End of example
}

/* 
 * Generador de informacion tabular 
 */
function do_tabs($type,$idd=0){
  // Aqui va el codigo de conexion a la base y el respectivo query
  $mysql = my_connect();
  my_use(DB_NAME,$mysql);
  if($type=="federal"){
    $query= sprintf("SELECT anio, original, ejercido FROM presupuestos group by anio order by anio");
    $cols = 3; 
 } else if ($type=="dependencia"){ 
    $query= sprintf("SELECT anio, original, ejercido from presupuestos WHERE dependencia='%s' order by anio",
		    mysql_real_escape_string($idd)
		    );
    $cols = 3; 
  } else if ($type=="gasto"){
    $query= sprintf("SELECT anio, sum(tv)'tv',sum(Radio) 'Radio', sum(DiariosDF) 'DiariosDF', sum(DiariosEdos) 'DiariosEdos', sum(Revistas) 'Revistas', sum(Complementos) 'Complementos', sum(Internacionales) 'Internacionales', sum(Estudios) 'Estudios', sum(Produccion) 'Produccion' FROM campanas GROUP BY anio");
    $cols = 10; 
  } else { 
    // Aqui va el otro query respectivo, son hasta 4 diferentes queries.
    // Falta lo de Número de  campañas por secretaria por mes
    die("Error do_tabs: no existe el type=".$type." for query"); 
  }
  
  // Corremos el query, que no puede ser vacio, porque de otra forma 
  // hubieramos entrado al "die" y no llegariamos aca. 
  
  $result = mysql_query($query,$mysql);
  my_check_result($result,$query,$mysql);
  
  // Aqui sabemos que fue un $type dependencia, gasto o federal. 
  // Lo ideal seria meter eso en un arreglo, como iremos metiendo 
  // cada row. 
      
  // Aqui termina el codigo de conexion a la base de datos y el query
  
  // Establecemos el row como el numero de resultados del query.
  $rows =  mysql_num_rows($result);
  
  // Aqui rellenaremos el arreglo "data" de acuerdo a si es 
  // federal, dependencia o federal. 
  if($type=="federal"){
    $data = array(array('Año','Presupuesto','Ejercido'));
    while($data[] = mysql_fetch_array($result, MYSQL_NUM)){
    }
  } else if ($type=="dependencia") { 
    $data = array(array('Año','Presupuesto','Ejercido'));
    while($data[] = mysql_fetch_array($result, MYSQL_NUM)){
    }
  } else if ($type=="gasto") { 
    $data = array(array('Año','TV','Radio','Diarios DF','Diarios Edos', 'Revistas','Complementos','Internacionales','Estudios','Produccion'));
    while($data[] = mysql_fetch_array($result, MYSQL_NUM)){
    }
  } else { 
    die("Error do_tabs: no existe el type=".$type." for filling the array"); 
  } 
  
  // reindizando en caso necesario
  $data= array_values($data);

  // Creamos el contexto de una grafica
  if ($type="gasto")
    $graph = new CanvasGraph(768,200);
  else 
    $graph = new CanvasGraph(300,200);

  // Creamos una tabla basica
  // Anexamos una porque regresamos unicamente el numero de renglones
  // de resultado del query, pero falta poner el header. 
  $table = new GTextTable($cols,$rows);
  $table->Set($data);
  
  // Anexamos la tabla a la grafica 
  $graph->Add($table);

  // Dibujamos la grafica
  $graph->Stroke();
}


/* Creamos el excel aca, 
 * necesitamos que exista un nombre para generar el archivo 
 */ 
function do_excel($type,$idd=0){
  // Aqui va la inicializacion del objeto PHPEXcel
  //Creamos el objeto de Excel 
  $objPHPExcel = new PHPExcel();
  
  //Propiedades del autor 
  $objPHPExcel->getProperties()->setCreator("Fundar, Centro de Analisis e Investigacion");
  $objPHPExcel->getProperties()->setLastModifiedBy("Fundar, Centro de Analisis e Investigacion");
  $objPHPExcel->getProperties()->setTitle("Publicidad oficial del gobierno federal, Mexico");
  $objPHPExcel->getProperties()->setSubject("Publicidad Oficial");
  $objPHPExcel->getProperties()->setDescription("Datos de gasto en Publicidad oficial del gobierno federal mexicano");
  // Termina inicializacion objeto PHPExcel

  // Aqui va el codigo de conexion a la base y el respectivo query
  $mysql = my_connect();
  my_use(DB_NAME,$mysql);
  if($type=="federal"){
    $query= sprintf("SELECT anio, original, ejercido FROM presupuestos group by anio order by anio");
    $cols = 3; 
 } else if ($type=="dependencia"){ 
    $query= sprintf("SELECT anio, original, ejercido from presupuestos WHERE dependencia='%s' order by anio",
		    mysql_real_escape_string($idd)
		    );
    $cols = 3; 
  } else if ($type=="gasto"){
    $query= sprintf("SELECT anio, sum(tv)'tv',sum(Radio) 'Radio', sum(DiariosDF) 'DiariosDF', sum(DiariosEdos) 'DiariosEdos', sum(Revistas) 'Revistas', sum(Complementos) 'Complementos', sum(Internacionales) 'Internacionales', sum(Estudios) 'Estudios', sum(Produccion) 'Produccion' FROM campanas GROUP BY anio");
    $cols = 10; 
  } else { 
    // Aqui va el otro query respectivo, son hasta 4 diferentes queries.
    // Falta lo de Número de  campañas por secretaria por mes
    die("Error do_excel: no existe el type=".$type." for query"); 
  }
  
  // Corremos el query, que no puede ser vacio, porque de otra forma 
  // hubieramos entrado al "die" y no llegariamos aca. 
  
  $result = mysql_query($query,$mysql);
  my_check_result($result,$query,$mysql);

  // Establecemos el row como el numero de resultados del query.
  $rows =  mysql_num_rows($result);
  
  if($type=="federal"){
    // Escribir el header del excel
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Año');
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Original');
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Ejercido');
    $starting_pos = ord('A');
    $index_pos = 0;
    $mprow=1; 
    while($data = mysql_fetch_array($result, MYSQL_NUM)){
      $index_pos = 0;
      $mprow++;
      foreach ($data as $mpval){
	$objPHPExcel->getActiveSheet()->SetCellValue(chr($starting_pos+$index_pos).$mprow, $mpval);
	$index_pos++;
      }
    }
  } else if($type=="dependencia") { 
    // Escribir el header del excel
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Año');
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Original');
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Ejercido');
    $starting_pos = ord('A');
    $index_pos = 0;
    $mprow=1; 
    while($data = mysql_fetch_array($result, MYSQL_NUM)){
      $index_pos = 0;
      $mprow++;
      foreach ($data as $mpval){
	$objPHPExcel->getActiveSheet()->SetCellValue(chr($starting_pos+$index_pos).$mprow, $mpval);
	$index_pos++;
      }
    }
  } else if ($type=="gasto"){ 
    // Escribir el header del excel
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Año');
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'TV');
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Radio');
    $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Diarios DF');
    $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Diarios Edos');
    $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Revistas');
    $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Complementos');    
    $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Internacionales');
    $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Estudios');
    $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Produccion');
        $starting_pos = ord('A');
    $index_pos = 0;
    $mprow=1; 
    while($data = mysql_fetch_array($result, MYSQL_NUM)){
      $index_pos = 0;
      $mprow++;
      foreach ($data as $mpval){
	$objPHPExcel->getActiveSheet()->SetCellValue(chr($starting_pos+$index_pos).$mprow, $mpval);
	$index_pos++;
      }
    }
  } else { 
    die("Error do_excel: no existe el type=".$type." for query"); 
  }

  //Renombramos la hoja -- se puede cambiar
  $objPHPExcel->getActiveSheet()->setTitle('Publicidad Oficial');

  $name = $type.$idd; 
  
  //Salvamos el excel: 
  $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
  $objWriter->save(getcwd().'/'.$name.'.xlsx');
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

function api ($api, $method, $type,$val=0) { 
  if($api!=API_KEY){
    printerr("API KEY Invalida, intente de nuevo");
  } else { 
    switch($method){
    case "graph":
      do_graph($type,$val);
      break;
    case "excel":
      do_excel($type,$val);
      break; 
    case "tabs":
      do_tabs($type,$val);
      break;
    default:
      do_tabs($type,$val);
    }
  }
}
?>