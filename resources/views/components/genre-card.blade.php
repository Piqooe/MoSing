@props(['genre', 'compact' => false, 'href' => '#'])

<a href="{{ $href }}" data-genre="{{ $genre['name'] }}"
    {{ $attributes->class([
        'group relative block overflow-hidden rounded-lg outline-none transition-[filter] hover:brightness-110',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent',
        $compact ? 'h-24 md:h-[110px]' : 'h-24 md:h-[140px]',
    ]) }}
    style="background-color: {{ $genre['color'] }}">
    <span class="absolute top-3 left-3.5 max-w-[60%] text-[17px] leading-tight font-bold tracking-[-0.4px] text-white md:top-4 md:left-[18px] md:text-[22px]">{{ $genre['name'] }}</span>

    <div class="absolute right-0 -bottom-1.5 size-[52px] transition-transform duration-200 group-hover:-translate-y-0.5 md:-right-3 md:-bottom-[18px] md:size-24">
        <x-cover :art="['palette' => $genre['palette'], 'pattern' => $genre['pattern']]" :seed="$genre['name']"
            class="size-full rounded shadow-[0_8px_24px_rgb(0_0_0/0.5)]" />
    </div>
</a>
