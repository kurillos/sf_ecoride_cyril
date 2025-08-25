function initializeRating() {
    const starRatingContainer = document.getElementById('ratingStarsForDriver');
    
    if (!starRatingContainer || starRatingContainer.dataset.initialized) {
        return;
    }
    starRatingContainer.dataset.initialized = true;
    starRatingContainer.innerHTML = '';

    const ratingInputId = starRatingContainer.dataset.targetInputId;
    const ratingInput = document.getElementById(ratingInputId);
    
    if (!ratingInput) {
        console.error(`L'input cible pour la notation (ID: ${ratingInputId}) n'a pas été trouvé. Vérifiez le template Twig.`);
        return;
    }

    const stars = [];

    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('span');
        star.classList.add('bi', 'bi-star', 'star');
        star.innerHTML = '&#9733;';
        starRatingContainer.appendChild(star);
        stars.push(star);

        star.addEventListener('mouseover', () => {
            hoverStars(i);
        });

        star.addEventListener('mouseout', () => {
            const currentRating = parseInt(ratingInput.value, 10) || 0;
            updateStars(currentRating);
        });

        star.addEventListener('click', () => {
            ratingInput.value = i;
            console.log(`La note a été mise à jour à : ${ratingInput.value}`);
            updateStars(i);
        });
    }

    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
                star.classList.remove('hover');
            } else {
                star.classList.remove('active');
                star.classList.remove('hover');
            }
        });
    }

    function hoverStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('hover');
            } else {
                star.classList.remove('hover');
            }
        });
    }

    const initialRating = parseInt(starRatingContainer.dataset.initialRating, 10);
    if (initialRating > 0) {
        updateStars(initialRating);
    }
}

document.addEventListener('DOMContentLoaded', initializeRating);
document.addEventListener('turbo:load', initializeRating);
