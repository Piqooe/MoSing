<script>
    (() => {
        const chips = [...document.querySelectorAll('[data-lib-filter]')];
        const items = [...document.querySelectorAll('[data-lib-type]')];
        const activeClasses = ['bg-accent', 'text-black'];
        const idleClasses = ['bg-card', 'text-white'];

        const apply = (filter) => {
            chips.forEach((chip) => {
                const on = chip.dataset.libFilter === filter;
                chip.setAttribute('aria-pressed', on ? 'true' : 'false');
                chip.classList.add(...(on ? activeClasses : idleClasses));
                chip.classList.remove(...(on ? idleClasses : activeClasses));
            });
            items.forEach((item) => {
                item.style.display = item.dataset.libType === filter ? '' : 'none';
            });
        };

        chips.forEach((chip) => chip.addEventListener('click', () => apply(chip.dataset.libFilter)));

        if (chips.length) apply(chips[0].dataset.libFilter);
    })();
</script>
