const confirmActionButton = document.getElementById('confirmActionButton');
const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
const modalMessageText = document.querySelector('.modalMessageText');

let actionToConfirm = null;

const executeReviewDecision = () => {
    if (!actionToConfirm) {
        return;
    }

    const { action, reviewId } = actionToConfirm;

    fetch(`/employee/review/${action}/${reviewId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            modalMessageText.textContent = data.message;
            messageModal.show();
            const reviewElement = document.getElementById(`review-${reviewId}`);
            if (reviewElement) {
                reviewElement.remove();
            }
        } else {
            modalMessageText.textContent = data.message;
            messageModal.show();
        }
    })
    .catch(error => {
        console.error('Erreur lors de la décision d\'avis:', error);
        modalMessageText.textContent = 'Une erreur est survenue lors du traitement de votre demande.';
        messageModal.show();
    })
    .finally(() => {
        confirmationModal.hide();
        actionToConfirm = null;
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

    document.getElementById('confirmationMessage').textContent = `Êtes-vous sûr de vouloir ${action === 'validate' ? 'valider' : 'rejeter'} cet avis ?`;
    actionToConfirm = { action, reviewId };
    confirmationModal.show();
};

document.querySelectorAll('.validate-review-btn, .reject-review-btn').forEach(button => {
    button.addEventListener('click', handleReviewDecisionClick);
});

confirmActionButton.addEventListener('click', executeReviewDecision);
