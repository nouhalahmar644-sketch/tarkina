# Base de données — Tarkina

Le site utilise une base MySQL nommée `tourisme`. Le fichier `tarkina.sql` contient
le schéma complet **et** les données (régions, hébergements, repas, guides, événements,
artisanat, utilisateurs, réservations, commandes, blog + commentaires).

## Installation (XAMPP / Windows)

1. Démarrer **Apache** et **MySQL** depuis le panneau XAMPP.
2. Importer la base en une commande :

   ```
   C:\xampp\mysql\bin\mysql.exe -u root < database\tarkina.sql
   ```

   (Le fichier crée la base `tourisme` automatiquement si elle n'existe pas.)

   *Alternative via phpMyAdmin :* ouvrir http://localhost/phpmyadmin → onglet
   **Importer** → choisir `database/tarkina.sql` → **Exécuter**.

3. Ouvrir le site : http://localhost/tarkina/

## Comptes de démonstration

| Rôle  | Email               | Mot de passe |
|-------|---------------------|--------------|
| Admin | admin@tarkina.tn    | admin123     |
| User  | user@tarkina.tn     | user123      |

Connexion sur `http://localhost/tarkina/login.php` ; un compte admin est redirigé
vers le tableau de bord (`admin/dashboard.php`).

> La connexion MySQL est configurée dans `db.php` (hôte `127.0.0.1`, utilisateur `root`,
> sans mot de passe — valeurs XAMPP par défaut).
