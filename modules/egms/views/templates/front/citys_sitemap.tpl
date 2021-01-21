<?xml version="1.0" encoding="utf-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {foreach from=$citys item=city}
        <sitemap>
            <loc>{$host}{$map.url}</loc>
            <lastmod>{$map.lastmod}</lastmod>
        </sitemap>
    {/foreach}
</sitemapindex>