#!/bin/bash
# AQMI - Automated Installation Script
# Automotive Quality Maturity Index
# Usage: bash install.sh

set -e

echo "========================================"
echo "  AQMI - Installation Automatique"
echo "  Automotive Quality Maturity Index"
echo "========================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check requirements
echo "🔍 Vérification des prérequis..."

# PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    echo -e "  PHP       : ${GREEN}$PHP_VERSION${NC}"
else
    echo -e "  PHP       : ${RED}Non installé${NC}"
    echo "  Veuillez installer PHP 8.1 ou supérieur"
    exit 1
fi

# Composer
if command -v composer &> /dev/null; then
    echo -e "  Composer  : ${GREEN}OK${NC}"
else
    echo -e "  Composer  : ${RED}Non installé${NC}"
    exit 1
fi

# MySQL
if command -v mysql &> /dev/null; then
    echo -e "  MySQL     : ${GREEN}OK${NC}"
else
    echo -e "  MySQL     : ${YELLOW}Non détecté (installation manuelle requise)${NC}"
fi

echo ""

# Configuration
echo "📝 Configuration..."

DB_NAME="${DB_NAME:-aqmi}"
DB_USER="${DB_USER:-aqmi_user}"
DB_PASS="${DB_PASS:-Aqmi@2024#Secure}"
DB_HOST="${DB_HOST:-localhost}"

# Create .env if not exists
if [ ! -f .env ]; then
    echo "  Création du fichier .env..."
    cp .env.example .env 2>/dev/null || cat > .env << EOF
APP_NAME=AQMI
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

DB_HOST=${DB_HOST}
DB_PORT=3306
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}

MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aqmi.com
MAIL_FROM_NAME=AQMI

DEFAULT_LANGUAGE=fr
ITEMS_PER_PAGE=20
EOF
    echo -e "  .env      : ${GREEN}Créé${NC}"
else
    echo -e "  .env      : ${YELLOW}Existe déjà${NC}"
fi

# Database setup
echo ""
echo "🗄️  Configuration de la base de données..."

read -p "  Créer la base de données ? (o/n) [o]: " CREATE_DB
CREATE_DB=${CREATE_DB:-o}

if [ "$CREATE_DB" = "o" ]; then
    echo "  Création de la base de données '${DB_NAME}'..."
    mysql -h "${DB_HOST}" -u root -p << SQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL
    echo -e "  Base de données : ${GREEN}OK${NC}"
fi

read -p "  Importer le schéma et les données ? (o/n) [o]: " IMPORT_DATA
IMPORT_DATA=${IMPORT_DATA:-o}

if [ "$IMPORT_DATA" = "o" ]; then
    echo "  Import du schéma..."
    mysql -h "${DB_HOST}" -u root -p "${DB_NAME}" < database/schema.sql
    echo "  Import des données de démonstration..."
    mysql -h "${DB_HOST}" -u root -p "${DB_NAME}" < database/seeders/seed.sql
    echo -e "  Données : ${GREEN}Importées${NC}"
fi

# Composer install
echo ""
echo "📦 Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader
echo -e "  Dépendances : ${GREEN}OK${NC}"

# Permissions
echo ""
echo "🔧 Configuration des permissions..."
chmod -R 775 storage/
chmod -R 775 public/uploads/
echo -e "  Permissions : ${GREEN}OK${NC}"

echo ""
echo "========================================"
echo -e "  ${GREEN}✅ Installation terminée !${NC}"
echo "========================================"
echo ""
echo "  Accès administration :"
echo "    URL  : https://votre-domaine.com/admin"
echo "    Email: admin@aqmi.com"
echo "    Mot de passe: Admin@2024#"
echo ""
echo "  Important :"
echo "    1. Modifiez le mot de passe administrateur"
echo "    2. Configurez les paramètres SMTP dans .env"
echo "    3. Mettez à jour APP_URL dans .env"
echo ""