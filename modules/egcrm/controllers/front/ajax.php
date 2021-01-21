<?php


class EgcallmeajaxModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
    
        parent::initContent();

        $context = Context::getContext();
        
        $action = Tools::getValue('action');
        
        $view = '';
        if (!$action) {
            $view = 'form';
        } else {
            $phone = Tools::getValue('eg_phone');
            $fname = Tools::getValue('eg_fname');
            $lname = Tools::getValue('eg_lname');
            $message = Tools::getValue('eg_message');
            if ($phone != "")
            	$this->newMessage($phone, $fname, $lname, $message, $context);
        }
        
        
        $this->context->smarty->assign(array(
                    'ajaxcontroller' => $this->context->link->getModuleLink('egcallme', 'ajax'),
                    'mask' => Configuration::get('EGCALLME_PHONE_MASK'),
                    'fname' => Configuration::get('EGCALLME_FIELD_FNAME'),
                    'lname' => Configuration::get('EGCALLME_FIELD_LNAME'),
                    'mess' => Configuration::get('EGCALLME_FIELD_MESS'),
                    'view' => $view
                ));

        $this->smartyOutputContent($this->getTemplatePath('ajax.tpl'));
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
    	
    private function newMessage($phone, $fname, $lname, $message, $context)
    {
    	$host = Tools::getHttpHost();
        // insert to DB
        $query = "insert into "._DB_PREFIX_.egcallme::INSTALL_SQL_BD1NAME." 
        (`id_shop`, `host`, `type`, `phone`, `fname`,`lname`, `message`) 
        values ('".(int)$context->shop->id."', '".$host."', 'callback', '".$phone."',
            '".$fname."','".$lname."', '".$message."')";
        Db::getInstance()->execute(trim($query));
        
        // notify by email
        $emails_param = Configuration::get('EGCALLME_EMAIL_NOTIFY');

        if (trim($emails_param)!="") {
            $param = array(
                '{phone}'    => $phone,
                '{message}'    => $message,
                '{fname}'    => $fname,
                '{lname}'    => $lname,
            	'{type}'	=> 'CallBack',
            	'{host}' => $host,
            	'{shost}' => str_replace('.','-',$host)
            );

            $emails = explode(';', $emails_param);

            $dir = egcallme::getModuleDir().'/mails/';
                
            foreach ($emails as $email) {
                $email_theme = "email_notify";
                Mail::Send(
                    (int)$context->language->id,
                    $email_theme,
                    Mail::l("NEW Callback ".$phone, $this->context->language->id),
                    $param,
                    trim($email),
                    "",
                    null,
                    null,
                    null,
                    null,
                    $dir,
                    false,
                    (int)$context->shop->id
                );
            }
            $param['{phone}'] = preg_replace('#\D+#', '', $param['{phone}']);          

 			$requests = Configuration::getMultiple(array(
									'EGCALLME_HTTPNOT_1',
									'EGCALLME_HTTPNOT_2',
                                    'EGCALLME_VIBER_CALLBACK'/*,
									'EGCALLME_HTTPNOT_3',
 									'EGCALLME_HTTPNOT_4'*/
						));
            $texts = Configuration::getMultiple(array(
									'EGCALLME_HTTPNOT_1_TXT',
									'EGCALLME_HTTPNOT_2_TXT',
                                    'EGCALLME_VIBER_CALLBACK_TXT'
									/*'EGCALLME_HTTPNOT_3_TXT',
            						'EGCALLME_HTTPNOT_4_TXT'*/
						));
			foreach ($requests as $key => $request) {
            	$request = $this->replaceKeywords($param, $request, $texts[$key.'_TXT']);
                if($key == 'EGCALLME_VIBER_CALLBACK')
                {
                    $request = Meta::replaceForCEOWord($request);
                    Tools::viberSend('message', $request);
                }else
                {
                    $result = file_get_contents($request);
                }
			}           
        }
    }
}
