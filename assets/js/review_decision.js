import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const messageModalElement = document.getElementById('messageModal');
    if (!messageModalElement) {
        console.error('The message modal is missing.');
        return;
    }
    const messageModal = Modal.getOrCreateInstance(messageModalElement);
    const modalMessageText = messageModalElement.querySelector('.modalMessageText');

    const handleReviewDecision = (event) => {
        const button = event.currentTarget;
        const action = button.dataset.action;
        const reviewId = button.dataset.reviewId;

        if (!action || !reviewId) {
            console.error('Action or review ID is missing.');
            return;
        }

        fetch(`/employee/review/${action}/${reviewId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Une erreur est survenue.');
                });
            }
            return response.json();
        })
        .then(data => {
            const reviewElement = document.getElementById(`review-${reviewId}`);
            if (reviewElement) {
                reviewElement.remove();
            }
            modalMessageText.textContent = data.message;
            messageModal.show();
        })
        .catch(error => {
            console.error('Erreur lors de la décision sur l\'avis:', error);
            modalMessageText.textContent = error.message;
            messageModal.show();
        });
    };

    document.querySelectorAll('.validate-review-btn, .reject-review-btn').forEach(button => {
        button.addEventListener('click', handleReviewDecision);
    });
});