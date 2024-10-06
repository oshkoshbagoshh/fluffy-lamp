<?php

namespace App\Jobs;

//use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Browsershot\Browsershot;
use App\Models\SeoResult;

class AnalyzeUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    /**
     * Create a new job instance.
     */
    public function __construct($url)
    {
        //
        $this->url = $url;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //
        $page = Browsershot::url($this->url);

        $results = [
            'url' => $this->url,
            'aria_landmarks' => $this->checkAriaLandmarks($page),
            'image_alt_tags' => $this->checkImgAltTags($page),
            'color_contrast' => $this->checkColorContrast($page),
            'semantic_html' => $this->checkSemanticHtml($page),
            'broken_links' => $this->checkBrokenLinks($page),
            'meta_tags' => $this->checkMetaTags($page),
            'lazy_loading_images' => $this->checkLazyLoadingImages($page),
            'favicon' => $this->checkLazyLoadingImages($page),
            'mobile_friendly' => $this->checkMobileFriendly($page),
            'input_types' => $this->checkInputTypes($page),
        ];

        // total score
        $results['total_score'] = $this->calculateTotaleScore($results);

        SeoResult::updateOrCreate(
            ['url' => $this->url],
            $results
        );

//        Analyze subpages
        $subpages = $this->extractSubpages($page);
        foreach ($subpages as $subpage) {
            AnalyzeUrl::dispatch($subpage);
        }
    }

    protected function checkAriaLandmarks($page)
    {
        return $page->evaluate("() => {
            const landmarks = document.querySelectorAll('[role=\"banner\"], [role=\"navigation\"], [role=\"main\"], [role=\"complementary\"], [role=\"contentinfo\"]');
            return landmarks.length > 0 ? 1 : 0;
        }");

    }

    // TODO:   // Implement other check methods (checkImgAltTags, checkColorContrast, etc.) similarly

    // Implement other check methods (checkImgAltTags, checkColorContrast, etc.) similarly

    // extract subpages
    protected function extractSubpages($page)
    {
        return $page->evaluate("(baseUrl) => {
        const baseDomain = new URL(baseUrl).hostname;
        return Array.from(document.querySelectorAll('a[href]'))
        .map(a => a.href)
        .filter(href => .startsWith('#') && new ref).hostname === baseDomain)
        .slice(0, 35);
        }", [$this->url]);
    }

//    Calculate SEO Score
    protected function calculateTotalScore($results)
    {
        $weights = [
            'aria_landmarks' => 0.10,
            'img_alt_tags' => 0.10,
            'color_contrast' => 0.10,
            'semantic_html' => 0.20,
            'broken_links' => 0.20,
            'meta_tags' => 0.10,
            'lazy_loading_images' => 0.05,
            'favicon' => 0.05,
            'mobile_friendly' => 0.10,
            'input_types' => 0.10
        ];

        return array_sum(array_map(function ($key, $weight) use ($results) {
            return $results[$key] * $weight;
        }, array_keys($weights), $weights));
    }

}
