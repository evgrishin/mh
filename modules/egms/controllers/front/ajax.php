<?php


class egmsajaxModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
	
		parent::initContent();

		$action = Tools::getValue('action');
		

			if ($action == "oneworder")
			{
				$this->oorder();
				$view = 'thanks';
			}
			if ($action == "specialmodal")
			{
				$view = 'specialmodal';

			}	
			if ($action == "apecialadd")
			{
				$this->apecialAdd();
				$view = 'specialsand';
			}		

		
		
		$this->context->smarty->assign(array(
					'host'	=> Tools::getHttpHost(),
					'ajaxcontroller' => $this->context->link->getModuleLink('egms', 'ajax'),
					'view' =>	$view
				)); 

		$this->smartyOutputContent($this->getTemplatePath('ajax.tpl'));
		
	}
	
	public function apecialAdd()
	{
		$ocontact = Tools::getValue('ocontact', '-');
				
		if(egmultishop::isMarketingSite())
		
		// посылаем смс мпаибо за заказ

			$this->newMessage("Special", "", "", $ocontact, "smscallme_special",$ocontact.";e.v.grishin@yandex.ru;info@matras-house.ru");

	}
	
	public function oorder()
	{
		$phone = Tools::getValue('ophone', '-');
		$name = Tools::getValue('oname', '-');
		$message = Tools::getValue('oprod', 'NaN');
		
		$this->newMessage("FastOrder", $phone, $name, $message, "smscallme_order");
		// если города основные
		

		// посылаем смс мпаибо за заказ

	    $sms_message = Configuration::get('EGCALLME_MESS_02');
		$sms_message = Meta::sprintf2($sms_message, array(
				'host' => $host,
				'type' => $type,
				'phone' => $phone,
				'cname' => $cname,
				'message' => $message
				));		
		
		$this->sendSMS($sms_message,$phone, 2);

		
	}
	
	public function callBack()
	{
		$phone = Tools::getValue('phone', '-');
		$message = Tools::getValue('message', '-');
				
		$this->newMessage("CallBack", $phone, "", $message, "smscallme_notify");
		
		// если города основные
		
		//if(egmultishop::isMarketingSite())
		//{
		// посылаем смс мпаибо за заказ
		$host = Tools::getHttpHost();
		
	    $sms_message = Configuration::get('EGCALLME_MESS_01');//"Обращение принято! Ожидайте звонка. Узнайте о предложениях %host/alcii";
		$sms_message = Meta::sprintf2($sms_message, array(
				'host' => $host,
				'type' => $type,
				'phone' => $phone,
				'cname' => $cname,
				'message' => $message
				));		
		
		$this->sendSMS($sms_message,$phone,2);
		
		//}
		
	}

	private function newMessage($type, $phone, $cname, $message,$email_theme, $email_param = "")
	{

		$host = Tools::getHttpHost();
		$phone = "+".preg_replace('#\D+#', '', $phone);
		// insert to DB
		$query = "insert into "._DB_PREFIX_.egmarketing::INSTALL_SQL_BD1NAME." 
		(`id_shop`, `host`, `type`, `phone`, `fname`, `lname`, `message`) 
		values ('".(int)$context->shop->id."', '".Tools::getHttpHost()."', '".$type."', '".$phone."',
			'".$cname."', '".$cname."', '".$message."')";
		Db::getInstance()->execute(trim($query));
		
		// notify by email
		if ($email_param == "")
		{
			$email_param = Configuration::get('EGCALLME_EMAIL_NOTIFY');
		}
		if (trim($email_param)!="")
		{
			$context = Context::getContext();
			
			$param = array(
				'{phone}'	=> $phone,
			 	'{message}'	=> $message,
				'{fname}'	=> $cname,
				'{shop_url}'	=> $host
			);
			
			$emails = explode(';', $email_param);
			
			foreach ($emails as $email)
			{
				Mail::Send(
					(int)$context->language->id,
					$email_theme,
					Mail::l($type, " ".$phone." ".$host),
					$param,
					$email,
					$cname,
					null,
					null,
					null,
					null,
					_PS_MAIL_DIR_,
					false,
					(int)$context->shop->id
				);
			}
		}

		// sms notification
		
		
		$sms_message = "%type %host %phone %cname %message";
		$sms_message = Meta::sprintf2($sms_message, array(
				'host' => $host,
				'type' => $type,
				'phone' => $phone,
				'cname' => $cname,
				'message' => $message
				));		
		
		//$this->sendSMS($sms_message,"79601652555",2);
		$param = array(
                '{phone}'    => str_replace('+','',$phone),
                '{message}'    => $message,
                '{fname}' => $cname,
            	'{type}'	=> 'FastOrder',
            	'{host}' => $host,
            	'{shost}' => str_replace('.','-',$host)
            );
		$this->sendTelegramm($param, $sms_message);
	}
	
    private function replaceKeywords($params, $request, $text)
    {
      	foreach ($params as $key => $value) {
    		$text = str_replace($key,$value,$text);
    	}
    	
    	foreach ($params as $key => $value) {
    		$request = str_replace($key,$value,$request);
    	}
    	$request = str_replace('{text}',urlencode($text),$request);
    	return $request;
    }	
	
	private function sendTelegramm($param, $sms_message)
	{
		//$request = Configuration::get('EGCALLME_HTTPNOT_3');
		//$request1 = Configuration::get('EGCALLME_HTTPNOT_4');
		//$text = Configuration::get('EGCALLME_HTTPNOT_3_TXT');
		//$text1 = Configuration::get('EGCALLME_HTTPNOT_4_TXT');
		//$request = $this->replaceKeywords($param, $request, $text);
		//$request1 = $this->replaceKeywords($param, $request1, $text1);
       // $result = file_get_contents($request);
       // $result1 = file_get_contents($request1);
	    $text = Configuration::get('EGMS_VIBER_TEXT');
	    $text = $this->replaceKeywords($param, $text, '');
		$text = Meta::replaceForCEOWord($text);
	    		
		Tools::viberSend('message', $text);
	}
	
	private function sendSMS($message, $phone, $max_sms = 1)
	{
		$sms = Configuration::get('EGCALLME_SMS_REQUEST');
		$phone = preg_replace('#\D+#', '', $phone);
		$sms = Meta::sprintf2($sms, array(
					'sendto' => $phone,
					'message' => substr($message, 0, 70*$max_sms)
				));
					
		if(trim($sms)!="" && (bool)Configuration::get('EGCALLME_SMS_NOYIFY') )
		{		
			if (!substr_count($_SERVER['DOCUMENT_ROOT'], "home/matras-house.ru/www"))
			{			
				$result = file_get_contents($sms);
			}
		}
	}
}


?>