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
            formData.append('_username', email); 
            formData.append('_password', password); 
            formData.append('_csrf_token', csrfToken);

            try {
                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    body: formData,
                    redirect: 'follow'
                });

              
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const data = await response.json();
                    if (response.ok) {
                        console.log("Connexion réussie (sans redirection explicite) :", data);
                    } else {
                        let errorMessage = 'Une erreur est survenue lors de la connexion.';
                        if (data && data.error) {
                            errorMessage = data.error;
                        } else if (data && data.message) {
                            errorMessage = data.message;
                        }
                        console.error("Échec de la connexion:", errorMessage);
                        alert(errorMessage);
                    }
                }
            } catch (error) {
                console.error('Erreur réseau ou lors de la requête Fetch:', error);
                alert("Impossible de se connecter. Vérifiez votre connexion.");
            }
        });
    }
});