<?php
/**
 * Enter description here ...
 * 
 */
if (!defined('_PS_VERSION_'))
  exit;
require_once(_PS_MODULE_DIR_.'egms/classes/page.php');
require_once(_PS_MODULE_DIR_.'egms/classes/city.php');    
  class egms extends Module
  {
    const INSTALL_SQL_FILE = 'install.sql';
    const INSTALL_SQL_BD3NAME = 'egms_city';
    const INSTALL_SQL_BD1NAME = 'egms_city_url';
	const INSTALL_SQL_BD4NAME = 'egms_delivery';    
	const INSTALL_SQL_BD5NAME = 'egms_pages';

    protected $tabs = array(
    		//array('name' => 'Multishop config', 'class_name' => 'AdminEGMSShops'),
    		//array('name' => 'Shops by Citys', 'class_name' => 'AdminEGMSShops'),
    		//array('name' => 'Delivery by Manufacturer', 'class_name' => 'AdminEGMSDelivery'),
    		//array('name' => 'Citys', 'class_name' => 'AdminEGMSCitys'),
            array('name' => 'Filter', 'class_name' => 'AdminEGMSFilter'),
    		array('name' => 'Pages', 'class_name' => 'AdminEGMSPages'),
    );  

    protected $city_info;
    protected $product;
    protected $combination;
    public $host;
  	
    public function __construct()
    {
	    $this->name = 'egms';
	    $this->tab = 'front_office_features';
	    $this->version = '0.1.1';
	    $this->author = 'Evgeny Grishin';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
        $this->host = Tools::getHttpHost();
		 
	    parent::__construct();
	 	
	    $this->displayName = $this->l('Super multishop module');
	    $this->description = $this->l('Super multishop module.');
	 
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	     
	    $this->city_info = Shop::getCEOData();
  	}

      public function getContent()
      {
          $output = 'hello';
          //$this->installAdminTab();

        $sql = "SELECT *  FROM "._DB_PREFIX_."product WHERE rprice=0 AND id_product > 3498 ";

        $od = Db::getInstance()->executeS($sql);

        foreach ($od as $d)
        {

            $rprice = Product::getPriceStatic($d['id_product'],false, $d['cache_default_attribute']);
		if($rprice == 0)
			$rprice = $d['price'];
            $sql = "UPDATE "._DB_PREFIX_."product SET rprice = ".$rprice.' where id_product='.$d['id_product'];

            Db::getInstance()->execute($sql);
        }

          return $output;
      }

      private function getCityListInfo()
    {
        $city_list = true;

        $this->smarty->assign(array(
            'city_name' => $this->city_info['city_name'],
            'city_lists' => (bool)$city_list,
            'city_link' => $this->context->link->getModuleLink('egms', 'citys'),
            'address' => $this->city_info['fake_address'],
            'email' => $this->city_info['email'],
            'phone' => $this->city_info['phone']
        ));
    }
      public static function getCityURL($id)
      {
          $sql = 'select su.domain, mu.`city_name`
				from `'._DB_PREFIX_.'shop_url` su
				INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
					mu.`id_url`=su.`id_shop_url`
				where su.id_shop_url='.(int)$id;

          if (!$row = Db::getInstance()->executeS($sql))
              return false;

          return array(
              'url' => $row[0]['domain'],
              'city_name' => $row[0]['city_name']
          );
      }

    function curl_post_async($url)
    {
        $ctx = stream_context_create(array('http' => array('timeout' => 0 )));
        @file_get_contents($url, 0, $ctx);

    }

      public function getAts($postdata='')
      {

          if($postdata!=''){
                         $this->setXML($postdata);

              $state_x = '//xsi:Event/xsi:eventData/xsi:calls/xsi:call/xsi:state';
              $phone_x = '//xsi:Event/xsi:eventData/xsi:calls/xsi:call/xsi:remoteParty/xsi:address';

              $state = $this->getVal($state_x);
              $phone_num = str_replace('+', '', str_replace('tel:', '', $this->getVal($phone_x)));
		$url = $this->context->link->getModuleLink('egms', 'ats').'?asic=mykey&phone='.$phone_num;
                 $this->curl_post_async($url);

/*                  $sql = "SELECT *  FROM "._DB_PREFIX_."orders o 
                INNER JOIN "._DB_PREFIX_."address a 
                    ON o.id_address_delivery = a.id_address
                WHERE a.phone_mobile like'%".$phone_num."%'";

                  $od = Db::getInstance()->executeS($sql);
*/
              //$od = $this->orderExis($phone_num);
/*              if($od)
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
                  if($state == 'Detached') {
                      Tools::viberSend('message', $text);
                  }
		
              }else
              {
                  $param = array('{phone}'    => $phone_num);
                  $text = Configuration::get('EGMS_VIBER_NEW_CALL');
                  $text = Tools::replaceKeywords($param, $text, '');
                  $text = Meta::replaceForCEOWord($text);
                      if($state == 'Detached') {
                          Tools::viberSend('message', $text);
                      }
                      die("ok");
              }*/
          }

      }

      function orderExis($phone)
      {
        $sql = "SELECT *  FROM "._DB_PREFIX_."orders o 
        INNER JOIN "._DB_PREFIX_."address a 
            ON o.id_address_delivery = a.id_address
        WHERE a.phone_mobile like'%".$phone."%'";

          $links = Db::getInstance()->executeS($sql);
        return $links;
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
  	public function hookDisplayTop($params)
	{
        //$this->context->controller->addJS($this->_path.'views/js/egms.js', 'all');

        $this->getCityListInfo();
	
		return $this->display(__FILE__, 'egms_city.tpl');
	}

	public function getMainDomain()
    {
          list($x1,$x2)=array_reverse(explode('.',$this->host));
          return $xdomain=$x2.'.'.$x1;
    }

	public function hookDisplayNav($params)
	{
        $city_q = "";
        $xdomain=$this->getMainDomain();

        if ($xdomain == $this->host){
            if (Tools::getValue('utm_medium')=='cpr') {
                if ($this->context->cookie->__isset('rurl')) {
                    $url = $this->context->cookie->__get('rurl');
                    $this->context->cookie->__unset('rurl');
                    Tools::redirect("//".$url);
                }else{
                    $city_q = 'show';
                }
            }
        }

        $this->context->cookie->__set('rurl', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); //
        $url = $this->context->cookie->__get('rurl');

        if ($xdomain == $this->host && Tools::getValue('utm_source')=='rsy') {
            $url = egms::getCityURL($this->context->cookie->__get('yandex_region'));
            if (!$url) {
                $city_q = 'show';
            }else {
                Tools::redirect("//".$url['url'].$_SERVER['REQUEST_URI']);
            }
        }

        $this->smarty->assign(array(
            'ajaxcontroller' => $this->context->link->getModuleLink($this->name, 'ajax'),
            'city' => $city_q,
            'url' => '//'.$this->host,
            'sitename' => Configuration::get('PS_SHOP_NAME')
        ));

        $this->getCityListInfo();

        return $this->display(__FILE__, 'egms_nav.tpl');
	}		
	
	public function hookHeader($params)
	{
	    global $smarty;
        $smarty->assign(array(
            'sitename' => $this->context->shop_info['shop_name'].' - '.$this->context->shop_info['city_name']
        ));

		$this->context->controller->addJS('//api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU', 'all');		
		$this->context->controller->addCSS($this->_path.'views/css/egms.css', 'all');
		$this->context->controller->addJS($this->_path.'views/js/modal.js', 'all');
        $this->context->controller->addJS($this->_path.'views/js/egms.js', 'all');
        //$this->context->controller->addCSS('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css','all');
       // $this->context->controller->addJS('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js','all');

		$this->smarty->assign(array(
			'yandex_verify' => (string)$this->city_info['veryf_yandex'],
			'google_verify' => (string)$this->city_info['veryf_google'],
			'mail_verify' => (string)$this->city_info['veryf_mail'],
			'city_link' => $this->context->link->getModuleLink('egms', 'citys'),
            'special_link' => $this->context->link->getModuleLink('egms', 'special')
		));
		
		return $this->display(__FILE__, 'egms_header.tpl');

	}

	
  	public function hookDisplayProductDeliveryTime($params)
	{

        $product = $params['product'];
        $id_product = is_array($product)?$product['id_product']:$product->id;
        $id_manufacturer = is_array($product)?$product['id_manufacturer']:$product->id_manufacturer;
        $product_price = is_array($product)?$product['price']:$product->price;
        $category_hide = is_array($product)?'1':'0';

	    $data = Shop::getCEOData($id_manufacturer);

        if (in_array($id_product,explode(',', $data['dlex'])))
            $data['free_pay'] = 0;

        if ($product_price>=$data['free_pay'])
            $data['free_pay'] = 0;
	    
	    $link_shipself = $this->context->link->getModuleLink('egms', 'delivery');
	    
		$this->smarty->assign(array(
		    'category_hide' => $category_hide,
		    'product_price' => $product_price,
			'free_price' => 	$data['free_pay'],
			'delivery_price' => $data['del_pay'],
			'link_shipself' => $link_shipself	
		));	
			
		return $this->display(__FILE__, 'egms_delivery.tpl');
	}
	public function hookDisplayCategoryHeader($params)
    {
        $id_page = Tools::getValue('id_category');

        return($this->getAdvContent("CHead", $id_page));
    }
	protected function getAdvContent($place, $id_page)
    {
    //TODO: should be flexibal for shops
        $id_egms_cu = $this->city_info['id_egms_cu'];

        $context = Context::getContext();
        //$id_shop = $context->shop->id;

        $sql = 'select adv.id_content_page id_content_page
                from '._DB_PREFIX_.'egms_adv adv
                where adv.id_egms_cu = '.(int)$id_egms_cu.
            ' and adv.place = "'.$place.'"
                  and adv.id_page = '.(int) $id_page.
            ' and adv.active = 1';
        ;
        $res = Db::getInstance()->executeS($sql);
        $id_content_page = $res[0]['id_content_page'];

        return ($id_content_page)?egmspage::getSitePage(null, $id_content_page):'';
    }
    public function hookDisplayCategoryImage($params)
    {
        $id_page = Tools::getValue('id_category');

        return($this->getAdvContent("CImage", $id_page));
    }

    public function hookDisplayCategoryInfo($params)
    {
        $id_page = Tools::getValue('id_category');

        return($this->getAdvContent("CInfo", $id_page));
    }

    public function hookDisplayProductAdv($params)
    {
        $id_page = 22;//Tools::getValue('id_category');

        //return ('sadasd');//return($this->getAdvContent("PAdv", $id_page));
    }

    public function hookDisplayCategoryFooter()
    {
        $id_page = Tools::getValue('id_category');

        return($this->getAdvContent("HFooter", $id_page));
    }


    public function hookDisplayProductDescriptionAdd($params)
    {
        $id_product = Tools::getValue('id_product');

        return($this->getAdvContent("Pdesc", $id_product));
    }

      public function hookActionValidateOrder($params)
	{

		$order = $params['order'];
		
		
		$total = $order->total_paid;
		$host = Tools::getHttpHost();
		$order_id = $order->id;
		
		$param = array(
				'{total_paid}'	=> $total, 
				'{shop_url}'	=> $host
			);



		Mail::Send(
				(int)$order->id_lang,
				'event_order',
				Mail::l('new order', (int)$order->id_lang),
				$param,
				Configuration::get('SMS_ORDER_NEW_EMAIL'),
				'Site Admin',
				null,
				null,
				null,
				null,
				_PS_MAIL_DIR_,
				false,
				(int)$order->id_shop
			);	
			
		Mail::Send(
				(int)$order->id_lang,
				'event_order',
				Mail::l('new order', (int)$order->id_lang),
				$param,
				Configuration::get('PS_SHOP_EMAIL'),
				'Site Admin',
				null,
				null,
				null,
				null,
				_PS_MAIL_DIR_,
				false,
				(int)$order->id_shop
			);		
		// notify to me
		$message = Configuration::get('SMS_ORDER_NEW');
		$address = new Address((int) $order->id_address_delivery);
		$message = Meta::sprintf2($message, array(
			'order' => $order_id,
			'host' => $host
			));
		$phone_client = (trim($address->phone)=="")?$address->phone_mobile:$address->phone;
		$phone_client = preg_replace('#\D+#', '', $phone_client);			
		//if (egmultishop::isLiveSite()) {
			//if (Configuration::get('BLOCK_EGMULTSOP_SNON')){
				// send to client if marketing site
				//if(egmultishop::isMarketingSite()>0){
					egms::SendSMS($phone_client, $message);
				//}
		
				
				// send to me in any way
		$phone = Configuration::get('SMS_ORDER_NEW_PHONE');
		$message = " ".$total." RUB ";
		//egmultishop::SendSMS($phone, "new order".$message.$host);
		//}

		$param = array(
                '{phone}'    => str_replace('+','',$phone_client),
                '{message}'    => $message,
                '{fname}' => $address->firstname,
            	'{order}' => $order_id,
            	'{type}'	=> 'NewOrder',
            	'{address}' => $address->address1,
            	'{host}' => $host,
            	'{shost}' => str_replace('.','-',$host)
            );
		$this->sendTelegramm($param, $message);
		//$this->sendTelegramm2($param, $sms_message);
			//}	
		// client notify	
	}	
	
  	private function sendTelegramm($param, $sms_message)
	{
		//$request = Configuration::get('EGCALLME_HTTPNOT_3');
		//$text = Configuration::get('EGCALLME_HTTPNOT_3_TXT');
		//$request = $this->replaceKeywords($param, $request, $text);
        //$result = file_get_contents($request);
		$text = Configuration::get('EGMS_VIBER_NEW_ORDER');
	    $text = $this->replaceKeywords($param, $text, '');
		$text = Meta::replaceForCEOWord($text);
		Tools::viberSend('message', $text);
	}
/*
      public function hookDisplayBackOfficeHeader()
      {
          if (Tools::getValue('configure') != $this->name)
              return;

          return '<script>
				var admin_egms_ajax_url = \''.$this->context->link->getAdminLink("AdminAjax").'\';
			</script>';
      }
*/
      public function hookDisplayProductButtons($params)
      {
          $prod = $params['product'];
          $this->product = $prod;

          $context = Context::getContext();

          $cur = Currency::getCurrencyInstance( Configuration::get('PS_CURRENCY_DEFAULT'));

          return "<script>
	$(document).ready(function() { 
			$(window).load(function() {
				dataDisplay('".$cur->iso_code."','".$_SERVER['SERVER_NAME']."','".$prod->id."','".$prod->name."', priceWithDiscountsDisplay,'".$prod->manufacturer_name."','".$prod->category."');
			});
			$(document).on('change', '.attribute_select', function(e){
				dataDisplay('".$cur->iso_code."','".$_SERVER['SERVER_NAME']."','".$prod->id."','".$prod->name."', priceWithDiscountsDisplay,'".$prod->manufacturer_name."','".$prod->category."');
			});
			$(document).on('click', '#add_to_cart button', function(e){
				dataAdd('".$prod->id."','".$prod->name."', priceWithDiscountsDisplay,'".$prod->manufacturer_name."','".$prod->category."',1);
			});
	});
		$(document).on('click', '#oorder', function(e){
		e.preventDefault();
		var phone;
		var product;
		phone = $('#ophone').val();
		pname = $('#oname').val();
		product = $('h1[itemprop=\"name\"]').text()+\" \"+$(\"#group_1 option:selected\").text()+\" \"+$(\"#our_price_display\").text();
		
		if (pname==''||phone.length<3)
		{
			alert('".$this->l('name empty')."');
			return false;
		}
		
		if (phone==''||phone.length<12)
		{
			alert('".$this->l('phone empty')."');
			return false;
		}

		$('#oprod').val(product);
		
			dataAdd('".$prod->id."','".$prod->name."', priceWithDiscountsDisplay,'".$prod->manufacturer_name."','".$prod->category."',1);
			dataPurchaseFast('F".date('YmdHi')."', '".$prod->id."', '".$prod->name."', priceWithDiscountsDisplay, '".$prod->manufacturer_name."', '".$prod->category."');
		    $.ajax({
		         type: 'POST',
		         url: egms_ajaxcontroller,
		         data: $('#buy_block').serialize(),
		         success: function(data) {
		        	 $('#wdata').html(data);
		         }
		    });	
		
	});
	
		</script>";
      }

      public  function hookOrderConfirmation($params)
      {
          $context = Context::getContext();
          $id_cart = $context->cart->id;
          $id_order = Order::getOrderByCartId(intval($id_cart));
          $order = $params['objOrder'];
          $products = $order->getProducts();
          $dl = '<script>
   		$(window).load(function() {
	window.dataLayer.push({
	    "ecommerce": {
	        "purchase": {
	            "actionField": {
	                "id" : "'.$order->id.'",
	            },
	            "products": [';
          foreach ($products as $product)
          {
              //id_manufacturer
              //id_category_default
              //product_attribute_id
              $price = $product['total_price'];
              $revenue = $price/100*20;
              $brand = ManufacturerCore::getNameById($product['id_manufacturer']);
              $name = ProductCore::getProductName($product['product_id']);
              $category = CategoryCore::getUrlRewriteInformations($product['id_category_default']);

              $variant = $this->getAttributeName($product['product_attribute_id']);

              $dl.='{
	                    "id": "'.$product['product_id'].'",
	                    "name": "'.$name.'",
	                    "price": '.$price.',
	                    "brand": "'.$brand.'",
	                    "category": "'.$category[0]['link_rewrite'].'",
	                    "variant": "'.$variant.'",
	                    "quantity": '.$product['product_quantity'].',
	                    "revenue": '.$revenue.',
	                    "shipping": '.$order->total_shipping.'
	                },';
          }
          $dl.=']
	        }
	    }
	});
	';
          foreach ($products as $product)
          {
              $price = $product['total_price'];
              $revenue = $price/100*20;
              $name = ProductCore::getProductName($product['product_id']);
              $category = CategoryCore::getUrlRewriteInformations($product['id_category_default']);
              $brand = ManufacturerCore::getNameById($product['id_manufacturer']);
              $variant = $this->getAttributeName($product['product_attribute_id']);

              $dl.='/**/ ga("ec:addProduct", {
				  "id": "'.$product['product_id'].'",
				  "name": "'.$name.'",
				  "category": "'.$category[0]['link_rewrite'].'",
				  "brand": "'.$brand.'",
				  "variant": "'.$variant.'",
				  "price": "'.$price.'",
				  "quantity": '.$product['product_quantity'].'
				});
				';
          }
          $dl.='ga("ec:setAction", "purchase", {
		  "id" : "'.$order->id.'",
		  "affiliation": location.hosts,
		  "revenue": "'.$order->total_paid_real.'",
		  "shipping": "'.$order->total_shipping.'"
		});

		ga("send", "pageview");
   		/**/
   		}); 
   		</script>';

          return $dl;
      }

      private function getAttributeName($id_product_attribute)
      {
          $id_lang = Context::getContext()->language->id;

          $sql = 'SELECT al.name as name
				FROM `'._DB_PREFIX_.'product_attribute_combination` pac 
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON al.`id_attribute` = pac.`id_attribute`
				WHERE pac.id_product_attribute = '.$id_product_attribute.'
				and al.`id_lang` = '.(int)$id_lang.'';

          $res = Db::getInstance()->executeS($sql);

          return $res[0]['name'];
      }
	
  	private function sendTelegramm2($param, $sms_message)
	{
		//$request = Configuration::get('EGCALLME_HTTPNOT_4');
		//$text = Configuration::get('EGCALLME_HTTPNOT_4_TXT');
		//$request = $this->replaceKeywords($param, $request, $text);
        //$result = file_get_contents($request);
		//Tools::viberSend('message', $text);
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
	
  	public static function SendSMS($phone, $message)
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$id_shop_group = $context->shop->id_shop_group;
		
		$max_sms = 6;
		$sms = Configuration::get('EGCALLME_SMS_REQUEST',null, $id_shop_group, $id_shop);
		$phone = preg_replace('#\D+#', '', $phone);
		$sms = Meta::sprintf2($sms, array(
				'sendto' => $phone,
				'message' => substr($message, 0, 70*$max_sms)
				));
					
		if(trim($sms)!=""){		
			$result = file_get_contents($sms);
		}
	}	
	
  	public function hookDisplayHome($params)
	{
		return egmspage::getSitePage('index');	
	}	

  	public function hookDisplayLeftColumn($params)
	{	
		return egmspage::getSitePage(null, 7);// 7 is row of social	
	}	
	
  	public function hookDisplayFooter($params)
	{
		return egmspage::getSitePage(null, 8);		
	}	
	
    public function hookDisplayTopColumn($params)
	{
        return egmspage::getSitePage(null, 9);
    }


	
	public function hookActionProductUpdate($params)
	{
		$id_product = Tools::getValue('id_product');
		$price_content = Tools::getValue('price_content');
		$update_price = Tools::getValue('update_price');
        $attr_content = Tools::getValue('attr_content');
        $attrs = explode(',', Tools::getValue('attrs'));
		$discount = Tools::getValue('discount');
		$product = $params['product'];
		$sizes = explode(',',Tools::getValue('sizes'));
		$prices= explode(',',Tools::getValue('prices'));
		$prices_dif = explode(',',Tools::getValue('prices_dif'));
		$attrr_group = Tools::getValue('attrr_group');
        $attrr_group_add = Tools::getValue('attrr_group_add');
        $ctype = Tools::getValue('ctype');
		$basePrice = $prices[0];

        $this->product = new Product($id_product);
		
		if($discount=="")
			$discount = 0;

		if($update_price == "true"){
/*
		    if ($ctype == 3)
            {
                $sizes = null;
                $prices = null;
                $prices_dif = null;
                // dr
                $sql = 'select * from dl_nomenkl n
                          inner join dl_price pr on pr.id_nomenkl = n.id_nomenkl
                          inner join dl_nomenkl_ord ord on ord.size = n.size
                        where n.id_product = '.$this->product->reference.'
                        order by ord.ord;';

                $links = Db::getInstance()->executeS($sql);
                foreach ($links as $row)
                {
                    $sizes[]=$row['size_ext'];
                    $prices[]=$row['price'];
                    //$prices_dif = $row[''];
                    $discount = $row['discount'];
                }
                $basePrice = $prices[0];

                foreach ($prices as $price)
                {
                    $prices_dif[] =  $price - $basePrice;
                }


            }
			*/
			
			$this->writePriceBook($id_product, $discount, $price_content, $attrr_group, $attrr_group_add, $attr_content, Tools::getValue('demping'), Tools::getValue('ctype'));
			
			$tab[0] = array();
			$combinations = 0;
				
			foreach($sizes as $key => $size)
			{
				$size = str_replace('x', '_', $size);
				$id_attr = egms::getAttributeId($size, $attrr_group);
				if ($id_attr)
				{
					$tab[0][]= $id_attr;
				}else{
                    unset($prices[$key]);
                    unset($prices_dif[$key]);
                    unset($sizes[$key]);
                    //unset($attrs[$key]);
				    // delete prices and prices_dif
                }
			}
			
			foreach ($attrs as $attr) {
                if (trim($attr) != '') {

                    $id_attr = egms::getAttributeId($attr, $attrr_group_add, 1);
                    if ($id_attr) {
                        $tab[1][] = $id_attr;
                    }
                }
			}
			
			$this->updateProductBasePrice($product->id, $basePrice);
			egms::deleteCombinatinImages($this->product);
				
			egms::setAttributesImpacts($product->id, $tab, $prices_dif);

			if ($tab[1]) {
                foreach ($tab[1] as $t) {
                    foreach ($prices_dif as $a)
                        $prices_dif_full[] = $a;
                }
            }else
            {
                $prices_dif_full = $prices_dif;
            }

            $this->combinations = array_values(egms::createCombinations($tab));
            $values = array_values(array_map(array($this, 'addAttribute'), $this->combinations, $prices_dif_full));
			//$combinations = $this->getCombination($tab);
			//$values = $this->getMap($product->id, $combinations, $prices_dif);

			SpecificPriceRule::disableAnyApplication();
				
			$product->deleteProductAttributes();
			$res = $product->generateMultipleCombinations($values, $this->combinations);
				
			SpecificPriceRule::enableAnyApplication();
			SpecificPriceRule::applyAllRules(array((int)$product->id));

            egms::addCombinationImages($this->product, $attrs);

				
		}
	}

      protected function addAttribute($attributes, $price = 0, $weight = 0)
      {
          $a = $attributes;
          if ($this->product->id)
          {
              return array(
                  'id_product' => (int)$this->product->id,
                  'price' => (float)$price,
                  'weight' => 0,
                  'ecotax' => 0,
                  'quantity' => 0,
                  'reference' => 0,
                  'default_on' => 0,
                  'available_date' => '0000-00-00'
              );
          }
          return array();
      }

      protected static function addCombinationImages($product, $attrs)
      {
            $id_lang = Context::getContext()->language->id;
            $combinations = $product->getAttributeCombinations($id_lang);

            foreach($attrs as $attr) {
                foreach ($combinations as $combination) {
                    if (($combination['attribute_name']) == trim($attr)) {
                        $id_product_attribute = $combination['id_product_attribute'];

                        $images = $product->getImages($id_lang);

                        foreach ($images as $image) {
                            $parts = explode(':', $image['legend']);
                            if (trim($parts[1]) == trim($attr)) {
                                $sql = "insert into " . _DB_PREFIX_ . "product_attribute_image(id_product_attribute, id_image)
                              values(" . $id_product_attribute . ", " . $image['id_image'] . ")";
                                Db::getInstance()->executeS($sql);
                            }
                        }
                    }
                }
            }

      }
      protected static function deleteCombinatinImages($product)
      {
            $sql = "delete pai
                      from "._DB_PREFIX_."product_attribute_image pai
                        inner join "._DB_PREFIX_."product_attribute pa on pai.id_product_attribute = pa.id_product_attribute
                        where pa.id_product = ".(int)$product->id;
          Db::getInstance()->executeS($sql);
      }

      protected static function createCombinations($list)
      {
          if (count($list) <= 1)
              return count($list) ? array_map(create_function('$v', 'return (array($v));'), $list[0]) : $list;
          $res = array();
          $first = array_pop($list);
          foreach ($first as $attribute)
          {
              $tab = egms::createCombinations($list);
              foreach ($tab as $to_add)
                  $res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
          }
          return $res;
      }
	
	public static function getAttributeId($name, $attrg, $ctyp=0)
	{
		$sql = 'select a.id_attribute from '._DB_PREFIX_.'attribute a
				 join '._DB_PREFIX_.'attribute_lang al on a.id_attribute = al.id_attribute
				where a.id_attribute_group = '.$attrg.'';
				if ($ctyp == 0)
				    $sql .= ' and al.name like \''.$name.'%\';';
                else
		            $sql .= ' and al.name = \''.$name.'\';';
		$links = Db::getInstance()->executeS($sql);
		if ($links[0]['id_attribute']> 0 )
			return $links[0]['id_attribute'];
			
			return false;
	}

	
	public function getCombination($tab)
	{
		$result = Array();
		
		foreach ($tab[0] as $key => $item)
		{
			$result[$key] = array($item);
		}
		return $result;
	}
	
	protected static function setAttributesImpacts($id_product, $tab, $prices)
	{
	    $attributes = array();
		foreach ($tab as $group)
            foreach ($group as $key => $attribute) {
                $price = $prices[$key];
                $weight = 0;
                $attributes[] = '(' . (int)$id_product . ', ' . (int)$attribute . ', ' . (float)$price . ', ' . (float)$weight . ')';
            }

		return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
		VALUES '.implode(',', $attributes).'
		ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');
	}
	
	public function updateProductBasePrice($id_product, $price)
	{
		$id_shop = $this->context->shop->id;
		
		$sql = "update "._DB_PREFIX_."product_shop
				 		set
				 		price = ".$price."
				 		where id_product=".(int) $id_product."
				 		and id_shop IN (".implode(', ', Shop::getContextListShopID()).")";
		if (!$links = Db::getInstance()->execute($sql))
			return false;
			
			$sql = "update "._DB_PREFIX_."product
				 		set
				 		price = ".$price."
				 		where id_product=".(int) $id_product;
			if (!$links = Db::getInstance()->execute($sql))
				return false;
	}
	
	public function hookDisplayAdminProductsExtra($params)
	{	
		$id_product = Tools::getValue('id_product');
		$sql = 'select p.id_product, p.prices, discount, id_attr_group, id_attr_group1, attributes, demping, ctype  from '._DB_PREFIX_.'egms_prices p
			where p.id_product = '. (int) $id_product;
		$links = Db::getInstance()->executeS($sql);

		$procent = $links[0]['discount'];
		$price_content = $links[0]['prices'];
		$attrr_group = $links[0]['id_attr_group'];
        $attrr_group1 = $links[0]['id_attr_group1'];
        $attributes = $links[0]['attributes'];
        $demping = $links[0]['demping'];
        $ctype = $links[0]['ctype'];
		if($attrr_group == '')
			$attrr_group = 0;
        if($attrr_group1 == '')
            $attrr_group1 = 0;
		$this->context->smarty->assign(array(
				'attrr_group' => $attrr_group,
                'id_attr_group1' => $attrr_group1,
                'attr_content' => $attributes,
				'price_content' => $price_content,
				'id_attr_group' => $attrr_group,
				'procent' => !$procent?0:$procent,
                'demping' => !$demping?0:$demping,
                'ctype' => $ctype,
                'ajax_url' => Context::getContext()->link->getAdminLink("AdminEGMSAjax"),
		));

		//$this->createAjaxController();
		
		return $this->display(__FILE__, 'views/templates/admin/price_tab.tpl');
	}

	public function hookDisplayProductTab($param) {

        global $smarty;
        $id_product = $param['product']->id;

        foreach (Image::getImages($this->context->language->id, $id_product) as $img) {
            $smarty->assign(array(
                'image_url' => $this->context->link->getImageLink($param['product']->link_rewrite, $img['id_image'], 'large_default'))
            );
        }
        $smarty->assign(array(
            'url_product' => $this->context->link->getProductLink($id_product)
        ));

        return $this->display(__FILE__, 'views/templates/hook/product_delivery_tab.tpl');
    }

    public function hookDisplayProductTabContent(){

      //  $content = 'my delivery'; //egmspage::getSitePage('delivery');

//        $this->smarty->assign(array(
  //          'content' => 	$content
    //    ));
      //  return $this->display(__FILE__, 'views/templates/hook/product_delivery_content.tpl');
    }

