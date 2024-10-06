<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

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
            'color_contrast' => $this->checkColorContrast($crawler),
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

    protected function checkColorContrast(Crawler $crawler)
    {
        // Note: Accurate color contrast checking often requires more complex analysis,
        // potentially involving JavaScript rendering and computation.
        // This is a simplified check that looks for the presence of CSS custom properties
        // often used for theming and color management.

        $hasColorVariables = $crawler->filter('style')->reduce(function (Crawler $node) {
                return strpos($node->text(), '--color') !== false;
            })->count() > 0;

        return [
            'uses_color_variables' => $hasColorVariables,
            'note' => 'Full color contrast analysis requires client-side rendering and computation.',
        ];
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

        return $headings;
    }

    protected function checkFormLabels(Crawler $crawler)
    {
        $totalInputs = $crawler->filter('input:not([type="hidden"]), select, textarea')->count();
        $labeledInputs = $crawler->filter('input:not([type="hidden"])[id], select[id], textarea[id]')->filter(function (Crawler $node) {
            $id = $node->attr('id');
            return $node->parents()->filter('form')->filter('label[for="' . $id . '"]')->count() > 0;
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
        $tabindexCount = $crawler->filter('[tabindex]')->count();
        $positiveTabindex = $crawler->filter('[tabindex]:not([tabindex="-1"])')->count();

        return [
            'interactive_elements' => $interactiveElements,
            'elements_with_tabindex' => $tabindexCount,
            'elements_with_positive_tabindex' => $positiveTabindex,
        ];
    }
}
