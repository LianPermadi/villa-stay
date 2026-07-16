@extends('layouts.app')

@section('title', 'Beranda - '.$settings->text('site_name'))

@section('content')
<section class="hero-section min-h-screen flex items-center justify-center text-center text-white px-4"
    style="background-image: linear-gradient(135deg, rgba(45,90,39,.9), rgba(30,61,26,.95)), url('{{ $settings->backgroundUrl('hero') }}');">
    <div class="max-w-4xl mx-auto animate-in stagger-1">
        <h1 class="font-display text-5xl md:text-7xl font-bold mb-6 leading-tight">{{ $settings->text('hero_title') }}</h1>
        <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-2xl mx-auto">{{ $settings->text('hero_subtitle') }}</p>
        <a href="{{ route('villas.index') }}" class="btn-primary text-lg px-8 py-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
            {{ $settings->text('hero_button') }}
        </a>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-in">
            <h2 class="font-display text-4xl font-bold text-primary mb-4">{{ $settings->text('features_title') }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $settings->text('features_subtitle') }}</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center animate-in stagger-1">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                <h3 class="font-display text-xl font-semibold mb-2">{{ $settings->text('feature_1_title') }}</h3>
                <p class="text-gray-600">{{ $settings->text('feature_1_description') }}</p>
            </div>
            <div class="text-center animate-in stagger-2">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                <h3 class="font-display text-xl font-semibold mb-2">{{ $settings->text('feature_2_title') }}</h3>
                <p class="text-gray-600">{{ $settings->text('feature_2_description') }}</p>
            </div>
            <div class="text-center animate-in stagger-3">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <h3 class="font-display text-xl font-semibold mb-2">{{ $settings->text('feature_3_title') }}</h3>
                <p class="text-gray-600">{{ $settings->text('feature_3_description') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12 animate-in">
            <h2 class="font-display text-4xl font-bold text-primary">{{ $settings->text('featured_title') }}</h2>
            <a href="{{ route('villas.index') }}" class="text-primary hover:text-primary-dark font-semibold flex items-center gap-2">
                {{ $settings->text('featured_link') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($featuredVillas as $index => $villa)
            <div class="card-villa animate-in stagger-{{ $index + 1 }}">
                <div class="h-48 relative overflow-hidden">
                    <img src="{{ $villa->primary_image_url }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                    <span class="absolute top-4 left-4 badge badge-available">{{ $settings->text('featured_badge') }}</span>
                    <span class="absolute top-4 right-4 badge {{ $villa->is_occupied_today ? 'badge-cancelled' : 'badge-available' }}">{{ $villa->occupancy_label }}</span>
                </div>
                <div class="p-6">
                    <h3 class="font-display text-xl font-semibold mb-2">{{ $villa->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $villa->description }}</p>
                    <div class="flex items-center justify-between"><span class="text-primary font-bold text-lg">{{ $villa->formatted_price }}</span><span class="text-gray-500 text-sm">{{ $settings->text('per_night_text') }}</span></div>
                    <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                        <span>{{ $villa->capacity }} {{ $settings->text('guest_unit') }}</span>
                        <span>{{ $villa->bedrooms }} {{ $settings->text('bedroom_unit') }}</span>
                    </div>
                    <a href="{{ route('villas.show', $villa->id) }}" class="btn-primary w-full justify-center mt-4 text-sm">{{ $settings->text('details_button') }}</a>
                </div>
            </div>
            @empty
            <div class="lg:col-span-4 text-center py-12 text-gray-500">{{ $settings->text('featured_empty') }}</div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-secondary">{{ $settings->text('location_kicker') }}</p>
                <h2 class="font-display text-4xl font-bold text-primary mt-2">{{ $settings->text('location_title') }}</h2>
                <p class="text-gray-600 text-lg mt-4">{{ $settings->text('location_subtitle') }}</p>
                <div class="mt-6 rounded-2xl bg-light border border-primary/10 p-5">
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $settings->text('address_label') }}</p>
                    <p class="mt-2 text-xl font-semibold text-gray-800 whitespace-pre-line">{{ $settings->address ?: 'Dili, Timor-Leste' }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ $settings->latitude }}, {{ $settings->longitude }}</p>
                </div>
                <a href="{{ $mapDirectionsUrl }}" target="_blank" rel="noopener" class="btn-primary mt-6">{{ $settings->text('map_button') }}</a>
            </div>
            <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-200 min-h-[420px]">
                <iframe src="{{ $mapEmbedUrl }}" title="Peta {{ $settings->text('site_name') }}" class="w-full h-[420px]" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-primary text-white bg-cover bg-center"
    @if($settings->backgroundUrl('cta')) style="background-image: linear-gradient(rgba(45,90,39,.9), rgba(30,61,26,.93)), url('{{ $settings->backgroundUrl('cta') }}');" @endif>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-display text-4xl font-bold mb-4">{{ $settings->text('cta_title') }}</h2>
        <p class="text-xl mb-8 text-gray-200">{{ $settings->text('cta_subtitle') }}</p>
        <a href="{{ route('villas.index') }}" class="btn-secondary text-white border-white hover:bg-white hover:text-primary">{{ $settings->text('cta_button') }}</a>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.animate-in').forEach(element => observer.observe(element));
    });
</script>
@endsection
