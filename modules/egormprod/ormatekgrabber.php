<?php

class ormatekgrabber
{
	public function __construct()
    {
    
    }
    
    public function getProductList()
    {
    	
    }
    
    public function getProductAtributs($id)
    {
    	
    }
    public function getProductPrice($url = "", $formfactor)
    {
		$url = 'http://ormatek.com/products/980/price';
		$data = array('formfactor_id' => '8'); 
		
		$ch = curl_init( $url );
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
		curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // переходит по редиректам
		curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		  
		curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		  
		$content = curl_exec( $ch );
		  
		$content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
		  
		curl_close( $ch );
		  
		echo $content;    	
    }
    
    public function getProductContent($url = "", $formfactor)
    {
    	
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $url
     */
    public static function getProductComments($url)
    {
		header('Content-type: text/html; charset=utf-8');
		
		$url = "http://ormatek.com/products/980";
		$doc = file_get_contents($url);
	
		$doc = mb_convert_encoding($doc, 'HTML-ENTITIES', "UTF-8");
	
		$query = ".//*[@class='comment']";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($doc);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);

	    $i = 0;
	    
	    if (!is_array($nodes))
	    	return null;
	    foreach( $nodes as $node ) 
	    {

	    	$name = $node->getElementsByTagName("b")->item(0)->nodeValue;
			$text = $node->getElementsByTagName("p")->item(0)->nodeValue;
			$date = $node->getElementsByTagName("span")->item(0)->nodeValue;
			$rating = 0;
			$i++;
			
			$param[] = array(
				'id'	=> $i,
			 	'name'	=> $name,
				'date'	=> $date,
				'rating'	=> $rating,
				'text'	=> $text
			);			
	    }

	    return $param;
    }
    
    public function writeComment($id_product, $comment)
    {
    	
    }
}