<?php

  /* 
   * DB Configuration 
   * (c) 2011 Mauro Parra <mauropm@gmail.com>
   * on behalf of Fundar
   */

  /* 
   * here you will define the parameters of the db. 
   * this has to be a different db than the one in your 
   * wordpress, since we are not a plugin, therefore 
   * we can't touch the wp's db. 
   */ 

define('DB_HOST','localhost');
define('DB_NAME','fundar');
define('DB_USER','fundar');
define('DB_PASS','fundar');
define('API_KEY','fundar314151892'); // This is the apikey you should use in each call. 

?>