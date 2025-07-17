import 'bootstrap';
import * as bootstrap from 'bootstrap';

// Rendre l'objet Bootstrap globalement accessible
window.bootstrap = bootstrap;

console.log("Bootstrap JS initialisé et rendu global.");

// Initialisation des composants Bootstrap qui nécessitent une activation explicite
document.addEventListener('DOMContentLoaded', () => {
    // Activer les tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Activer les popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Activer les toasts
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });

    // Activer les onglets
    const triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button'));
    console.log('Found tab buttons:', triggerTabList.length);
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function (event) {
            console.log('Tab button clicked:', triggerEl.id);
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
