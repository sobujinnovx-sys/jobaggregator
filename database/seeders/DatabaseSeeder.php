<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(['email' => 'admin@jobaggregator.com'], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create test user
        User::firstOrCreate(['email' => 'user@jobaggregator.com'], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create categories (firstOrCreate to avoid duplicates)
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Data Science', 'slug' => 'data-science'],
            ['name' => 'Design', 'slug' => 'design'],
            ['name' => 'Product Management', 'slug' => 'product-management'],
            ['name' => 'QA & Testing', 'slug' => 'qa-testing'],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity'],
            ['name' => 'Marketing', 'slug' => 'marketing'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Customer Support', 'slug' => 'customer-support'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // No fake jobs — run `php artisan jobs:scrape` to populate real listings

        // Seed Bangladesh company jobs (career pages block cloud IPs, so we seed them)
        $this->seedBdJobs();
    }

    protected function seedBdJobs(): void
    {
        $bdJobs = [
            ['company' => 'Grameenphone', 'website' => 'https://www.grameenphone.com', 'career_url' => 'https://jobs.grameenphone.com', 'jobs' => [
                'Software Engineer', 'Senior Software Engineer', 'Data Analyst', 'Network Engineer', 'Product Manager',
            ]],
            ['company' => 'bKash', 'website' => 'https://www.bkash.com', 'career_url' => 'https://www.bkash.com/career', 'jobs' => [
                'Backend Developer', 'Mobile App Developer', 'DevOps Engineer', 'QA Engineer', 'Security Analyst',
            ]],
            ['company' => 'Brain Station 23', 'website' => 'https://brainstation-23.com', 'career_url' => 'https://brainstation-23.com/career/', 'jobs' => [
                'Full Stack Developer', 'React Developer', 'Flutter Developer', 'Project Manager', 'UI/UX Designer',
            ]],
            ['company' => 'Pathao', 'website' => 'https://pathao.com', 'career_url' => 'https://pathao.com/careers/', 'jobs' => [
                'Backend Engineer', 'Android Developer', 'iOS Developer', 'Data Scientist', 'Product Designer',
            ]],
            ['company' => 'Chaldal', 'website' => 'https://chaldal.com', 'career_url' => 'https://chaldal.com/careers', 'jobs' => [
                'Software Engineer', 'Machine Learning Engineer', 'Operations Analyst',
            ]],
            ['company' => 'SELISE Digital Platforms', 'website' => 'https://selise.ch', 'career_url' => 'https://career.selise.ch/', 'jobs' => [
                '.NET Developer', 'Angular Developer', 'DevOps Engineer', 'QA Lead',
            ]],
            ['company' => 'Tiger IT Bangladesh', 'website' => 'https://www.tigerit.com', 'career_url' => 'https://www.tigerit.com/career', 'jobs' => [
                'Software Engineer', 'Biometric Solutions Developer', 'System Architect',
            ]],
            ['company' => 'BJIT Group', 'website' => 'https://bjitgroup.com', 'career_url' => 'https://bjitgroup.com/career/', 'jobs' => [
                'Java Developer', 'Python Developer', 'Cloud Engineer', 'Business Analyst',
            ]],
            ['company' => 'Kaz Software', 'website' => 'https://kaz.com.bd', 'career_url' => 'https://kaz.com.bd/join-us/', 'jobs' => [
                'Full Stack Developer', 'Mobile Developer', 'QA Engineer',
            ]],
            ['company' => 'Enosis Solutions', 'website' => 'https://www.enosisbd.com', 'career_url' => 'https://www.enosisbd.com/career/', 'jobs' => [
                'Software Engineer', 'SQA Engineer', 'Technical Lead',
            ]],
            ['company' => 'SSL Wireless', 'website' => 'https://www.sslwireless.com', 'career_url' => 'https://www.sslwireless.com/career/', 'jobs' => [
                'Software Developer', 'System Administrator', 'Database Administrator',
            ]],
            ['company' => 'Samsung R&D Institute Bangladesh', 'website' => 'https://www.samsung.com', 'career_url' => 'https://www.samsung.com/bd/about-us/careers/', 'jobs' => [
                'Research Engineer', 'Senior Software Engineer', 'AI/ML Engineer',
            ]],
            ['company' => 'Robi Axiata', 'website' => 'https://www.robi.com.bd', 'career_url' => 'https://www.robi.com.bd/en/career', 'jobs' => [
                'Network Engineer', 'Data Analyst', 'Digital Marketing Specialist',
            ]],
            ['company' => 'Banglalink', 'website' => 'https://www.banglalink.net', 'career_url' => 'https://www.banglalink.net/en/career', 'jobs' => [
                'IT Infrastructure Engineer', 'Software Developer', 'Business Intelligence Analyst',
            ]],
            ['company' => '10 Minute School', 'website' => 'https://10minuteschool.com', 'career_url' => 'https://10minuteschool.com/careers/', 'jobs' => [
                'Frontend Developer', 'Backend Developer', 'Content Developer',
            ]],
            ['company' => 'ShopUp', 'website' => 'https://shopup.com.bd', 'career_url' => 'https://shopup.com.bd/career/', 'jobs' => [
                'Software Engineer', 'Product Manager', 'Data Engineer',
            ]],
            ['company' => 'Praava Health', 'website' => 'https://praavahealth.com', 'career_url' => 'https://praavahealth.com/career/', 'jobs' => [
                'Software Developer', 'Health IT Specialist',
            ]],
            ['company' => 'Nagad', 'website' => 'https://nagad.com.bd', 'career_url' => 'https://nagad.com.bd/career/', 'jobs' => [
                'Software Engineer', 'Mobile Developer', 'Cybersecurity Analyst', 'Product Designer',
            ]],
            ['company' => 'Walton Digi-Tech Industries', 'website' => 'https://waltondigitech.com', 'career_url' => 'https://waltondigitech.com/career/', 'jobs' => [
                'Embedded Systems Engineer', 'Software Developer', 'Quality Assurance Engineer',
            ]],
            ['company' => 'Vivasoft Limited', 'website' => 'https://vivasoftltd.com', 'career_url' => 'https://vivasoftltd.com/career/', 'jobs' => [
                'Full Stack Developer', '.NET Developer', 'DevOps Engineer',
            ]],
            ['company' => 'BRAC IT Services', 'website' => 'https://www.bracits.com', 'career_url' => 'https://www.bracits.com/career', 'jobs' => [
                'Software Engineer', 'Database Administrator', 'IT Support Specialist',
            ]],
            ['company' => 'Nascenia', 'website' => 'https://nascenia.com', 'career_url' => 'https://nascenia.com/careers/', 'jobs' => [
                'Ruby on Rails Developer', 'React Developer', 'QA Engineer',
            ]],
            ['company' => 'DataSoft Systems Bangladesh', 'website' => 'https://www.datasoft-bd.com', 'career_url' => 'https://www.datasoft-bd.com/career/', 'jobs' => [
                'Software Engineer', 'System Analyst', 'Project Coordinator',
            ]],
            ['company' => 'Cefalo Bangladesh', 'website' => 'https://www.cefalo.com', 'career_url' => 'https://www.cefalo.com/en/careers', 'jobs' => [
                'Software Engineer', 'Frontend Developer', 'Scrum Master',
            ]],
            ['company' => 'W3 Engineers', 'website' => 'https://w3engineers.com', 'career_url' => 'https://w3engineers.com/career/', 'jobs' => [
                'Android Developer', 'iOS Developer', 'Blockchain Developer',
            ]],
        ];

        $experienceLevels = ['junior', 'mid', 'senior', 'lead'];

        foreach ($bdJobs as $entry) {
            $company = Company::firstOrCreate(
                ['name' => $entry['company']],
                ['website' => $entry['website']]
            );

            foreach ($entry['jobs'] as $title) {
                $externalId = 'bd_seed_' . Str::slug($entry['company']) . '_' . Str::slug($title);

                JobListing::firstOrCreate(
                    ['external_id' => $externalId],
                    [
                        'title'            => $title,
                        'company_id'       => $company->id,
                        'category_id'      => $this->guessCategory($title),
                        'location_type'    => 'onsite',
                        'location'         => 'Dhaka, Bangladesh',
                        'experience_level' => $this->guessExperienceLevel($title, $experienceLevels),
                        'description'      => "Join {$entry['company']} as a {$title}. Visit the company career page to apply and see full details.",
                        'apply_link'       => $entry['career_url'],
                        'salary_range'     => null,
                        'status'           => 'approved',
                        'source'           => 'bd_career',
                        'external_id'      => $externalId,
                        'posted_at'        => now(),
                    ]
                );
            }
        }
    }

    protected function guessCategory(string $title): ?int
    {
        $title = strtolower($title);
        $categories = Category::pluck('id', 'slug')->toArray();

        if (Str::contains($title, ['frontend', 'backend', 'full stack', 'fullstack', 'web', 'react', 'angular', 'vue', '.net', 'ruby', 'rails', 'laravel', 'php', 'java', 'python', 'software engineer', 'software developer', 'blockchain'])) {
            return $categories['web-development'] ?? null;
        }
        if (Str::contains($title, ['mobile', 'android', 'ios', 'flutter'])) {
            return $categories['mobile-development'] ?? null;
        }
        if (Str::contains($title, ['devops', 'cloud', 'infrastructure', 'system admin', 'network', 'sre'])) {
            return $categories['devops'] ?? null;
        }
        if (Str::contains($title, ['data', 'machine learning', 'ai', 'ml', 'analytics', 'business intelligence'])) {
            return $categories['data-science'] ?? null;
        }
        if (Str::contains($title, ['design', 'ui', 'ux'])) {
            return $categories['design'] ?? null;
        }
        if (Str::contains($title, ['product manager', 'project manager', 'scrum', 'product owner'])) {
            return $categories['product-management'] ?? null;
        }
        if (Str::contains($title, ['qa', 'test', 'sqa', 'quality'])) {
            return $categories['qa-testing'] ?? null;
        }
        if (Str::contains($title, ['security', 'cyber'])) {
            return $categories['cybersecurity'] ?? null;
        }
        if (Str::contains($title, ['marketing', 'seo', 'content'])) {
            return $categories['marketing'] ?? null;
        }

        return $categories['web-development'] ?? null;
    }

    protected function guessExperienceLevel(string $title, array $levels): string
    {
        $t = strtolower($title);
        if (Str::contains($t, ['senior', 'sr.', 'lead', 'principal', 'architect'])) return 'senior';
        if (Str::contains($t, ['junior', 'jr.', 'intern', 'trainee', 'associate'])) return 'junior';
        if (Str::contains($t, ['head', 'director', 'chief', 'vp'])) return 'lead';
        return 'mid';
    }
}
