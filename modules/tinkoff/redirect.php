<?php
set_error_handler('exceptions_error_handler', E_ALL);
function exceptions_error_handler($severity) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        die('NOTOK');
    }
}
try{
    include(dirname(__FILE__).'/../../config/config.inc.php');
    include(dirname(__FILE__).'/tinkoff.php');
    if(isset($_COOKIE['tinkoff_order'])){
        Tools::redirect($_COOKIE['tinkoff_order']);
    }
}
catch(Exception $e){
    die('NOTOK');
}