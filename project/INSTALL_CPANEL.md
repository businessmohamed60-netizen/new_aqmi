# Guide d'installation NOVAQYS / AQMI — cPanel

## Prérequis cPanel

Avant de commencer, assurez-vous que votre hébergement cPanel dispose de :

| Composant | Version requise |
|-----------|----------------|
| PHP | 8.1 ou supérieur |
| MariaDB / MySQL | 5.7+ / 10.3+ |
| Composer | 2.x |
| Apache | 2.4+ (mod_rewrite activé) |

**Extensions PHP requises** (vérifiez dans **Select PHP Version** → Extensions) :

- `pdo_mysql`
- `mbstring`
- `gd`
- `xml`
- `zip`
- `curl`
- `json`
- `fileinfo`
- `openssl`

---

## 1. Télécharger l'application

Deux méthodes :

### Méthode A — Via File Manager (recommandé)

1. Connectez-vous à **cPanel**
2. Allez dans **File Manager**
3. Ouvrez le dossier `public_html` (ou un sous-dossier comme `novaqys` si vous utilisez un sous-domaine)
4. Cliquez sur **Upload** et sélectionnez l'archive ZIP du projet
5. Une fois uploadée, faites un clic droit sur le ZIP → **Extract**

### Méthode B — Via FTP

```bash
# Utilisez FileZilla, Cyberduck ou tout client FTP
# Hôte : votre-domaine.com
# Utilisateur : votre nom d'utilisateur cPanel
# Mot de passe : votre mot de passe cPanel
# Dossier : public_html/
```

---

## 2. Structure des fichiers

Une fois uploadé, la structure doit ressembler à :

```
public_html/
├── public/              # ✅ Document root
├── app/                 # Code applicatif
├── routes/              # Routes
├── resources/           # Vues, traductions
├── database/            # Schéma SQL, données
├── storage/             # Logs, exports, rapports
├── vendor/              # Dépendances Composer
├── .env                 # Configuration
├── .htaccess            # Redirection vers public/
├── composer.json
└── install.sh
```

> **Important :** Si votre hébergement ne permet pas de changer le document root pour po
inter vers le dossier `public/`, le `.htaccess` racine redirige automatiquement tout le trafic vers `public/`.

---

## 3. Créer la base de données

1. Dans **cPanel**, cliquez sur **MySQL® Databases** (ou **MariaDB Databases**)
2. Créez une nouvelle base de données :
   - **Database Name** : `aqmi` (ou `votreuser_aqmi`)
   - Cliquez sur **Create Database**
3. Créez un utilisateur :
   - **Username** : `aqmi_user` (ou `votreuser_aqmi`)
   - **Password** : utilisez le **Password Generator** (ex: `Aqmi@2024#Secure`)
   - Cliquez sur **Create User**
4. Ajoutez l'utilisateur à la base de données :
   - Sélectionnez l'utilisateur et la base de données
   - Cochez **ALL PRIVILEGES**
   - Cliquez sur **Make Changes**

### Importer le schéma SQL via phpMyAdmin

1. Dans **cPanel**, cliquez sur **phpMyAdmin**
2. Sélectionnez la base de données créée (dans le panneau de gauche)
3. Cliquez sur l'onglet **Import**
4. Cliquez sur **Choose File** et sélectionnez `database/schema.sql`
5. Cliquez sur **Go**
6. Répétez l'opération pour `database/seeders/seed.sql` (données de démonstration)

> **Alternative MySQL** : si vous avez accès SSH, utilisez :
> ```bash
> mysql -u votreuser -p votrebase < database/schema.sql
> mysql -u votreuser -p votrebase < database/seeders/seed.sql
> ```

---

## 4. Configurer l'environnement (.env)

1. Dans **File Manager**, naviguez jusqu'au dossier racine de l'application
2. Si le fichier `.env` n'existe pas, créez-le à partir du modèle
3. Modifiez-le (clic droit → **Edit**) avec les valeurs de votre hébergement :

```ini
APP_NAME="NOVAQYS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_HOST=localhost
DB_PORT=3306
DB_NAME=votre_nom_de_base
DB_USER=votre_utilisateur
DB_PASS=votre_mot_de_passe

MAIL_HOST=smtp.votre-hebergeur.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@domaine.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="NOVAQYS"

DEFAULT_LANGUAGE=fr
ITEMS_PER_PAGE=20
```

> **Important :** Remplacez `APP_URL` par l'URL réelle de votre site. Ne mettez pas de slash à la fin.

---

## 5. Configuration des emails (OTP, notifications)

L'application utilise **PHPMailer** pour envoyer des emails :
- **Code OTP** à 6 chiffres pour la connexion sécurisée (étape après le login)
- **Lien de réinitialisation** de mot de passe
- **Notifications** (nouveau lead, rapport généré)

### 5.1 Principe de fonctionnement

Lorsqu'un utilisateur se connecte avec son email et mot de passe, l'application :
1. Vérifie les identifiants
2. **Génère un code OTP à 6 chiffres** (valable 5 minutes)
3. **Envoie l'email OTP** avec le template responsive (fond sombre, logo, code)
4. Redirige vers `/aqmi/otp` pour saisir le code
5. Après validation, l'utilisateur est connecté

