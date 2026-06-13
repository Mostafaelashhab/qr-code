{{-- Use the compiled Vite assets when available; otherwise fall back to the
     Tailwind v4 browser build so the UI still renders before `npm run build`. --}}
@if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
@endif
