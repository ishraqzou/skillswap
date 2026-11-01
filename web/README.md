# SkillSwap — Application Web (Tâche 18)

Application web de la plateforme SkillSwap, développée en **PHP 8** selon une
architecture **MVC** orientée objet, avec accès aux données exclusivement via
**PDO** (requêtes préparées), interface **Bootstrap 5** et base **MySQL/MariaDB**.

---

## 1. Prérequis

- XAMPP (Apache + MySQL/MariaDB + PHP 8.0 ou supérieur)
- Extensions PHP : `pdo_mysql`, `mbstring` (toutes deux activées par défaut dans XAMPP)

## 2. Installation

1. **Copier le projet** dans le dossier web de XAMPP :
   `C:\xampp\htdocs\skillswap`  (Windows) ou `/opt/lampp/htdocs/skillswap` (Linux)

2. **Créer la base de données.** Depuis phpMyAdmin ou en ligne de commande,
   exécuter **dans cet ordre** :
   ```
   database/skillswap_database.sql     (schéma complet + données de référence)
   database/schema_additions.sql       (colonnes de confirmation de séance)
   ```
   Copier `config/config.example.php` vers `config/config.php`, puis adapter les paramètres (base de données, APP_URL).

3. **Vérifier la configuration** dans `config/config.php` :
   ```php
   define('APP_URL', 'http://localhost/skillswap/public');
   define('DB_HOST', '127.0.0.1');
   define('DB_NAME', 'skillswap');
   define('DB_USER', 'root');
   define('DB_PASS', '');        // mot de passe vide = valeur par défaut XAMPP
   ```

4. **Activer `mod_rewrite`** dans Apache (actif par défaut sous XAMPP), afin que
   les fichiers `.htaccess` fournis assurent la réécriture d'URL.

5. Ouvrir : **http://localhost/skillswap/public**

> **Note de sécurité (production).** Seul le dossier `public/` doit être exposé
> par le serveur web. Le `.htaccess` à la racine bloque déjà l'accès direct à
> `app/` et `config/` ; en production, il est préférable de faire pointer le
> `DocumentRoot` directement sur `public/`.

## 3. Comptes de démonstration

| Rôle | Identifiant | Mot de passe |
|---|---|---|
| Administrateur | `admin@skillswap.local` | `Admin1234` |
| Utilisateur | `salma.demo@skillswap.local` | `Demo1234` |
| Utilisateur | `karim.demo@skillswap.local` | `Demo1234` |

> Ces mots de passe sont destinés à la démonstration et doivent être changés
> avant toute mise en production.

## 4. Architecture (MVC)

```
skillswap/
├── config/config.php          Configuration (BD, sécurité, règles métier)
├── app/
│   ├── Core/                  Noyau : Router, Controller, Model, Database, Auth, Validator
│   ├── Models/                15 modèles, un par table du schéma relationnel (Tâche 12)
│   ├── Controllers/           13 contrôleurs, un par domaine fonctionnel
│   └── Services/              Logique métier centralisée (points, certificats)
├── views/
│   ├── layouts/               3 gabarits : public, app (utilisateur), admin
│   ├── partials/              Fragments réutilisables (messages flash)
│   └── <domaine>/             Vues par domaine fonctionnel
├── public/                    SEUL dossier exposé : index.php (front controller), assets
└── database/                  Scripts SQL
```

**Flux d'une requête :** `public/index.php` (front controller) → `Router` →
`Controller` → `Model` (PDO) → `View` rendue dans un `layout`.

## 5. Règles métier implémentées

Les règles centrales du catalogue (Tâche 5) sont appliquées de manière
centralisée dans `app/Services/` afin de garantir leur uniformité :

| Règle | Implémentation |
|---|---|
| BR-001 | Unicité de l'e-mail vérifiée à l'inscription + contrainte `UNIQUE` |
| BR-002 | Politique de mot de passe + hachage `bcrypt` |
| BR-003 | Verrouillage temporaire après 5 tentatives échouées |
| BR-011 | Description de compétence d'au moins 20 caractères |
| BR-015 | Interdiction de doublon de compétence active |
| BR-017/018 | Pas d'auto-demande ; une seule demande en attente entre deux membres |
| BR-023 | Création automatique d'une séance à l'acceptation d'une demande |
| BR-024 | Détection de chevauchement de créneaux |
| **BR-026** | Séance complétée **uniquement** après confirmation des **deux** participants |
| **BR-027** | Aucun point attribué si la séance est annulée |
| **BR-039** | 10 points à **chacun** des deux participants |
| BR-042/046 | Ajustement manuel réservé à l'admin, **motif obligatoire**, tracé |
| BR-045 | Solde de points jamais négatif (`GREATEST(0, ...)`) |
| BR-043 | Déblocage automatique des badges par seuil |
| **BR-047** | Certificat généré **uniquement** si : séance terminée + 2 avis + aucun litige |
| BR-052/054 | Révocation / régénération réservées à l'admin, avec traçabilité |

## 6. Sécurité

- Requêtes **exclusivement préparées** (PDO, `EMULATE_PREPARES = false`)
- Mots de passe hachés en **bcrypt**, jamais stockés ni affichés en clair
- Échappement systématique des sorties (`htmlspecialchars`) contre les failles XSS
- Validation **côté serveur** systématique, indépendante des contrôles JavaScript
- Contrôle d'accès par rôle à chaque action sensible (`Auth::requireLogin` / `requireAdmin`)
- Expiration de session par inactivité et régénération de l'identifiant à la connexion
- Cookies de session en `HttpOnly` + `SameSite=Lax`

## 7. Correspondance avec les livrables précédents

| Livrable | Correspondance dans le code |
|---|---|
| Tâche 5 — Règles métier | `app/Services/`, `app/Core/Validator.php`, contrôleurs |
| Tâche 7 — Cas d'utilisation | Une action de contrôleur par cas d'utilisation (UC-XX) |
| Tâche 8 — Diagramme de classes | `app/Models/` (une classe par entité) |
| Tâche 12 — Schéma relationnel | `database/skillswap_database.sql` |
| Tâche 17 — Conception UI/UX | `public/assets/css/style.css`, `views/layouts/` |

## 8. Remarque sur la compatibilité MariaDB

Le script `skillswap_database.sql` utilise `ON UPDATE RESTRICT` (au lieu de
`ON UPDATE CASCADE`) sur les clés étrangères également couvertes par une
contrainte `CHECK`. MariaDB (moteur fourni avec XAMPP) refuse en effet une
contrainte `CHECK` portant sur une colonne soumise à `ON UPDATE CASCADE`
(erreur 1901). Ce choix préserve l'intégralité des contrôles d'intégrité
définis à la Tâche 14 tout en garantissant l'exécution du script sous XAMPP.
