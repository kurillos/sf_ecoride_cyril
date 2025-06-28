document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form'); // Ou utilisez un ID: document.getElementById('loginForm')

    if (loginForm) { // Vérifie si le formulaire existe sur la page
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault(); // Empêche la soumission de formulaire HTML par défaut

            const emailInput = loginForm.querySelector('input[name="_username"]');
            const passwordInput = loginForm.querySelector('input[name="_password"]');
            const csrfTokenInput = loginForm.querySelector('input[name="_csrf_token"]');

            if (!emailInput || !passwordInput || !csrfTokenInput) {
                console.error("Un ou plusieurs champs du formulaire de connexion sont manquants.");
                return;
            }

            const email = emailInput.value;
            const password = passwordInput.value;
            const csrfToken = csrfTokenInput.value;

            const formData = new FormData();
            formData.append('_username', email); // Nom attendu par Symfony
            formData.append('_password', password); // Nom attendu par Symfony
            formData.append('_csrf_token', csrfToken); // Nom attendu par Symfony

            try {
                const response = await fetch(loginForm.action, { // Utilisez l'action du formulaire (app_login)
                    method: 'POST', // La méthode doit être POST pour le login
                    body: formData,
                    // FormData gère automatiquement le Content-Type: multipart/form-data
                    // Si vous voulez simuler le x-www-form-urlencoded, vous pouvez faire:
                    // headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    // body: new URLSearchParams(formData).toString(),
                    // Mais FormData est souvent plus robuste.
                    redirect: 'follow' // Permet à fetch de suivre les redirections (important pour le login)
                });

                // Vérifier si la réponse est une redirection (connexion réussie)
                if (response.redirected) {
                    window.location.href = response.url; // Rediriger le navigateur manuellement
                } else {
                    const data = await response.json(); // Ou response.text() si votre API renvoie du texte
                    if (response.ok) {
                        // Si la réponse est OK mais pas une redirection, c'est peut-être un succès sans redirection explicite
                        console.log("Connexion réussie (sans redirection explicite) :", data);
                        // Vous pouvez ajouter ici une logique pour rediriger ou afficher un message de succès
                    } else {
                        // Gérer les erreurs (ex: "Invalid credentials" renvoyé par l'API)
                        let errorMessage = 'Une erreur est survenue lors de la connexion.';
                        if (data && data.error) { // Si votre API renvoie une propriété 'error'
                            errorMessage = data.error;
                        } else if (data && data.message) { // Ou une propriété 'message'
                            errorMessage = data.message;
                        }
                        console.error("Échec de la connexion:", errorMessage);
                        // Afficher l'erreur à l'utilisateur sur la page
                        alert(errorMessage); // Pour un test rapide
                    }
                }
            } catch (error) {
                console.error('Erreur réseau ou lors de la requête Fetch:', error);
                alert("Impossible de se connecter. Vérifiez votre connexion.");
            }
        });
    }
});