### 5.2 Configuration des variables SMTP (.env)

Dans le fichier `.env` à la racine (voir section 4), configurez les paramètres SMTP :

```ini
# ── Configuration SMTP ──
MAIL_HOST=smtp.votre-hebergeur.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@domaine.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="NOVAQYS"
```

| Variable | Description | Valeur typique |
|----------|-------------|----------------|
| `MAIL_HOST` | Serveur SMTP | `smtp.hostinger.com`, `smtp.gmail.com`, `smtp.sendgrid.net` |
| `MAIL_PORT` | Port SMTP | `587` (TLS) ou `465` (SSL) |
| `MAIL_USERNAME` | Identifiant SMTP | Adresse email complète |
| `MAIL_PASSWORD` | Mot de passe SMTP | Mot de passe ou mot de passe d'application |
| `MAIL_ENCRYPTION` | Chiffrement | `tls` (port 587) ou `ssl` (port 465) |
| `MAIL_FROM_ADDRESS` | Expéditeur affiché | `noreply@votre-domaine.com` |
| `MAIL_FROM_NAME` | Nom de l'expéditeur | `NOVAQYS` |

### 5.3 Mode automatique : SMTP ou Log

L'application détecte automatiquement le mode d'envoi :

- **Mode SMTP** : Si `MAIL_HOST` est différent de `smtp.example.com`, les emails sont envoyés via SMTP
- **Mode Log** : Si `MAIL_HOST` reste `smtp.example.com` (valeur par défaut), les emails **ne sont pas envoyés** mais écrits dans le fichier `logs/mail.log` (mode développement)

> **Pour tester sans SMTP** : le code OTP est écrit dans `logs/mail.log` avec le message `OTP CODE: XXXXXX`. Vous pouvez le récupérer depuis **File Manager** → ouvrir `logs/mail.log`.

### 5.4 Configuration par hébergeur

#### cPanel (Email / Webmail)

```ini
MAIL_HOST=mail.votre-domaine.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@votre-domaine.com
MAIL_PASSWORD=votre-mot-de-passe-email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="NOVAQYS"
```

> Créez d'abord l'adresse email dans **cPanel** → **Email Accounts**, puis utilisez ces identifiants.

#### Gmail (recommandé pour le développement)

```ini
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre.email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-application
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre.email@gmail.com
MAIL_FROM_NAME="NOVAQYS"
```

> ⚠️ Avec Gmail, vous devez créer un **Mot de passe d'application** :
> 1. Allez sur https://myaccount.google.com/security
> 2. Activez l'authentification à deux facteurs (2FA)
> 3. Allez dans **Mots de passe des applications** (rechercher dans les paramètres)
> 4. Générez un mot de passe pour l'application
> 5. Utilisez ce mot de passe dans `MAIL_PASSWORD`

#### SendGrid / Mailgun / SES

```ini
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxx_votre_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="NOVAQYS"
```

### 5.5 Templates d'email

Deux templates HTML sont disponibles dans `app/Helpers/Mailer.php` :

| Méthode | Usage | Délai d'expiration |
|---------|-------|-------------------|
| `otpTemplate()` | Code OTP à 6 chiffres | 5 minutes |
| `resetTemplate()` | Lien de réinitialisation mot de passe | 30 minutes |

Les deux templates sont responsives, avec un design sombre aux couleurs NOVAQYS.

### 5.6 Vérification

Pour vérifier que les emails sont bien envoyés :

1. Connectez-vous sur `/aqmi/login`
2. Saisissez un email valide et le mot de passe
3. Si SMTP est configuré → vous recevez l'email OTP dans votre boîte de réception
4. Si mode Log → ouvrez `logs/mail.log` depuis **File Manager** et cherchez `OTP CODE:`
5. Saisissez le code OTP sur la page `/aqmi/otp`

> **Problème : l'email n'arrive pas** → Vérifiez les logs :
> - `storage/logs/error.log` (erreurs applicatives)
> - Les dossiers **Spam** / **Indésirables** de votre boîte email
> - Les paramètres SMTP dans `.env`

---

## 6. Installer les dépendances Composer

### Méthode A — Via Terminal cPanel (SSH)

Si votre hébergement propose un accès SSH :

```bash
cd ~/public_html
composer install --no-dev --optimize-autoloader
```

### Méthode B — Via le dossier vendor pré-packagé

Si vous n'avez pas accès SSH, incluez le dossier `vendor/` dans l'archive ZIP que vous uploadez, déjà généré avec :

```bash
composer install --no-dev --optimize-autoloader
```

### Méthode C — Via le PHP CLI de cPanel

1. Dans **cPanel**, cliquez sur **Terminal** (si disponible)
2. Exécutez :
```bash
cd public_html
/usr/local/bin/php8.2 /usr/local/bin/composer install --no-dev --optimize-autoloader
```

---

## 7. Permissions des fichiers

Dans **File Manager** :

