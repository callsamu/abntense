<?php

namespace App\Http\Controllers;


use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ReferenceController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function from_website(Request $request)
    {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];

        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        $resp = Http::withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br, zstd',
            'DNT' => '1',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-User' => '?1',
            'Sec-Ch-Ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
            'Sec-Ch-Ua-Mobile' => '?0',
            'Sec-Ch-Ua-Platform' => '"macOS"',
            'Cache-Control' => 'max-age=0',
        ])
            ->timeout(30)
            ->withOptions([
                'verify' => false,
                'allow_redirects' => true,
            ])
            ->get($url);

        $status = $resp->status();
        $contentType = $resp->header('Content-Type');

        if ($status != 200) {
            return response()->json(['error' => 'Failed to fetch'], $status);
        }

        if (Str::doesntContain($contentType, 'text/html')) {
            return response()->json(['error' => 'Not HTML content'], 404);
        }

        $crawler = new Crawler($resp->body());

        $title = null;
        try {
            $title = $crawler->filter('title')->text();
        } catch (\Exception $e) {
            $title = 'Unknown';
        }

        $author = null;
        $authorStrategies = [
            fn() => $crawler->filter('meta[name="author"]')->attr('content'),
            fn() => $crawler->filter('meta[property="article:author"]')->attr('content'),
            fn() => $crawler->filter('meta[name="parsely-author"]')->attr('content'),

            fn() => $crawler->filter('[itemprop="author"] [itemprop="name"]')->text(),
            fn() => $crawler->filter('[itemprop="author"]')->text(),

            fn() => $crawler->filter('.author-name')->text(),
            fn() => $crawler->filter('.byline')->text(),
            fn() => $crawler->filter('[class*="author"]')->first()->text(),
            fn() => $crawler->filter('a[rel="author"]')->text(),

            fn() => $crawler->filter('article .author')->text(),
            fn() => $crawler->filter('.post-author')->text(),
        ];

        foreach ($authorStrategies as $strategy) {
            try {
                $result = $strategy();
                if ($result && trim($result)) {
                    $author = trim($result);
                    // Clean common prefixes
                    $author = preg_replace('/^(By|by|BY)\s+/i', '', $author);
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Date
        $date = null;
        $dateStrategies = [
            fn() => $crawler->filter('meta[property="article:published_time"]')->attr('content'),
            fn() => $crawler->filter('meta[name="publish-date"]')->attr('content'),
            fn() => $crawler->filter('meta[name="date"]')->attr('content'),
            fn() => $crawler->filter('[itemprop="datePublished"]')->attr('content'),
            fn() => $crawler->filter('time')->attr('datetime'),
        ];

        foreach ($dateStrategies as $strategy) {
            try {
                $result = $strategy();
                if ($result && trim($result)) {
                    $date = trim($result);
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $visited = now()->toDateTimeString();

        return response()->json([
            'title' => $title,
            'author' => $author,
            'date' => $date,
            'visited' => $visited,
        ]);
    }
    //
}
