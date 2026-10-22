/**
 * Force a full reload when the browser restores a page from back/forward cache (bfcache).
 * After logout, "Back" would otherwise show a cached authenticated page without hitting the server.
 */
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});
