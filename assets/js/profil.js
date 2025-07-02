document.addEventListener('DOMContentLoaded', () => {
    const preferencesCollection = document.querySelector('.user-preferences-collection');

    if (preferencesCollection) {
        let index = preferencesCollection.CDATA_SECTION_NODE.index;

        const addPreferenceButton = document.querySelector('.add-preference-button');
        const removePreferenceButtons = document.querySelectorAll('.remove-preference-button');

        removePreferenceButtons.forEach(button => {
            button.addEventListener('click', () => {
                button.closest('.preference-item').remove();
            });
        });

        addPreferenceButton.addEventListener('click', () => {
            addPreferenceForm(preferencesCollection, index);
            index++;
        });

        // ajouter un champ de préférence
        function addPreferenceForm(collectionHolder, index) {
            const prototype = collectionHolder.dataset.protoype;
            let newForm = prototype.replace(/__name__/g, index);

            const div = document.createElement('div');
            div.innerHTML = newForm;
            div.classList.add('preference-item', 'mb-3', 'p-3', 'rounded');


        // ajout d'un bouton de suppression
        const removeButton = document.createElement('button');
        removeButton.type ='button';
        removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-preference-button', 'mt-2');
        removeButton.textContent = 'Supprimer';
        div.appendChild(removeButton);

        collectionHolder.appendChild(div);

        removeButton.addEventListener('click', () => {
            div.remove();
        })
        }
    } })