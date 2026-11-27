<script>
    (() => {
        const tokenOf = (form) => form.querySelector('input[name="_token"]')?.value ?? '';

        const setButton = (button, on) => {
            button.setAttribute('aria-pressed', on ? 'true' : 'false');

            if (button.dataset.variant === 'icon') {
                button.classList.toggle('text-accent', on);
                button.classList.toggle('text-text-secondary', !on);
                button.classList.toggle('hover:text-white', !on);
                button.querySelector('svg')?.classList.toggle('fill-current', on);
                if (button.dataset.labelOn) button.setAttribute('aria-label', on ? button.dataset.labelOn : button.dataset.labelOff);
                return;
            }

            button.textContent = on ? button.dataset.on : button.dataset.off;
            button.classList.toggle('bg-accent', on);
            button.classList.toggle('text-black', on);
            button.classList.toggle('border-accent', on);
            button.classList.toggle('border-outline', !on);
            button.classList.toggle('text-white', !on);
        };

        const refreshLibrary = async () => {
            const html = await (await fetch(location.href, { headers: { Accept: 'text/html' } })).text();
            const fresh = new DOMParser().parseFromString(html, 'text/html');
            const pane = document.querySelector('[data-sidebar-scroll="library"]');
            const freshPane = fresh.querySelector('[data-sidebar-scroll="library"]');
            if (!pane || !freshPane) return;

            const filter = window.libraryFilter?.current();
            pane.innerHTML = freshPane.innerHTML;
            if (filter) window.libraryFilter?.apply(filter);
        };

        document.querySelectorAll('form[data-library-toggle]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('button');
                button.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': tokenOf(form),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });

                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    const data = await response.json();
                    setButton(button, data.following ?? data.saved ?? false);
                    await refreshLibrary();
                } finally {
                    button.disabled = false;
                }
            });
        });
    })();
</script>
