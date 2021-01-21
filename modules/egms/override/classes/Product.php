<?php

require_once(_PS_MODULE_DIR_.'egms/classes/egms_shop.php');



class Product extends ProductCore

{

	public static function checkAccessStatic($id_product, $id_customer)
	{
		$product = new Product($id_product);
		
		if (!egms_shop::getEgmsAccess($product->id_manufacturer))
			return false;

		return parent::checkAccessStatic($id_product, $id_customer);
	}	
}







