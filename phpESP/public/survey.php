<?php
/* $Id$ */

/* vim: set tabstop=4 shiftwidth=4 expandtab: */

// Matthew Gregg
// <greggmc at musc.edu>

	if (!defined('ESP_BASE'))
		define('ESP_BASE', dirname(dirname(__FILE__)) .'/');

	$CONFIG = ESP_BASE . 'admin/phpESP.ini.php';
	if(!file_exists($CONFIG)) {
		echo("<b>FATAL: Unable to open config file Aborting.</b>");
		exit;
	}
	if(!extension_loaded('mysql')) {
		echo('<b>FATAL: Mysql extension not loaded. Aborting.</b>');
		exit;
	}
	require_once($CONFIG);	
	
	esp_init_db();	
	
	$_name = '';
	$_title = '';
	$_css = '';
	if (isset($HTTP_GET_VARS['name'])) {
		$_name = _addslashes($HTTP_GET_VARS['name']);
		unset($HTTP_GET_VARS['name']);
		$HTTP_SERVER_VARS['QUERY_STRING'] =
			ereg_replace('(^|&)name=[^&]*&?', '', $HTTP_SERVER_VARS['QUERY_STRING']);
	}

	if (!empty($_name)) {
        	$_sql = "SELECT id,title,theme FROM survey WHERE name = '$_name'";
        	if ($_result = mysql_query($_sql)) {
            		if (mysql_num_rows($_result) > 0)
                		list($sid, $_title, $_css) = mysql_fetch_row($_result);
            		mysql_free_result($_result);
        		}
        	unset($_sql);
        	unset($_result);
		}

    // call the handler-prefix once $sid is set to handle
    // authentication / authorization
	include($ESPCONFIG['handler_prefix']);

	if (empty($_name) && isset($sid) && $sid) {
        $_sql = "SELECT title,theme FROM survey WHERE id = '$sid'";
        if ($_result = mysql_query($_sql)) {
            if (mysql_num_rows($_result) > 0){
                list($_title, $_css) = mysql_fetch_row($_result);
            }
            mysql_free_result($_result);
        }
        unset($_sql);
        unset($_result);
	}

?>
<html>
<head><title><?php echo($_title); ?></title>
<?php
    if (!empty($_css)) {
	    echo('<link rel="stylesheet" href="'. $GLOBALS['ESPCONFIG']['css_url'].$_css ."\" type=\"text/css\">\n");
    }
    unset($_css);
?>
</head>
<body>
<?php
	unset($_name);
	unset($_title);
	include($ESPCONFIG['handler']);
?>
</body>
</html>
