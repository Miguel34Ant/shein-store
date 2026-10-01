document.querySelectorAll('[data-favorite-toggle]').forEach((button) => {
    const storageKey = 'prisma-studio-favorites';
    const favorites = new Set(JSON.parse(localStorage.getItem(storageKey) || '[]'));
    const productId = button.dataset.productId;

    const syncButton = () => {
        const isFavorite = favorites.has(productId);
        button.setAttribute('aria-pressed', String(isFavorite));
        button.textContent = isFavorite ? '♥' : '♡';
    };

    syncButton();
    button.addEventListener('click', () => {
        if (favorites.has(productId)) {
            favorites.delete(productId);
        } else {
            favorites.add(productId);
        }
        localStorage.setItem(storageKey, JSON.stringify([...favorites]));
        syncButton();
    });
});