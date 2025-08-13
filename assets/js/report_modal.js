document.addEventListener('DOMContentLoaded', () => {


    const reportModalElement = document.getElementById('reportModal');
    const reportModal = new bootstrap.Modal(reportModalElement);
    const takeOwnershipBtns = document.querySelectorAll('.take-ownership-btn');
    const sanctionForm = document.getElementById('sanction-form');

    const viewReportModalElement = document.getElementById('viewReportModal');
    const viewReportModal = new bootstrap.Modal(viewReportModalElement);
    const viewReportBtns = document.querySelectorAll('.view-report-btn');

    const showSanctionModal = (reportId, tripId, reportReason) => {
        const dummyData = {
            reportId: reportId,
            trip: {
                id: tripId,
                departureLocation: "Paris",
                arrivalLocation: "Bordeaux",
                date: "13/08/2025",
                time: "10:00",
                driver: "Jose",
                passengers: ["John Doe", "Jane Doe"]
            },
            report: {
                message: reportReason || "Aucun motif spécifié."
            }
        };

        document.getElementById('modal-report-id').textContent = dummyData.reportId;
        document.getElementById('modal-departure-location').textContent = dummyData.trip.departureLocation;
        document.getElementById('modal-arrival-location').textContent = dummyData.trip.arrivalLocation;
        document.getElementById('modal-trip-date').textContent = dummyData.trip.date;
        document.getElementById('modal-trip-time').textContent = dummyData.trip.time;
        document.getElementById('modal-driver-name').textContent = dummyData.trip.driver;
        document.getElementById('modal-report-message').textContent = dummyData.report.message;

        const passengersContainer = document.getElementById('modal-passengers');
        passengersContainer.innerHTML = '';
        dummyData.trip.passengers.forEach(passenger => {
            const p = document.createElement('p');
            p.innerHTML = `<strong>Passager :</strong> ${passenger}`;
            passengersContainer.appendChild(p);
        });

        reportModal.show();
    };

    const showViewModal = (reportId, tripId, reportReason) => {
        const dummyData = {
            reportId: reportId,
            trip: {
                id: tripId,
                departureLocation: "Paris",
                arrivalLocation: "Bordeaux",
                date: "13/08/2025",
                time: "10:00",
                driver: "Jose",
                passengers: ["John Doe", "Jane Doe"]
            },
            report: {
                message: reportReason || "Aucun motif spécifié."
            }
        };

        document.getElementById('modal-view-report-id').textContent = dummyData.reportId;
        document.getElementById('modal-view-departure-location').textContent = dummyData.trip.departureLocation;
        document.getElementById('modal-view-arrival-location').textContent = dummyData.trip.arrivalLocation;
        document.getElementById('modal-view-trip-date').textContent = dummyData.trip.date;
        document.getElementById('modal-view-trip-time').textContent = dummyData.trip.time;
        document.getElementById('modal-view-driver-name').textContent = dummyData.trip.driver;
        document.getElementById('modal-view-report-message').textContent = dummyData.report.message;

        const passengersContainer = document.getElementById('modal-view-passengers');
        passengersContainer.innerHTML = '';
        dummyData.trip.passengers.forEach(passenger => {
            const p = document.createElement('p');
            p.innerHTML = `<strong>Passager :</strong> ${passenger}`;
            passengersContainer.appendChild(p);
        });

        viewReportModal.show();
    };

    takeOwnershipBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const reportId = e.target.dataset.reportId;
            const tripId = e.target.dataset.tripId;
            const reportReason = e.target.dataset.reportReason;
            showSanctionModal(reportId, tripId, reportReason);
        });
    });

    viewReportBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const reportId = e.target.dataset.reportId;
            const tripId = e.target.dataset.tripId;
            const reportReason = e.target.dataset.reportReason;
            showViewModal(reportId, tripId, reportReason);
        });
    });

    sanctionForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const sanctionType = document.getElementById('sanction-type').value;
        const sanctionComment = document.getElementById('sanction-comment').value;

        console.log('Formulaire de sanction soumis !');
        console.log(`Type de sanction : ${sanctionType}`);
        console.log(`Commentaire : ${sanctionComment}`);

        reportModal.hide();
    });
});
