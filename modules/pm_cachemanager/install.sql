DROP TABLE IF EXISTS `PREFIX_pm_cachemanager_cache`;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_cachemanager_cache` (
  `cache_id` char(32) NOT NULL,
  `id_cache_content` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NULL,
  `id_group` int(10) unsigned NULL,
  `id_customer` int(10) unsigned NULL,
  `id_product` int(10) unsigned NULL,
  `id_category` int(10) unsigned NULL,
  `id_supplier` int(10) unsigned NULL,
  `id_manufacturer` int(10) unsigned NULL,
  `id_cms` int(10) unsigned NULL,
  `id_hook` int(10) unsigned NULL,
  `id_module` int(10) unsigned NULL,
  PRIMARY KEY (`cache_id`),
  KEY `id_cache_content` (`id_cache_content`),
  KEY `id_shop` (`id_shop`),
  KEY `id_group` (`id_group`),
  KEY `id_customer` (`id_customer`),
  KEY `id_product` (`id_product`),
  KEY `id_category` (`id_category`),
  KEY `id_supplier` (`id_supplier`),
  KEY `id_hook` (`id_hook`),
  KEY `id_module` (`id_module`),
  KEY `id_manufacturer` (`id_manufacturer`),
  KEY `id_cms` (`id_cms`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX_pm_cachemanager_cache_content`;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_cachemanager_cache_content` (
  `id_cache_content` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_md5` char(32) NOT NULL,
  `css_diff` mediumtext NULL,
  `js_diff` mediumtext NULL,
  `jsdef_diff` mediumtext NULL,
  `content` mediumtext NOT NULL,
  `expire` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_cache_content`),
  UNIQUE KEY `content_md5` (`content_md5`),
  KEY `expire` (`expire`),
  KEY `content_md5_2` (`content_md5`, `expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIX_pm_cachemanager_hooks`;

CREATE TABLE IF NOT EXISTS `PREFIX_pm_cachemanager_hooks` (
  `hook_name` varchar(64) NOT NULL,
  `module_name` varchar(64) NOT NULL,
  `lifetime` int(10) NOT NULL,
  `use_global` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`hook_name`,`module_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;