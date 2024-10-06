<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Checker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>
<body>
<section class="section">
    <div class="container">
        <h1 class="title">SEO Checker</h1>

        @if (session('message'))
            <div class="notification is-info">
                {{ session('message') }}
            </div>
        @endif

        <form action="{{ route('analyze') }}" method="POST">
            @csrf
            <div class="field">
                <label class="label">Enter URL to analyze</label>
                <div class="control">
                    <input class="input" type="url" name="url" placeholder="https://example.com" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit">Analyze</button>
                </div>
            </div>
        </form>

        @if (isset($results))
            <div class="box mt-5">
                <h2 class="title is-4">Analysis Results</h2>

                <div class="content">
                    <h3 class="title is-5">ARIA Landmarks</h3>
                    <ul>
                        @foreach ($results['aria_landmarks'] as $landmark => $count)
                            <li>{{ ucfirst($landmark) }}: {{ $count }}</li>
                        @endforeach
                    </ul>

                    <h3 class="title is-5">Image Alt Tags</h3>
                    <p>Total Images: {{ $results['img_alt_tags']['total_images'] }}</p>
                    <p>Images with Alt: {{ $results['img_alt_tags']['images_with_alt'] }}</p>
                    <p>Images without Alt: {{ $results['img_alt_tags']['images_without_alt'] }}</p>

                    <h3 class="title is-5">Color Contrast Issues</h3>
                    <p>Total Issues: {{ $results['color_contrast']['totalIssues'] }}</p>
                    @if ($results['color_contrast']['totalIssues'] > 0)
                        <ul>
                            @foreach ($results['color_contrast']['contrastIssues'] as $issue)
                                <li>{{ $issue['element'] }} - Background: {{ $issue['backgroundColor'] }}, Color: {{ $issue['color'] }}, Ratio: {{ $issue['contrastRatio'] }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <h3 class="title is-5">Heading Structure</h3>
                    <ul>
                        @foreach ($results['heading_structure']['headings'] as $heading => $count)
                            <li>{{ strtoupper($heading) }}: {{ $count }}</li>
                        @endforeach
                    </ul>
                    <p>Valid Structure: {{ $results['heading_structure']['is_valid_structure'] ? 'Yes' : 'No' }}</p>

                    <h3 class="title is-5">Form Labels</h3>
                    <p>Total Inputs: {{ $results['form_labels']['total_inputs'] }}</p>
                    <p>Labeled Inputs: {{ $results['form_labels']['labeled_inputs'] }}</p>
                    <p>Unlabeled Inputs: {{ $results['form_labels']['unlabeled_inputs'] }}</p>

                    <h3 class="title is-5">Keyboard Accessibility</h3>
                    <p>Interactive Elements: {{ $results['keyboard_accessibility']['interactive_elements'] }}</p>
                    <p>Elements with tabindex="-1": {{ $results['keyboard_accessibility']['tabindex_negative'] }}</p>
                    <p>Elements with outline: none: {{ $results['keyboard_accessibility']['outline_none'] }}</p>
                </div>
            </div>
        @endif
    </div>
</section>
</body>
</html>
