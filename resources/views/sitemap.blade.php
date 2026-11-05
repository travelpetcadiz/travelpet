{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('inicio') }}</loc><changefreq>daily</changefreq></url>
    <url><loc>{{ route('transporte') }}</loc><changefreq>monthly</changefreq></url>
@foreach ($guia as $url)
    <url><loc>{{ $url }}</loc><changefreq>weekly</changefreq></url>
@endforeach
@foreach ($sitios as $s)
    <url><loc>{{ route('sitio', $s) }}</loc><lastmod>{{ $s->updated_at->toDateString() }}</lastmod></url>
@endforeach
</urlset>
