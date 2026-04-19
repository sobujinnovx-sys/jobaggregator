<?php

namespace App\Console\Commands;

use App\Models\JobAlert;
use App\Models\JobListing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Send email notifications to subscribers about new matching jobs.
 */
class SendJobAlerts extends Command
{
    protected $signature = 'jobs:send-alerts';
    protected $description = 'Send email notifications for new job listings matching user alerts';

    public function handle(): int
    {
        $alerts = JobAlert::where('is_active', true)->get();
        $sentCount = 0;

        foreach ($alerts as $alert) {
            $query = JobListing::approved()
                ->where('created_at', '>=', now()->subDay());

            if ($alert->keyword) {
                $query->byKeyword($alert->keyword);
            }

            if ($alert->location_type) {
                $query->byLocationType($alert->location_type);
            }

            $newJobs = $query->with('company')->get();

            if ($newJobs->isEmpty()) {
                continue;
            }

            try {
                Mail::raw(
                    $this->buildEmailBody($newJobs, $alert),
                    function ($message) use ($alert) {
                        $message->to($alert->email)
                                ->subject('New Job Listings - Job Aggregator');
                    }
                );
                $sentCount++;
            } catch (\Exception $e) {
                Log::warning("Failed to send alert to {$alert->email}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sentCount} alert emails.");
        return self::SUCCESS;
    }

    private function buildEmailBody($jobs, $alert): string
    {
        $body = "Hi! Here are new job listings matching your alert";
        if ($alert->keyword) {
            $body .= " for \"{$alert->keyword}\"";
        }
        $body .= ":\n\n";

        foreach ($jobs as $job) {
            $body .= "- {$job->title} at {$job->company->name}";
            $body .= " ({$job->location_type})";
            $body .= "\n  Apply: {$job->apply_link}\n\n";
        }

        $body .= "---\nJob Aggregator Dashboard";
        return $body;
    }
}
