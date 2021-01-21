<?php
include(dirname(__FILE__).'/../../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../../init.php');
include(dirname(__FILE__).'../../../egms.php');

$ats = new egms();//egmsatsModuleFrontController();
$postdata = file_get_contents("php://input");
$ats->getAts($postdata);
die('ok1');

/*
class Viber
{
    private $url_api = "https://chatapi.viber.com/pa/";

    private $url_get_acc = "https://chatapi.viber.com/pa/broadcast_message";//"https://chatapi.viber.com/pa/get_account_info";

    private $token = "47b6553dd967d01f-1ca6a618da76fc92-9455628c1f81a758"; //"47b654010467d2f8-f52c0757a69bcb9a-616bef1095c97908";//
    private $xml;

    public function message_post
    (
        $from,          // ID администратора Public Account.
        array $sender,  // Данные отправителя.
        $text           // Текст.
    )
    {
        $data['from']   = $from;
        $data['sender'] = $sender;
        $data['type']   = 'text';
        $data['text']   = $text;
        return $this->call_api('send_message', $data);
    }

    function send($message){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://chatapi.viber.com/pa/broadcast_message",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => JSON_encode($message),
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/JSON",
                "X-Viber-Auth-Token: ".$this->token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
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


$viber = new Viber();

//print_r($viber->get_account_info());

$message['broadcast_list'] =['zOFmUZmgxwBweJrEXfM0Tw==,1zGzcD0nJY+KC+PFOntX+g==,6YfwiFt35cvJ6nesPnd8Kw==,y9kNyU9roDh6qsyn8iXutg==,FBV4B9ofRkyy80oWoIAECw==,sVNV0cFlNJOCQKxN0emmUw=='];
$message['type'] = "text";
$message['sender'] = ['name' => 'CRM'];

$postdata = file_get_contents("php://input");
echo 'send2';
phpinfo();
$viber->setXML($postdata);

//$filename = microtime().'.xml';
//file_put_contents($filename, $postdata);

$state_x = '//xsi:Event/xsi:eventData/xsi:calls/xsi:call/xsi:state';
$phone_x = '//xsi:Event/xsi:eventData/xsi:calls/xsi:call/xsi:remoteParty/xsi:address';

$state = $viber->getVal($state_x);
$phone_num = $viber->getVal($phone_x);

if($state == 'Detached'){
    $actual_link = 'Входящий звонок: '.str_replace('tel:', '', $phone_num);
}


$message['text'] = $actual_link;

$viber->send($message);

*/

//print_r($t);

?>
