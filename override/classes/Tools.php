<?php



class Tools extends ToolsCore

{

    /**
     * Get the user's journey
     *
     * @param integer $id_category Category ID
     * @param string $path Path end
     * @param boolean $linkOntheLastItem Put or not a link on the current category
     * @param string [optionnal] $categoryType defined what type of categories is used (products or cms)
     */

    public static function getPath($id_category, $path = '', $link_on_the_item = false, $category_type = 'products', Context $context = null)
    {
        if (!$context)
            $context = Context::getContext();

        $id_category = (int)$id_category;
        if ($id_category == 1)
            return '<span class="navigation_end">'.$path.'</span>';

        $pipe = Configuration::get('PS_NAVIGATION_PIPE');
        if (empty($pipe))
            $pipe = '>';

        $json_path = '<script type="application/ld+json">{"@context": "http://schema.org","@type": "BreadcrumbList", "itemListElement": [';
        $json_path .= '{"@type": "ListItem", "position": 1, "item": {"@id": "' . _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . '", "name": "' . $context->shop_info['shop_name'] . ' - ' . $context->shop_info['city_name'] . '"}}';
        $full_path = '';
        if ($category_type === 'products')
        {
            $interval = Category::getInterval($id_category);
            $id_root_category = $context->shop->getCategory();
            $interval_root = Category::getInterval($id_root_category);
            if ($interval)
            {
                $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
						FROM '._DB_PREFIX_.'category c
						LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
						'.Shop::addSqlAssociation('category', 'c').'
						WHERE c.nleft <= '.$interval['nleft'].'
							AND c.nright >= '.$interval['nright'].'
							AND c.nleft >= '.$interval_root['nleft'].'
							AND c.nright <= '.$interval_root['nright'].'
							AND cl.id_lang = '.(int)$context->language->id.'
							AND c.active = 1
							AND c.level_depth > '.(int)$interval_root['level_depth'].'
						ORDER BY c.level_depth ASC';
                $categories = Db::getInstance()->executeS($sql);
                //Tools::addBreadcrumb($_GET['selected_filters']);
                $n = 1;
                $ac = count($context->selected_filter_bread[0]);
                $n_categories = count($categories) + $ac;
                foreach ($categories as $category)
                {

                    $link = Tools::safeOutput($context->link->getCategoryLink((int)$category['id_category'], $category['link_rewrite']));
                    $cat_name = htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8');
                    $cat_name = Meta::replaceForCEOWord($cat_name);

                    $full_path .=
                        (($n < $n_categories || $link_on_the_item) ? '<a href="'.$link.'" title="'.$cat_name.'" data-gg="">' : '').
                        $cat_name.
                        (($n < $n_categories || $link_on_the_item) ? '</a>' : '');

                        if($n != $n_categories || !empty($path))
                            $full_path .='<span class="navigation-pipe">'.$pipe.'</span>';
                    $n++;
                    $json_path .= ',{"@type": "ListItem", "position": '. ($n).', "item": {"@id": "'.$link.'", "name": "'.$cat_name.'"}}';
                }

                foreach ($context->selected_filter_bread[0] as $key => $val)
                {
                    $link = Tools::safeOutput($context->link->getCategoryLink((int)$category['id_category'], $category['link_rewrite']).$context->selected_filter_bread[0][$key]);
                    $cat_name = htmlentities($context->selected_filter_bread[1][$key], ENT_NOQUOTES, 'UTF-8');

                    $full_path .=
                        (($n < $n_categories || $link_on_the_item) ? '<a href="'.$link.'" title="'.$cat_name.'" data-gg="">' : '').
                        $cat_name.
                        (($n < $n_categories || $link_on_the_item) ? '</a>' : '');

                    if($n != $n_categories || !empty($path))
                        $full_path .='<span class="navigation-pipe">'.$pipe.'</span>';
                    $n++;
                    $json_path .= ',{"@type": "ListItem", "position": '. ($n).', "item": {"@id": "'.$link.'", "name": "'.$cat_name.'"}}';
                }

                $json_path .= ']}</script>';

                return $json_path.$full_path.$path;
            }
        }
        elseif ($category_type === 'CMS')
        {
            $category = new CMSCategory($id_category, $context->language->id);
            if (!Validate::isLoadedObject($category))
                die(Tools::displayError());
            $category_link = $context->link->getCMSCategoryLink($category);

            if ($path != $category->name)
                $full_path .= '<a href="'.Tools::safeOutput($category_link).'" data-gg="">'.htmlentities($category->name, ENT_NOQUOTES, 'UTF-8').'</a><span class="navigation-pipe">'.$pipe.'</span>'.$path;
            else
                $full_path = ($link_on_the_item ? '<a href="'.Tools::safeOutput($category_link).'" data-gg="">' : '').htmlentities($path, ENT_NOQUOTES, 'UTF-8').($link_on_the_item ? '</a>' : '');

            return Tools::getPath($category->id_parent, $full_path, $link_on_the_item, $category_type);
        }
    }

