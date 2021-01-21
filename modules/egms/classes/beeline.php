<?php

class beeline
{
    private $url_get_acc = "https://chatapi.viber.com/pa/broadcast_message";
    private $token;
    private $xml;

    public function __construct()
    {
        $this->token = Configuration::get('EGMS_BEELINE_TOKEN');
    }

    public function message_post
    (
        $from,          // Public Account.
        array $sender,  // sender data.
        $text           // text.
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
            CURLOPT_URL => $this->url_get_acc,
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

?>
