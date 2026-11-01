{{-- Crédito de una foto: quién la hizo y, si es libre, su licencia con enlace (lo exigen las licencias CC) --}}
@props(['foto'])
@php($c = $foto->credito())
<figcaption class="text-xs text-gris">
    @if ($c['autor_url'])
        <a href="{{ $c['autor_url'] }}" target="_blank" rel="noopener nofollow" class="underline">{{ $c['texto'] }}</a>
    @else
        {{ $c['texto'] }}
    @endif
    @if ($c['licencia'])
        ·
        @if ($c['licencia_url'])
            <a href="{{ $c['licencia_url'] }}" target="_blank" rel="noopener nofollow license" class="underline">{{ $c['licencia'] }}</a>
        @else
            {{ $c['licencia'] }}
        @endif
    @endif
</figcaption>
