<?php

class ShopUrl extends ShopUrlCore

{

	public static function cacheMainDomainForShop($id_shop)

	{

		if (!isset(self::$main_domain_ssl[(int)$id_shop]) || !isset(self::$main_domain[(int)$id_shop]))

		{

			$host = Tools::getHttpHost();

			$row = Db::getInstance()->getRow('

			SELECT domain, domain_ssl

			FROM '._DB_PREFIX_.'shop_url

			WHERE domain = "'.$host.'"

			AND id_shop = '.($id_shop !== null ? (int)$id_shop : (int)Context::getContext()->shop->id));

			self::$main_domain[(int)$id_shop] = $row['domain'];

			self::$main_domain_ssl[(int)$id_shop] = $row['domain_ssl'];

		}

	}



}