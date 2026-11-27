@props(['meta' => 'Date added', 'album' => true])

<div {{ $attributes->class([
    'hidden h-9 items-center gap-4 border-b border-border px-4 text-sm text-text-secondary md:grid',
    $album
        ? 'md:grid-cols-[32px_minmax(0,1fr)_minmax(0,180px)_32px_56px_18px] xl:grid-cols-[32px_minmax(0,1fr)_240px_140px_32px_56px_18px]'
        : 'md:grid-cols-[32px_minmax(0,1fr)_140px_32px_56px_18px]',
]) }}>
    <span class="text-center">#</span>
    <span>Title</span>
    @if ($album)
        <span>Album</span>
    @endif
    <span @class(['hidden', $album ? 'xl:block' : 'md:block'])>{{ $meta }}</span>
    <span aria-hidden="true"></span>
    <span class="flex justify-end">
        <x-lucide-clock-3 class="size-4" aria-label="Duration" />
    </span>
    <span aria-hidden="true"></span>
</div>
