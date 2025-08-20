import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const reportModalElement = document.getElementById('reportModal');
    const viewReportModalElement = document.getElementById('viewReportModal');
    const messageModalElement = document.getElementById('messageModal');
    const confirmationModalElement = document.getElementById('confirmationModal');

    if (!reportModalElement || !viewReportModalElement || !messageModalElement || !confirmationModalElement) {
        console.error('One or more modals are missing.');
        return;
    }

    const reportModal = Modal.getOrCreateInstance(reportModalElement);
    const viewReportModal = Modal.getOrCreateInstance(viewReportModalElement);
    const messageModal = Modal.getOrCreateInstance(messageModalElement);
    const confirmationModal = Modal.getOrCreateInstance(confirmationModalElement);

    const modalMessageText = messageModalElement.querySelector('.modalMessageText');
    const confirmationMessage = confirmationModalElement.querySelector('#confirmationMessage');
    const confirmActionButton = confirmationModalElement.querySelector('#confirmActionButton');

    let sanctionToConfirm = null;

    const executeSanction = () => {
        if (!sanctionToConfirm) return;

        const { reportId, sanctionType, sanctionComment } = sanctionToConfirm;

        fetch(`/employee/sanction/${reportId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                type: sanctionType,
                comment: sanctionComment,
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            reportModal.hide();
            modalMessageText.textContent = data.message;
            messageModal.show();
            const reportElement = document.getElementById(`report-${reportId}`);
            if (reportElement) {
                reportElement.remove();
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'application de la sanction:', error);
            modalMessageText.textContent = error.message || 'Une erreur est survenue lors de l\'application de la sanction.';
            messageModal.show();
        })
        .finally(() => {
            sanctionToConfirm = null;
            confirmationModal.hide();
        });
    };

    if (reportModalElement) {
        reportModalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const reportId = button.getAttribute('data-report-id');
            const modalTitle = document.getElementById('modal-report-id');
            if (modalTitle) {
                modalTitle.textContent = reportId;
            }

            fetch(`/employee/report/${reportId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur de réseau ou le signalement n\'existe pas.');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modal-departure-location').textContent = data.reportedTrip.departureLocation;
                    document.getElementById('modal-arrival-location').textContent = data.reportedTrip.destinationLocation;
                    document.getElementById('modal-trip-date').textContent = new Date(data.reportedTrip.tripDate).toLocaleDateString('fr-FR');
                    document.getElementById('modal-trip-time').textContent = data.reportedTrip.tripTime;
                    document.getElementById('modal-driver-name').textContent = data.reportedTrip.driver.firstName;
                    document.getElementById('modal-report-message').textContent = data.reason;

                    const passengersContainer = document.getElementById('modal-passengers');
                    passengersContainer.innerHTML = '';
                    if (data.reportedTrip.passengers && data.reportedTrip.passengers.length > 0) {
                        const passengerList = document.createElement('ul');
                        passengerList.className = 'list-unstyled';
                        passengersContainer.appendChild(passengerList);
                        data.reportedTrip.passengers.forEach(passenger => {
                            const li = document.createElement('li');
                            li.textContent = `Passager : ${passenger.firstName}`;
                            passengerList.appendChild(li);
                        });
                    } else {
                        passengersContainer.innerHTML = '<p>Aucun passager</p>';
                    }

                    const sanctionForm = document.getElementById('sanction-form');
                    sanctionForm.dataset.reportId = reportId;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données du signalement:', error);
                    const modalBody = reportModalElement.querySelector('.modal-body');
                    if (modalBody) {
                        modalBody.innerHTML = `<div class="alert alert-danger" role="alert">Une erreur est survenue lors du chargement des détails du signalement.</div>`;
                    }
                });
        });

        const sanctionForm = document.getElementById('sanction-form');
        sanctionForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const reportId = this.dataset.reportId;
            const sanctionType = document.getElementById('sanction-type').value;
            const sanctionComment = document.getElementById('sanction-comment').value;

            sanctionToConfirm = { reportId, sanctionType, sanctionComment };

            confirmationMessage.textContent = `Êtes-vous sûr de vouloir appliquer cette sanction ?`;
            confirmationModal.show();
        });
    }

    if (viewReportModalElement) {
        viewReportModalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const reportId = button.getAttribute('data-report-id');
            const modalTitle = document.getElementById('modal-view-report-id');
            if (modalTitle) {
                modalTitle.textContent = reportId;
            }

            fetch(`/employee/report/${reportId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur de réseau ou le signalement n\'existe pas.');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modal-view-departure-location').textContent = data.reportedTrip.departureLocation;
                    document.getElementById('modal-view-arrival-location').textContent = data.reportedTrip.destinationLocation;
                    document.getElementById('modal-view-trip-date').textContent = new Date(data.reportedTrip.tripDate).toLocaleDateString('fr-FR');
                    document.getElementById('modal-view-trip-time').textContent = data.reportedTrip.tripTime;
                    document.getElementById('modal-view-driver-name').textContent = data.reportedTrip.driver.firstName;
                    document.getElementById('modal-view-report-message').textContent = data.reason;

                    const passengersContainer = document.getElementById('modal-view-passengers');
                    passengersContainer.innerHTML = '';
                    if (data.reportedTrip.passengers && data.reportedTrip.passengers.length > 0) {
                        const passengerList = document.createElement('ul');
                        passengerList.className = 'list-unstyled';
                        passengersContainer.appendChild(passengerList);
                        data.reportedTrip.passengers.forEach(passenger => {
                            const li = document.createElement('li');
                            li.textContent = `Passager : ${passenger.firstName}`;
                            passengerList.appendChild(li);
                        });
                    } else {
                        passengersContainer.innerHTML = '<p>Aucun passager</p>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données du signalement pour la modale de visualisation:', error);
                    const modalBody = viewReportModalElement.querySelector('.modal-body');
                    if (modalBody) {
                        modalBody.innerHTML = `<div class="alert alert-danger" role="alert">Une erreur est survenue lors du chargement des détails du signalement.</div>`;
                    }
                });
        });
    }

    confirmActionButton.addEventListener('click', () => {
        if (sanctionToConfirm) {
            executeSanction();
        }
    });
});;