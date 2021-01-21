<?php

class egmsspecialModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        parent::initContent();

        $this->ajax = true;

        $id_product = Tools::getValue('get_special',0);

        if ($id_product  >0) {

            $id_content = egmsspecialModuleFrontController::getSpecial($id_product)['id_content'];

            $content = $this->getContent($id_content);

            $title = Meta::replaceForCEOWord($content['title']);
            $body = Meta::replaceForCEOWord($content['body']);

            $arr = array(
                "title" => "".$title,
                "content" => "".$body,
                "trigger" => "focus",
                "data-container" => "body",
                "placement" => "bottom"

            );

            die(json_encode($arr));
        }

    }

    public function getContent($id_content)
    {
        $sql = 'select p.*
                from '._DB_PREFIX_.'egms_pages p
                where p.id_page = '.$id_content;
        $res = Db::getInstance()->executeS($sql);
        return $res[0];
    }

    public static function getSpecial($id_product)
    {
        //TODO: should be flexible for shops

        $sql = 'select s.*
                from '._DB_PREFIX_.'egms_special s
                where s.id_product = '.$id_product.
            ' and s.active = 1';
        $res = Db::getInstance()->executeS($sql);
        return $res[0];
    }

}


?>