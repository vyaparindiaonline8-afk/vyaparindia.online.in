{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Landing Pages -->
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/search') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/help') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Seller Mini-Storefronts -->
    @foreach ($sellerPages as $page)
        @if($page->slug)
            <url>
                <loc>{{ route('minisite.show', $page->slug) }}</loc>
                <lastmod>{{ $page->updated_at ? $page->updated_at->tz('UTC')->toAtomString() : now()->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.9</priority>
            </url>
            <url>
                <loc>{{ route('minisite.products', $page->slug) }}</loc>
                <changefreq>daily</changefreq>
                <priority>0.8</priority>
            </url>
            <url>
                <loc>{{ route('minisite.contact', $page->slug) }}</loc>
                <changefreq>monthly</changefreq>
                <priority>0.6</priority>
            </url>
        @endif
    @endforeach

    <!-- Public Products -->
    @foreach ($products as $prod)
        @if($prod->slug)
            <url>
                <loc>{{ route('product.show', $prod->slug) }}</loc>
                <lastmod>{{ $prod->updated_at ? $prod->updated_at->tz('UTC')->toAtomString() : now()->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
            @if($prod->user && $prod->user->sellerPage && $prod->user->sellerPage->slug)
                <url>
                    <loc>{{ route('minisite.product', [$prod->user->sellerPage->slug, $prod->slug]) }}</loc>
                    <lastmod>{{ $prod->updated_at ? $prod->updated_at->tz('UTC')->toAtomString() : now()->tz('UTC')->toAtomString() }}</lastmod>
                    <changefreq>weekly</changefreq>
                    <priority>0.8</priority>
                </url>
            @endif
        @endif
    @endforeach
</urlset>
