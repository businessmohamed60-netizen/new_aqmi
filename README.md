# AQMI - Automotive Quality Maturity Index

Plateforme d'auto-évaluation de la maturité qualité pour les entreprises industrielles.

## Technologies

- PHP 8.2+
- MySQL / MariaDB
- Bootstrap 5.3
- Chart.js / ApexCharts
- jQuery
- Composer (PHPMailer, Dompdf)

## Prérequis

- PHP 8.1 ou supérieur
- MySQL 5.7+ ou MariaDB 10.3+
- Composer
- Apache avec mod_rewrite (ou Nginx)
- Extensions PHP : `pdo_mysql`, `mbstring`, `gd`, `xml`, `zip`

## Installation Rapide

### 1. Télécharger les fichiers
```bash
# Placer les fichiers dans le répertoire web (ex: public_html/)
# Ou cloner depuis le dépôt
```

### 2. Configurer la base de données
```bash
# Créer une base de données MySQL
mysql -u root -p -e "CREATE DATABASE aqmi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importer le schéma
mysql -u root -p aqmi < database/schema.sql

# Importer les données de démonstration
mysql -u root -p aqmi < database/seeders/seed.sql
```

### 3. Configurer l'environnement
```bash
# Copier et modifier le fichier .env
# Ajuster les paramètres de connexion à la base de données
# DB_NAME=aqmi
# DB_USER=votre_utilisateur
# DB_PASS=votre_mot_de_passe
```

### 4. Installer les dépendances Composer
```bash
composer install --no-dev --optimize-autoloader
```

### 5. Configurer le serveur web

#### Apache (.htaccess déjà inclus)
- Placer le dossier `public/` comme document root
- Ou utiliser le .htaccess à la racine qui redirige vers `public/`

#### Nginx
```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /chemin/vers/aqmi/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Permissions
```bash
chmod -R 775 storage/
chmod -R 775 public/uploads/
```

### 7. Accès
- **Site public** : https://votre-domaine.com/
- **Administration** : https://votre-domaine.com/admin
- **Email** : admin@aqmi.com
- **Mot de passe** : Admin@2024#

## Structure du Projet

```
AQMI/
├── public/              # Point d'entrée, assets (CSS, JS, images)
├── app/                 # Code applicatif
│   ├── Controllers/     # Contrôleurs MVC
│   ├── Models/          # Modèles
│   ├── Services/        # Services métier (Scoring, PDF, Email...)
│   ├── Repositories/    # Repositories
│   ├── Helpers/         # Helpers (Auth, Router, Database, Security...)
│   ├── Middleware/       # Middlewares (Auth, CSRF, Admin)
│   └── Config/          # Configuration
├── routes/              # Définition des routes
├── resources/           # Ressources
│   ├── views/           # Templates
│   ├── lang/            # Traductions (FR, EN, AR)
│   └── templates/       # Templates PDF/Email
├── storage/             # Stockage (logs, rapports, exports)
├── database/            # Migrations et seeders
└── admin/               # Pages d'administration (cPanel compat)
```

## Fonctionnalités

- **Questionnaire dynamique** : Évaluation multi-étapes avec 10 domaines
- **Scoring intelligent** : Calcul pondéré des scores par domaine
- **Rapport PDF** : Génération automatique de rapports professionnels
- **Dashboard administrateur** : KPIs, graphiques, statistiques
- **CRUD complet** : Questions, Domaines, Niveaux, Recommandations
- **Gestion des leads** : Capture et export des données prospects
- **Multilingue** : Français, Anglais, Arabe
- **Sécurité** : CSRF, XSS, authentification, logs d'audit
- **Import/Export** : CSV pour les questions et les leads

## Sécurité

- Mots de passe hashés (bcrypt)
- Protection CSRF sur tous les formulaires
- Échappement XSS sur toutes les sorties
- Validation côté serveur
- Logs de connexion et d'actions
- Sessions sécurisées

## Licence

Propriétaire - Tous droits réservés"# new_aqmi" 
