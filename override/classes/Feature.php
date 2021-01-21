<?php


class Feature extends FeatureCore
{

    public static function getFeatures_cust($id_lang, $with_shop = true, $id_product)
    {

		$countf = (int)(Db::getInstance()->getValue('select count(*) from `'._DB_PREFIX_.'egms_features`'));
	
        $sql = '
		SELECT DISTINCT f.id_feature, f.*, fl.*
		FROM `'._DB_PREFIX_.'feature` f
		'.($with_shop ? Shop::addSqlAssociation('feature', 'f') : '').'
		LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = '.(int)$id_lang.')';
		if ($countf > 0 ) 
			$sql .= ' INNER ';
		else
			$sql .= ' LEFT ';
		$sql .= ' JOIN `'._DB_PREFIX_.'egms_features` ef on ef.id_feature = f.id_feature ';
        $sql .= ' inner JOIN `' . _DB_PREFIX_ . 'product` p on p.id_product = ' . $id_product . '  and ef.id_category_default = p.id_category_default
		ORDER BY f.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }

}