@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'noindex' => false,
])

@php
    $appName = __('ui.app_name');
    $fullTitle = $title ? $title.' · '.$appName : $appName.' · '.__('ui.tagline');
    $desc = $description ?: __('ui.tagline');
    $ogImage = url($image ?: '/og-image.svg');
    $ogLocale = app()->getLocale() === 'ar' ? 'ar_AR' : 'en_US';
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $desc }}">
@if ($noindex)
    <meta name="robots" content="noindex, nofollow">
@endif
<link rel="canonical" href="{{ url()->current() }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="{{ $ogLocale }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image" content="{{ $ogImage }}">

{{-- Icons & PWA --}}
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/favicon.svg">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#4f46e5">
