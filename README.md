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
├── 📂 CSS/                       # Feuilles de style du site
├── 📂 JS/                        # Scripts JavaScript
├── 📂 html/                      # Pages HTML
├── 📂 PHP/
│   ├── 📂 classe/                # Classes PHP
│   └── 📂 codes/                 # Traitements PHP (connexion, CRUD, etc.)
├── 📂 MySQL/                     # Fichier de connexion à MySQL
├── 📂 upload/                    # Fichiers uploadés
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
