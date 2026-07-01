# Réservation Salle

Application de gestion de réservation de salles (connexion, réservation, panneau d'affichage, gestion utilisateurs, exportations...).

## Technologies utilisées

- PHP : 8.2
- MySQL : 8.0
- HTML5
- CSS3
- JavaScript
- Apache (via XAMPP)

## Prérequis

- XAMPP installé : https://www.apachefriends.org/fr/index.html
- Un éditeur de code (VS Code recommandé)
- Git

## Installation

1. Cloner le dépôt :
```bash
   git clone <url-du-repo>
```

2. Placer le dossier `www` du projet dans le répertoire `htdocs` de XAMPP, en le renommant si besoin (ex: `reservation_salle`) :
   - Windows : `C:\xampp\htdocs\reservation_salle`
   - Mac : `/Applications/XAMPP/htdocs/reservation_salle`

3. Démarrer **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.

4. Créer la base de données :
   - Ouvrir phpMyAdmin : [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
   - Créer une nouvelle base de données nommée `reservation_salle`
   - Importer le fichier `database.sql` fourni dans le projet

5. Configurer la connexion à la base de données dans le fichier `PHP/codes/connexionBDD.php` :
```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "reservation_salle";
```

6. Lancer le projet dans le navigateur à l'adresse :
   [http://localhost/reservation_salle](http://localhost/reservation_salle)

## Structure du projet

reservation_salle/

## Structure du projet

reservation_salle/
├── 📂 CSS/
│   ├── 📄 connexion.css          # Style de la page de connexion
│   ├── 📄 exportations.css       # Style de la page d'exportations
│   ├── 📄 menage.css             # Style de la page ménage
│   ├── 📄 pdf.css                # Style des documents PDF générés
│   ├── 📄 style.css              # Feuille de style principale
│   └── 📄 utilisateur.css        # Style de la page utilisateur
│   ├── 📄 association.css        # Feuille de style principale
│   └── 📄 associationAdmin.css   # Style de la page utilisateur
|   ├── 📄 associationMembre.css  # style de la page du membre de l'asscocaiation
│   └── 📄 connexion.css          # Style de la page de connexion
|   ├── 📄 dashboard.css          # style du tableau de bord
│   └── 📄 gestionnaire.css       # Style de la page gestionnaire
|   ├── 📄 reservation.css        # style de la page reservation
│   └── 📄 utilisateur.css        # Style de la page
|   ├── 📄 register.css           # Style de la page
│   └── 📄 utilisateur.css        # Style de la page
|   ├── 📄 register.css           # Style de la page
│   └── 📄 utilisateur.css        # Style de la page
|
|
|
|
|
├── 📂 JS/
│   ├── 📄 JS.js                  # Script principal du site
│   ├── 📄 menage.js              # Logique JS de la page ménage
│   ├── 📄 pdf.js                 # Génération/gestion des PDF
│   └── 📄 utilisateur.js         # Logique JS de la page utilisateur
│   ├── 📄 association.js         # Logique JS de la page ménage
│   ├── 📄 calendar_reservation.js   # Génération/gestion des PDF
|   ├── 📄 dashboard.js              # Logique JS de la page ménage
│   ├── 📄 fileUp2.js                # Génération/gestion des PDF
|   ├── 📄 gestionnaire.js           # Logique JS de la page ménage
│   ├── 📄 menagePersonnel.js        # Génération/gestion des PDF
|   ├── 📄 exportation.js            # Logique JS de la page ménage
│   ├── 📄 pdf.js                    # Génération/gestion des PDF
|
|
|
|
|
|
|
├── 📂 html/
│   ├── 📄 connexion.html         # Page de connexion
│   ├── 📄 exportations.html      # Page d'exportations
│   ├── 📄 index.html             # Page d'accueil
│   ├── 📄 menage.html            # Page de gestion du ménage
│   ├── 📄 panneaudaffichage.html # Page panneau d'affichage
│   ├── 📄 register.html          # Page d'inscription
│   └── 📄 utilisateur.html       # Page profil utilisateur
│   ├── 📄 association.html       # page de l'association
│   ├── 📄 associationAdmin.html  # page administrateur de l'association
|   ├── 📄 associationMembre.html # page membre de l'association
│   ├── 📄 dashboard.html         # page tableau de bord    
|   ├── 📄 dashboard_gestionnaire.html # page tableau de bord du gestionnaire
│   ├── 📄 menagePersonnel.html        # page personnel de ménage     
|   ├── 📄 reservation.html            # page reservation de salle
│   ├── 📄 utilisateur.html            # page utilisateur  
|
|
|
|
├── 📂 PHP/
│   ├── 📂 classe/
│   │   └── 📄 association.php    # Classe de gestion des associations
│   └── 📂 codes/
│       ├── 📄 connexionBDD.php   # Connexion à la base de données
│       ├── 📄 login.php          # Traitement de la connexion
│       ├── 📄 logout.php         # Déconnexion
│       ├── 📄 register.php       # Traitement de l'inscription
│       ├── 📄 utilisateurs.php   # Gestion des utilisateurs

│       ├── 📄 updateUser.php     # Mise à jour d'un utilisateur
│       ├── 📄 deleteUser.php     # Suppression d'un utilisateur
│       ├── 📄 menage.php         # Logique de la page ménage
│       ├── 📄 exportations.php   # Logique des exportations
│       ├── 📄 panneaudaffichage.php # Logique du panneau d'affichage
|       ├── 📄 ajout_association.php   # Ajout d'une association
│       ├── 📄 ajouter_membre.php      # Ajout d'un membre 
|       ├── 📄 ajouter_salle.php       # Ajout d'une salle
│       ├── 📄 associationAdmin.php   # logique de la page administrateur       
|       ├── 📄 cancelReservation.php   # Annulation de la reservation
│       ├── 📄 changeReservation.php   # Modifier la reservation   
|       ├── 📄 connexionBDD.php        # Suppression d'un utilisateur
│       ├── 📄 createReservation2.php  # Logique de la page ménage  
|       ├── 📄 deleteReservation.php   # Suppression d'un utilisateur
│       ├── 📄 envoie_commentaire.php  # Logique de la page ménage  
|       ├── 📄 export_calendar.php     # Suppression d'un utilisateur
│       ├── 📄 export_excel.php        # Logique de la page ménage   
|       ├── 📄 gestionnaire.php        # Suppression d'un utilisateur
│       ├── 📄 get_association_details.php  # Logique de la page ménage  
|       ├── 📄 deleteReservation.php   # Suppression d'un utilisateur
│       ├── 📄 lireReservation.php  # Logique de la page ménage   
|       ├── 📄 login.php            # Logique de la page ménage   
|       ├── 📄 logout.php           # Suppression d'un utilisateur
│       ├── 📄 menagePersonnel.php  # Logique de la page ménage  
|       ├── 📄 modifier_association.php     # Suppression d'un utilisateur
│       ├── 📄 modifier_reservation.php     # Logique de la page ménage          
|       ├── 📄 modifier_salle.php           # Logique de la page ménage   
|       ├── 📄 register.php                 # Suppression d'un utilisateur
│       ├── 📄 reservation.php              # Logique de la page ménage  
|       ├── 📄 responsable.php              # Suppression d'un utilisateur
│       ├── 📄 supprimer_association.php    # Logique de la page ménage 
|       ├── 📄 supprimer_reservation.php    # Logique de la page ménage   
|       ├── 📄 supprimer_salle.php          # Suppression d'un utilisateur
│       ├── 📄 updateUser.php               # Logique de la page ménage  
|       ├── 📄 validateReservation.php      # Suppression d'un utilisateur
│         
|
|
├── 📂 MySQL/
│   └── 📄 connexion.php          # Fichier de connexion à MySQL
│
├── 📂 upload/                    # Dossier de stockage des fichiers uploadés
│
├── 📄 index.php                  # Point d'entrée principal de l'application
└── 📄 README.md



## Fonctionnalités

- Connexion / inscription utilisateur
- Réservation de salle
- Panneau d'affichage
- Gestion du ménage
- Exportations (PDF)
- Gestion des utilisateurs (mise à jour, suppression)

## Auteur

Ton nom
