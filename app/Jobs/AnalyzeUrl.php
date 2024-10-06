<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;

class AnalyzeUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function handle()
    {
        $client = new Client();
        $response = $client->get($this->url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $results = [
            'aria_landmarks' => $this->checkAriaLandmarks($crawler),
            'img_alt_tags' => $this->checkImgAltTags($crawler),
            'color_contrast' => $this->checkColorContrast($this->url),
            'heading_structure' => $this->checkHeadingStructure($crawler),
            'form_labels' => $this->checkFormLabels($crawler),
            'keyboard_accessibility' => $this->checkKeyboardAccessibility($crawler),
        ];

        // TODO: Save or process the results as needed
        // For example, you might want to save this to a database or send a notification
        \Log::info('URL Analysis Results', $results);
    }

    protected function checkAriaLandmarks(Crawler $crawler)
    {
        $landmarks = [
            'header' => $crawler->filter('[role="banner"], header')->count(),
            'nav' => $crawler->filter('[role="navigation"], nav')->count(),
            'main' => $crawler->filter('[role="main"], main')->count(),
            'footer' => $crawler->filter('[role="contentinfo"], footer')->count(),
        ];

        return $landmarks;
    }

    protected function checkImgAltTags(Crawler $crawler)
    {
        $totalImages = $crawler->filter('img')->count();
        $imagesWithAlt = $crawler->filter('img[alt]')->count();
        $imagesWithoutAlt = $totalImages - $imagesWithAlt;

        return [
            'total_images' => $totalImages,
            'images_with_alt' => $imagesWithAlt,
            'images_without_alt' => $imagesWithoutAlt,
        ];
    }

    protected function checkColorContrast($url)
    {
        $process = new Process(['node', base_path('node_scripts/analyzeColorContrast.js'), $url]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return json_decode($process->getOutput(), true);
    }

    protected function checkHeadingStructure(Crawler $crawler)
    {
        $headings = [
            'h1' => $crawler->filter('h1')->count(),
            'h2' => $crawler->filter('h2')->count(),
            'h3' => $crawler->filter('h3')->count(),
            'h4' => $crawler->filter('h4')->count(),
            'h5' => $crawler->filter('h5')->count(),
            'h6' => $crawler->filter('h6')->count(),
        ];

        $isValid = $headings['h1'] === 1 && $headings['h2'] >= $headings['h3'] && $headings['h3'] >= $headings['h4'];

        return [
            'headings' => $headings,
            'is_valid_structure' => $isValid,
        ];
    }

    protected function checkFormLabels(Crawler $crawler)
    {
        $totalInputs = $crawler->filter('input:not([type="hidden"]), select, textarea')->count();
        $labeledInputs = $crawler->filter('input:not([type="hidden"])[id], select[id], textarea[id]')->filter(function (Crawler $node) {
            $id = $node->attr('id');
            return $node->siblings()->filter("label[for='$id']")->count() > 0;
        })->count();

        return [
            'total_inputs' => $totalInputs,
            'labeled_inputs' => $labeledInputs,
            'unlabeled_inputs' => $totalInputs - $labeledInputs,
        ];
    }

    protected function checkKeyboardAccessibility(Crawler $crawler)
    {
        $interactiveElements = $crawler->filter('a, button, input, select, textarea')->count();
        $tabindexNegative = $crawler->filter('[tabindex="-1"]')->count();
        $outlineNone = $crawler->filter('[style*="outline: none"], [style*="outline:none"]')->count();

        return [
            'interactive_elements' => $interactiveElements,
            'tabindex_negative' => $tabindexNegative,
            'outline_none' => $outlineNone,
        ];
    }
}