    public static function addBreadcrumb($selected_filter)
    {

    }

    public static function viberSend($method, $message = '')
    {

	//$url = "https://api.telegram.org/bot1391695820:AAFOCXE2Sle-6VBOAI2leETXw7fTF-pwccM/sendMessage?chat_id=1326633739&text=dasaasd";
	$url = 'https://api.telegram.org/bot1391695820:AAFOCXE2Sle-6VBOAI2leETXw7fTF-pwccM/sendMessage?chat_id=-406760551&text='.urlencode($message);
    
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Не возвращать ответ
	curl_exec($ch); // Делаем запрос
	curl_close($ch);


/*        if ($method == 'init')
        {
            $message_json = '{"url": "https://yurl.ru", "event_types":["conversation_started" ]}';
            Tools::viberRequest('set_webhook', $message);
        }
        if ($method == 'message')
        {
            $users = Configuration::get('EGMS_VIBER_USERS');

            $message_json['broadcast_list'] =explode(',',$users);
            $message_json['type'] = "text";
            $message_json['sender'] = ['name' => 'BOT'];
            $message_json['text'] = $message;
            Tools::viberRequest('broadcast_message', $message_json);
        }
        if ($method == 'post')
        {

        }*/
    }

    public static function viberRequest($method, $message)
    {
        $token = Configuration::get('EGMS_VIBER_TOKEN');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://chatapi.viber.com/pa/".$method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => JSON_encode($message),
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/JSON",
                "X-Viber-Auth-Token: ".$token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        //if ($err) {
        //    echo "cURL Error #:" . $err;
        //} else {
        //    echo $response;
        //}
    }

	public static function generateHtaccess($path = null, $rewrite_settings = null, $cache_control = null, $specific = '', $disable_multiviews = null, $medias = false, $disable_modsec = null)

