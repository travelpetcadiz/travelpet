{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('inicio') }}</loc><changefreq>daily</changefreq></url>
@foreach ($sitios as $s)
    <url><loc>{{ route('sitio', $s) }}</loc><lastmod>{{ $s->updated_at->toDateString() }}</lastmod></url>
@endforeach
</urlset>
