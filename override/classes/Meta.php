<?php





class Meta extends MetaCore

{



	public static function getMetaTags($id_lang, $page_name, $title = '')

	{

		global $maintenance;

		if (!(isset($maintenance) && (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP'))))))

		{

			if ($page_name == 'product' && ($id_product = Tools::getValue('id_product')))

				return Meta::replaceForCEO(Meta::getProductMetas($id_product, $id_lang, $page_name));

			elseif ($page_name == 'category' && ($id_category = Tools::getValue('id_category')))

				return Meta::replaceForCEO(Meta::getCategoryMetas($id_category, $id_lang, $page_name, $title));

			elseif ($page_name == 'manufacturer' && ($id_manufacturer = Tools::getValue('id_manufacturer')))

				return Meta::replaceForCEO(Meta::getManufacturerMetas($id_manufacturer, $id_lang, $page_name));

			elseif ($page_name == 'supplier' && ($id_supplier = Tools::getValue('id_supplier')))

				return Meta::replaceForCEO(Meta::getSupplierMetas($id_supplier, $id_lang, $page_name));

			elseif ($page_name == 'cms' && ($id_cms = Tools::getValue('id_cms')))

				return Meta::replaceForCEO(Meta::getCmsMetas($id_cms, $id_lang, $page_name));

			elseif ($page_name == 'cms' && ($id_cms_category = Tools::getValue('id_cms_category')))

				return Meta::replaceForCEO(Meta::getCmsCategoryMetas($id_cms_category, $id_lang, $page_name));

		}



		return Meta::replaceForCEO(Meta::getHomeMetas($id_lang, $page_name));

	}	

	

	public static function replaceForCEO($ress)

	{

		$ceo_word = Shop::getCEOData();

		foreach ($ress as $key=>$res)

		{

			$ress[$key] = Meta::sprintf2($ress[$key],$ceo_word);

		}

		return $ress;

	}
	
	public static function replaceForCEOWord($ress)

	{

		$ceo_word = Shop::getCEOData();

		$ress = Meta::sprintf2($ress,$ceo_word);

		return $ress;

	}	

	

	

	public static function sprintf2($str='', $vars=array(), $char='%')

	{

	    if (!$str) return '';

	    if (count($vars) > 0)

	    {

	        foreach ($vars as $k => $v)

	        {

	            $str = str_replace($char . $k, $v, $str);

	        }

	    }

	

	    return $str;

	}

	

}