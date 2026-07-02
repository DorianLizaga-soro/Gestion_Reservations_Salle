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
   $servername = "db";
   $username = "root";
   $password = "rootpassword";
   $dbname = "reservation_salle";
```

6. Lancer le projet dans le navigateur à l'adresse :
   [http://localhost/reservation_salle](http://localhost/reservation_salle)



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

## Base de données 

La base de données reservation_salle est composée de 8 tables :

utilisateurs (
  id, nom, email, password, role, id_association
)

associations (
  id, nom, couleur, id_responsable
)

salles (
  id, nom, capacite, description
)

reservations (
  id, id_association, id_salle, id_creneau_recurrent,
  type, date_, heure_debut, heure_fin, statut,
  id_createur, date_creation, Motif, commentaire, programme_pdf
)

creneauxrecurrents (
  id, id_association, id_salle, jour_semaine,
  heure_debut, heure_fin, frequence_jours, date_debut, actif
)

commentaires (
  id, id_reservation, id_auteur, contenu, date_creation
)

messages (
  id, id_reservation, id_auteur, contenu, date_envoi, lu
)

menage (
  id, id_reservation, id_personnel, date_prevue, date_validation, statut
)

pdfs (
  id, id_reservation, nom_fichier, chemin, date_upload, date_expiration
)

Rôles utilisateurs (role) : gestionnaire, responsable_association, membre_association, personnel_menage

Relations principales :


Une association a un utilisateur responsable (id_responsable)
Une reservation est liée à une association, une salle, et éventuellement un creneau_recurrent
Les tables commentaires, messages, menage et pdfs sont toutes rattachées à une reservation
Toutes les suppressions en cascade (ON DELETE CASCADE) sont gérées au niveau de la base


Le schéma complet est disponible dans Schematic_for_the_DATABASE_Reservations.pdf, et le script de création est dans reservation_salle.sql.


## Fonctionnalités

- Connexion / inscription utilisateur
- Réservation de salle
- Panneau d'affichage
- Gestion du ménage
- Exportations (PDF)
- Gestion des utilisateurs (mise à jour, suppression)

## Auteur

Zoro, Doryann, Reinier, Ghost.