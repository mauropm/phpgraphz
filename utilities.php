<?php

  /*
   * Utilities
   * (c) 2010 Mauro Parra <mauropm@gmail.com>
   */

  /* 
   * Database Configuration
   */ 

require_once('config.php');

/*
 * Db utilities
 */ 

function my_connect($host=DB_HOST,$user=DB_USER,$pass=DB_PASS){
  $mysql = mysql_connect($host, $user, $pass);
  if (!$mysql) {
    printerr("No se puede conectar al servidor mysql",ERR);
  } else {
    return $mysql; 
  }
}

function my_use($dbname=DB_NAME,$link){
  $output = mysql_select_db($dbname,$link); 
  if(!$output){
    printerr("No se puede seleccionar la base de datos. Error #: ". mysql_errno($link) ." con mensaje ". mysql_error($link),
	     ERR);
  }
}

function my_check_result($result, $query,$link){
  if ( !$result ){
    $message ="Query invalida: ". mysql_error() ."\n";
    $message .="La query original es: ". $query;
    printerr($message,ERR);
  }
}

function check_user($username,$mysql){
  $query = sprintf("SELECT username FROM admins WHERE username='%s'",
		   mysql_real_escape_string($username));
  $result = mysql_query($query,$mysql);
  my_check_result($result,$query,$mysql);
  return mysql_num_rows($result); //regresa cero si el usuario no existe. 
}


function check_password($username,$password,$mysql){
  $query = sprintf("SELECT username,password FROM admins WHERE username='%s' AND password='%s'",
		   mysql_real_escape_string($username),
		   mysql_real_escape_string($password));
  $result = mysql_query($query,$mysql);
  my_check_result($result,$query,$mysql);
  return mysql_num_rows($result); //regresa cero si el password es incorrecto
}



/* 
 * Formating utilities
 */ 

function println($text){
  print $text . "\n";
}

function printerr($text,$level){
  if($level==ERR){
    die($text);
  } else {
  println($text);
  }
}

/* 
 * form utilities.
 */ 

/* print a text box */
function input_text($element_name, $values) {
  print '<input type="text" name="' . $element_name .'" value="';
  print htmlentities($values[$element_name]) . '">';
}

/* print a password box */ 
function input_password($element_name, $values){ 
  print '<input type="password" name="' . $element_name .'" value="';
  print htmlentities($values[$element_name]) . '">';
   
}

//print a submit button
function input_submit($element_name, $label) {
  print '<input type="submit" name="' . $element_name .'" value="';
  print htmlentities($label) .'"/>';
}

//print a textarea
function input_textarea($element_name, $values) {
  print '<textarea name="' . $element_name .'">';
  print htmlentities($values[$element_name]) . '</textarea>';
}

//print a radio button or checkbox
function input_radiocheck($type, $element_name, $values, $element_value) {
  print '<input type="' . $type . '" name="' . $element_name .'" value="' . $element_value . '" ';
  if ($element_value == $values[$element_name]) {
        print ' checked="checked"';
  }
    print '/>';
}

//print a <select> menu
function input_select($element_name, $selected, $options, $multiple = false) {
    // print out the <select> tag
    print '<select name="' . $element_name;
    // if multiple choices are permitted, add the multiple attribute
    // and add a [] to the end of the tag name
    if ($multiple) { print '[]" multiple="multiple'; }
    print '">';

    // set up the list of things to be selected
    $selected_options = array();
    if ($multiple) {
        foreach ($selected[$element_name] as $val) {
            $selected_options[$val] = true;
        }
    } else {
        $selected_options[ $selected[$element_name] ] = true;
    }

    // print out the <option> tags
    foreach ($options as $option => $label) {
        print '<option value="' . htmlentities($option) . '"';
        if ($selected_options[$option]) {
            print ' selected="selected"';
        }
        print '>' . htmlentities($label) . '</option>';
    }
    print '</select>';
}
?>