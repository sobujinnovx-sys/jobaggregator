<?php

namespace App\Console\Commands;

use App\Services\JobScraperService;
use Illuminate\Console\Command;

class ScrapeJobs extends Command
{
    protected $signature = 'jobs:scrape
        {--source=all : Source to scrape (all, remotive, remoteok, arbeitnow, jobicy, himalayas, greenhouse, lever)}
        {--region=all : Region filter (all, global, bd)}
        {--list : List available sources}';

    protected $description = 'Scrape real job listings from global remote APIs and Bangladesh company career pages';

    public function handle(JobScraperService $service): int
    {
        if ($this->option('list')) {
            $this->showSources();
            return self::SUCCESS;
        }

        $service->setLogger(fn (string $msg) => $this->line($msg));

        $region = $this->option('region');
        $source = $this->option('source');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║       Job Aggregator — Real Job Scraper      ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $startTime = microtime(true);

        if ($source !== 'all') {
            $this->info("Scraping source: {$source}");
            $result = $service->scrapeSource($source);
        } elseif ($region === 'global') {
            $this->info('Scraping global remote job APIs...');
            $result = $service->scrapeGlobalRemote();
        } elseif ($region === 'bd') {
            $this->info('Scraping Bangladesh company career pages...');
            $result = $service->scrapeBangladesh();
        } else {
            $this->info('Scraping ALL sources (global remote + Bangladesh)...');
            $result = $service->scrapeAll();
        }

        $elapsed = round(microtime(true) - $startTime, 1);

        $this->info('');
        $this->info('═══════════════════════════════════════════════');
        $this->info("✅ Created: {$result['created']} new jobs");
        $this->info("⏭️  Skipped: {$result['skipped']} duplicates");

        if (!empty($result['errors'])) {
            $this->warn("⚠️  Errors: " . count($result['errors']));
            foreach ($result['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }

        $this->info("⏱️  Time: {$elapsed}s");
        $this->info('═══════════════════════════════════════════════');

        return self::SUCCESS;
    }

    protected function showSources(): void
    {
        $this->info('');
        $this->info('Available scraping sources:');
        $this->info('');
        $this->table(
            ['Source', 'Type', 'Description'],
            [
                ['remotive', 'Global Remote', 'Remotive.com API — remote jobs worldwide'],
                ['remoteok', 'Global Remote', 'RemoteOK.com API — remote tech jobs'],
                ['arbeitnow', 'Global Remote', 'Arbeitnow.com API — remote & onsite jobs (EU focus)'],
                ['jobicy', 'Global Remote', 'Jobicy.com API — remote job listings'],
                ['himalayas', 'Global Remote', 'Himalayas.app API — remote jobs with salary data'],
                ['greenhouse', 'Company ATS', 'Greenhouse boards — GitLab, Canonical, Cloudflare, etc.'],
                ['lever', 'Company ATS', 'Lever boards — Netflix, GitHub, Atlassian, etc.'],
                ['bd_career', 'Bangladesh', '35+ BD companies — GP, bKash, Pathao, Brain Station 23, etc.'],
            ]
        );
        $this->info('');
        $this->info('Usage:');
        $this->line('  php artisan jobs:scrape                  # Scrape all sources');
        $this->line('  php artisan jobs:scrape --region=global   # Global remote only');
        $this->line('  php artisan jobs:scrape --region=bd       # Bangladesh only');
        $this->line('  php artisan jobs:scrape --source=remotive # Specific source');
    }
}
