
    const addAnotherVehicleBtn = document.querySelector('.add-another-vehicle');
    const vehicleFieldsList = document.getElementById('vehicle-fields-list');

    console.log("Script profil.js chargé !");
    console.log("Bouton d'ajout trouvé:", addAnotherVehicleBtn);
    console.log("Liste des champs de véhicules trouvés:", vehicleFieldsList);

    if (addAnotherVehicleBtn && vehicleFieldsList) {
        let index = vehicleFieldsList.children.length;
        console.log("Nombre initial de véhicules (enfants) détectés:", index);

        addAnotherVehicleBtn.addEventListener('click', () => {
            console.log("Bouton 'Ajouter un véhicule' cliqué !");
            const prototype = vehicleFieldsList.dataset.prototype;

            if (!prototype) {
                console.error("Erreur: l'attribut data-prototype est manquant");
                return;
            }


            let newForm = prototype.replace(/__name__/g, index);

            const newVehicleDiv = document.createElement('div');
            newVehicleDiv.classList.add('vehicle-item', 'mb-3', 'p-3', 'border', 'rounded');
            newVehicleDiv.innerHTML = newForm;


            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-vehicle-button', 'mt-2');
            removeButton.textContent = 'Supprimer';
            removeButton.addEventListener('click', () => {
                newVehicleDiv.remove();
            });
            newVehicleDiv.appendChild(removeButton);

            vehicleFieldsList.appendChild(newVehicleDiv);
            index++;
            console.log("Nouveau véhicule ajouté, nouvel index:", index);
        });

        document.querySelectorAll('.remove-vehicle-button').forEach(button => {
            button.addEventListener('click', () => {
                console.log("Button supprimer existant cliqué !");
                button.closest('.vehicle-item').remove();
            });
        });
    } else {
        console.warn("Certains élements JS n'ont pas été trouvés. Bouton AJouter:", addAnotherVehicleBtn, "Liste Véhicules:", vehicleFieldsList);
    }