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

        <div class="box mt-5">
            <h2 class="title is-4">Sample Analysis Results</h2>
            <p class="subtitle is-6">This is a demo table with sample data</p>

            <table class="table is-fullwidth is-striped is-hoverable">
                <thead>
                <tr>
                    <th>Category</th>
                    <th>Metric</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>ARIA Landmarks</td>
                    <td>Header</td>
                    <td>1</td>
                    <td><span class="tag is-success">Good</span></td>
                </tr>
                <tr>
                    <td>ARIA Landmarks</td>
                    <td>Navigation</td>
                    <td>1</td>
                    <td><span class="tag is-success">Good</span></td>
                </tr>
                <tr>
                    <td>ARIA Landmarks</td>
                    <td>Main</td>
                    <td>1</td>
                    <td><span class="tag is-success">Good</span></td>
                </tr>
                <tr>
                    <td>ARIA Landmarks</td>
                    <td>Footer</td>
                    <td>1</td>
                    <td><span class="tag is-success">Good</span></td>
                </tr>
                <tr>
                    <td>Image Alt Tags</td>
                    <td>Images with Alt</td>
                    <td>8/10</td>
                    <td><span class="tag is-warning">Needs Improvement</span></td>
                </tr>
                <tr>
                    <td>Color Contrast</td>
                    <td>Contrast Issues</td>
                    <td>2</td>
                    <td><span class="tag is-danger">Poor</span></td>
                </tr>
                <tr>
                    <td>Heading Structure</td>
                    <td>Valid Structure</td>
                    <td>Yes</td>
                    <td><span class="tag is-success">Good</span></td>
                </tr>
                <tr>
                    <td>Form Labels</td>
                    <td>Labeled Inputs</td>
                    <td>5/6</td>
                    <td><span class="tag is-warning">Needs Improvement</span></td>
                </tr>
                <tr>
                    <td>Keyboard Accessibility</td>
                    <td>Interactive Elements</td>
                    <td>10</td>
                    <td><span class="tag is-success">Good</span></td>
                </tr>
                </tbody>
            </table>
        </div>

        @if (isset($results))
            <!-- Actual results display (as in the previous version) -->
        @endif
    </div>
</section>
</body>
</html>
