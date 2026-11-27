@props(['playlist', 'totalDuration' => 0])

@php
    use App\Support\Artwork;
    use App\Support\Format;

    $hasArt = ! empty((array) ($playlist->artwork ?? [])) || ! empty($playlist->cover_url);
    $hero = $hasArt ? Artwork::resolve($playlist->artwork, $playlist->title)['accent'] : '#3F3F46';
    $tint = "color-mix(in srgb, {$hero} 78%, #000)";

    $owner = $playlist->owner->name ?? 'MoSing';
    $isBrand = $owner === 'MoSing';
    $count = (int) ($playlist->songs_count ?? 0);
    $songsLabel = $count.' '.str('song')->plural($count);
    $saves = $playlist->saves ?? null;
    $label = ($playlist->is_public ?? true) ? 'Public Playlist' : 'Playlist';
@endphp

<header {{ $attributes->class(['relative md:-mt-16 md:pt-16']) }}
    style="--hero: {{ $tint }}">
    <div class="absolute inset-0 -z-10 bg-linear-to-b from-(--hero)/70 to-transparent md:to-(--hero)/20" aria-hidden="true"></div>

    <div class="flex flex-col gap-2.5 px-4 pb-2 md:flex-row md:items-end md:gap-7 md:px-6 md:pt-3 md:pb-7">
        <div class="md:hidden">
            <button type="button" data-history="back" aria-label="Go back"
                class="flex size-8 items-center justify-center rounded-full bg-black/40 text-white transition hover:bg-black/70">
                <x-lucide-chevron-left class="size-5" />
            </button>
        </div>

        <x-cover :src="$playlist->cover_url" :art="$playlist->artwork" :seed="$playlist->title" :empty="! $hasArt"
            :label="$hasArt ? $playlist->title : null" label-size="text-base md:text-sm lg:text-[17px]"
            :alt="$playlist->title"
            class="mx-auto size-[220px] rounded shadow-[0_8px_24px_rgb(0_0_0/0.5)] md:mx-0 md:size-48 md:rounded-md lg:size-[232px]" />

        <div class="flex min-w-0 flex-1 flex-col gap-2.5 pt-2.5 md:pt-0">
            <p class="hidden text-sm font-semibold md:block">{{ $label }}</p>

            <h1 class="text-2xl font-bold break-words md:line-clamp-2 md:pb-1 md:text-5xl md:leading-[1.15] md:tracking-[-1.4px] lg:text-6xl xl:text-[72px] xl:tracking-[-2.2px]">
                {{ $playlist->title }}
            </h1>

            @if ($playlist->description)
                <p class="line-clamp-2 text-[13px] text-text-secondary md:text-sm md:leading-[21px]">{{ $playlist->description }}</p>
            @endif

            <div class="flex flex-col gap-2.5 md:flex-row md:flex-wrap md:items-center md:gap-1.5 md:text-sm">
                <span class="flex items-center gap-2 md:gap-1.5">
                    @if ($isBrand)
                        <span class="flex size-[22px] items-center justify-center rounded-full bg-accent md:size-6">
                            <x-lucide-audio-waveform class="size-3 text-black md:size-3.5" />
                        </span>
                    @else
                        <span class="flex size-[22px] items-center justify-center rounded-full bg-linear-to-br from-[#8B5CF6] to-[#1E1B4B] text-[9px] font-bold md:size-6 md:text-[10px]">
                            {{ Format::initials($owner) }}
                        </span>
                    @endif
                    <a href="#" class="text-[13px] font-bold hover:underline md:text-sm">{{ $owner }}</a>
                </span>

                <p class="text-xs text-text-secondary md:contents md:text-sm">
                    @if ($saves)
                        <span class="md:hidden">{{ Format::plays($saves) }} saves • </span>
                        <span class="hidden md:inline" aria-hidden="true">•</span>
                        <span class="hidden md:inline">{{ Format::plays($saves) }} saves</span>
                    @endif
                    <span class="hidden md:inline" aria-hidden="true">•</span>
                    <span class="md:hidden">{{ $songsLabel }}@if ($count) • {{ Format::totalDuration($totalDuration, true) }}@endif</span>
                    <span class="hidden md:inline">{{ $songsLabel }}@if ($count), {{ Format::totalDuration($totalDuration) }}@endif</span>
                </p>
            </div>
        </div>
    </div>
</header>
