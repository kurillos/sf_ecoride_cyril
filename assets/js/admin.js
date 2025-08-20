document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const tripData = JSON.parse(body.getAttribute('data-trip-counts'));
    const creditData = JSON.parse(body.getAttribute('data-credit-earnings'));

    // Graphique covoiturage
    const tripsCtx = document.getElementById('tripsChart');
    if (tripsCtx) {
        new Chart(tripsCtx, {
            type: 'line',
            data: {
                labels: Object.keys(tripData),
                datasets: [{
                    label: 'Nombre de covoiturages',
                    data: Object.values(tripData),
                    borderColor: '#0d6efd',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Graphique crédits
    const creditsCtx = document.getElementById('creditsChart');
    if (creditsCtx) {
        new Chart(creditsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(creditData),
                datasets: [{
                    label: 'Credits gagnés',
                    data: Object.values(creditData),
                    backgroundColor: 'rgba(25, 135, 84, 0.5)',
                    borderColor: '#198754',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }    
});