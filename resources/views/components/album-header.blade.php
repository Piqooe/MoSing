@props(['album', 'totalDuration' => 0])

@php
    use App\Support\Artwork;
    use App\Support\Format;

    $hero = Artwork::resolve($album->artwork, $album->title)['accent'];
    $tint = "color-mix(in srgb, {$hero} 78%, #000)";
    $count = $album->songs->count();
    $songsLabel = $count.' '.str('song')->plural($count);
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

        <x-cover :src="$album->cover_url" :art="$album->artwork" :seed="$album->title"
            :label="$album->title" label-size="text-base md:text-sm lg:text-[17px]" :alt="$album->title"
            class="mx-auto size-[220px] rounded shadow-[0_8px_24px_rgb(0_0_0/0.5)] md:mx-0 md:size-48 md:rounded-md lg:size-[232px]" />

        <div class="flex min-w-0 flex-1 flex-col gap-2.5 pt-2.5 md:pt-0">
            <p class="hidden text-sm font-semibold md:block">{{ ucfirst($album->type ?? 'album') }}</p>

            <h1 class="text-2xl font-bold break-words md:line-clamp-2 md:pb-1 md:text-5xl md:leading-[1.15] md:tracking-[-1.4px] lg:text-6xl xl:text-[72px] xl:tracking-[-2.2px]">
                {{ $album->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-1.5 text-[13px] md:text-sm">
                <a href="{{ route('artists.show', $album->artist) }}" class="flex items-center gap-1.5 font-bold hover:underline">
                    <x-cover :src="$album->artist->photo_url" :art="$album->artist->artwork" :seed="$album->artist->name" :artist="true"
                        class="size-[22px] rounded-full md:size-6" />
                    {{ $album->artist->name }}
                </a>
                @if ($album->release_year)
                    <span class="text-text-secondary" aria-hidden="true">•</span>
                    <span class="text-text-secondary">{{ $album->release_year }}</span>
                @endif
                <span class="text-text-secondary" aria-hidden="true">•</span>
                <span class="text-text-secondary">{{ $songsLabel }}@if ($count), {{ Format::totalDuration($totalDuration) }}@endif</span>
            </div>
        </div>
    </div>
</header>
