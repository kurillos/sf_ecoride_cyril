import { Collapse, Dropdown, Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const dropdownElementList = Array.from(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownElementList.map(dropdownToggleEl => new Dropdown(dropdownToggleEl));

    const collapseElementList = Array.from(document.querySelectorAll('[data-bs-toggle="collapse"]'));
    collapseElementList.map(collapseEl => new Collapse(collapseEl, { toggle: false }));

    if (typeof Modal === 'undefined') {
        console.error('Bootstrap Modal component not found');
    }
});

console.log("Bootstrap components (Collapse, Dropdown, Modal) initialized.");
