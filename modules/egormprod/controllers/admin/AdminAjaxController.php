<?php


class AdminAjaxController extends ModuleAdminController
{
	
	private	$ormprod;
	
	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'view';
		$this->meta_title = $this->l('Your Merchant Expertise');
		
		parent::__construct();
		if (!$this->module->active)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
	}

	public function ajaxProcessInsertProductInfo()
	{
		$this->ormprod = new egormprod();

		$id_page = Tools::getValue('id_page');		
		$attrgroup = Tools::getValue('attrgroup');
		$url_page = Tools::getValue('url_page');
		
		$this->ormprod->insertProductInfo($id_page, $attrgroup, $url_page);
		
	}
		

	public function ajaxProcessUpdateProductInfo()
	{
		$this->ormprod = new egormprod();
		
		$t_tmp = Tools::getValue('uc');
		if ($t_tmp == 'true')
			$action = "load,update";
		else
			$action = "update";
		
		$type = array();	
		if (Tools::getValue('up')=='true')
			$type[] = "price";
		if (Tools::getValue('comment')=='true')
			$type[] = "comment";
		if (Tools::getValue('upname')=='true')
			$type[] = "upname";
		
		$id_page = Tools::getValue('id_page');
		
		$this->ormprod->updateProductInfo($type, $id_page,$action);
		
	}
	
	public function ajaxProcessGetTable()
	{
		
		$this->ormprod = new egormprod();
		
		$list = $this->ormprod->getProductList(0,false,false);
		
		$link = new Link();

		$output = "";		
		foreach($list as $row)
		{

			$output .= "<tr><td>-</td>";
			$output .= "<td>".$row['id_product']."</td>";
			$output .= "<td><span>".$row['product_sname']."</span><br><span style='font-size: 8px;'><a href='".$row['product_url']."'>".$row['product_url']."</a></span></td>";
			$output .= "<td><span>".$row['name']."</span><br><span style='font-size: 8px;'><a href='".$link->getProductLink($row['id_product'])."'>".$link->getProductLink($row['id_product'])."</a></span></td>";
			$output .= "<td>".$row['product_attrgroup']."</td>";
			$output .= "<td>".$row['price_discount']."</td>";
			$output .= "<td>".$row['dname']."</td>";			
			$output .= "<td><a href='#' onclick='UpdateRow(this);return(false);'>R</a>";
			$output .= " <a href='".Context::getContext()->link->getAdminLink('AdminProducts')."&updateproduct&id_product=".(int)$row['id_product']."' target='new'>E</a>";
			$output .= " <a href='#' onclick='InsertRow(this);return(false);'>I</a></td>";
			$output.="</tr>";

		}
		
		die ($output);
	}
	
}
