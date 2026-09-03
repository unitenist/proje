<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="author" content="Sermet Sandıkçı">

    <title inertia>{{ config('app.name', 'UNITENIST') }}</title>

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('unitenist-theme');
                var theme = stored === 'light' || stored === 'dark'
                    ? stored
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="bg-bg font-sans text-[color:var(--color-text)] antialiased">
    @inertia
</body>
</html>
