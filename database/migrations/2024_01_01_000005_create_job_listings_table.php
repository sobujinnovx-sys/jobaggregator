<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('location_type', ['remote', 'onsite', 'hybrid'])->default('onsite');
            $table->string('location')->nullable(); // City/Country
            $table->enum('experience_level', ['junior', 'mid', 'senior', 'lead'])->default('mid');
            $table->text('description')->nullable();
            $table->string('apply_link');
            $table->string('salary_range')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('source')->default('manual'); // manual, scraped
            $table->string('external_id')->nullable()->unique(); // Prevent duplicate scraped jobs
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['location_type', 'experience_level', 'status']);

            // Fulltext indexes only supported by MySQL/MariaDB
            if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
                $table->fullText(['title', 'description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
