# EcoRide

## Description du projet

EcoRide est une palteforme de covoiturage qui se soucie de l'écologie. L'objectif est de réduire votre emprunte carbonne tout en passant un agréable trajet. EcoRide est plus qu'un simple covoiturage, c'est une aventure.

Ce projet s'inscrit dans l'obtention de l'exmaen developpeur full-stack web et mobile avec l'école Studi.

## Stack technique

* **Frontend :**

    * HTML 5
    * CSS 3
    * JavaScript

* **Backend**

    * PHP avec le framework Symfony
    * Moteur de template Twig

## Installation et lancement en local

### Prérequis

* PHP ( version 8.4 ou supérieur )
* Composer
* Node.js et npm
* Symfony CLI
* MySQL server
* MongoDB Server

### Etapes d'installation

1. Cloner le projet

Ouvrez votre terminal  

git clone https://github.com/kurillos/sf_ecoride_cyril.git
cd ecoride

2. Configuration de l'environnement

Créez un fichier .env.local à la racine du projet.

ouvrez le fichier .env.local et mettre à jour les ligne suivante : 

DATABASE_URL="mysql://app:root@172.18.0.3:3306/ecoride_db?serverVersion=8.0.32&charset=utf8mb4"

MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0

MAILER_DSN="smtp://localhost:1025"

3. Installez les dépendances :

Installer les dépendances avec composer à l'aide de la commande suivante : 

composer install

Installer les dépendances de NPM à l'aide de la commande suivante :

npm install

4. Compilation des assets

Compilez les fichiers CSS et JavaScript à l'aide de la commande suivante :

npm run build

5. Configurez la base de données 

Créez la base de données et eécuter les migrations pour générer le schéma de base de données. 

Tapez les commandes suivantes dans votre terminal :

Pour la base de données mySQL :

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

pour la base de données MongoDB : 

php bin/console doctrine:mongodb:schema:create

6. Lancement du serveur

Demarrez le serveur web local.

symfony server:start

L'application sera disponible à l'adresse : localhost:8000

7. Utilisation

Ouvrez votre navigateur et naviguez vers l'adresse locale. Vous pouvez vous inscrire, vous connecter et commencez à utilisez l'application.

Contributeurs 
    - Cyril BOCAGE