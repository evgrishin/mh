
CREATE TABLE IF NOT EXISTS `PREFIX_DB1NAME` (
  `id_product` int(11) NOT NULL,
  `id_shop` int(11) NULL,
  `product_url` varchar(70) NOT NULL,
  `product_sname` varchar(128) NULL,
  `comment_update` timestamp,
  `price_update` timestamp,
  `description_update` timestamp,
  `message` varchar(200),  
  PRIMARY KEY (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
