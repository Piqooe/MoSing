<script>
    (() => {
        const key = 'mosing:sidebar-scroll';
        const panes = document.querySelectorAll('[data-sidebar-scroll]');
        let saved = {};
        try { saved = JSON.parse(sessionStorage.getItem(key) || '{}'); } catch (e) {}

        panes.forEach((pane) => {
            const top = saved[pane.dataset.sidebarScroll];
            if (typeof top === 'number') {
                pane.scrollTop = top;
            } else {
                pane.querySelector('[aria-current="page"]')?.scrollIntoView({ block: 'nearest' });
            }
        });

        addEventListener('pagehide', () => {
            const state = {};
            panes.forEach((pane) => { state[pane.dataset.sidebarScroll] = pane.scrollTop; });
            try { sessionStorage.setItem(key, JSON.stringify(state)); } catch (e) {}
        });
    })();
</script>
