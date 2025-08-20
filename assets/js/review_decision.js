import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const messageModalElement = document.getElementById('messageModal');
    const confirmationModalElement = document.getElementById('confirmationModal');

    if (!messageModalElement || !confirmationModalElement) {
        console.error('One or more modals are missing.');
        return;
    }

    const messageModal = Modal.getOrCreateInstance(messageModalElement);
    const confirmationModal = Modal.getOrCreateInstance(confirmationModalElement);
    
    const modalMessageText = messageModalElement.querySelector('.modalMessageText');
    const confirmationMessage = confirmationModalElement.querySelector('#confirmationMessage');
    const confirmActionButton = confirmationModalElement.querySelector('#confirmActionButton');

    let actionToConfirm = null;

    const executeReviewDecision = () => {
        if (!actionToConfirm) return;

        const { action, reviewId } = actionToConfirm;

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
        })
        .finally(() => {
            actionToConfirm = null;
            confirmationModal.hide();
        });
    };

    const handleReviewDecisionClick = (event) => {
        const button = event.currentTarget;
        const action = button.dataset.action;
        const reviewId = button.dataset.reviewId;

        if (!action || !reviewId) {
            console.error('Action or review ID is missing.');
            return;
        }

        actionToConfirm = { action, reviewId };

        const actionText = action === 'validate' ? 'valider' : 'refuser';
        confirmationMessage.textContent = `Êtes-vous sûr de vouloir ${actionText} cet avis ?`;
        
        confirmationModal.show();
    };

    document.querySelectorAll('.validate-review-btn, .reject-review-btn').forEach(button => {
        button.addEventListener('click', handleReviewDecisionClick);
    });

    confirmActionButton.addEventListener('click', executeReviewDecision);
});