<?php

class Carrier extends CarrierCore
{
	public static function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY, $clear = false)
	{
		// Filter by groups and no groups => return empty array
		if ($ids_group && (!is_array($ids_group) || !count($ids_group)))
			return array();

		$sql = '
		SELECT c.*, cl.delay
		FROM `'._DB_PREFIX_.'carrier` c
		LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
		LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)'.
		($id_zone ? 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = '.(int)$id_zone.')' : '').'
		'.Shop::addSqlAssociation('carrier', 'c').'
		WHERE c.`deleted` = '.($delete ? '1' : '0');
		if ($active)
			$sql .= ' AND c.`active` = 1 ';
		if ($id_zone)
			$sql .= ' AND cz.`id_zone` = '.(int)$id_zone.' AND z.`active` = 1 ';
		if ($ids_group)
			$sql .= ' AND c.id_carrier IN (SELECT id_carrier FROM '._DB_PREFIX_.'carrier_group WHERE id_group IN ('.implode(',', array_map('intval', $ids_group)).')) ';

		switch ($modules_filters)
		{
			case 1 :
				$sql .= ' AND c.is_module = 0 ';
				break;
			case 2 :
				$sql .= ' AND c.is_module = 1 ';
				break;
			case 3 :
				$sql .= ' AND c.is_module = 1 AND c.need_range = 1 ';
				break;
			case 4 :
				$sql .= ' AND (c.is_module = 0 OR c.need_range = 1) ';
				break;
		}
		$carriers = Shop::getCarriers();
		if ($clear == false)
			if ($carriers !='')
				$sql .= ' AND c.id_carrier in ('.$carriers.') ';
			else 
				$sql .= ' AND 1 = 2'; 
		$sql .= ' GROUP BY c.`id_carrier` ORDER BY c.`position` ASC';


		$cache_id = 'Carrier::getCarriers_'.md5($sql);
		if (!Cache::isStored($cache_id))
		{
			$carriers = Db::getInstance()->executeS($sql);
			Cache::store($cache_id, $carriers);
		}
		$carriers = Cache::retrieve($cache_id);
		foreach ($carriers as $key => $carrier)
			if ($carrier['name'] == '0')
				$carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
		return $carriers;
	}
	
}
