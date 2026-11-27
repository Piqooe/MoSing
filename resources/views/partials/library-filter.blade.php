<script>
    (() => {
        const activeClasses = ['bg-accent', 'text-black'];
        const idleClasses = ['bg-card', 'text-white'];
        let current = null;

        const apply = (filter) => {
            current = filter;
            document.querySelectorAll('[data-lib-filter]').forEach((chip) => {
                const on = chip.dataset.libFilter === filter;
                chip.setAttribute('aria-pressed', on ? 'true' : 'false');
                chip.classList.add(...(on ? activeClasses : idleClasses));
                chip.classList.remove(...(on ? idleClasses : activeClasses));
            });
            document.querySelectorAll('[data-lib-type]').forEach((item) => {
                item.style.display = item.dataset.libType === filter ? '' : 'none';
            });
        };

        document.querySelectorAll('[data-lib-filter]').forEach((chip) => {
            chip.addEventListener('click', () => apply(chip.dataset.libFilter));
        });

        window.libraryFilter = {
            current: () => current,
            apply,
        };

        const first = document.querySelector('[data-lib-filter]');
        if (first) apply(first.dataset.libFilter);
    })();
</script>
