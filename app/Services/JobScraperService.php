<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JobScraperService
{
    protected int $created = 0;
    protected int $skipped = 0;
    protected array $errors = [];
    protected ?\Closure $logger = null;
    protected array $categoryCache = [];

    // ─── Greenhouse companies (public board API) ────────────────
    protected array $greenhouseCompanies = [
        'gitlab' => 'GitLab',
        'canonical' => 'Canonical',
        'sourcegraph' => 'Sourcegraph',
        'netlify' => 'Netlify',
        'grafanalabs' => 'Grafana Labs',
        'cockroachlabs' => 'CockroachDB',
        'wikimediafoundation' => 'Wikimedia Foundation',
        'airtable' => 'Airtable',
        'figma' => 'Figma',
        'hashicorp' => 'HashiCorp',
        'datadog' => 'Datadog',
        'postman' => 'Postman',
        'cloudflare' => 'Cloudflare',
        'twilio' => 'Twilio',
        'elastic' => 'Elastic',
        'discord' => 'Discord',
        'dbtlabs' => 'dbt Labs',
        'snyk' => 'Snyk',
        'sentry' => 'Sentry',
        'miro' => 'Miro',
    ];

    // ─── Lever companies (public postings API) ──────────────────
    protected array $leverCompanies = [
        'Netflix' => 'Netflix',
        'github' => 'GitHub',
        'atlassian' => 'Atlassian',
        'Shopify' => 'Shopify',
    ];

    // ─── Bangladesh company career pages ────────────────────────
    protected array $bdCompanies = [
        ['name' => 'Grameenphone', 'career_url' => 'https://jobs.grameenphone.com/search', 'website' => 'https://grameenphone.com'],
        ['name' => 'bKash', 'career_url' => 'https://www.bkash.com/career', 'website' => 'https://www.bkash.com'],
        ['name' => 'Brain Station 23', 'career_url' => 'https://brainstation-23.com/career/', 'website' => 'https://brainstation-23.com'],
        ['name' => 'Pathao', 'career_url' => 'https://pathao.com/careers/', 'website' => 'https://pathao.com'],
        ['name' => 'Chaldal', 'career_url' => 'https://chaldal.com/careers', 'website' => 'https://chaldal.com'],
        ['name' => 'SELISE Digital Platforms', 'career_url' => 'https://career.selise.ch/', 'website' => 'https://selise.ch'],
        ['name' => 'Tiger IT Bangladesh', 'career_url' => 'https://www.tigerit.com/career', 'website' => 'https://www.tigerit.com'],
        ['name' => 'BJIT Group', 'career_url' => 'https://bjitgroup.com/career/', 'website' => 'https://bjitgroup.com'],
        ['name' => 'Kaz Software', 'career_url' => 'https://kaz.com.bd/join-us/', 'website' => 'https://kaz.com.bd'],
        ['name' => 'Vivasoft Limited', 'career_url' => 'https://vivasoftltd.com/career/', 'website' => 'https://vivasoftltd.com'],
        ['name' => 'Cefalo Bangladesh', 'career_url' => 'https://www.cefalo.com/en/careers', 'website' => 'https://www.cefalo.com'],
        ['name' => 'Therap BD', 'career_url' => 'https://therapbd.com/careers/', 'website' => 'https://therapbd.com'],
        ['name' => 'Enosis Solutions', 'career_url' => 'https://www.enosisbd.com/career/', 'website' => 'https://www.enosisbd.com'],
        ['name' => 'Welldev', 'career_url' => 'https://welldev.io/career/', 'website' => 'https://welldev.io'],
        ['name' => 'Nascenia', 'career_url' => 'https://nascenia.com/careers/', 'website' => 'https://nascenia.com'],
        ['name' => 'DataSoft Systems Bangladesh', 'career_url' => 'https://www.datasoft-bd.com/career/', 'website' => 'https://www.datasoft-bd.com'],
        ['name' => 'SSL Wireless', 'career_url' => 'https://www.sslwireless.com/career/', 'website' => 'https://www.sslwireless.com'],
        ['name' => 'Samsung R&D Institute Bangladesh', 'career_url' => 'https://www.samsung.com/bd/about-us/careers/', 'website' => 'https://www.samsung.com'],
        ['name' => 'Robi Axiata', 'career_url' => 'https://www.robi.com.bd/en/career', 'website' => 'https://www.robi.com.bd'],
        ['name' => 'Banglalink', 'career_url' => 'https://www.banglalink.net/en/career', 'website' => 'https://www.banglalink.net'],
        ['name' => '10 Minute School', 'career_url' => 'https://10minuteschool.com/careers/', 'website' => 'https://10minuteschool.com'],
        ['name' => 'ShopUp', 'career_url' => 'https://shopup.com.bd/career/', 'website' => 'https://shopup.com.bd'],
        ['name' => 'Praava Health', 'career_url' => 'https://praavahealth.com/career/', 'website' => 'https://praavahealth.com'],
        ['name' => 'BRAC IT Services', 'career_url' => 'https://www.bracits.com/career', 'website' => 'https://www.bracits.com'],
        ['name' => 'Nagad', 'career_url' => 'https://nagad.com.bd/career/', 'website' => 'https://nagad.com.bd'],
        ['name' => 'Daraz Bangladesh', 'career_url' => 'https://careers.daraz.com/jobs?country=Bangladesh', 'website' => 'https://www.daraz.com.bd'],
        ['name' => 'Craftsmen', 'career_url' => 'https://craftsmen.tech/career/', 'website' => 'https://craftsmen.tech'],
        ['name' => 'W3 Engineers', 'career_url' => 'https://w3engineers.com/career/', 'website' => 'https://w3engineers.com'],
        ['name' => 'TechnoVista Limited', 'career_url' => 'https://technovista.com.bd/career/', 'website' => 'https://technovista.com.bd'],
        ['name' => 'Deligent', 'career_url' => 'https://dfrbd.com/career/', 'website' => 'https://dfrbd.com'],
        ['name' => 'Walton Digi-Tech Industries', 'career_url' => 'https://waltondigitech.com/career/', 'website' => 'https://waltondigitech.com'],
        ['name' => 'Optimizely Bangladesh', 'career_url' => 'https://www.optimizely.com/careers/', 'website' => 'https://www.optimizely.com'],
        ['name' => 'Portonics Limited', 'career_url' => 'https://portonics.com/career/', 'website' => 'https://portonics.com'],
        ['name' => 'Field Buzz', 'career_url' => 'https://www.fieldbuzz.com/en/careers/', 'website' => 'https://www.fieldbuzz.com'],
        ['name' => 'Sheba Platform Limited', 'career_url' => 'https://sheba.xyz/career', 'website' => 'https://sheba.xyz'],
    ];

    // ─── Public Methods ─────────────────────────────────────────

    public function setLogger(\Closure $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    public function scrapeAll(): array
    {
        $this->reset();
        $this->scrapeRemotive();
        $this->scrapeRemoteOk();
        $this->scrapeArbeitnow();
        $this->scrapeJobicy();
        $this->scrapeHimalayas();
        $this->scrapeGreenhouse();
        $this->scrapeLever();
        $this->scrapeBdCareerPages();
        return $this->results();
    }

    public function scrapeGlobalRemote(): array
    {
        $this->reset();
        $this->scrapeRemotive();
        $this->scrapeRemoteOk();
        $this->scrapeArbeitnow();
        $this->scrapeJobicy();
        $this->scrapeHimalayas();
        return $this->results();
    }

    public function scrapeBangladesh(): array
    {
        $this->reset();
        $this->scrapeBdCareerPages();
        return $this->results();
    }

    public function scrapeSource(string $source): array
    {
        $this->reset();
        $method = 'scrape' . Str::studly($source);
        if (method_exists($this, $method)) {
            $this->{$method}();
        } else {
            $this->errors[] = "Unknown source: {$source}";
        }
        return $this->results();
    }

    // ─── Global Remote API Scrapers ─────────────────────────────

    protected function scrapeRemotive(): void
    {
        $this->log('📡 Scraping Remotive API...');

        try {
            $response = Http::timeout(30)
                ->withHeaders($this->httpHeaders())
                ->get('https://remotive.com/api/remote-jobs');

            if (!$response->ok()) {
                $this->errors[] = '[Remotive] HTTP ' . $response->status();
                return;
            }

            $jobs = $response->json('jobs', []);
            $count = 0;

            foreach ($jobs as $job) {
                $saved = $this->saveJob([
                    'title'            => $job['title'] ?? '',
                    'company_name'     => $job['company_name'] ?? 'Unknown',
                    'company_website'  => null,
                    'description'      => $this->cleanHtml($job['description'] ?? ''),
                    'apply_link'       => $job['url'] ?? '',
                    'location'         => $job['candidate_required_location'] ?? 'Worldwide',
                    'location_type'    => 'remote',
                    'salary_range'     => !empty($job['salary']) ? $job['salary'] : null,
                    'experience_level' => $this->guessExperience($job['title'] ?? ''),
                    'category_hint'    => $job['category'] ?? '',
                    'tags'             => $job['tags'] ?? [],
                    'source'           => 'remotive',
                    'external_id'      => 'remotive_' . ($job['id'] ?? md5($job['url'] ?? '')),
                    'posted_at'        => $job['publication_date'] ?? null,
                ]);
                if ($saved) $count++;
            }

            $this->log("   ✅ Remotive: {$count} new jobs (". count($jobs) . " total)");
        } catch (\Exception $e) {
            $this->logError('Remotive', $e);
        }
    }

    protected function scrapeRemoteOk(): void
    {
        $this->log('📡 Scraping RemoteOK API...');

        try {
            $response = Http::timeout(30)
                ->withHeaders(array_merge($this->httpHeaders(), [
                    'User-Agent' => 'JobAggregator/1.0',
                ]))
                ->get('https://remoteok.com/api');

            if (!$response->ok()) {
                $this->errors[] = '[RemoteOK] HTTP ' . $response->status();
                return;
            }

            $data = $response->json();
            if (!is_array($data)) return;

            // First element is a legal notice, skip it
            array_shift($data);
            $count = 0;

            foreach ($data as $job) {
                if (empty($job['position'] ?? null)) continue;

                $salaryMin = $job['salary_min'] ?? null;
                $salaryMax = $job['salary_max'] ?? null;
                $salary = null;
                if ($salaryMin && $salaryMax) {
                    $salary = '$' . number_format($salaryMin) . ' - $' . number_format($salaryMax);
                } elseif ($salaryMin) {
                    $salary = 'From $' . number_format($salaryMin);
                }

                $saved = $this->saveJob([
                    'title'            => $job['position'],
                    'company_name'     => $job['company'] ?? 'Unknown',
                    'company_website'  => $job['company_logo'] ?? null,
                    'description'      => $this->cleanHtml($job['description'] ?? ''),
                    'apply_link'       => $job['url'] ?? ('https://remoteok.com/remote-jobs/' . ($job['slug'] ?? '')),
                    'location'         => $job['location'] ?? 'Remote',
                    'location_type'    => 'remote',
                    'salary_range'     => $salary,
                    'experience_level' => $this->guessExperience($job['position']),
                    'category_hint'    => '',
                    'tags'             => $job['tags'] ?? [],
                    'source'           => 'remoteok',
                    'external_id'      => 'remoteok_' . ($job['id'] ?? md5($job['slug'] ?? '')),
                    'posted_at'        => $job['date'] ?? null,
                ]);
                if ($saved) $count++;
            }

            $this->log("   ✅ RemoteOK: {$count} new jobs (" . count($data) . " total)");
        } catch (\Exception $e) {
            $this->logError('RemoteOK', $e);
        }
    }

    protected function scrapeArbeitnow(): void
    {
        $this->log('📡 Scraping Arbeitnow API...');

        try {
            $count = 0;
            $totalFetched = 0;
            $url = 'https://www.arbeitnow.com/api/job-board-api';

            // Fetch up to 3 pages
            for ($page = 1; $page <= 3; $page++) {
                $response = Http::timeout(30)
                    ->withHeaders($this->httpHeaders())
                    ->get($url, ['page' => $page]);

                if (!$response->ok()) break;

                $jobs = $response->json('data', []);
                if (empty($jobs)) break;
                $totalFetched += count($jobs);

                foreach ($jobs as $job) {
                    $saved = $this->saveJob([
                        'title'            => $job['title'] ?? '',
                        'company_name'     => $job['company_name'] ?? 'Unknown',
                        'company_website'  => null,
                        'description'      => $this->cleanHtml($job['description'] ?? ''),
                        'apply_link'       => $job['url'] ?? '',
                        'location'         => $job['location'] ?? 'Remote',
                        'location_type'    => !empty($job['remote']) ? 'remote' : 'onsite',
                        'salary_range'     => null,
                        'experience_level' => $this->guessExperience($job['title'] ?? ''),
                        'category_hint'    => '',
                        'tags'             => $job['tags'] ?? [],
                        'source'           => 'arbeitnow',
                        'external_id'      => 'arbeitnow_' . ($job['slug'] ?? md5($job['url'] ?? '')),
                        'posted_at'        => isset($job['created_at']) ? date('Y-m-d H:i:s', $job['created_at']) : null,
                    ]);
                    if ($saved) $count++;
                }

                // Check if there's a next page
                $meta = $response->json('meta', []);
                if (($meta['current_page'] ?? 1) >= ($meta['last_page'] ?? 1)) break;
            }

            $this->log("   ✅ Arbeitnow: {$count} new jobs ({$totalFetched} total)");
        } catch (\Exception $e) {
            $this->logError('Arbeitnow', $e);
        }
    }

    protected function scrapeJobicy(): void
    {
        $this->log('📡 Scraping Jobicy API...');

        try {
            $response = Http::timeout(30)
                ->withHeaders($this->httpHeaders())
                ->get('https://jobicy.com/api/v2/remote-jobs', [
                    'count' => 50,
                ]);

            if (!$response->ok()) {
                $this->errors[] = '[Jobicy] HTTP ' . $response->status();
                return;
            }

            $jobs = $response->json('jobs', []);
            $count = 0;

            foreach ($jobs as $job) {
                $saved = $this->saveJob([
                    'title'            => $job['jobTitle'] ?? '',
                    'company_name'     => $job['companyName'] ?? 'Unknown',
                    'company_website'  => null,
                    'description'      => $this->cleanHtml($job['jobExcerpt'] ?? ''),
                    'apply_link'       => $job['url'] ?? '',
                    'location'         => $job['jobGeo'] ?? 'Anywhere',
                    'location_type'    => 'remote',
                    'salary_range'     => $this->formatJobicySalary($job),
                    'experience_level' => $this->guessExperience($job['jobTitle'] ?? '', $job['jobLevel'] ?? null),
                    'category_hint'    => is_array($job['jobIndustry'] ?? null) ? implode(', ', $job['jobIndustry']) : ($job['jobIndustry'] ?? ''),
                    'tags'             => [],
                    'source'           => 'jobicy',
                    'external_id'      => 'jobicy_' . ($job['id'] ?? md5($job['url'] ?? '')),
                    'posted_at'        => $job['pubDate'] ?? null,
                ]);
                if ($saved) $count++;
            }

            $this->log("   ✅ Jobicy: {$count} new jobs (" . count($jobs) . " total)");
        } catch (\Exception $e) {
            $this->logError('Jobicy', $e);
        }
    }

    protected function scrapeHimalayas(): void
    {
        $this->log('📡 Scraping Himalayas API...');

        try {
            $response = Http::timeout(30)
                ->withHeaders($this->httpHeaders())
                ->get('https://himalayas.app/jobs/api', [
                    'limit' => 50,
                ]);

            if (!$response->ok()) {
                $this->errors[] = '[Himalayas] HTTP ' . $response->status();
                return;
            }

            $jobs = $response->json('jobs', []);
            $count = 0;

            foreach ($jobs as $job) {
                $location = 'Remote';
                if (!empty($job['locationRestrictions']) && is_array($job['locationRestrictions'])) {
                    $location = implode(', ', array_slice($job['locationRestrictions'], 0, 3));
                }

                $saved = $this->saveJob([
                    'title'            => $job['title'] ?? '',
                    'company_name'     => $job['companyName'] ?? 'Unknown',
                    'company_website'  => null,
                    'description'      => Str::limit($this->cleanHtml($job['description'] ?? ''), 2000),
                    'apply_link'       => $job['applicationLink'] ?? $job['url'] ?? '',
                    'location'         => $location,
                    'location_type'    => 'remote',
                    'salary_range'     => $this->formatHimalayasSalary($job),
                    'experience_level' => $this->guessExperience($job['title'] ?? '', $job['seniority'] ?? null),
                    'category_hint'    => is_array($job['categories'] ?? null) ? implode(', ', $job['categories']) : '',
                    'tags'             => [],
                    'source'           => 'himalayas',
                    'external_id'      => 'himalayas_' . ($job['id'] ?? md5(($job['title'] ?? '') . ($job['companyName'] ?? ''))),
                    'posted_at'        => $job['pubDate'] ?? null,
                ]);
                if ($saved) $count++;
            }

            $this->log("   ✅ Himalayas: {$count} new jobs (" . count($jobs) . " total)");
        } catch (\Exception $e) {
            $this->logError('Himalayas', $e);
        }
    }

    // ─── ATS Platform Scrapers ──────────────────────────────────

    protected function scrapeGreenhouse(): void
    {
        $this->log('📡 Scraping Greenhouse boards...');
        $totalCount = 0;

        foreach ($this->greenhouseCompanies as $slug => $companyName) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders($this->httpHeaders())
                    ->get("https://boards-api.greenhouse.io/v1/boards/{$slug}/jobs");

                if (!$response->ok()) {
                    $this->log("   ⚠️  {$companyName}: HTTP {$response->status()}");
                    continue;
                }

                $jobs = $response->json('jobs', []);
                $count = 0;

                foreach ($jobs as $job) {
                    $location = $job['location']['name'] ?? '';
                    $locationType = $this->guessLocationType($location);

                    $deptName = '';
                    if (!empty($job['departments'])) {
                        $deptName = $job['departments'][0]['name'] ?? '';
                    }

                    $saved = $this->saveJob([
                        'title'            => $job['title'] ?? '',
                        'company_name'     => $companyName,
                        'company_website'  => null,
                        'description'      => '',
                        'apply_link'       => $job['absolute_url'] ?? '',
                        'location'         => $location ?: 'Not specified',
                        'location_type'    => $locationType,
                        'salary_range'     => null,
                        'experience_level' => $this->guessExperience($job['title'] ?? ''),
                        'category_hint'    => $deptName,
                        'tags'             => [],
                        'source'           => 'greenhouse',
                        'external_id'      => "greenhouse_{$slug}_" . ($job['id'] ?? ''),
                        'posted_at'        => isset($job['updated_at']) ? date('Y-m-d H:i:s', strtotime($job['updated_at'])) : null,
                    ]);
                    if ($saved) $count++;
                }

                $totalCount += $count;
                if ($count > 0 || count($jobs) > 0) {
                    $this->log("   ✅ {$companyName}: {$count} new (" . count($jobs) . " total)");
                }
            } catch (\Exception $e) {
                $this->log("   ⚠️  {$companyName}: {$e->getMessage()}");
            }
        }

        $this->log("   📊 Greenhouse total: {$totalCount} new jobs");
    }

    protected function scrapeLever(): void
    {
        $this->log('📡 Scraping Lever boards...');
        $totalCount = 0;

        foreach ($this->leverCompanies as $slug => $companyName) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders($this->httpHeaders())
                    ->get("https://api.lever.co/v0/postings/{$slug}");

                if (!$response->ok()) {
                    $this->log("   ⚠️  {$companyName}: HTTP {$response->status()}");
                    continue;
                }

                $jobs = $response->json();
                if (!is_array($jobs)) continue;
                $count = 0;

                foreach ($jobs as $job) {
                    $categories = $job['categories'] ?? [];
                    $location = $categories['location'] ?? '';
                    $locationType = $this->guessLocationType($location);

                    $saved = $this->saveJob([
                        'title'            => $job['text'] ?? '',
                        'company_name'     => $companyName,
                        'company_website'  => null,
                        'description'      => $this->cleanHtml($job['descriptionPlain'] ?? ''),
                        'apply_link'       => $job['hostedUrl'] ?? '',
                        'location'         => $location ?: 'Not specified',
                        'location_type'    => $locationType,
                        'salary_range'     => null,
                        'experience_level' => $this->guessExperience($job['text'] ?? ''),
                        'category_hint'    => $categories['department'] ?? '',
                        'tags'             => [],
                        'source'           => 'lever',
                        'external_id'      => "lever_{$slug}_" . ($job['id'] ?? ''),
                        'posted_at'        => isset($job['createdAt']) ? date('Y-m-d H:i:s', $job['createdAt'] / 1000) : null,
                    ]);
                    if ($saved) $count++;
                }

                $totalCount += $count;
                if ($count > 0 || count($jobs) > 0) {
                    $this->log("   ✅ {$companyName}: {$count} new (" . count($jobs) . " total)");
                }
            } catch (\Exception $e) {
                $this->log("   ⚠️  {$companyName}: {$e->getMessage()}");
            }
        }

        $this->log("   📊 Lever total: {$totalCount} new jobs");
    }

    // ─── Bangladesh Career Page Scrapers ────────────────────────

    protected function scrapeBdCareerPages(): void
    {
        $this->log('📡 Scraping Bangladesh company career pages...');
        $totalCount = 0;

        foreach ($this->bdCompanies as $company) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.5',
                    ])
                    ->get($company['career_url']);

                if (!$response->ok()) {
                    $this->log("   ⚠️  {$company['name']}: HTTP {$response->status()}");
                    continue;
                }

                $html = $response->body();
                $jobs = $this->extractJobsFromHtml($html, $company['career_url']);

                $companyModel = $this->findOrCreateCompany($company['name'], $company['website']);
                $count = 0;

                foreach ($jobs as $job) {
                    // Only save if title or URL looks like an actual job posting
                    if (!$this->looksLikeActualJob($job['title'], $job['url'])) continue;

                    $saved = $this->saveJob([
                        'title'            => $job['title'],
                        'company_name'     => $company['name'],
                        'company_website'  => $company['website'],
                        'description'      => $job['description'] ?? '',
                        'apply_link'       => $job['url'],
                        'location'         => $job['location'] ?? 'Dhaka, Bangladesh',
                        'location_type'    => 'onsite',
                        'salary_range'     => null,
                        'experience_level' => $this->guessExperience($job['title']),
                        'category_hint'    => '',
                        'tags'             => [],
                        'source'           => 'bd_career',
                        'external_id'      => 'bd_' . Str::slug($company['name']) . '_' . md5($job['url']),
                        'posted_at'        => now(),
                    ]);
                    if ($saved) $count++;
                }

                $totalCount += $count;
                $this->log("   " . ($count > 0 ? '✅' : '⏭️ ') . " {$company['name']}: {$count} new (" . count($jobs) . " found)");

            } catch (\Exception $e) {
                $this->log("   ⚠️  {$company['name']}: " . Str::limit($e->getMessage(), 80));
            }
        }

        $this->log("   📊 Bangladesh total: {$totalCount} new jobs");
    }

    // ─── HTML Parsing for Career Pages ──────────────────────────

    protected function extractJobsFromHtml(string $html, string $baseUrl): array
    {
        $jobs = [];

        // Strategy 1: JSON-LD structured data
        if ($jsonLdJobs = $this->extractJsonLd($html, $baseUrl)) {
            return $jsonLdJobs;
        }

        // Strategy 2: DOM parsing with common selectors
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        // Common job listing container selectors
        $containerQueries = [
            '//div[contains(@class,"job")]//a[contains(@href,"/job") or contains(@href,"/career") or contains(@href,"/position") or contains(@href,"/apply") or contains(@href,"/opening") or contains(@href,"/vacancy")]',
            '//div[contains(@class,"career")]//a[contains(@href,"/job") or contains(@href,"/career") or contains(@href,"/position") or contains(@href,"/apply")]',
            '//div[contains(@class,"position")]//a',
            '//div[contains(@class,"vacancy")]//a',
            '//div[contains(@class,"opening")]//a',
            '//ul[contains(@class,"job")]//a',
            '//section[contains(@class,"job") or contains(@class,"career")]//a',
            '//div[contains(@class,"listing")]//a',
            '//article[contains(@class,"job")]//a',
            '//tr[contains(@class,"job")]//a',
        ];

        $foundLinks = [];
        foreach ($containerQueries as $query) {
            $links = @$xpath->query($query);
            if (!$links) continue;

            foreach ($links as $link) {
                $text = trim($link->textContent);
                $href = $link->getAttribute('href');

                if (strlen($text) < 5 || strlen($text) > 200) continue;
                if ($this->isNavigationLink($text)) continue;
                if ($this->looksLikeNewsHeadline($text)) continue;
                if (empty($href) || $href === '#') continue;

                $absoluteUrl = $this->resolveUrl($href, $baseUrl);
                if ($this->isNonJobUrl($absoluteUrl)) continue;
                $key = md5($absoluteUrl);

                if (!isset($foundLinks[$key])) {
                    $foundLinks[$key] = [
                        'title'       => $this->cleanTitle($text),
                        'url'         => $absoluteUrl,
                        'description' => '',
                        'location'    => null,
                    ];
                }
            }
        }

        if (!empty($foundLinks)) {
            return array_values($foundLinks);
        }

        // Strategy 3: Broader search - look for any links with job-related paths
        $allLinks = $xpath->query('//a[@href]');
        if (!$allLinks) return [];

        foreach ($allLinks as $link) {
            $text = trim($link->textContent);
            $href = $link->getAttribute('href');

            if (strlen($text) < 8 || strlen($text) > 200) continue;
            if ($this->isNavigationLink($text)) continue;
            if ($this->looksLikeNewsHeadline($text)) continue;
            if (empty($href) || $href === '#') continue;

            // Only include links that look like job postings
            $hrefLower = strtolower($href);
            $isJobLink = Str::contains($hrefLower, ['job', 'career', 'position', 'vacancy', 'opening', 'apply', 'recruit']);

            // Also check if the text looks like a job title
            $textLower = strtolower($text);
            $isJobTitle = Str::contains($textLower, [
                'developer', 'engineer', 'designer', 'manager', 'analyst',
                'architect', 'specialist', 'lead', 'senior', 'junior',
                'officer', 'executive', 'coordinator', 'consultant',
                'intern', 'head of', 'director', 'administrator',
            ]);

            if ($isJobLink || $isJobTitle) {
                $absoluteUrl = $this->resolveUrl($href, $baseUrl);
                if ($this->isNonJobUrl($absoluteUrl)) continue;
                $key = md5($absoluteUrl);

                if (!isset($foundLinks[$key])) {
                    $foundLinks[$key] = [
                        'title'       => $this->cleanTitle($text),
                        'url'         => $absoluteUrl,
                        'description' => '',
                        'location'    => null,
                    ];
                }
            }
        }

        return array_values($foundLinks);
    }

    protected function extractJsonLd(string $html, string $baseUrl): ?array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $matches);

        if (empty($matches[1])) return null;

        $jobs = [];
        foreach ($matches[1] as $jsonStr) {
            $data = json_decode($jsonStr, true);
            if (!$data) continue;

            $items = [];
            if (($data['@type'] ?? '') === 'JobPosting') {
                $items[] = $data;
            } elseif (isset($data['@graph'])) {
                $items = array_filter($data['@graph'], fn($item) => ($item['@type'] ?? '') === 'JobPosting');
            } elseif (is_array($data) && isset($data[0]['@type']) && $data[0]['@type'] === 'JobPosting') {
                $items = $data;
            }

            foreach ($items as $item) {
                $jobs[] = [
                    'title'       => $item['title'] ?? $item['name'] ?? 'Unknown Position',
                    'url'         => $item['url'] ?? $baseUrl,
                    'description' => strip_tags($item['description'] ?? ''),
                    'location'    => $item['jobLocation']['address']['addressLocality'] ?? null,
                ];
            }
        }

        return !empty($jobs) ? $jobs : null;
    }

    // ─── Job Creation & Dedup ───────────────────────────────────

    protected function saveJob(array $data): bool
    {
        if (empty($data['title']) || empty($data['external_id'])) {
            $this->skipped++;
            return false;
        }

        // Dedup by external_id
        if (JobListing::where('external_id', $data['external_id'])->exists()) {
            $this->skipped++;
            return false;
        }

        $company = $this->findOrCreateCompany(
            $data['company_name'] ?? 'Unknown',
            $data['company_website'] ?? null
        );

        $categoryId = $this->guessCategory(
            $data['title'],
            $data['category_hint'] ?? '',
            $data['tags'] ?? []
        );

        JobListing::create([
            'title'            => Str::limit($data['title'], 250),
            'company_id'       => $company->id,
            'category_id'      => $categoryId,
            'location_type'    => $data['location_type'] ?? 'remote',
            'location'         => Str::limit($data['location'] ?? 'Remote', 250),
            'experience_level' => $data['experience_level'] ?? 'mid',
            'description'      => Str::limit($data['description'] ?? '', 5000),
            'apply_link'       => $data['apply_link'],
            'salary_range'     => $data['salary_range'],
            'status'           => 'approved',
            'source'           => $data['source'] ?? 'scraped',
            'external_id'      => $data['external_id'],
            'posted_at'        => $this->parseDate($data['posted_at'] ?? null),
        ]);

        $this->created++;
        return true;
    }

    protected function findOrCreateCompany(string $name, ?string $website = null): Company
    {
        return Company::firstOrCreate(
            ['name' => Str::limit($name, 250)],
            ['website' => $website]
        );
    }

    // ─── Category Mapping ───────────────────────────────────────

    protected function guessCategory(string $title, string $hint = '', array $tags = []): ?int
    {
        $text = strtolower($title . ' ' . $hint . ' ' . implode(' ', $tags));

        $mappings = [
            'Web Development'    => ['web', 'frontend', 'front-end', 'front end', 'backend', 'back-end', 'back end', 'fullstack', 'full-stack', 'full stack', 'php', 'laravel', 'react', 'vue', 'angular', 'node', 'django', 'ruby', 'rails', 'html', 'css', 'wordpress', 'shopify', 'javascript', 'typescript', 'nextjs', 'nuxt', 'svelte', 'golang', 'rust developer', 'java developer', 'python developer', '.net', 'software engineer', 'software developer', 'web developer'],
            'Mobile Development' => ['mobile', 'ios', 'android', 'flutter', 'react native', 'swift developer', 'kotlin developer', 'xamarin', 'ionic'],
            'DevOps'             => ['devops', 'dev ops', 'sre', 'site reliability', 'infrastructure', 'cloud engineer', 'aws', 'azure', 'gcp', 'kubernetes', 'docker', 'terraform', 'ci/cd', 'platform engineer', 'systems engineer', 'linux admin', 'network engineer'],
            'Data Science'       => ['data scien', 'machine learning', 'artificial intelligence', ' ai ', ' ml ', 'analytics', 'data analyst', 'data engineer', 'nlp', 'deep learning', 'big data', 'business intelligence', 'bi developer', 'statistician'],
            'Design'             => ['designer', 'ui/', 'ux/', 'ui ', 'ux ', 'user experience', 'user interface', 'graphic design', 'figma', 'creative director', 'visual design', 'product design', 'brand design', 'motion design'],
            'Product Management' => ['product manager', 'product owner', 'scrum master', 'agile coach', 'program manager', 'technical program', 'project manager'],
            'QA & Testing'       => [' qa ', 'quality assur', 'tester', 'test engineer', 'automation engineer', 'selenium', 'sdet', 'quality engineer'],
            'Cybersecurity'      => ['security engineer', 'cybersecurity', 'penetration', 'vulnerability', 'security analyst', 'infosec', 'soc analyst', 'security architect'],
            'Marketing'          => ['marketing', 'seo', 'content writer', 'copywriter', 'social media', 'growth', 'content manager', 'brand manager'],
            'Sales'              => ['sales', 'account executive', 'business development', 'account manager', 'revenue'],
            'Customer Support'   => ['customer support', 'customer success', 'help desk', 'technical support', 'support engineer'],
        ];

        foreach ($mappings as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $this->getCategoryId($categoryName);
                }
            }
        }

        // Default fallback based on common patterns
        if (Str::contains($text, ['engineer', 'developer', 'programmer', 'coder', 'coding'])) {
            return $this->getCategoryId('Web Development');
        }

        return null;
    }

    protected function getCategoryId(string $name): ?int
    {
        if (empty($this->categoryCache)) {
            $this->categoryCache = Category::pluck('id', 'name')->toArray();
        }

        return $this->categoryCache[$name] ?? null;
    }

    // ─── Experience Level Detection ─────────────────────────────

    protected function guessExperience(string $title, ?string $level = null): string
    {
        if ($level) {
            $level = strtolower($level);
            if (Str::contains($level, ['lead', 'principal', 'staff', 'director', 'head', 'vp', 'chief'])) return 'lead';
            if (Str::contains($level, ['senior', 'sr'])) return 'senior';
            if (Str::contains($level, ['junior', 'entry', 'intern', 'associate', 'graduate'])) return 'junior';
            if (Str::contains($level, ['mid', 'intermediate'])) return 'mid';
        }

        $text = strtolower($title);
        if (Str::contains($text, ['director', 'head of', 'vp ', 'vice president', 'chief', 'principal', 'staff engineer', 'distinguished'])) return 'lead';
        if (Str::contains($text, ['lead', 'architect', 'manager'])) return 'lead';
        if (Str::contains($text, ['senior', 'sr.', 'sr ', 'iii', 'level 3', 'experienced'])) return 'senior';
        if (Str::contains($text, ['junior', 'jr.', 'jr ', 'intern', 'entry', 'associate', 'trainee', 'graduate', 'fresher'])) return 'junior';

        return 'mid';
    }

    // ─── Location Type Detection ────────────────────────────────

    protected function guessLocationType(string $location): string
    {
        $loc = strtolower($location);

        if (Str::contains($loc, ['remote', 'anywhere', 'worldwide', 'global', 'distributed', 'work from home', 'wfh'])) {
            return 'remote';
        }
        if (Str::contains($loc, ['hybrid', 'flexible'])) {
            return 'hybrid';
        }

        return 'onsite';
    }

    // ─── Utility Methods ────────────────────────────────────────

    protected function httpHeaders(): array
    {
        return [
            'User-Agent' => 'JobAggregator/1.0 (Laravel Job Board)',
            'Accept'     => 'application/json',
        ];
    }

    protected function cleanHtml(string $html): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    protected function cleanTitle(string $title): string
    {
        $title = preg_replace('/\s+/', ' ', trim($title));
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        return Str::limit($title, 250);
    }

    protected function isNavigationLink(string $text): bool
    {
        $textLower = strtolower(trim($text));

        // Exact matches
        $skip = [
            'apply now', 'learn more', 'read more', 'view all', 'see all',
            'careers', 'career', 'home', 'about', 'about us', 'contact', 'contact us',
            'login', 'sign up', 'register', 'sign in', 'log in',
            'view details', 'more info', 'click here', 'back',
            'next', 'previous', 'menu', 'close', 'search',
            'facebook', 'twitter', 'linkedin', 'instagram', 'youtube',
            'overview', 'culture', 'benefits', 'perks', 'our team',
            'life at', 'why join us', 'who we are', 'what we do',
            'our story', 'our values', 'our mission', 'news', 'blog',
            'events', 'press', 'media', 'gallery', 'photos',
            'international', 'global', 'awards', 'achievements',
            'partners', 'clients', 'services', 'products', 'solutions',
            'privacy policy', 'terms', 'sitemap', 'cookie policy',
            'executive team', 'leadership', 'management', 'board',
            'explore opportunities', 'view all openings', 'see openings',
            'job fields', 'search jobs', 'find jobs',
        ];
        if (in_array($textLower, $skip)) return true;

        // Partial matches — text contains these patterns
        $skipPatterns = [
            'working at ', 'life at ', 'join our ', 'why work',
            'about our', 'our culture', 'our benefit', 'meet the',
            'follow us', 'connect with', 'stay updated',
            'subscribe', 'newsletter', 'cookie', 'privacy',
            'terms of', 'powered by', 'copyright', '©',
            'কোর্স', 'ক্যারিয়ার', 'নিয়োগ', 'prep', 'preparation', // non-job BD content
            'explore job', 'explore career', 'kick start your career',
        ];
        foreach ($skipPatterns as $pattern) {
            if (str_contains($textLower, $pattern)) return true;
        }

        return false;
    }

    protected function looksLikeActualJob(string $title, string $url): bool
    {
        $titleLower = strtolower($title);
        $urlLower = strtolower($url);

        // URL looks like a job posting
        $jobUrlPatterns = ['/job', '/position', '/vacancy', '/opening', '/apply', '/recruit', '/internship'];
        foreach ($jobUrlPatterns as $p) {
            if (str_contains($urlLower, $p)) return true;
        }

        // Title contains job-role keywords
        $jobTitleWords = [
            'developer', 'engineer', 'designer', 'manager', 'analyst',
            'architect', 'specialist', 'lead', 'senior', 'junior',
            'officer', 'executive', 'coordinator', 'consultant',
            'intern', 'internship', 'head of', 'director', 'administrator',
            'associate', 'assistant', 'supervisor', 'technician',
            'programmer', 'qa', 'tester', 'devops', 'sre',
            'product owner', 'scrum master', 'data scientist',
        ];
        foreach ($jobTitleWords as $word) {
            if (str_contains($titleLower, $word)) return true;
        }

        return false;
    }

    protected function looksLikeNewsHeadline(string $text): bool
    {
        $lower = strtolower(trim($text));

        // News/PR headline patterns — these contain past-tense verbs or announcements
        $newsPatterns = [
            '/\b(champions?|secures?|establishes?|reaffirms?|announces?|launches?|wins?|celebrates?)\b/i',
            '/\b(awarded|recognized|selected|ranked|featured|named|honored)\b/i',
            '/\bat\s+(the\s+)?[A-Z][\w\s]+(festival|conference|summit|expo|event|awards?)/i',
            '/\b(voices? from|perspectives? from|insights? from)\b/i',
            '/^(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d/i',
            '/^\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4}/',  // date-only text
        ];

        foreach ($newsPatterns as $pattern) {
            if (preg_match($pattern, $text)) return true;
        }

        // Text that's just a company/org name (no job-related words)
        if (preg_match('/^[A-Z][\w\s\.]+Ltd\.?$/i', $text)) return true;

        return false;
    }

    protected function isNonJobUrl(string $url): bool
    {
        $urlLower = strtolower($url);

        $skipPaths = [
            '/news', '/blog', '/press', '/media', '/events', '/gallery',
            '/about', '/contact', '/team', '/leadership', '/awards',
            '/culture', '/benefits', '/perks', '/values', '/mission',
            '/privacy', '/terms', '/cookie', '/sitemap',
            '/products', '/services', '/solutions', '/clients', '/partners',
            '/international', '/global', '/executive',
            '/hire-', // "hire developers" service pages, not job postings
            'mailto:', 'tel:', 'javascript:',
            '/jobs-prep', // non-job content
        ];

        foreach ($skipPaths as $path) {
            if (str_contains($urlLower, $path)) return true;
        }

        return false;
    }

    protected function resolveUrl(string $href, string $baseUrl): string
    {
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        $parsed = parse_url($baseUrl);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';

        if (str_starts_with($href, '//')) {
            return $scheme . ':' . $href;
        }
        if (str_starts_with($href, '/')) {
            return $scheme . '://' . $host . $href;
        }

        // Relative URL
        $basePath = isset($parsed['path']) ? dirname($parsed['path']) : '';
        return $scheme . '://' . $host . $basePath . '/' . $href;
    }

    protected function parseDate(?string $dateStr): ?string
    {
        if (!$dateStr) return now()->toDateTimeString();

        try {
            return \Carbon\Carbon::parse($dateStr)->toDateTimeString();
        } catch (\Exception $e) {
            return now()->toDateTimeString();
        }
    }

    protected function formatJobicySalary(array $job): ?string
    {
        $min = $job['annualSalaryMin'] ?? null;
        $max = $job['annualSalaryMax'] ?? null;
        if ($min && $max) return '$' . number_format($min) . ' - $' . number_format($max);
        if ($min) return 'From $' . number_format($min);
        return null;
    }

    protected function formatHimalayasSalary(array $job): ?string
    {
        $min = $job['minSalary'] ?? null;
        $max = $job['maxSalary'] ?? null;
        $currency = $job['salaryCurrency'] ?? 'USD';
        if ($min && $max) return $currency . ' ' . number_format($min) . ' - ' . number_format($max);
        if ($min) return 'From ' . $currency . ' ' . number_format($min);
        return null;
    }

    protected function log(string $message): void
    {
        if ($this->logger) {
            ($this->logger)($message);
        }
    }

    protected function logError(string $source, \Exception $e): void
    {
        $msg = "[{$source}] {$e->getMessage()}";
        $this->errors[] = $msg;
        $this->log("   ❌ {$source}: {$e->getMessage()}");
        Log::error("Scraper [{$source}] failed", ['error' => $e->getMessage()]);
    }

    protected function reset(): void
    {
        $this->created = 0;
        $this->skipped = 0;
        $this->errors = [];
    }

    protected function results(): array
    {
        return [
            'created' => $this->created,
            'skipped' => $this->skipped,
            'errors'  => $this->errors,
        ];
    }
}
