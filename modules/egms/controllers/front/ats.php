<?php


class egmsatsModuleFrontController extends ModuleFrontController
{
    private $xml;


    public function initContent()
    {

        parent::initContent();
        $r = "";
        if(Tools::getValue('asic')=='mykey')
        {
            $this->sendRequest(Tools::getValue('phone'));

        }else
        {
            $postdata = file_get_contents("php://input");

        //    file_put_contents(time().'.txt', $postdata);

            if($postdata!='') {
                $this->setXML($postdata);

                //$state_x = '//xsi:Event/xsi:eventData/xsi:calls/xsi:call/xsi:state';
                //$phone_x = '//xsi:Event/xsi:eventData/xsi:calls/xsi:call/xsi:remoteParty/xsi:address';

                $state_x = '//xsi:Event/xsi:eventData/xsi:call/xsi:state';
                $phone_x = '//xsi:Event/xsi:eventData/xsi:call/xsi:remoteParty/xsi:address';

                $state = $this->getVal($state_x);
                $phone_num = $this->getVal($phone_x);
                
//                file_put_contents(time().$phone_num.'.txt', $postdata);
                
                //if ($state == 'Detached') {
                if($state == 'Alerting') {
                    $url = $this->context->link->getModuleLink('egms', 'ats').'?asic=mykey&phone='.$phone_num;
                    $this->curl_post_async($url);
                    $r = "-r";
                }
            }
        }

        die('ok'.$r);
    }

    function curl_post_async($url)
    {
        $ctx = stream_context_create(array('http' => array('timeout' => 0 )));
        @file_get_contents($url, 0, $ctx);

    }

    function sendRequest($phone_num)
    {

    
	$phone_num = trim(str_replace("tel:", "", $phone_num));

//        Tools::viberSend('message', 'hello-'.$phone_num);

        $sql = "SELECT *  FROM "._DB_PREFIX_."orders o 
                INNER JOIN "._DB_PREFIX_."address a 
                    ON o.id_address_delivery = a.id_address
                WHERE a.phone_mobile like'%".$phone_num."%' order by o.id_order desc ";

        $od = Db::getInstance()->executeS($sql);

        if($od)
        {
            $order_id = $od[0]['id_order'];
            $fio = $od[0]['firstname'];
            $city = $od[0]['city'];
            $order_amount = $od[0]['total_paid'];
            $sn = $od[0]['shipping_number'];
            $param = array(
                '{phone}'    => $phone_num,
                '{order}' => $order_id,
                '{shipping_number}' => $sn,
                '{fio}' => $fio,
                '{city}' => $city,
                '{fio}' => $fio,
                '{amount}' => (int)$order_amount,
            );
            $text = Configuration::get('EGMS_VIBER_CLIENT_CALL');
            $text = Tools::replaceKeywords($param, $text, '');
            $text = Meta::replaceForCEOWord($text);

            Tools::viberSend('message', $text);
            die("client call");

        }else
        {
            $param = array('{phone}'    => $phone_num);
            $text = Configuration::get('EGMS_VIBER_NEW_CALL');
            $text = Tools::replaceKeywords($param, $text, '');
            $text = Meta::replaceForCEOWord($text);
            Tools::viberSend('message', $text);

            die("new call");
        }

    }

    function getVal($path){
        $xmldoc = $this->xml;
        foreach ($xmldoc->xpath($path) as $string) {
            return $string ;
        }
    }

    function setXML($postdata){
        $this->xml = new SimpleXMLElement($postdata);
    }

}


?>
