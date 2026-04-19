<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = App\Models\JobListing::where('source', 'bd_career')->with('company')->get();
foreach ($jobs as $j) {
    echo $j->company->name . ' | ' . $j->title . ' | ' . $j->apply_link . PHP_EOL;
}
