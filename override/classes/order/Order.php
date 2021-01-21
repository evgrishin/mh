<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Order extends OrderCore
{

	public function getShipping()
	{
		return Db::getInstance()->executeS('
			SELECT DISTINCT oc.`id_order_invoice`, oc.`weight`, oc.`shipping_cost_tax_excl`, oc.`shipping_cost_tax_incl`, c.`url`, oc.`id_carrier`, c.`name` as `carrier_name`, oc.`date_add`, "Delivery" as `type`, "true" as `can_edit`, oc.`tracking_number`, o.delivery_date, oc.`id_order_carrier`, osl.`name` as order_state_name, c.`name` as state_name
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh
				ON (o.`id_order` = oh.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'order_carrier` oc
				ON (o.`id_order` = oc.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'carrier` c
				ON (oc.`id_carrier` = c.`id_carrier`)
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl
				ON (oh.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE o.`id_order` = '.(int)$this->id.'
			GROUP BY c.id_carrier');
	}
	
	public function getUniqReference()
	{
		return $this->id;
	}


	public static function getUniqReferenceOf($id_order)
	{
		return $id_order;
	}	

}
