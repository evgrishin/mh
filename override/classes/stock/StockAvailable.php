<?php
class StockAvailable extends StockAvailableCore

{

    public static function getQuantityAvailableByProduct($id_product = null, $id_product_attribute = null, $id_shop = null)
    {
        return 0;
    }

    public static function outOfStock($id_product, $id_shop = null)
    {
        return 0;
    }

}