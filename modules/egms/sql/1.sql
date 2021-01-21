insert into `ps_egms_product_tmp`(`id_product`,`id_shop`,`product_name`,`product_description_old`)
SELECT `id_product`, 1, `name`, `description` FROM `ps_product_lang` WHERE 1