<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\AnalyzeUrl;

class SeoCheckerController extends Controller
{
    public function index()
    {
        return view('seo-checker');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');

        // Dispatch the job
        AnalyzeUrl::dispatch($url);

        // For now, we'll redirect back with a message
        // In a real application, you might want to use websockets or polling to get real-time results
        return redirect()->route('home')->with('message', 'Analysis started for ' . $url . '. Results will be available soon.');
    }
}
