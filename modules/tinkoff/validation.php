<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/tinkoff.php');
require_once 'TinkoffMerchantAPI.php';

$arrRequest = $_POST;

$tinkoff = new tinkoff();
$secure_cart = explode('_', $arrRequest['OrderId']);
$cart = new Cart((int)($secure_cart[0]));
$customer = new Customer((int)$cart->id_customer);

$arrFields = array(
    'OrderId'			=> $arrRequest['OrderId'],
    'Amount'			=> $arrRequest['Amount'] * 100,
    'Description'		=> $arrRequest['Description'],
    'DATA'              => $arrRequest['DATA'],
);

$Tinkoff = new TinkoffMerchantAPI( $tinkoff->ti_merchant_id, $tinkoff->ti_secret_key, $tinkoff->ti_gateway );
$request = $Tinkoff->buildQuery('Init', $arrFields);
$request = json_decode($request);

if(isset($request->PaymentURL)){
    $tinkoff->validateOrder((int)($secure_cart[0]), Configuration::get('PS_OS_BANKWIRE'), (float)($arrRequest['Amount']), $tinkoff->displayName, 'Wait for pay', array(), NULL, false, $customer->secure_key);
    $redirect = $request->PaymentURL;
}
else{
    $tinkoff->validateOrder((int)($secure_cart[0]), Configuration::get('PS_OS_ERROR'), 0, $tinkoff->displayName, 'Wrong signature', array(), NULL, false, $customer->secure_key);
    $language = new Language($cart->id_lang);
}

//$tinkoff = Module::getInstanceByName('tinkoff');

//$cart = new Cart( (int)$_POST['OrderId']);
//$customer = new Customer((int)$cart->id_customer);

$order_id = Order::getOrderByCartId($arrRequest['OrderId']);
$order = new Order($order_id);

//$tinkoff_order = 'index.php?controller=order-confirmation&id_cart='.$arrRequest['OrderId'].'&id_module='.$tinkoff->id.'&id_order='.$order_id.'&key='.$customer->secure_key;

$tinkoff_order = 'guest-tracking?id_order='.$order->reference.'&email='.$customer->email;

setcookie('tinkoff_order', $tinkoff_order);
if(!isset($redirect)){
    $redirect = $tinkoff_order;
}

Tools::redirect($redirect);