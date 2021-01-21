<?php
if (!defined('_PS_VERSION_'))
  exit;
  
  class egormprod extends Module
  {
	
  	const INSTALL_SQL_FILE = 'install.sql';
	const INSTALL_SQL_BD1NAME = 'egormprod';
	const INSTALL_SQL_BD2NAME = 'egormprod_data';
	const INSTALL_SQL_BDNAME = 'gb_load';
	public $basePrice;
  	
    public function __construct()
    {
	    $this->name = 'egormprod';
	    $this->tab = 'front_office_features';
	    $this->version = '0.1.1';
	    $this->author = 'Evgeny Grishin';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
		 
	    parent::__construct();
	 
	    $this->displayName = $this->l('Get content products of ormatek');
	    $this->description = $this->l('Addon for copy ormatek product information.');
	 
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
  	}	
  	public function hookDisplayTop($params)
	{

	}
	
	public static function getAttributeId($name)
	{
		$sql = 'select a.id_attribute from '._DB_PREFIX_.'attribute a
				 join '._DB_PREFIX_.'attribute_lang al on a.id_attribute = al.id_attribute
				where a.id_attribute_group = 1
				and al.name like \''.$name.'%\';';
		$links = Db::getInstance()->executeS($sql);
		if ($links[0]['id_attribute']> 0 )
			return $links[0]['id_attribute'];
		
		return false;
	}
	
  	public function hookDisplayProductDeliveryTime($params)//hookDisplayProductButtons($params)
  	{

  		$product = $params['product'];
  		$id_lang = Context::getContext()->language->id;
  		
  		$file = dirname(__FILE__).'/content/'.$product->id.'_c.html';
  		$fileu = dirname(__FILE__).'/content/'.$product->id.'_u.html';
  		$content = '';
		  		
		if (Tools::getValue("u",false)!==false) {

		  	$url = Tools::getValue('url');
			if (Tools::getValue('url',false)!==false) {
				if ($url !='') {
					file_put_contents($fileu, $url);
				}
				
				$url = file_get_contents($fileu);
				
				if ($url != ''){
					$content = $this->getProductContent($url);
					file_put_contents($file, $content);
					$messages[] = "load from url";
				}else {
					$messages[] = "Error: url not found";
				}
			}			
			
			if($content=='') {
				$messages[] = "update content";
				$content = file_get_contents($file);
			}

			$query = "//select[@id='sku-variants']/option";
		
			$dom = new DomDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($content);
			$xpath = new DomXPath($dom);
	   		$nodes = $xpath->query($query);
	   		
		    if ($nodes->length==0){
		    	// update only base price
		    	$query = "string(//div[@id='price-container']/span/text())";
				$price = $xpath->evaluate($query);
				$query = "string(//div[@id='price-container']/del/text())";
				$oldprice = $xpath->evaluate($query);
				$price = preg_replace("/[^0-9]/", '', $price);
				$oldprice = preg_replace("/[^0-9]/", '', $oldprice);
				if ($price == "" && $oldprice == "")
				{
					$tab[0] = array();
			    	$combinations = 0;
			    	
					$query = "string(//div[@id='complectData']/text())";
					$node = $xpath->evaluate($query);
					$product_data = json_decode($node);
					$offers = $product_data->items[0]->OFFERS;
					//array_multisort($offers['BASE_PRICE'], SORT_ASC);
					foreach ($offers as $offer){
				    	$size = $offer->SHIRINA.'×'.$offer->DLINA; 
				    	$id_attr = egormprod::getAttributeId($size);
				    	if ($id_attr)
				    	{
				    		$tab[0][]= $id_attr;
				    		$prices[] = $offer->BASE_PRICE;
				    	}else{
				    		$messages[] = "Error: size ".$size." not found ";
				    	}
					}
					
				    array_multisort($prices, SORT_ASC,SORT_NUMERIC, $tab[0]);
				    $this->basePrice = $prices[0];
				    foreach ($prices as $key => $price_tmp){
				    	$prices[$key] = ($price_tmp - $this->basePrice);
				    }
				    
				    
			    $this->updateProductBasePrice($product->id);
			    
			    egormprod::setAttributesImpacts($product->id, $tab);//AdminAttributeGeneratorController::setAttributesImpacts($product->id, $tab);
				$combinations = $this->getCombination($tab);
				
				$values = $this->getMap($product->id, $combinations, $prices);//array_values(array_map(array($this, 'addAttribute'), $this->combinations));
				
				SpecificPriceRule::disableAnyApplication();
			
				$product->deleteProductAttributes();
				$product->generateMultipleCombinations($values, $combinations);
							
				SpecificPriceRule::enableAnyApplication();
				SpecificPriceRule::applyAllRules(array((int)$product->id));
				
				$messageTextErr = "";
				foreach ($messages as $message)
					$messageTextErr.=$message."\\r\\n";				    
					
				}else{
					// pillows
					$this->basePrice = ($oldprice>0)?$oldprice:$price; 
					$this->updateProductBasePrice($product->id);
				}					
		    }else{
			    $tab[0] = array();
			    $combinations = 0;
	
			    foreach( $nodes as $key => $node ) 
			    {
			    	$product_data = json_decode($node->getAttribute('data-price'));
			    	if ($key == 0){
			    		$this->basePrice = $product_data->VALUE;
			    	}
			    	$f1 = json_decode($node->getAttribute('data-cases'));
			    	
			    	$size = str_replace(' ', '', $node->getAttribute('value'));
			    	$id_attr = egormprod::getAttributeId($size);
			    	if ($id_attr)
			    	{
			    		$tab[0][]= $id_attr;
			    		$prices[] = ($product_data->VALUE - $this->basePrice);
			    	}else{
			    		$messages[] = "Error: size ".$size." not found ";
			    	}
			    	
			    }
			    $this->updateProductBasePrice($product->id);
			    
			    egormprod::setAttributesImpacts($product->id, $tab);//AdminAttributeGeneratorController::setAttributesImpacts($product->id, $tab);
				$combinations = $this->getCombination($tab);
				
				$values = $this->getMap($product->id, $combinations, $prices);//array_values(array_map(array($this, 'addAttribute'), $this->combinations));
				
				SpecificPriceRule::disableAnyApplication();
			
				$product->deleteProductAttributes();
				$product->generateMultipleCombinations($values, $combinations);
							
				SpecificPriceRule::enableAnyApplication();
				SpecificPriceRule::applyAllRules(array((int)$product->id));
				
				$messageTextErr = "";
				foreach ($messages as $message)
					$messageTextErr.=$message."\\r\\n";
		    }
			
		}
		
  	  	if (Tools::getValue("admin",false)!==false)
		{
			$f=1;
			$this->context->cookie->__set('a', 1);
  		}
  		if (Tools::getValue("admin")=="-1")
		{
			$f = 1;
			$this->context->cookie->__unset('a');
		}		

		if ($this->context->cookie->__isset('a'))
		{
			$url = file_get_contents($fileu);
		
			$this->smarty->assign(array(
				'messageTextErr' => $messageTextErr,
				'url' => $url	
			));	
				
			return $this->display(__FILE__, 'up.tpl');
		}
  	}
  	
  	public function updateProductBasePrice($id_product, $price)
  	{
  		$id_shop = $this->context->shop->id;
		
		$sql = "update "._DB_PREFIX_."product_shop  
				 		set 
				 		price = ".$this->basePrice." 
				 		where id_product=".(int) $id_product."
				 		and id_shop IN (".implode(', ', Shop::getContextListShopID()).")";
		if (!$links = Db::getInstance()->execute($sql))
			return false;
				
		$sql = "update "._DB_PREFIX_."product  
				 		set 
				 		price = ".$this->basePrice." 
				 		where id_product=".(int) $id_product;
		if (!$links = Db::getInstance()->execute($sql))
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
  	
  	public function getMap($id_product, $combinations, $prices)
  	{
  		$result = array();
  		
  		foreach ($combinations as $key => $combination )
  		{
			 $result[] = array(
				'id_product' => $id_product,
				'price' => $prices[$key],
				'weight' => 0,
				'ecotax' => 0,
				'quantity' => 0,
				'reference' => '',
				'default_on' => 0,
				'available_date' => '0000-00-00'
			);
  		}	
  		return $result; 
  	}
  	
    protected static function setAttributesImpacts($id_product, $tab)
    {
        $attributes = array();
        foreach ($tab as $group)
            foreach ($group as $attribute)
				{
					$price = 0;
					$weight = 0;
					$attributes[] = '('.(int)$id_product.', '.(int)$attribute.', '.(float)$price.', '.(float)$weight.')';
				}

		return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
		VALUES '.implode(',', $attributes).'
		ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');
    }
  	
  	public function getContent()
	{
		$this->registerHook('displayProductButtons');
		$this->registerHook('displayTop'); 
		//$this->createAjaxController();
		$output = null;
		
		$output = '<div class="row">
<div class="panel panel-default">
  <div class="panel-heading">'.$this->l('panel price update').'</div>
  <div class="panel-body">
    <button type="button" id="update" class="btn btn-primary">'.$this->l('proccess').'</button>
	<div class="checkbox">
	  <label><input type="checkbox" id="uc">'.$this->l('load new content').'</label>
	</div>
	<div class="checkbox">
	  <label><input type="checkbox" id="up">'.$this->l('update price').'</label>
	</div>	
	<div class="checkbox">
	  <label><input type="checkbox" id="comment">'.$this->l('update comment').'</label>
	</div>	
	<div class="checkbox">
	  <label><input type="checkbox" id="upname">'.$this->l('update product name').'</label>
	</div>			
  </div>
</div>
</div>
  <div class="row">
<table class="table table-striped table-hover table-bordered" id="main_table">
<thead>
 <tr>
 <th>-</th>
 <th>ID</th>
 <th>URL</th>
 <th>product name db</th>
 <th>product_attrgroup</th>
 <th>price_discount</th>
 <th>discount db</th>
 <th>actions</th>
 </tr>
 </thead>
 <tbody></tbody>
</table>
  </div>';
		
		return $output;
		
	}
	
  	public function renderList()
	{
					
		$fields_list = array(		
			'id_product' => array(
				'title' => $this->l('ID'),
				'type' => 'init',
			),		
			'name' => array(
				'title' => $this->l('product name'),
				'type' => 'text',
			),
			'product_sname' => array(
				'title' => $this->l('product name site'),
				'type' => 'text',
			),				
			'product_url' => array(
				'title' => $this->l('product url'),
				'type' => 'text',
			),				
			'product_attrgroup' => array(
				'title' => $this->l('product attrgroup'),
				'type' => 'text',
			),			
			'comment_update' => array(
				'title' => $this->l('comment update'),
				'type' => 'datetime',
			),
			'price_update' => array(
				'title' => $this->l('price update'),
				'type' => 'datetime',
			),	
			'dname' => array(
				'title' => $this->l('my disc'),
				'type' => 'text',
			),				
			'price_discount' => array(
				'title' => $this->l('orm discount'),
				'type' => 'text',
			),														
		);
		
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_product';
		$helper->actions = array('edit');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Link list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$links = $this->getProductList(0,false,false);
		if (is_array($links) && count($links))
			return $helper->generateList($links,  $fields_list);
		else
			return false;
	}

  	public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('configure') != $this->name)
			return;
			
		$this->context->controller->addJS($this->_path.'/views/js/admin.js');
		return '<script>
				var admin_ormprod_ajax_url = \''.$this->context->link->getAdminLink("AdminAjax").'\';
			</script>';
	}
	
  	public function createAjaxController()
	{
		$tab = new Tab();
		$tab->active = 1;
		$languages = Language::getLanguages(false);
		if (is_array($languages))
			foreach ($languages as $language)
				$tab->name[$language['id_lang']] = 'ajax controller';
		$tab->class_name = 'AdminAjax';
		$tab->module = $this->name;
		$tab->id_parent = - 1;
		return (bool)$tab->add();
	}
	
	public function getProductList($id_product = 0, $effected = false, $new_prod = false)
	{
		$join = ($effected)?' INNER ':' LEFT ';
		$sql = 'SELECT 
					p.id_product, pl.name, o.product_url, 
					o.product_sname, o.comment_update, o.price_update, 
					o.product_attrgroup, d.name dname, o.price_discount
				FROM `'._DB_PREFIX_.'product` p
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON
					p.`id_product`=pl.`id_product` 
				and p.`id_shop_default`=pl.`id_shop` 
				LEFT JOIN (select cp.id_product, c.id_category, cl.name 
							from '._DB_PREFIX_.'category_product cp
						inner join '._DB_PREFIX_.'category c on
							cp.id_category = c.id_category
						inner join '._DB_PREFIX_.'category_lang cl on
							cp.id_category = cl.id_category
						where cl.name like "%\%"
							and cl.id_shop = '.$this->context->shop->id.'
						) d on
					d.id_product = p.id_product
				'.$join.' JOIN `'._DB_PREFIX_.self::INSTALL_SQL_BD1NAME.'` o ON
					o.`id_product`=p.`id_product`
					where p.active = 1';
		if ($id_product!=0)
			$sql.=" and p.`id_product`=".(int)$id_product;
		
		if (!$links = Db::getInstance()->executeS($sql))
			return false;
		
		
		if ($new_prod)
		{	
			$newprods = $this->getNewProducts("http://old.ormatek.com/sitemap");
			if (is_array($newprods))
			foreach ($newprods as $newprod)	
			{
				$links[] = array(
						'id_product' => 0,
						'name' => '',
						'product_url' => $newprod['url'],
						'product_sname' => $newprod['sname'],
						'comment_update' => '',
						'price_update' => '',
						'product_attrgroup' => '',
						'dname' => '',
						'price_discount' => ''
						);
			}
		}
		return $links;
	}
	
	private function getNewProducts($sitemap)
	{
		$content = $this->getProductContent($sitemap);
		$baseurl = "http://ormatek.com";
		$query = ".//div[@class='right']/div/ul/li/ul/li/ul";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);
	    
   		if ($nodes->length==0)
	    	return false;

  		$alinks = $nodes->item(0)->getElementsByTagName("a");
  		
  		foreach ($alinks as $alink)
  		{
  			$url = $baseurl.$alink->getAttribute('href');
  			
  			$sql = $sql = "select * from  "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
  			where product_url='".$url."'";
  			
  			if (!$res = Db::getInstance()->executeS($sql))
			{
  				$links[] = array(
  						'url' => $url,
  						'sname' => 'name'
  					);
			}
  		}

			
		return $links;	
		
	}
	
	public function setProductURL($id_product, $url)
	{
		$sql = "select id_product
		from "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
		where id_product=".(int) $id_product;		

		if (!$links = Db::getInstance()->executeS($sql))
		{
			//insert
			$sql = "insert into "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
			(`id_product`, `id_shop`, `product_url`) VALUES 
			(".$id_product.",null,'".$url."')";
			
		}else{
			//update
			$sql = "update "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
			 set 
			 `product_url` = '".$url."'
			 where id_product=".(int) $id_product;
		}
		if (!$links = Db::getInstance()->execute($sql))
			return false;	
		
		return true;		
	}
	
	private function updateProductComment($id_product, $content, $action="")
	{

		$ac_types = explode(',',$action);
		
		
		foreach ($ac_types as $ac_type)
		{
			switch (trim($ac_type)){

				case "load":
					$comments = $this->getProductComments($content);
					$this->updateBlobField($id_product, "comments", $comments);
				break;
				
				case "update":
					$comments = $this->getBlobField($id_product, "comments");
					if(!$comments && $content!="")
					{
						$comments = $this->getProductComments($content);
						$this->updateBlobField($id_product, "comments", $comments);
					}
					$this->reloadProductComment($id_product, $comments);
				break;	

			}
		}
		
		//$comments = $this->getProductComments($content);
		
		//$this->updateBlobField($id_product, "comments", $comments);
		
		//return($this->reloadProductComment($id_product, $comments));
	}
	
	private function reloadProductComment($id_product, $comments)
	{
		if($comments)
		{
			$this->deleteProductComments($id_product);
			
			foreach ($comments as $comment)
			{
				$this->insertProductComment($id_product, $comment);
			}
			
			//TODO: rewrite to updateDb1TableFields
			$sql = "update "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME." 
				 set 
				 `comment_update` = CURRENT_TIMESTAMP
				 where id_product=".(int) $id_product;
			if (!$links = Db::getInstance()->execute($sql))
				return false;
		}
	}
	
	
	
	private function insertProductComment($id_product, $comment)
	{
		$c = $comment;
		
		$sql = "INSERT INTO  `"._DB_PREFIX_."product_comment` (
			`id_product` ,`id_customer` , `id_guest`, `title` ,
			`content`,`customer_name` ,`grade` ,
			`validate` ,`deleted` ,`date_add`)
			VALUES ( '".$id_product."',  '0', null,null,  
			'".addslashes($comment['text'])."',  '".$comment['name']."',  '".$comment['rating'].".0',  '1',  '0',  '".$comment['date']."');";
			
		if (!$links = Db::getInstance()->execute($sql))
			return false;
		
		return true;
		
	}
	
	private function deleteProductComments($id_product)
	{

		$sql = "DELETE FROM `ps_product_comment` WHERE `id_product` = ".(int)$id_product;
			
		if (!$links = Db::getInstance()->execute($sql))
			return false;
		
		return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param string $type
	 * 	1)name
	 *  2)comments
	 *  3)price
	 * @param int $id_product
	 * @param string $action
	 * 	1)update - update in database
	 *  2)load - load from site
	 */
	
  	public function updateProductInfo($ac_types, $id_product=0, $action="")
	{
		$products = $this->getProductList($id_product, true);
		
		//$ac_types = explode(',',$type);

			
		foreach ($products as $product)
		{
			if (is_array($ac_types)){
				foreach ($ac_types as $ac_type)
				{
					if (strripos($action,"load")===false)
					{
						$content = $this->getBlobField($product['id_product'], "content", false);
						
						if (trim($content)=="")
						{
							$content = $this->getProductContent($product['product_url']);
							
							$this->updateBlobField($product['id_product'], "content", $content,false);
						}
					}
					else
					{
						$content = $this->getProductContent($product['product_url']);
						
						$this->updateBlobField($product['id_product'], "content", $content,false);
					}
					
					$this->updateProduct($ac_type, $product['id_product'], $content, $action);
				}
			}

		}
	}
	private function updateProduct($type, $id_product, $content, $action="")
	{
		$ac_types = explode(',',$type);
		
		foreach ($ac_types as $ac_type)
		{
			switch (trim($ac_type)){

				case "name":
					$this->updateProductName($id_product, $content, $action);
				break;
				
				case "comment":
					$this->updateProductComment($id_product, $content, $action);
				break;	

				case "price":
					$this->updateProductPrice($id_product, $content, $action);
				break;	
			}
			
		}
	}
	
	private function updateProductPrice($id_product, $content, $action)
	{
		$product = $this->getProductList($id_product);
		
		$ac_types = explode(',',$action);
		
		foreach ($ac_types as $ac_type)
		{

			switch (trim($ac_type)){
			
				case "load":
					$attributes = $this->getProductAttributes($id_product, $content);
					
					$url = $product[0]['product_url'];
					
					foreach ($attributes as $key => $attribute)
					{
						if ($attribute['ff']>0)
						{
							$this->chengeProductPrice($url,$attribute['ff']);
						
							$price_content = $this->getProductContent($url,true);
						
							$attributes[$key]['price'] = $this->getProductAttributePrice($price_content);
						}
					}
					$a = $attributes;
					$this->updateBlobField($id_product,'price', $attributes);	
					
				break;	

				case "update":
					$attributes = $this->getBlobField($id_product,'price');
					
					$this->updateDiscount($id_product, $content);
					
					$this->updateProductAttributePrice($id_product, $attributes, $product[0]['product_attrgroup']);
					
					//TODO: remake to updateDb1TableFields
					$sql = "update "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME." 
				 		set 
				 		`price_update` = CURRENT_TIMESTAMP
				 		where id_product=".(int) $id_product;
					if (!$links = Db::getInstance()->execute($sql))
						return false;
				break;
			}
		}
	}
	
	private function updateProductAttributePrice($id_product, $attributes, $product_attrgroup="")
	{
		$err_text = "\r\n";
		
		$dbAttributes = $this->getProductAttributesDb($id_product, $product_attrgroup);
		 
		 if ($product_attrgroup=="")
		 {
		 	if(count($attributes)==1)
		 	{
		 		//обновляем цену товара
		 		//$err_text .= "\r\n update price";
		 		$id_product_attribute = $dbAttributes[0]['id_product_attribute'];
		 		$this->updateProductAttributePriceDb(
		 									$id_product, 
		 									$id_product_attribute, 
		 									$attributes[0]['price'],
		 									true
		 									);
		 	}
		 	else
		 	{
		 		$err_text .= "\r\n error 9999";
		 	}
		 }
		 else	
		 {
		 //	$first = true;
		 	$pattributes = array();
			 foreach($attributes as $index => $attribute)
			 {
			 	$keys = false;
			 	foreach($dbAttributes as $key => $dbAttribute)
			 	{
			 		if($attribute['name'] == $dbAttribute['name'])
			 		{
			 			$keys = $key;
			 			continue ;
			 		}
			 	}
			 	
			 	
			 	if($keys!==false)
			 	{
			 		$id_product_attribute = $dbAttributes[$keys]['id_product_attribute'];
			 		
			 		//if ($index>0)
			 			if ($attribute['price']==$attributes[0]['price'])
			 			{
			 				//$first = false;
			 				$this->updateProductAttributePriceDb($id_product, 
			 					$id_product_attribute, $attribute['price'], true);
			 			}else {
			 				$this->updateProductAttributePriceDb($id_product, 
			 					$id_product_attribute, 
			 					$attribute['price']-$attributes[0]['price'], false);
			 			}
			 		
			 		
			 		$attribute['ff'] = "l";
			 		$pattributes[] = $attribute;
			 	}
			 	else
			 	{
			 		// атрибут не найден в БД
			 		$err_text .= "\r\not in db ".$attribute['name'];
			 	}
			 }
			 foreach ($pattributes as $pattribute)
			 {
			 	if ($pattribute['ff']!="l")
			 	{
				 	// атрибута небыло на сайте
			 		$err_text .= "\r\not on site ".$pattribute['name'];
			 	}
			 }
		 }
		 
		 $this->updateDb1TableFields($id_product,"message",$err_text);
	}
	
	private function updateProductAttributePriceDb($id_product, $id_product_attribute, $price, $first=false)
	{
		$id_shop = $this->context->shop->id;
		
		if ($first == true){
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

			$price = 0;
		}
		
		$sql = "update "._DB_PREFIX_."product_attribute  
				 		set 
				 		price = ".$price." 
				 		where id_product=".(int) $id_product."
				 		 and id_product_attribute = ".(int)$id_product_attribute;
		if (!$links = Db::getInstance()->execute($sql))
			return false;
		
		$sql = "update "._DB_PREFIX_."product_attribute_shop  
				 		set 
				 		price = ".$price." 
				 		where id_product_attribute = ".(int)$id_product_attribute."
				 		  and id_shop IN (".implode(', ', Shop::getContextListShopID()).")";
		if (!$links = Db::getInstance()->execute($sql))
			return false;	
			
	}
	

	
  	private function updateDb1TableFields($id_product,$field_name, $value)
	{
		
		$sql = "select id_product from "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME." 
				WHERE `id_product` = ".(int)$id_product;
			
		if (!$links = Db::getInstance()->executeS($sql))
		{
			//insert
			$sql = "insert into "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
				(`id_product`,`".$field_name."`)
				values (".$id_product.", '".$value."')";
			
			if (!$links = Db::getInstance()->execute($sql))
				return false;
		}
		else
		{
			//update
			$sql = "update "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
				set
				`".$field_name."` = '".$value."' 
				where id_product = ".(int)$id_product;
			
			if (!$links = Db::getInstance()->execute($sql))
				return false;
		}
	}
	
	private function getProductAttributesDb($id_product, $product_attrgroup="")
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$id_shop = $this->context->shop->id;
		
		$sql = "select  p.id_product, a.position, pac.id_attribute, 
					al.name, agl.name gname, pa.id_product_attribute
			from ps_product p
			inner join ps_product_attribute pa on
				pa.id_product = p.id_product
			inner join ps_product_attribute_combination pac on
				pac.id_product_attribute = pa.id_product_attribute
			inner join ps_attribute a on
				a.id_attribute = pac.id_attribute
			inner join ps_attribute_shop ash on
				ash.id_attribute = a.id_attribute
			inner join ps_attribute_lang al on
				al.id_attribute = a.id_attribute
			inner join ps_attribute_group ag on
				ag.id_attribute_group = a.id_attribute_group
			inner join ps_attribute_group_lang agl on	
				agl.id_attribute_group = ag.id_attribute_group
			where al.id_lang = ".$default_lang."
			and agl.id_lang = ".$default_lang."
			and ash.id_shop = ".$id_shop."
		    and p.id_product = ".$id_product." ";
		    if($product_attrgroup!="")
		    {
		    	$sql.=" and agl.name = '".$product_attrgroup."' ";
		    }
			$sql.=" order by 1, 2";
		if (!$links = Db::getInstance()->executeS($sql, true, false))
				return false;
		return $links;
	}
	
  	public function insertProductInfo($id_page, $attrgroup, $url_page)
	{
		$sql = "select id_product from "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME." 
				WHERE `id_product` = ".(int)$id_page;
			
		if (!$links = Db::getInstance()->executeS($sql))
		{
		
			$sql = "insert into "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
			(`id_product`, `id_shop`, `product_url`, `product_sname`, 
			`product_attrgroup`, `message`) 
				 values(".$id_page.",NULL,'".$url_page."','','".$attrgroup."','')";
			
			if (!$links = Db::getInstance()->execute($sql))
				return false;
		}
			
		return true;
	}
	
	private function updateDiscount($id_product,$content) 
	{
		$query = ".//p[@class='product_discount']/text()";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);
	    
	    if ($nodes->length==0)
	    	$discount = "0";
		else	 
	    	$discount = $nodes->item(0)->textContent;	

		$sql = $sql = "update "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
			 set 
			 `price_discount` = '".$discount."'
			 where id_product=".(int) $id_product;
		
		if (!$links = Db::getInstance()->execute($sql))
			return false;
			
		return true;	    
	
	}
	
	private function updateBlobField($id_product,$field_name, $attributes,$serialize=true)
	{
		if(!$serialize)
			$array_string=mysql_escape_string($attributes);
		else
			$array_string=mysql_escape_string(serialize($attributes));
		
		$sql = "select id_product from ".self::INSTALL_SQL_BDNAME."."._DB_PREFIX_.self::INSTALL_SQL_BD2NAME." 
				WHERE `id_product` = ".(int)$id_product;
			
		if (!$links = Db::getInstance()->executeS($sql))
		{
			//insert
			$sql = "insert into ".self::INSTALL_SQL_BDNAME."."._DB_PREFIX_.self::INSTALL_SQL_BD2NAME."
				(`id_product`,`".$field_name."`)
				values (".$id_product.", '".$array_string."')";
			
			if (!$links = Db::getInstance()->execute($sql))
				return false;
		}
		else
		{
			//update
			$sql = "update ".self::INSTALL_SQL_BDNAME."."._DB_PREFIX_.self::INSTALL_SQL_BD2NAME."
				set
				`".$field_name."` = '".$array_string."' 
				where id_product = ".(int)$id_product;
			
			if (!$links = Db::getInstance()->execute($sql))
				return false;
		}
	}
	
	private function getBlobField($id_product,$field_name,$serialize=true)
	{
		$sql = "select ".$field_name." from ".self::INSTALL_SQL_BDNAME."."._DB_PREFIX_.self::INSTALL_SQL_BD2NAME." 
				WHERE `id_product` = ".(int)$id_product;
		if (!$links = Db::getInstance()->executeS($sql, true, false))
				return false;
		if (!$serialize)		
			return $links[0][$field_name];
		return unserialize($links[0][$field_name]);
	}
	
	private function chengeProductPrice($url, $ff)
	{
		$url.="/price";
		$data = array('formfactor_id' => $ff);
		
		$ch = curl_init( $url );
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
		curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // переходит по редиректам
		curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
		curl_setopt($ch, CURLOPT_POST,1);
  		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookies/cookie.txt');
  		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$content = curl_exec( $ch );
		curl_close( $ch );
		//return mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");		
	}
	
	private function getProductAttributePrice($content)
	{
		$query = ".//p[@class='price']/span/text()";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);
	    
	    if ($nodes->length==0)
	    	return null;
			 
	    $price = str_replace(' ','',$nodes->item(0)->textContent);	
		
		return $price;
		
	}
	
	private function getProductAttributes($id_product, $content)
	{
		$query = ".//*[@class='ff_selector']/ul/li";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);
	    
	    if ($nodes->length==0)
	    {
	    	$attrs[] = array(
	    			'ff' => 0,
	    			'name' => '',
	    			'price' => $this->getProductAttributePrice($content)
	    		);
	    	return $attrs;
	    }
			
	    foreach ($nodes as $node)
	    {
	    	$ff = $node->getAttribute('data-id');

	    	$attrs[] = array(
	    			'ff' => ($ff=="")?0:$ff,
	    			'name' => trim($node->nodeValue),
	    			'price' => ($ff=="")?$this->getProductAttributePrice($content):0
	    		);
	    }
		
		return $attrs;
	}
	
	private function updateProductName($id_product, $content, $action="")
	{
		$query = ".//*[@class='container_h1']/h1";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);
	    
	    if ($nodes->length==0)
	    	return null;
			
		$sname = trim($nodes->item(0)->nodeValue);
		
		$this->updateBlobField($id_product,'name', $sname, false);
		
		$sql = $sql = "update "._DB_PREFIX_.self::INSTALL_SQL_BD1NAME."
			 set 
			 `product_sname` = '".$sname."'
			 where id_product=".(int) $id_product;
		
		if (!$links = Db::getInstance()->execute($sql))
			return false;
		
		return true;
		
	}
	
	private function getProductContent($url, $cookie_use = false)
	{
		$ch = curl_init( $url );
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
		curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // переходит по редиректам
		curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
		if ($cookie_use)
		{		  
			curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookies/cookie.txt');
		}  
		$content = curl_exec( $ch );
		curl_close( $ch );
		
		$content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
		
		return $content;
	}
	
    private function getProductComments($content)
    {
		$query = ".//*[@class='comment']";
	
		$dom = new DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
	
		$xpath = new DomXPath($dom);
	
   		$nodes = $xpath->query($query);

	    $i = 0;
	    $param = array();
	    if ($nodes->length==0)
	    	return null;
	    foreach( $nodes as $node ) 
	    {

	    	$name = $node->getElementsByTagName("b")->item(0)->nodeValue;
			$text = $node->getElementsByTagName("p")->item(0)->nodeValue;
			$date = $node->getElementsByTagName("span")->item(0)->nodeValue;
			$date = $this->dateCorrect($date);
			$rating = 0;
			$rating_res = $xpath->query("div[@class='rating']/input[@checked='checked']",$node);//
		
			if($rating_res->length==0)
				continue;
			
			$rating = $rating_res->item(0)->getAttribute('value');
			
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
    
    private function dateCorrect($date)
    {
    	$r_from = array('(',')','СЏРЅРІР°СЂСЏ','С„РµРІСЂР°Р»СЏ','РјР°СЂС‚Р°','Р°РїСЂРµР»СЏ','РјР°СЏ','РёСЋРЅСЏ','РёСЋР»СЏ','Р°РІРіСѓСЃС‚Р°','СЃРµРЅС‚СЏР±СЂСЏ','РѕРєС‚СЏР±СЂСЏ','РЅРѕСЏР±СЂСЏ','РґРµРєР°Р±СЂСЏ');
    	
    	$r_to = array('','','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    	
    	$date = str_replace($r_from, $r_to, $date);
    	    	 
    	return date("Y-m-d", strtotime($date));
    } 

	public function install($keep = true)
	{
		if ($keep)
			{
				if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
					return false;
				else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
					return false;
				$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'DB1NAME'), array(_DB_PREFIX_, _MYSQL_ENGINE_, self::INSTALL_SQL_BD1NAME), $sql);
				$sql = preg_split("/;\s*[\r\n]+/", trim($sql));
	
				foreach ($sql as $query)
					if (!Db::getInstance()->execute(trim($query)))
						return false;
	
			}
			
	  if (!parent::install()|| 
		!Configuration::updateValue('EGORMATEKPROD_MANUF', 0)||
		!$this->registerHook('displayBackOfficeHeader')
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
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS
			`'._DB_PREFIX_.self::INSTALL_SQL_BD1NAME.'`');
	}	
	
	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || 
	  		!Configuration::deleteByName('EGORMATEKPROD_MANUF') ||
	  		($keep && !$this->deleteTables())
			)
	    return false;
	  return true;
	}		
	
  }
  
  