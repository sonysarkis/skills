<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes SPA</title>
    
    @php
        $manifestPath = public_path('vendor/skills/.vite/manifest.json');
        
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $jsFile = 'vendor/skills/' . $manifest['resources/js/main.ts']['file'];
            if (isset($manifest['resources/js/main.ts']['css'])) {
                $cssFile = 'vendor/skills/' . $manifest['resources/js/main.ts']['css'][0];
            }
        }
    @endphp

    @if(isset($cssFile))
        <link rel="stylesheet" href="{{ asset($cssFile) }}">
    @endif
</head>
<body>
    <div id="skills-app"></div>

    @if(isset($jsFile))
        <script type="module" src="{{ asset($jsFile) }}"></script>
    @else
        <p style="color: red; text-align: center;">Please run "php artisan vendor:publish --tag=quotes-assets" to load the interface.</p>
    @endif
</body>
</html>
