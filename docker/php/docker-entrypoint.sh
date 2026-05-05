#!/bin/sh
set -e

cd /var/www

if [ -f composer.json ]; then
  echo "Instaluję zależności Composer..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "Nie znaleziono pliku composer.json, pomijam instalację Composer."
fi

if [ -f package.json ]; then
  echo "Instaluję zależności Node.js..."
  npm install
  echo "Buduję assety frontend..."
  npm run build
else
  echo "Nie znaleziono pliku package.json, pomijam instalację Node.js."
fi

exec "$@"