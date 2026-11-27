const scrollRoot = document.querySelector('[data-scroll-root]');
const topbar = document.querySelector('[data-topbar]');

if (scrollRoot && topbar) {
    const update = () => topbar.toggleAttribute('data-scrolled', scrollRoot.scrollTop > 40);
    scrollRoot.addEventListener('scroll', update, { passive: true });
    update();
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-history]');
    if (!button) return;
    button.dataset.history === 'back' ? history.back() : history.forward();
});

const likedStorageKey = 'mosing:liked-song-overrides';
const toast = document.querySelector('[data-toast]');
const toastMessage = toast?.querySelector('[data-toast-message]');
const toastUndo = toast?.querySelector('[data-toast-undo]');
let likedOverrides = {};
let toastTimer;
let undoLike = null;

try {
    const stored = localStorage.getItem(likedStorageKey);
    if (stored) {
        const parsed = JSON.parse(stored);
        if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
            likedOverrides = parsed;
        }
    }
} catch (error) {
    console.error('Unable to read liked songs from browser storage:', error);
}

const persistLikedOverrides = () => {
    try {
        localStorage.setItem(likedStorageKey, JSON.stringify(likedOverrides));
        return true;
    } catch (error) {
        console.error('Unable to save liked songs to browser storage:', error);
        return false;
    }
};

const hasLikedOverride = (songId) => Object.prototype.hasOwnProperty.call(likedOverrides, songId);

const syncLikeButton = (button, liked) => {
    const title = button.dataset.songTitle || 'song';
    button.dataset.songLiked = String(liked);
    button.setAttribute('aria-pressed', String(liked));
    button.setAttribute('aria-label', `${liked ? 'Remove' : 'Save'} ${title} ${liked ? 'from' : 'to'} Liked Songs`);
    button.classList.toggle('text-accent', liked);
    button.classList.toggle('text-text-secondary', !liked);
    button.classList.toggle('opacity-0', !liked);
    button.querySelector('svg')?.classList.toggle('fill-current', liked);
};

const renderLikedList = () => {
    const list = document.querySelector('[data-liked-song-list]');
    if (!list) return;

    const rows = [...list.querySelectorAll('[data-liked-song-row]')];
    let count = 0;

    rows.forEach((row) => {
        const button = row.querySelector('[data-song-like]');
        const liked = button?.dataset.songLiked === 'true';
        row.hidden = !liked;
        if (liked) {
            count += 1;
            const number = row.querySelector('[data-track-number]');
            if (number) number.textContent = String(count);
        }
    });

    document.querySelectorAll('[data-liked-count]').forEach((element) => {
        element.textContent = String(count);
    });
    document.querySelectorAll('[data-liked-count-label]').forEach((element) => {
        element.textContent = count === 1 ? 'song' : 'songs';
    });

    const content = document.querySelector('[data-liked-songs-content]');
    const emptyState = document.querySelector('[data-liked-empty]');
    if (content) content.hidden = count === 0;
    if (emptyState) emptyState.hidden = count > 0;
};

const initializeLikeButtons = (root = document) => {
    root.querySelectorAll('[data-song-like]').forEach((button) => {
        const songId = button.dataset.songId;
        const liked = hasLikedOverride(songId)
            ? likedOverrides[songId] === true
            : button.dataset.songLiked === 'true';
        syncLikeButton(button, liked);
    });
    renderLikedList();
};

const showToast = (message, undo = null) => {
    if (!toast || !toastMessage || !toastUndo) return;

    clearTimeout(toastTimer);
    toastMessage.textContent = message;
    undoLike = undo;
    toastUndo.hidden = !undo;
    toast.hidden = false;
    toastTimer = setTimeout(() => {
        toast.hidden = true;
        undoLike = null;
    }, 5000);
};

const updateSongLike = (songId, liked) => {
    likedOverrides[songId] = liked;
    const persisted = persistLikedOverrides();

    document.querySelectorAll('[data-song-like]').forEach((button) => {
        if (button.dataset.songId === songId) syncLikeButton(button, liked);
    });
    renderLikedList();

    return persisted;
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-song-like]');
    if (button) {
        event.preventDefault();

        const songId = button.dataset.songId;
        if (!songId) return;

        const previous = button.dataset.songLiked === 'true';
        const liked = !previous;
        const persisted = updateSongLike(songId, liked);
        const title = button.dataset.songTitle || 'Song';

        showToast(
            `${liked ? 'Added' : 'Removed'} ${title} ${liked ? 'to' : 'from'} Liked Songs${persisted ? '' : ' (not saved on this device)'}`,
            () => updateSongLike(songId, previous),
        );
        return;
    }

    if (event.target.closest('[data-toast-undo]') && undoLike) {
        undoLike();
        toast.hidden = true;
        undoLike = null;
        clearTimeout(toastTimer);
        return;
    }

    if (event.target.closest('[data-toast-close]') && toast) {
        toast.hidden = true;
        undoLike = null;
        clearTimeout(toastTimer);
    }
});

window.MoSingLikes = { initialize: initializeLikeButtons };
initializeLikeButtons();
