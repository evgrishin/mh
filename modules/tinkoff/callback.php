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
    require_once 'TinkoffMerchantAPI.php';
    $tinkoff = new tinkoff();

    $_POST['Password'] = $tinkoff->ti_secret_key;
    ksort($_POST);
    $sorted = $_POST;
    $original_token = $sorted['Token'];
    unset($sorted['Token']);
    $values = implode('', array_values($sorted));
    $token = hash('sha256', $values);

    if($token == $original_token){
        $order_id = Order::getOrderByCartId((int)$_POST['OrderId']);
        $order = new Order($order_id);
        $status = $order->current_state;

        if($_POST['Status'] == 'AUTHORIZED' && $status == Configuration::get('PS_OS_PAYMENT')){
            die('OK');
        }
        switch ($_POST['Status']) {
            case 'AUTHORIZED': $order_status = Configuration::get('PS_OS_BANKWIRE'); break;
            case 'CONFIRMED': $order_status = Configuration::get('PS_OS_PAYMENT'); break;
            case 'CANCELED': $order_status = Configuration::get('PS_OS_CANCELED'); break;
            case 'REJECTED': $order_status = Configuration::get('PS_OS_CANCELED'); break;
            case 'REVERSED': $order_status = Configuration::get('PS_OS_CANCELED'); break;
            case 'REFUNDED': $order_status = Configuration::get('PS_OS_REFUND'); break;
        }
        $order->setCurrentState($order_status);
        die('OK');
    }
    else{
        die('NOTOK');
    }
}
catch(Exception $e){
    die('NOTOK');
}