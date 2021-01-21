<?xml version="1.0" encoding="utf-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<sitemap>
<loc>{$host}?map=mainmenu</loc>
<lastmod>{$mainmenudate}</lastmod>
</sitemap>
<sitemap>
<loc>{$host}?map=categories</loc>
<lastmod>{$categoriesdate}</lastmod>
</sitemap>
{foreach from=$maps item=map}
<sitemap>
	<loc>{$host}{$map.url}</loc>
	<lastmod>{$map.lastmod}</lastmod>
</sitemap>
{/foreach}
<sitemap>
<loc>{$host}?map=information</loc>
<lastmod>{$informationdate}</lastmod>
</sitemap>
</sitemapindex>