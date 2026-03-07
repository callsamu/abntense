<?php

namespace App\Services;

use App\Data\Reference\EntryType;
use App\Data\Reference\PersonData;
use App\Data\Reference\ReferenceData;
use App\Exceptions\WebsiteMetadataException;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class WebsiteMetadataExtractor
{
    private const TIMEOUT = 30;
    private const MAX_REDIRECTS = 5;
    private readonly array $authorStrategies;
    private readonly array $dateStrategies;

    public function __construct() {
        $this->initializeStrategies();
    }

    /**
     * Extract metadata from a website URL
     *
     * @throws WebsiteMetadataException|ConnectionException
     */
    public function extract(string $url): ReferenceData
    {
        $this->validateUrl($url);

        $html = $this->fetchHtml($url);
        $crawler = new Crawler($html);

        $author = PersonData::collect([
            PersonData::fromString($this->extractAuthor($crawler)),
        ]);

        $title = $this->extractTitle($crawler);
        $date = $this->extractDate($crawler);

        $data = ReferenceData::from([
            'type' => EntryType::Web,
            'title' => $title,
            'author' => $author,
            'date' => $date,
            'url' => $url,
            'accessed' => CarbonImmutable::now(),
        ]);

        return $data;
    }

    /**
     * @throws WebsiteMetadataException
     */
    private function validateUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new WebsiteMetadataException('Invalid URL format');
        }
    }

    /**
     * @throws WebsiteMetadataException
     * @throws ConnectionException
     */
    private function fetchHtml(string $url): string
    {
        $parsedUrl = parse_url($url);
        $domain = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');

        try {
            $response = Http::withHeaders($this->getBrowserHeaders($domain))
                ->timeout(self::TIMEOUT)
                ->withOptions([
                    'verify' => false,
                    'allow_redirects' => [
                        'max' => self::MAX_REDIRECTS,
                        'strict' => true,
                    ],
                ])
                ->get($url);

        } catch (RequestException $e) {
            throw new WebsiteMetadataException(
                "Failed to fetch URL: {$e->getMessage()}",
                previous: $e
            );
        }

        if ($response->status() !== 200) {
            throw new WebsiteMetadataException(
                "HTTP {$response->status()}: Failed to fetch URL",
                code: $response->status()
            );
        }

        $contentType = $response->header('Content-Type');

        if (!Str::contains($contentType, 'text/html')) {
            throw new WebsiteMetadataException(
                "Invalid content type: $contentType. Expected text/html"
            );
        }

        return $response->body();
    }

    private function extractTitle(Crawler $crawler): string
    {
        try {
            return $crawler->filter('title')->text();
        } catch (\Exception $e) {
            // Fallback strategies
            try {
                return $crawler->filter('meta[property="og:title"]')->attr('content');
            } catch (\Exception $e) {
                try {
                    return $crawler->filter('h1')->first()->text();
                } catch (\Exception $e) {
                    return 'Unknown';
                }
            }
        }
    }

    private function extractAuthor(Crawler $crawler): ?string
    {
        foreach ($this->authorStrategies as $strategy) {
            try {
                $result = $strategy($crawler);

                if ($result && trim($result)) {
                    return $this->cleanAuthor(trim($result));
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    private function extractDate(Crawler $crawler): ?CarbonImmutable
    {
        foreach ($this->dateStrategies as $strategy) {
            try {
                $result = $strategy($crawler);

                if ($result && trim($result)) {
                    return $this->parseDate(trim($result));
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    private function cleanAuthor(string $author): string
    {
        // Remove common prefixes
        $author = preg_replace('/^(By|by|BY)\s+/i', '', $author);

        // Remove extra whitespace
        $author = preg_replace('/\s+/', ' ', $author);

        return trim($author);
    }

    private function parseDate(string $date): ?CarbonImmutable
    {
        try {
            return CarbonImmutable::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function initializeStrategies(): void
    {
        if (empty($this->authorStrategies)) {
            $this->authorStrategies = [
                // Meta tags
                fn(Crawler $c) => $c->filter('meta[name="author"]')->attr('content'),
                fn(Crawler $c) => $c->filter('meta[property="article:author"]')->attr('content'),
                fn(Crawler $c) => $c->filter('meta[name="parsely-author"]')->attr('content'),

                // Schema.org
                fn(Crawler $c) => $c->filter('[itemprop="author"] [itemprop="name"]')->text(),
                fn(Crawler $c) => $c->filter('[itemprop="author"]')->text(),

                // Common CSS classes
                fn(Crawler $c) => $c->filter('.author-name')->text(),
                fn(Crawler $c) => $c->filter('.byline')->text(),
                fn(Crawler $c) => $c->filter('[class*="author"]')->first()->text(),
                fn(Crawler $c) => $c->filter('a[rel="author"]')->text(),

                // Structural
                fn(Crawler $c) => $c->filter('article .author')->text(),
                fn(Crawler $c) => $c->filter('.post-author')->text(),
            ];
        }

        if (empty($this->dateStrategies)) {
            $this->dateStrategies = [
                fn(Crawler $c) => $c->filter('meta[property="article:published_time"]')->attr('content'),
                fn(Crawler $c) => $c->filter('meta[name="publish-date"]')->attr('content'),
                fn(Crawler $c) => $c->filter('meta[name="date"]')->attr('content'),
                fn(Crawler $c) => $c->filter('[itemprop="datePublished"]')->attr('content'),
                fn(Crawler $c) => $c->filter('time')->attr('datetime'),
            ];
        }
    }

    private function getBrowserHeaders(string $referer): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br, zstd',
            'Referer' => $referer . '/',
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
        ];
    }
}
