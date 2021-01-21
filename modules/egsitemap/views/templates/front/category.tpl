{if isset($pages)}
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$pages item=page}
			<url>
				<loc>{$page.url}</loc>
				<lastmod>{$page.lastmod}</lastmod>
				<changefreq>{$page.cange}</changefreq>
				<priority>{$page.priority}</priority>
			</url>
{/foreach}
</urlset>	
{/if}