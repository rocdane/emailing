#!/bin/bash

set -euo pipefail

echo "📁 Passage dans le répertoire distant : /home/$USER/domains/$DOMAIN/public_html"

cd /home/$USER/domains/$DOMAIN/public_html

export NVM_DIR="$HOME/.nvm"
INSTALL_DIR="$HOME/.local/bin"
COMPOSER="$INSTALL_DIR/composer"
SETUP_FILE="composer-setup.php"

log() {
  echo -e "\033[1;34m🔧 $1\033[0m"
}

setup_node() {
  log "Vérification/installation de NVM et Node.js..."
  mkdir -p "$NVM_DIR"

  if [ ! -s "$NVM_DIR/nvm.sh" ]; then
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
  fi

  [ -s "$NVM_DIR/nvm.sh" ] && source "$NVM_DIR/nvm.sh"

  if ! command -v nvm >/dev/null; then
    echo "❌ NVM non disponible. Abandon."
    exit 1
  fi

  set +u
  LATEST_NODE=$(nvm ls-remote --no-colors | grep -oE 'v[0-9]+\.[0-9]+\.[0-9]+' | tail -1)
  CURRENT_NODE=$(nvm version default)
  set -u

  if [ "$CURRENT_NODE" != "$LATEST_NODE" ]; then
    log "Mise à jour de Node.js vers $LATEST_NODE..."
    nvm install "$LATEST_NODE"
    nvm alias default "$LATEST_NODE"
  else
    log "Node.js est à jour ($CURRENT_NODE)"
  fi
}

setup_composer() {
  log "Vérification/installation de Composer..."
  mkdir -p "$INSTALL_DIR"

  if [ -x "$COMPOSER" ]; then
    CURRENT_VERSION=$($COMPOSER --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
    LATEST_VERSION=$(curl -s https://getcomposer.org/versions | php -r '
      $versions = json_decode(stream_get_contents(STDIN), true);
      echo $versions["stable"][0]["version"];
    ')
    if [ "$CURRENT_VERSION" != "$LATEST_VERSION" ]; then
      log "Mise à jour Composer..."
      php -r "copy('https://getcomposer.org/installer', '$SETUP_FILE');"
      EXPECTED_SIGNATURE=$(curl -s https://getcomposer.org/installer.sig)
      ACTUAL_SIGNATURE=$(php -r "echo hash_file('sha384', '$SETUP_FILE');")
      if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
        echo "❌ Signature Composer invalide."
        rm -f "$SETUP_FILE"
        exit 1
      fi
      php "$SETUP_FILE" --install-dir="$INSTALL_DIR" --filename=composer
      rm -f "$SETUP_FILE"
    else
      log "Composer est à jour ($CURRENT_VERSION)"
    fi
  else
    log "Installation de Composer..."
    php -r "copy('https://getcomposer.org/installer', '$SETUP_FILE');"
    EXPECTED_SIGNATURE=$(curl -s https://getcomposer.org/installer.sig)
    ACTUAL_SIGNATURE=$(php -r "echo hash_file('sha384', '$SETUP_FILE');")
    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
      echo "❌ Signature Composer invalide."
      rm -f "$SETUP_FILE"
      exit 1
    fi
    php "$SETUP_FILE" --install-dir="$INSTALL_DIR" --filename=composer
    rm -f "$SETUP_FILE"
  fi
}

deploy_laravel() {
  log "Installation des dépendances PHP (prod)..."
  $COMPOSER install --no-dev --optimize-autoloader

  if [ -f package.json ]; then
    log "Installation des dépendances front + build..."
    npm install
    npm run build
  else
    log "Aucun frontend détecté (pas de package.json)"
  fi

  if ! php -r "exit((bool)env('APP_KEY'));" 2>/dev/null; then
    echo "🔐 Aucune clé APP_KEY détectée, génération..."
    php artisan key:generate
  else
    echo "✅ Clé APP_KEY déjà définie, aucune action nécessaire."
  fi

  log "Optimisation Laravel..."
  php artisan view:clear && php artisan view:cache
  php artisan config:clear && php artisan config:cache
  php artisan route:clear && php artisan route:cache
  php artisan cache:clear && php artisan optimize:clear
  php artisan optimize

  log "Migration base de données..."
  php artisan migrate --force

  log "Vérification des liens de stockage..."
  php artisan storage:link || true

  log "Permissions Laravel..."
  chmod -R 775 storage bootstrap/cache
}

# === Exécution ===
setup_node
setup_composer
deploy_laravel

echo -e "\033[1;32m✅ App Laravel déployée avec succès ! 🎉\033[0m"

rm -- "$0"