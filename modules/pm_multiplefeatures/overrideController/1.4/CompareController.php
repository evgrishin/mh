<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 16477 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CompareController extends CompareControllerCore
{

	public function process()
	{

		//Clean compare product table
		CompareProduct::cleanCompareProducts('week');

		$hasProduct = false;

		if (!Configuration::get('PS_COMPARATOR_MAX_ITEM'))
				return Tools::redirect('404.php');

		if ($product_list = Tools::getValue('compare_product_list') AND $postProducts = (isset($product_list) ? rtrim($product_list,'|') : ''))
			$ids = array_unique(explode('|', $postProducts));
		elseif (isset(self::$cookie->id_compare))
			$ids = CompareProduct::getCompareProducts(self::$cookie->id_compare);
		else
			$ids = null;

		if ($ids)
		{
			if (sizeof($ids) > 0)
			{
				if (sizeof($ids) > Configuration::get('PS_COMPARATOR_MAX_ITEM'))
					$ids = array_slice($ids, 0,  Configuration::get('PS_COMPARATOR_MAX_ITEM'));

				$listProducts = array();
				$listFeatures = array();

				foreach ($ids AS $id)
				{
					$curProduct = new Product((int)($id), true, (int)(self::$cookie->id_lang));
					if (!Validate::isLoadedObject($curProduct))
						continue;
					if (!$curProduct->active)
					{
						unset($ids[$k]);
						continue;
					}
					foreach ($curProduct->getFrontFeatures(self::$cookie->id_lang) AS $key=>$feature) {
						$listFeatures[$curProduct->id][$feature['id_feature']] = Module::getInstanceByName('pm_multiplefeatures')->getFrontFeatures($curProduct->id, null, $feature['id_feature']);
					}

					$cover = Product::getCover((int)$id);

					$curProduct->id_image = Tools::htmlentitiesUTF8(Product::defineProductImage(array('id_image' => $cover['id_image'], 'id_product' => $id), self::$cookie->id_lang));
					$curProduct->allow_oosp = Product::isAvailableWhenOutOfStock($curProduct->out_of_stock);
					$listProducts[] = $curProduct;
				}

				if (sizeof($listProducts) > 0)
				{
					$width = 80 / sizeof($listProducts);

					$hasProduct = true;
					$ordered_features = Feature::getFeaturesForComparison($ids, self::$cookie->id_lang);
					self::$smarty->assign(array(
						'ordered_features' => $ordered_features,
						'product_features' => $listFeatures,
						'products' => $listProducts,
						'link' => new Link(),
						'width' => $width,
						'homeSize' => Image::getSize('home')
					));
					self::$smarty->assign('HOOK_EXTRA_PRODUCT_COMPARISON', Module::hookExec('extraProductComparison', array('list_ids_product' => $ids)));
				}
			}
		}
		self::$smarty->assign('hasProduct', $hasProduct);
	}

}

