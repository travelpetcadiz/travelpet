#!/usr/bin/env bash
# Despliegue automático de TravelPet: si hay commits nuevos en origin/main,
# pasa los tests, reconstruye la imagen y la arranca. Si la web no responde,
# vuelve a la imagen anterior. Pensado para cron cada minuto:
#   * * * * * /home/luisb/travel/docker/deploy.sh >> /home/luisb/travel/storage/logs/deploy.log 2>&1
set -euo pipefail

APP_DIR="/home/luisb/travel"
RAMA="main"
IMAGEN="travelpet:production"
URL_SALUD="http://127.0.0.1:8070/"
export GIT_SSH_COMMAND="ssh -i $HOME/.ssh/travelpet_deploy -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"

cd "$APP_DIR"

# Un solo despliegue a la vez
exec 9>/tmp/travelpet-deploy.lock
flock -n 9 || exit 0

git fetch --quiet origin "$RAMA"
ACTUAL=$(git rev-parse HEAD)
NUEVO=$(git rev-parse "origin/$RAMA")
[ "$ACTUAL" = "$NUEVO" ] && exit 0

# Un commit que ya falló no se reintenta cada minuto
[ "$(cat .deploy-fallido 2>/dev/null)" = "$NUEVO" ] && exit 0

log() { echo "[$(date '+%F %T')] $*"; }
log "Nuevo commit ${NUEVO:0:7}: $(git log -1 --format=%s "$NUEVO")"

if ! git merge --ff-only --quiet "origin/$RAMA"; then
    log "ERROR: hay cambios locales en el servidor que chocan con GitHub. No se despliega."
    echo "$NUEVO" > .deploy-fallido
    exit 1
fi

composer install --no-interaction --quiet
if ! php artisan test --compact; then
    log "ERROR: fallan los tests. No se despliega; la web sigue con ${ACTUAL:0:7}."
    echo "$NUEVO" > .deploy-fallido
    exit 1
fi

docker image inspect "$IMAGEN" >/dev/null 2>&1 && docker tag "$IMAGEN" travelpet:anterior
if ! docker compose build --quiet; then
    log "ERROR: falla la construcción de la imagen. No se despliega."
    echo "$NUEVO" > .deploy-fallido
    exit 1
fi
docker compose up -d --quiet-pull

for i in $(seq 1 30); do
    [ "$(curl -s -o /dev/null -w '%{http_code}' "$URL_SALUD")" = "200" ] && { rm -f .deploy-fallido; log "Desplegado ${NUEVO:0:7}."; exit 0; }
    sleep 2
done

log "ERROR: la web no responde con ${NUEVO:0:7}. Vuelvo a la imagen anterior."
docker tag travelpet:anterior "$IMAGEN"
docker compose up -d --no-build --force-recreate
echo "$NUEVO" > .deploy-fallido
exit 1
