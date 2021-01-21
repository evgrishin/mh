<?php
/**

 */


//require_once(_PS_MODULE_DIR_.'egms/classes/egms_shop.php');

class egmspage extends ObjectModel
{
	/** @var string Name */
	public $id_page;
	public $page_name;
	public $page_type;
	public $title;
	public $meta;
	public $keywords;
	public $body;
	
	/**
	 * @see ObjectModel::$definition
	 */
	//TODO: add check fot length of fields
	public static $definition = array(
		'table' => 'egms_pages',
		'primary' => 'id_page',
		'fields' => array(
			'id_page' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'page_name' => array('type' => self::TYPE_STRING, 'required' => true),
			'page_type' => array('type' => self::TYPE_STRING, 'required' => true),
			'title' => array('type' => self::TYPE_STRING, 'required' => true),
			'meta' => array('type' => self::TYPE_STRING, 'required' => true),
			'keywords' => array('type' => self::TYPE_STRING, 'required' => true),
			'body' => array('type' => self::TYPE_HTML, 'lang' => false),
		),
	);
/*
	public function add($autodate = true, $null_values = false)
	{

		return parent::add($autodate, $null_values);
	}
*/
	public function delete()
	{
		return false;
	}
	
	public static function getPage($id_page=null)
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'egms_pages';
		if ($id_page != null)
			$sql.= ' WHERE id_page='.(int)$id_page;
		$sql .= ' ORDER BY id_page';
			
		return (Db::getInstance()->executeS($sql));
	}

	public static function getSitePage($type, $id_page = null)
	{
		
		if (is_null($id_page))
		{
			$shop_data = egms_shop::getShopData();
			
			$id_page = $shop_data['page_'.$type];	
		}

		$row = egmspage::getPage($id_page);

		$content = Meta::replaceForCEOWord($row[0]['body']);
		
		return $content;
	}	
	
}