	{

		if (defined('PS_INSTALLATION_IN_PROGRESS') && $rewrite_settings === null)

			return true;



		// Default values for parameters

		if (is_null($path))

			$path = _PS_ROOT_DIR_.'/.htaccess';

		if (is_null($cache_control))

			$cache_control = (int)Configuration::get('PS_HTACCESS_CACHE_CONTROL');

		if (is_null($disable_multiviews))

			$disable_multiviews = (int)Configuration::get('PS_HTACCESS_DISABLE_MULTIVIEWS');



		if ($disable_modsec === null)

			$disable_modsec =  (int)Configuration::get('PS_HTACCESS_DISABLE_MODSEC');



		// Check current content of .htaccess and save all code outside of prestashop comments

		$specific_before = $specific_after = '';

		if (file_exists($path))

		{

			$content = file_get_contents($path);

			if (preg_match('#^(.*)\# ~~start~~.*\# ~~end~~[^\n]*(.*)$#s', $content, $m))

			{

				$specific_before = $m[1];

				$specific_after = $m[2];

			}

			else

			{

				// For retrocompatibility

				if (preg_match('#\# http://www\.prestashop\.com - http://www\.prestashop\.com/forums\s*(.*)<IfModule mod_rewrite\.c>#si', $content, $m))

					$specific_before = $m[1];

				else

					$specific_before = $content;

			}

		}



		// Write .htaccess data

		if (!$write_fd = @fopen($path, 'w'))

			return false;

		if ($specific_before)

			fwrite($write_fd, trim($specific_before)."\n\n");



		$domains = array();

		foreach (ShopUrl::getShopUrls() as $shop_url)

		{

			if (!isset($domains[$shop_url->domain]))

				$domains[$shop_url->domain] = array();



			$domains[$shop_url->domain][] = array(

				'physical' =>	$shop_url->physical_uri,

				'virtual' =>	$shop_url->virtual_uri,

				'id_shop' =>	$shop_url->id_shop

			);

							

			if (!$shop_url->main){

				$url = Tools::getSubdomain($shop_url->domain);

				$domains[$url[1].'.'.$url[0]][] = array(

					'physical' =>	$shop_url->physical_uri,

					'virtual' =>	(array_count_values($url)>2)?$url[2].'/':'',

					'id_shop' =>	$shop_url->id_shop

				);

								

			}



			if ($shop_url->domain == $shop_url->domain_ssl)

				continue;



			if (!isset($domains[$shop_url->domain_ssl]))

				$domains[$shop_url->domain_ssl] = array();



			$domains[$shop_url->domain_ssl][] = array(

				'physical' =>	$shop_url->physical_uri,

				'virtual' =>	$shop_url->virtual_uri,

				'id_shop' =>	$shop_url->id_shop

			);

		}		



		// Write data in .htaccess file

		fwrite($write_fd, "# ~~start~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again\n");

		fwrite($write_fd, "# .htaccess automaticaly generated by PrestaShop e-commerce open-source solution\n");

		fwrite($write_fd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n\n");



		if ($disable_modsec)

			fwrite($write_fd, "<IfModule mod_security.c>\nSecFilterEngine Off\nSecFilterScanPOST Off\n</IfModule>\n\n");



		// RewriteEngine

		fwrite($write_fd, "<IfModule mod_rewrite.c>\n");



		// Ensure HTTP_MOD_REWRITE variable is set in environment

		fwrite($write_fd, "<IfModule mod_env.c>\n");

		fwrite($write_fd, "SetEnv HTTP_MOD_REWRITE On\n");

		fwrite($write_fd, "</IfModule>\n\n");



		// Disable multiviews ?

		if ($disable_multiviews)

			fwrite($write_fd, "\n# Disable Multiviews\nOptions -Multiviews\n\n");



		fwrite($write_fd, "RewriteEngine on\n");



		if (!$medias && defined('_MEDIA_SERVER_1_') && defined('_MEDIA_SERVER_2_') && defined('_MEDIA_SERVER_3_'))

			$medias = array(_MEDIA_SERVER_1_, _MEDIA_SERVER_2_, _MEDIA_SERVER_3_);



		$media_domains = '';

		if ($medias[0] != '')

			$media_domains = 'RewriteCond %{HTTP_HOST} ^'.$medias[0].'$ [OR]'."\n";

		if ($medias[1] != '')

			$media_domains .= 'RewriteCond %{HTTP_HOST} ^'.$medias[1].'$ [OR]'."\n";

		if ($medias[2] != '')

			$media_domains .= 'RewriteCond %{HTTP_HOST} ^'.$medias[2].'$ [OR]'."\n";



		if (Configuration::get('PS_WEBSERVICE_CGI_HOST'))

			fwrite($write_fd, "RewriteCond %{HTTP:Authorization} ^(.*)\nRewriteRule . - [E=HTTP_AUTHORIZATION:%1]\n\n");



		foreach ($domains as $domain => $list_uri)

		{

			$physicals = array();

			foreach ($list_uri as $uri)

			{

				fwrite($write_fd, PHP_EOL.PHP_EOL.'#Domain: '.$domain.PHP_EOL);

				if (Shop::isFeatureActive())

					fwrite($write_fd, 'RewriteCond %{HTTP_HOST} ^'.$domain.'$'."\n");

				fwrite($write_fd, 'RewriteRule . - [E=REWRITEBASE:'.$uri['physical'].']'."\n");



				// Webservice

				fwrite($write_fd, 'RewriteRule ^api$ api/ [L]'."\n\n");

				fwrite($write_fd, 'RewriteRule ^api/(.*)$ %{ENV:REWRITEBASE}webservice/dispatcher.php?url=$1 [QSA,L]'."\n\n");



				if (!$rewrite_settings)

					$rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS', null, null, (int)$uri['id_shop']);



				$domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^'.$domain.'$'."\n";

				// Rewrite virtual multishop uri

				if ($uri['virtual'])

				{

					if (!$rewrite_settings)

					{

						fwrite($write_fd, $media_domains);

						if (Shop::isFeatureActive())

							fwrite($write_fd, $domain_rewrite_cond);

						fwrite($write_fd, 'RewriteRule ^'.trim($uri['virtual'], '/').'/?$ '.$uri['physical'].$uri['virtual']."index.php [L,R]\n");

					}

					else

					{

						fwrite($write_fd, $media_domains);

						if (Shop::isFeatureActive())

							fwrite($write_fd, $domain_rewrite_cond);

						fwrite($write_fd, 'RewriteRule ^'.trim($uri['virtual'], '/').'$ '.$uri['physical'].$uri['virtual']." [L,R]\n");

					}

					fwrite($write_fd, $media_domains);

					if (Shop::isFeatureActive())

						fwrite($write_fd, $domain_rewrite_cond);

					fwrite($write_fd, 'RewriteRule ^'.ltrim($uri['virtual'], '/').'(.*) '.$uri['physical']."$1 [L]\n\n");

				}



				if ($rewrite_settings)

				{

					// Compatibility with the old image filesystem

					fwrite($write_fd, "# Images\n");

					if (Configuration::get('PS_LEGACY_IMAGES'))

					{

						fwrite($write_fd, $media_domains);

						if (Shop::isFeatureActive())

							fwrite($write_fd, $domain_rewrite_cond);

						fwrite($write_fd, 'RewriteRule ^([a-z0-9]+)\-([a-z0-9]+)(\-[_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1-$2$3$4.jpg [L]'."\n");

						fwrite($write_fd, $media_domains);

						if (Shop::isFeatureActive())

							fwrite($write_fd, $domain_rewrite_cond);

						fwrite($write_fd, 'RewriteRule ^([0-9]+)\-([0-9]+)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1-$2$3.jpg [L]'."\n");

					}



					// Rewrite product images < 100 millions

					for ($i = 1; $i <= 8; $i++)

					{

						$img_path = $img_name = '';

						for ($j = 1; $j <= $i; $j++)

						{

							$img_path .= '$'.$j.'/';

							$img_name .= '$'.$j;

						}

						$img_name .= '$'.$j;

						fwrite($write_fd, $media_domains);

						if (Shop::isFeatureActive())

							fwrite($write_fd, $domain_rewrite_cond);

						fwrite($write_fd, 'RewriteRule ^'.str_repeat('([0-9])', $i).'(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/'.$img_path.$img_name.'$'.($j + 1).".jpg [L]\n");

					}

					fwrite($write_fd, $media_domains);

					if (Shop::isFeatureActive())

						fwrite($write_fd, $domain_rewrite_cond);

					fwrite($write_fd, 'RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/c/$1$2$3.jpg [L]'."\n");

					fwrite($write_fd, $media_domains);

					if (Shop::isFeatureActive())

						fwrite($write_fd, $domain_rewrite_cond);

					fwrite($write_fd, 'RewriteRule ^c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/c/$1$2.jpg [L]'."\n");

				}



				fwrite($write_fd, "# AlphaImageLoader for IE and fancybox\n");

				if (Shop::isFeatureActive())

					fwrite($write_fd, $domain_rewrite_cond);

				fwrite($write_fd, 'RewriteRule ^images_ie/?([^/]+)\.(jpe?g|png|gif)$ js/jquery/plugins/fancybox/images/$1.$2 [L]'."\n");

			}

			// Redirections to dispatcher

			if ($rewrite_settings)

			{

				fwrite($write_fd, "\n# Dispatcher\n");

				fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -s [OR]\n");

				fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -l [OR]\n");

				fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -d\n");

				if (Shop::isFeatureActive())

					fwrite($write_fd, $domain_rewrite_cond);

				fwrite($write_fd, "RewriteRule ^.*$ - [NC,L]\n");

				if (Shop::isFeatureActive())

					fwrite($write_fd, $domain_rewrite_cond);

				fwrite($write_fd, "RewriteRule ^.*\$ %{ENV:REWRITEBASE}index.php [NC,L]\n");

			}

		}



		fwrite($write_fd, "</IfModule>\n\n");



		fwrite($write_fd, "AddType application/vnd.ms-fontobject .eot\n");

		fwrite($write_fd, "AddType font/ttf .ttf\n");

		fwrite($write_fd, "AddType font/otf .otf\n");

		fwrite($write_fd, "AddType application/x-font-woff .woff\n");

		fwrite($write_fd, "<IfModule mod_headers.c>

	<FilesMatch \"\.(ttf|ttc|otf|eot|woff|svg)$\">

		Header add Access-Control-Allow-Origin \"*\"

	</FilesMatch>

</IfModule>\n\n");



		// Cache control

		if ($cache_control)

		{

			$cache_control = "<IfModule mod_expires.c>

	ExpiresActive On

	ExpiresByType image/gif \"access plus 1 month\"

	ExpiresByType image/jpeg \"access plus 1 month\"

	ExpiresByType image/png \"access plus 1 month\"

	ExpiresByType text/css \"access plus 1 week\"

	ExpiresByType text/javascript \"access plus 1 week\"

	ExpiresByType application/javascript \"access plus 1 week\"

	ExpiresByType application/x-javascript \"access plus 1 week\"

	ExpiresByType image/x-icon \"access plus 1 year\"

	ExpiresByType image/svg+xml \"access plus 1 year\"

	ExpiresByType image/vnd.microsoft.icon \"access plus 1 year\"

	ExpiresByType application/font-woff \"access plus 1 year\"

	ExpiresByType application/x-font-woff \"access plus 1 year\"

	ExpiresByType application/vnd.ms-fontobject \"access plus 1 year\"

	ExpiresByType font/opentype \"access plus 1 year\"

	ExpiresByType font/ttf \"access plus 1 year\"

	ExpiresByType font/otf \"access plus 1 year\"

	ExpiresByType application/x-font-ttf \"access plus 1 year\"

	ExpiresByType application/x-font-otf \"access plus 1 year\"

</IfModule>



<IfModule mod_headers.c>

	Header unset Etag

</IfModule>

FileETag none

<IfModule mod_deflate.c>

	<IfModule mod_filter.c>

		AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/x-javascript font/ttf application/x-font-ttf font/otf application/x-font-otf font/opentype

	</IfModule>

</IfModule>\n\n";

			fwrite($write_fd, $cache_control);

		}



		// In case the user hasn't rewrite mod enabled

		fwrite($write_fd, "#If rewrite mod isn't enabled\n");



		// Do not remove ($domains is already iterated upper)

		reset($domains);

		$domain = current($domains);

		fwrite($write_fd, 'ErrorDocument 404 '.$domain[0]['physical']."index.php?controller=404\n\n");



		fwrite($write_fd, "# ~~end~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again");

		if ($specific_after)

			fwrite($write_fd, "\n\n".trim($specific_after));

		fclose($write_fd);



		if (!defined('PS_INSTALLATION_IN_PROGRESS'))

			Hook::exec('actionHtaccessCreate');



		return true;

	}

	

	public static function getSubdomain($domain = "")

	{

		$d = ($domain == "")?Tools::getHttpHost():$domain;

		return list($x1,$x2,$x3)=array_reverse(explode('.',$d));	

	}	

	

	public static function getMaindomain($domain = "")

	{

		$d = ($domain == "")?Tools::getHttpHost():$domain;

		$l = array_reverse(explode('.',$d));

		return 	(array_count_values($l)>1)?$l[1].'.'.$l[0]:$d;

	}		

	

}
