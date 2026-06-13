<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap of the public, indexable pages.
     */
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('login'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('register'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        $lastmod = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($url['loc'])."</loc>\n";
            $xml .= '    <lastmod>'.$lastmod."</lastmod>\n";
            $xml .= '    <changefreq>'.$url['changefreq']."</changefreq>\n";
            $xml .= '    <priority>'.$url['priority']."</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * robots.txt — allow public pages, block authenticated areas, point to the sitemap.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /app/',
            'Disallow: /admin/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