1. Naviguez jusqu'à la racine de l'application
2. Faites un clic droit sur le dossier `storage/` → **Change Permissions**
   - Cochez : Owner (Read/Write/Execute), Group (Read/Write/Execute), Public (Read/Execute)
   - Valeur numérique : **775**
   - Cochez **Apply to subdirectories**
3. Faites la même chose pour `public/uploads/`
4. Vérifiez que `storage/logs`, `storage/reports`, `storage/exports`, `storage/cache` existent (si non, créez-les)

> **cPanel applique automatiquement le bon propriétaire** : les fichiers uploadés via File Manager ou FTP appartiennent déjà à l'utilisateur cPanel. Les permissions 775 sont suffisantes.

---

## 8. Configurer le domaine / sous-domaine

### Si vous installez à la racine (`public_html/`)

L'application est automatiquement accessible sur votre domaine principal.

### Si vous installez dans un sous-dossier (ex: `public_html/novaqys/`)

1. Dans **cPanel**, cliquez sur **Subdomains**
2. Créez un sous-domaine :
   - **Subdomain** : `novaqys`
   - **Document Root** : `public_html/novaqys/public`
   - Cliquez sur **Create**

### Configuration Apache

Le fichier `.htaccess` est déjà fourni et configure automatiquement :

- La réécriture vers `public/index.php`
- La protection des fichiers sensibles
- La compression GZIP (si module dispo)
- Les en-têtes de cache (si module dispo)

**Ne modifiez pas ces fichiers** sauf si vous savez ce que vous faites.

---

## 9. Vérification de l'installation

1. Ouvrez votre navigateur et accédez à :
   ```
   https://votre-domaine.com/
   ```
2. Vous devriez voir la **landing page NOVAQYS**
3. Testez le lien **AQMI** dans la navigation → `/assessment/start`
4. Testez l'administration : `/login`

### Identifiants par défaut (données de démonstration)

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | `admin@aqmi.com` | `Admin@2024#` |

> ⚠️ **Changez immédiatement le mot de passe** après la première connexion.

---

## 10. Résolution des problèmes

### Problème : Page blanche (500 Internal Server Error)

```bash
# Vérifiez les logs PHP :
# Dans cPanel → Errors → Error Log
# Ou via File Manager : storage/logs/error.log
```

Solutions possibles :
- Vérifiez que PHP 8.1+ est actif dans **Select PHP Version**
- Vérifiez que toutes les extensions PHP sont activées
- Vérifiez les permissions des fichiers (644 pour les fichiers, 755 pour les dossiers)
- Vérifiez le fichier `.env` (DB_HOST, DB_NAME, DB_USER, DB_PASS)

### Problème : 404 Not Found sur les pages autres que l'accueil

Le `mod_rewrite` d'Apache n'est pas activé :

1. Dans **cPanel** → **Select PHP Version** → **Switch to PHP Options**
2. Ou créez/modifiez un fichier `php.ini` :
```ini
allow_url_rewrite = On
```
3. Vérifiez que le fichier `public/.htaccess` existe avec ces contenus :
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Problème : Erreur de connexion à la base de données

- Vérifiez les identifiants dans `.env`
- Vérifiez que la base de données existe dans phpMyAdmin
- Vérifiez que l'utilisateur a tous les privilèges
- Vérifiez que vous utilisez `localhost` comme `DB_HOST` (cPanel utilise des connexions locales)

### Problème : Caractères accentués mal affichés (mojibake)

```sql
-- Vérifiez que la base de données est en utf8mb4 :
ALTER DATABASE nom_de_la_base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Vérifiez que les tables sont en utf8mb4 :
ALTER TABLE nom_table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Problème : Le logo NOVAQYS ne s'affiche pas

- Vérifiez que `public/novaqys-logo-new.svg` existe
- Vérifiez que `public/favicon.png` existe
- Les permissions doivent être 644

---

## 11. Maintenance

### Mettre à jour les dépendances

```bash
composer update --no-dev --optimize-autoloader
```

### Sauvegarder la base de données

Via **phpMyAdmin** → Export → Méthode rapide → SQL → Go

Ou via SSH :
```bash
mysqldump -u utilisateur -p nom_base > backup_$(date +%Y%m%d).sql
```

### Logs

Les logs d'application se trouvent dans :
```
storage/logs/
```

Les logs d'accès Apache se gèrent depuis **cPanel** → **Metrics** → **Awstats** ou **Visitor Logs**.

---

## 12. Sécurité

- [x] Changez le mot de passe administrateur
- [x] Activez HTTPS (SSL/TLS) via **cPanel** → **SSL/TLS Wizard**
- [x] Protégez le dossier `storage/` par un fichier `.htaccess` (déjà inclus)
- [x] Désactivez l'affichage des erreurs : `APP_DEBUG=false` dans `.env`
- [x] Utilisez des mots de passe forts pour la base de données
- [ ] Configurez un certificat SSL AutoSSL dans cPanel → SSL/TLS

---

## Support

Pour toute assistance technique :
- **Email** : contact@novaqys.com
- **Documentation** : guide d'utilisation disponible dans l'application