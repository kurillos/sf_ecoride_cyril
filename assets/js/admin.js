document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.container[data-trip-counts]');

    if (container) {
        const tripCounts = container.dataset.tripCounts ? JSON.parse(container.dataset.tripCounts) : null;
        const creditEarnings = container.dataset.creditEarnings ? JSON.parse(container.dataset.creditEarnings) : null;

        if (tripCounts) {
            const tripLabels = Object.keys(tripCounts);
            const tripData = Object.values(tripCounts);
            const tripsCtx = document.getElementById('tripsChart').getContext('2d');
            if(tripsCtx) {
                new Chart(tripsCtx, {
                    type: 'line',
                    data: {
                        labels: tripLabels,
                        datasets: [{
                            label: 'Nombre de covoiturages',
                            data: tripData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 1,
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        if (creditEarnings) {
            const creditLabels = Object.keys(creditEarnings);
            const creditData = Object.values(creditEarnings);
            const creditsCtx = document.getElementById('creditsChart').getContext('2d');
            if(creditsCtx) {
                new Chart(creditsCtx, {
                    type: 'bar',
                    data: {
                        labels: creditLabels,
                        datasets: [{
                            label: 'Crédits gagnés (€)',
                            data: creditData,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    } else {
        console.error("Le conteneur de données n'a pas été trouvé ou les attributs de données sont manquants.");
    }
});
