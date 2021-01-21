<?php


class egsitemapsitemapModuleFrontController extends ModuleFrontController
{
	private $host;
	
	
	public function __construct()
	{
		 parent::__construct();
		$this->host = Tools::getHttpHost(true);
	}
	
	
	public function initContent()
	{
		
		parent::initContent();

		$this->ajax = true;	
		
		$dati = Configuration::get('EGSITEMAP_INF_DATI'); 
		if (Tools::getValue('map'))
		{
			$map = Tools::getValue('map');
			
			switch ($map){
				case "mainmenu":
					$period = Configuration::get('EGSITEMAP_MAIN_PERIOD');
					$prior = Configuration::get('EGSITEMAP_MAIN_PRIOR');
					$pages = $this->getMainmenu($period, $prior);
				break;
				case "categories":
					$period = Configuration::get('EGSITEMAP_CAT_PERIOD');
					$prior = Configuration::get('EGSITEMAP_CAT_PRIOR');
					$pages = $this->getCategories($period, $prior);
				break;
				case "information":
					$period = Configuration::get('EGSITEMAP_INF_PERIOD');
					$prior = Configuration::get('EGSITEMAP_INF_PRIOR');
					$pages = $this->getInformation($dati, $period, $prior);
				break;
				default:
					$period = Configuration::get('EGSITEMAP_MAP_PERIOD');
					$prior = Configuration::get('EGSITEMAP_MAP_PRIOR');
					$pages = $this->getPagesByCategories('weekly', '0.8', $map);
				break;
				
			}		
				if (!empty($pages))
				{
					$this->context->smarty->assign(array(
						'pages' => $pages
					));
				}
				
				$this->smartyOutputContent($this->getTemplatePath('category.tpl'));			
		}
		else 
		{
			$maps = $this->getSitemaps();
			$this->context->smarty->assign(array(
				'host' => $this->context->link->getModuleLink('egsitemap', 'sitemap'),
				'maps' => $maps,
				'mainmenudate' => $this->getMainmenu("", "", true),
				'categoriesdate' => $this->getCategories("", "", true),
				'informationdate' => $dati
			));
						
			$this->smartyOutputContent($this->getTemplatePath('default.tpl'));
		}
	
	}
	
	public function getSitemaps()
	{
				$sql = "select ps_category.id_category,
					date(ps_category.date_upd) date_upd,
					ps_category_lang.link_rewrite  
					from ps_category
					inner join ps_category_lang on	
						ps_category.id_category = ps_category_lang.id_category
					where ps_category_lang.id_shop = ".$this->context->shop->id."
					and ps_category.active = 1
					and ps_category.level_depth >= 2
					and ps_category.id_category in (
							select id_category_default 
							from ps_product 
							where active = 1 
							group by id_category_default
							)
					group by ps_category.id_category,
					ps_category.date_upd,
					ps_category_lang.link_rewrite";
			
		if ($results = Db::getInstance()->ExecuteS($sql))
		{
		  	foreach ($results as $row){
	    		
	    		$dati = $row['date_upd'];
	    		

	    			//$url = $this->host.'mainsitemap/'.$row['id_category'].'-'.$row['link_rewrite'];
	    			$url = '?map='.$row['link_rewrite'];

	    			$maps[] = array(
	    				'url' => $url,
	    				'lastmod' => $dati
					);
	    	}				
		}
		return $maps;
	}
	
	public function getMainmenu($cange, $period, $maxdate=false)
	{

		$sql = "select ps_category.id_category,
					date(ps_category.date_upd) date_upd,
					ps_category_lang.link_rewrite  
					from ps_category
					inner join ps_category_lang on	
						ps_category.id_category = ps_category_lang.id_category
					where ps_category_lang.id_shop = ".$this->context->shop->id."
					and ps_category.active = 1
					and ps_category.level_depth in (1,2)";
		
					($maxdate)?$sql.=" order by 2 desc":"";
			
		if ($results = Db::getInstance()->ExecuteS($sql))
		{
			if($maxdate)
				return $results[0]['date_upd'];
			
	    	foreach ($results as $row){
	    		
	    		$dati = $row['date_upd'];
	    		
	    		if ($row['link_rewrite']!="home")
	    			$link = $this->host.'/'.$row['id_category'].'-'.$row['link_rewrite'];
	    		else
	    		{
	    			$link = $this->host.'/';
	    			// float date is on
	    			if(Configuration::get('EGSITEMAP_MAIN_FDAT'))
	    			{
	    				$dati = date('Y-m-d');
	    			}
	    		}	
	    			$pages[] = array(
	    				'url' => $link,
	    				'lastmod' => $dati,
	    				'cange' => $cange,
	    				'priority' => $period
					);
	    	}
		}	
		return $pages;
	}
	
