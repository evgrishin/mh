
CREATE TABLE IF NOT EXISTS `PREFIX_egmultishop_url` (
  `id_url` int(11) NOT NULL,
  `city_name` varchar(50) NOT NULL,
  `city1_name` varchar(50) NOT NULL,
  `city2_name` varchar(50) NOT NULL,
  `yandex_verify` varchar(20) NOT NULL,
  `yandex_metr` text NOT NULL,
  `google_verify` varchar(50) NOT NULL,
  `google_anal` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `delivery` text NOT NULL,
  `selfout` text NOT NULL,
  PRIMARY KEY (`id_url`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_egmultishop_url` (`id_url`, `city_name`, `city1_name`, `city2_name`, `yandex_verify`, `yandex_metr`, `google_verify`, `google_anal`, `phone`) VALUES
(1, 'Нижний Новгород', 'Нижнем Новгороде', '', 'wewerrwrwre', 'qweqwe', '777777', '', '8(960)165-25-25', 'доставка НН', 'самовывоз НН'),
(2, 'Нижний Новгород', 'Нижнем Новгороде', '', '', '', '', 'df', '8(960)165-25-25', '0', '0'),
(3, 'Москва', 'Москва', '', '', '', '', '', '8(960)165-25-25', '0', '0');

