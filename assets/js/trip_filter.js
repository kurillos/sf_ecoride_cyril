import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

document.addEventListener('DOMContentLoaded', () => {
    function setupNoUiSliderFilter(sliderId, minInputId, maxInputId, rangeMin, rangeMax, unit = '') {
        const sliderElement = document.getElementById(sliderId);
        const minInput = document.getElementById(minInputId);
        const maxInput = document.getElementById(maxInputId);
        

        if (!sliderElement || !minInput || !maxInput) {
            console.warn(`Element manquant pour ${sliderId} filter.`);
            return;
        }

        // Initialisation de noUiSlider
        noUiSlider.create(sliderElement, {
            start: [parseFloat(minInput.value), parseFloat(maxInput.value)],
            connect: true,
            range: {
                'min': rangeMin,
                'max': rangeMax
            },
            step: 1,
            pips: {
                mode: 'range',
                density: 4,
                format: {
                    to: function(value) {
                        return value + unit;
                    }
                }
            }
        });

        sliderElement.noUiSlider.on('update', (values, handle) => {
            // poignée de gauche Mini = 0 / poignée de droite Max = 1
            if (handle === 0) {
                minInput.value = parseFloat(values[0]);
            } else {
                maxInput.value = parseFloat(values[1]);
            }
        });

        // empêche les valeurs de dépasser les limites
        minInput.addEventListener('change', () => {
            const newValue = parseFloat(minInput.value);
            if (newValue > sliderElement.noUiSlider.get()[1]) {
                minInput.value = sliderElement.noUiSlider.get()[1];
            }
            sliderElement.noUiSlider.set([newValue, null]);
        });

        maxInput.addEventListener('change', () => {
            const newValue = parseFloat(maxInput.value);
            if (newValue < sliderElement.noUiSlider.get()[0]) {
                maxInput.value = sliderElement.noUiSlider.get()[0];
            }
            sliderElement.noUiSlider.set([null, newValue]);
        });

        minInput.addEventListener('input', () => {
            if (parseInt(minInput.value) > parseInt(maxInput.value)) {
                maxInput.value = minInput.value;
                sliderElement.noUiSlider.set([parseInt(minInput.value), parseInt(minInput.value)]);
            }
        });

        maxInput.addEventListener('input', () => {
            if (parseInt(maxInput.value) < parseInt(minInput.value)) {
                minInput.value = maxInput.value;
                sliderElement.noUiSlider.set([parseInt(maxInput.value), parseInt(maxInput.value)]);
            }
        });
    }

    // Système de notation par étoiles
    function setupStarRating(containerId, hiddenInputId, maxStars = 5) {
        const container = document.getElementById(containerId);
        const hiddenInput = document.getElementById(hiddenInputId);

        if (!container || !hiddenInput) {
            console.warn(`Conteneur ou champ caché manquant pour les étoiles (${containerId}).`);
            return;
        }

        let currentRating = parseInt(hiddenInput.value) || 0;
        
        // Créer les étoiles une seule fois
        for (let i = 1; i <= maxStars; i++) {
            const star = document.createElement('span');
            star.classList.add('star', 'bi');
            star.dataset.rating = i;
            container.appendChild(star);
        }
        const stars = container.querySelectorAll('.star');

        const updateStarDisplay = (ratingToShow) => {
            stars.forEach(star => {
                const starRating = parseInt(star.dataset.rating);
                if (starRating <= ratingToShow) {
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            });
        };

        // Initialisation de l'affichage des étoiles
        updateStarDisplay(currentRating);

        container.addEventListener('mouseover', (event) => {
            const hoveredStar = event.target.closest('.star');
            if (hoveredStar) {
                const hoveredRating = parseInt(hoveredStar.dataset.rating);
                updateStarDisplay(hoveredRating);
            }
        });

        container.addEventListener('mouseout', () => {
            updateStarDisplay(currentRating);
        });

        container.addEventListener('click', (event) => {
            const clickedStar = event.target.closest('.star');
            if (clickedStar) {
                const newRating = parseInt(clickedStar.dataset.rating);
                currentRating = newRating;
                hiddenInput.value = newRating;
                updateStarDisplay(currentRating);
            }
        });
    }
    
    setupNoUiSliderFilter('priceSlider', 'minPrice', 'maxPrice', 0, 500, '€');
    setupNoUiSliderFilter('durationSlider', 'minDuration', 'maxDuration', 0, 24, 'h');
    setupStarRating('ratingStars', 'minRating', 5);
    setupStarRating('ratingStarsForDriver', 'minRatingForDriver', 5);
});