	public function getCategories($cange, $period, $maxdate=false)
	{
        $pages = '';

		$sql = "select ps_category.id_category,
					date(ps_category.date_upd) date_upd,
					ps_category_lang.link_rewrite  
					from ps_category
					inner join ps_category_lang on	
						ps_category.id_category = ps_category_lang.id_category
					where ps_category_lang.id_shop = ".$this->context->shop->id."
					and ps_category.active = 1
					and ps_category.id_category not in (select id_category_redirect 
					                                    from ps_egms_filter 
					                                    where `index` = 1 
					                                    and ps_egms_filter.id_shop = ".$this->context->shop->id.")
					and ps_category.level_depth >= 3";
		
		($maxdate)?$sql.=" order by 2 desc":"";
			
		if ($results = Db::getInstance()->ExecuteS($sql)) {
            if ($maxdate)
                return $results[0]['date_upd'];

            foreach ($results as $row) {
                $pages[] = array(
                    'url' => Context::getContext()->link->getCategoryLink($row['id_category']),
                    'lastmod' => $row['date_upd'],
                    'cange' => $cange,
                    'priority' => $period
                );
            }
        }

        $sql = 'SELECT * FROM '._DB_PREFIX_.'egms_filter';
        $sql.= " WHERE `index` = 1 and id_shop=".(int)Context::getContext()->shop->id;

        if ($results = Db::getInstance()->ExecuteS($sql)) {

            foreach ($results as $row) {
                $pages[] = array(
                    'url' => Context::getContext()->link->getCategoryLink($row['id_category'], null, null).$row['url'],
                    'lastmod' => $row['f_date'],
                    'cange' => $cange,
                    'priority' => $period
                );
            }

		}	
				
		return $pages;
	}

	public function getInformation($dates, $cange, $period)
	{
		$text = Configuration::get('EGSITEMAP_INF');
		$urls = explode(',',$text);
		if (is_array($urls))
		{
			foreach ($urls as $url)
			{
				$pages[] = array(
		    		'url' => $this->host.'/'.trim($url),
		    		'lastmod' => $dates,
		    		'cange' => $cange,
		    		'priority' => $period
				);
			}
		}
		return $pages;
	}	
	
	public function getPagesByCategories($cange, $period, $categories)
	{
				
		   		$sql = "select ps_product_lang.id_product, 
		   				ps_product_lang.link_rewrite, date(ps_product.date_upd) date_upd
					from ps_product
					inner join ps_product_lang on	
						ps_product.id_product = ps_product_lang.id_product
					inner join ps_category_lang on
						ps_category_lang.id_category = ps_product.id_category_default
					where ps_product_lang.id_shop = ".$this->context->shop->id."
					and ps_category_lang.id_shop = ".$this->context->shop->id."
					and ps_category_lang.link_rewrite='".$categories."'
					and ps_product.active = 1";
		   		
				/*
		   		$sql = "select ps_product_lang.id_product, 
		   				ps_product_lang.link_rewrite, date(ps_product.date_upd) date_upd
					from ps_product
					inner join ps_product_lang on	
						ps_product.id_product = ps_product_lang.id_product
					inner join ps_category_product on
						ps_category_product.id_product = ps_product.id_product
					inner join ps_category_lang on
						ps_category_lang.id_category = ps_category_product.id_category
					where ps_product_lang.id_shop = ".$this->context->shop->id."
					and ps_category_lang.id_shop = ".$this->context->shop->id."
					and ps_category_lang.link_rewrite='".$categories."'
					and ps_product.active = 1";
		   		*/
			$pages = array();
			if ($results = Db::getInstance()->ExecuteS($sql))
			{
    			foreach ($results as $row)
    				$pages[] = array(
    						'url' => $this->host.'/'.$categories.'/'.$row['id_product'].'-'.$row['link_rewrite'].'.html',
    						'lastmod' => $row['date_upd'],
    						'cange' => $cange,
    						'priority' => $period
			 			);
			}
			return $pages;
	}
	

}


?>