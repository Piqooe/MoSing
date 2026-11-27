<dialog data-create-playlist
    class="m-auto w-[min(440px,calc(100vw-2rem))] rounded-xl border border-border bg-[#181818] p-6 text-white shadow-[0_16px_48px_rgb(0_0_0/0.6)] backdrop:bg-black/60">
    <div class="flex flex-col gap-3">
        <span class="flex size-[52px] items-center justify-center rounded-lg bg-accent">
            <x-lucide-list-music class="size-6 text-black" />
        </span>
        <h2 class="text-xl font-bold">Buat playlist baru</h2>
        <p class="text-sm text-text-secondary">Beri nama playlist kamu. Lagu bisa ditambahkan setelahnya.</p>
    </div>

    <form method="POST" action="{{ route('playlists.store') }}" class="mt-5 flex flex-col gap-4">
        @csrf

        <div class="flex flex-col gap-1.5">
            <label for="create-playlist-title" class="text-sm font-semibold">Nama playlist</label>
            <input id="create-playlist-title" type="text" name="title" value="{{ old('title') }}" required maxlength="120"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white placeholder:text-text-secondary focus:border-accent focus:outline-none">
            @error('title')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="create-playlist-description" class="text-sm font-semibold">Deskripsi <span class="font-normal text-text-secondary">(opsional)</span></label>
            <textarea id="create-playlist-description" name="description" rows="3" maxlength="500"
                class="w-full resize-none rounded-md border border-border bg-card px-3.5 py-2 text-sm text-white placeholder:text-text-secondary focus:border-accent focus:outline-none">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-1 flex gap-3">
            <x-button variant="secondary" class="flex-1" data-create-playlist-close>Batal</x-button>
            <x-button type="submit" class="flex-1">Buat</x-button>
        </div>
    </form>
</dialog>

<script>
    (() => {
        const dialog = document.querySelector('[data-create-playlist]');
        if (!dialog) return;

        const open = () => {
            if (!dialog.open) dialog.showModal();
            dialog.querySelector('input[name="title"]').focus();
        };
        const close = () => dialog.close();

        window.openCreatePlaylistModal = open;

        dialog.querySelectorAll('[data-create-playlist-close]').forEach((button) => {
            button.addEventListener('click', close);
        });
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) close();
        });

        @if ($errors->has('title') || $errors->has('description'))
            open();
        @endif
    })();
</script>
