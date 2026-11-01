# SkillSwap

Plateforme web d'échange de compétences entre pairs : on apprend une compétence en en transmettant une autre, sans argent, avec un système de points, d'avis et de certificats vérifiables. Projet contribuant à l'**ODD 4 : éducation de qualité**.

## Fonctionnalités
- Inscription, connexion, profil avec photo
- Publication de compétences, catalogue et recherche
- Demandes d'échange, séances, messagerie
- Points, avis, badges
- Certificats vérifiables (code de vérification, version imprimable en PDF)
- Espace administrateur (web et application desktop)
- Sécurité : mots de passe hachés (bcrypt), requêtes préparées (PDO), protection CSRF, verrouillage après échecs de connexion, upload d'images contrôlé

## Technologies
- **Web** : PHP 8 (architecture MVC), MySQL, PDO, Bootstrap 5
- **Desktop (administration)** : C# / WPF (.NET 8), MySQL

## Structure
- `web/` : application web
- `desktop/` : application desktop d'administration

## Installation (web)
1. Installer XAMPP, démarrer Apache et MySQL.
2. Copier `web/` dans `C:\xampp\htdocs\` sous le nom `skillswapweb`.
3. Créer la base `skillswap` dans phpMyAdmin, puis importer `database/skillswap_database.sql` et `database/schema_additions.sql`.
4. Copier `config/config.example.php` vers `config/config.php` et adapter les paramètres.
5. Ouvrir `http://localhost/skillswapweb/public/`.

## Comptes de démonstration (environnement local)
- Administrateur : `admin@skillswap.local` / `Admin1234`
- Utilisateur : `salma.demo@skillswap.local` / `Demo1234`

## Application desktop
Prérequis : .NET SDK 8, MySQL démarré. Dans `desktop/` : `dotnet run`, puis se connecter avec le compte administrateur.

## Auteur
Zouaoui Ishraq, étudiante en Licence Business Information Systems (ESEN).