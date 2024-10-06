<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AnalyzeUrl;

class AnalyzeSeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
//    protected $signature = 'app:analyze-seo';
    protected $signature = 'seo:analyze {urls*}';

    /**
     * The console command description.
     *
     * @var string
     */
//    protected $description = 'Command description';
    protected  $description = 'Analyze SEO for given URLs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $urls = $this->argument('urls');

        foreach ($urls as $url) {
            AnalyzeUrl::dispatch($url);
        }

        $this->info('Analysis jobs have been dispatched.');
    }
}