public function hookDisplayFooterProduct($params) {
        global $smarty;

        $product = $params['product'];

        $id_category = $product->id_category_default;
        $id_manufacturer = $product->id_manufacturer;
        $featurs = $product->getFrontFeatures(1);

        $featurs_ids =array();
        foreach ($featurs as $f)
        {
            $featurs_ids[] = $f['id_feature_value'];
        }

        $tags = $this->getAtag($id_category, $id_manufacturer, $featurs_ids);

	if($tags)
        $smarty->assign(array(
                'tags' => $tags
            ));

        return $this->display(__FILE__, 'views/templates/hook/product_futer.tpl');

    }

    private function getAtag($id_category, $id_manufacturer, $features){

        $sql = "
        SELECT id_tag, tag_name, tag_link
		FROM "._DB_PREFIX_."egms_atag
		WHERE id_manufacturer = 0
		AND id_feature=0
		AND id_feature_value = 0
		AND id_category = ".$id_category."
		union
        SELECT id_tag, tag_name, tag_link
		FROM "._DB_PREFIX_."egms_atag
		WHERE id_manufacturer = ".$id_manufacturer."
		AND id_category = ".$id_category."
		union
        SELECT id_tag, tag_name, tag_link
		FROM "._DB_PREFIX_."egms_atag
		WHERE id_category = ".$id_category."
		AND id_feature_value in (".implode(', ', $features).")	
		";


      $rows = Db::getInstance()->executeS($sql);

          return $rows;
    }



	public function writePriceBook($id_product, $discount, $price_content, $attrr_group, $attrr_group_add, $attrs, $demping, $ctype)
	{
		$sql = "select id_product
		from "._DB_PREFIX_."egms_prices
		where id_product=".(int) $id_product;
		
		if (!$links = Db::getInstance()->executeS($sql))
		{
			//insert
			$sql = "insert into "._DB_PREFIX_."egms_prices
			(`id_product`, `id_shop`, `prices`, `discount`, `id_attr_group`, `id_attr_group1`,`attributes`, `demping`, `ctype`) VALUES
			(".$id_product.",null,'".$price_content."', ". $discount.", ".$attrr_group.", ".$attrr_group_add.",'".$attrs."', ".$demping.", ".$ctype.")";
			
		}else{
			//update
			$sql = "update "._DB_PREFIX_."egms_prices
			 set
			 `prices` = '".$price_content."',
			 `discount` = ".$discount.",
			 `id_attr_group` = ".$attrr_group.",
			 `id_attr_group1` = ".$attrr_group_add.",
			 `attributes` = '".$attrs."',
			 `demping` = '".$demping."',
			 `ctype` = '".$ctype."'
			 where id_product=".(int) $id_product;
		}
		if (!$links = Db::getInstance()->execute($sql))
			return false;
			
			return true;
	}
	
  	public function installAdminTab()
	{
		$retval = true;
		$id_parent = 0;
		foreach ($this->tabs as $key => $ctab)
		{
			$tab = new Tab();
			$tab->active = 1;
			$languages = Language::getLanguages(true);
			if (is_array($languages))
				foreach ($languages as $language)
					$tab->name[$language['id_lang']] = $ctab['name'];
			$tab->class_name = $ctab['class_name'];
			$tab->module = $this->name;
			$tab->id_parent = $id_parent;
			$retval = (bool)$tab->add();
			if ($key==0)
				$id_parent = $tab->id;
		}
		return $retval;
	}

      public function createAjaxController()
      {
          $tab = new Tab();
          $tab->active = 1;
          $languages = Language::getLanguages(false);
          if (is_array($languages))
              foreach ($languages as $language)
                  $tab->name[$language['id_lang']] = 'ajax controller';
          $tab->class_name = 'AdminEGMSAjax';
          $tab->module = $this->name;
          $tab->id_parent = - 1;
          return (bool)$tab->add();
      }
		
	public static function uninstallAdminTab()
	{
		$retval = true;
		$tabs = new egms();
		foreach ($tabs->tabs as $ctab)
		{
			$idTab = Tab::getIdFromClassName($ctab['class_name']);
			if ($idTab != 0)
			{
				$tab = new Tab($idTab);
				$tab->delete();
				$retval = true;
			}
		}
		return $retval;
	}			

	public function install($keep = true)
	{
	/*	
	   if ($keep) {
            if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
                return false;
            } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
                return false;
            }
            $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'DB1NAME'),
                array(_DB_PREFIX_, _MYSQL_ENGINE_, self::INSTALL_SQL_BD1NAME), $sql);
            $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'DB5NAME'),
                array(_DB_PREFIX_, _MYSQL_ENGINE_, self::INSTALL_SQL_BD5NAME), $sql);
            $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'DB3NAME'),
                array(_DB_PREFIX_, _MYSQL_ENGINE_, self::INSTALL_SQL_BD3NAME), $sql);  
			$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'DB4NAME'),
                array(_DB_PREFIX_, _MYSQL_ENGINE_, self::INSTALL_SQL_BD4NAME), $sql);                                               
            $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

            foreach ($sql as $query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }

        }		
		*/	
	  if (!parent::install()|| 
		!Configuration::updateValue('EGMS_PAGE_INDEX', '')||
		!Configuration::updateValue('EGMS_PAGE_CONTACT', '')||
		!Configuration::updateValue('EGMS_PAGE_SHIPSELF', '')||
		!Configuration::updateValue('EGMS_PAGE_DELIVERY', '')||
	  	!Configuration::updateValue('EGMS_SUBDOMAIN', '1')||
		!$this->installAdminTab()||
		!$this->registerHook('displayTop') ||
		!$this->registerHook('displayNav') ||
	  	!$this->registerHook('displayAdminProductsExtra') ||
	  	!$this->registerHook('actionProductUpdate') ||
		!$this->registerHook('header') ||
		!$this->registerHook('displayFooter')
		)
	    return false;
	  return true;
	}  	
	
  	public function reset()
	{
		if (!$this->uninstall(false))
			return false;
		if (!$this->install(false))
			return false;
		return true;
	}	
	
    public function deleteTables()
    {
    	$sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::INSTALL_SQL_BD1NAME.'`;';
    	$sql .= 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::INSTALL_SQL_BD2NAME.'`;';
    	$sql .= 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::INSTALL_SQL_BD3NAME.'`;';
    	$sql .= 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::INSTALL_SQL_BD4NAME.'`;';
    	$sql .= 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::INSTALL_SQL_BD5NAME.'`;';
    	
        //return Db::getInstance()->execute($sql);
    }
	
	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || 
	  		!Configuration::deleteByName('EGMS_PAGE_INDEX') ||
	  		!Configuration::deleteByName('EGMS_PAGE_CONTACT') ||
	  		!Configuration::deleteByName('EGMS_PAGE_SHIPSELF') ||
	  		!Configuration::deleteByName('EGMS_PAGE_DELIVERY') ||
	  		//!$this->unregisterHook('displayFooter')|| 		
	  		//($keep && !$this->deleteTables())||
	  		!$this->uninstallAdminTab()
			)
	    return false;
	  return true;
	}		
	
  }
  
  
