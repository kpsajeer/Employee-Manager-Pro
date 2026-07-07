#!/usr/bin/env bash
# ============================================================
# Employee Manager – Replit startup script
# Uses Replit's built-in PostgreSQL (DATABASE_URL is pre-set).
# ============================================================
set -euo pipefail

WORKDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "========================================"
echo "  Employee Manager – Starting up"
echo "========================================"

# ── Verify DATABASE_URL is set ───────────────────────────────
if [ -z "${DATABASE_URL:-}" ]; then
    echo "ERROR: DATABASE_URL is not set."
    echo "Make sure a PostgreSQL database is provisioned for this Replit project."
    exit 1
fi
echo "[PostgreSQL] DATABASE_URL is set."

# ── Composer: install dependencies if vendor/ is not ready ───
if [ ! -d "$WORKDIR/vendor/composer" ]; then
    echo "[Composer] Installing PHP dependencies…"
    cd "$WORKDIR" && composer install --no-interaction --prefer-dist --quiet
    echo "[Composer] Done."
fi

# ── Bootstrap schema + seed data (first run only) ────────────
echo "[Setup] Checking schema…"
php "$WORKDIR/setup.php"

# ── PHP built-in server ───────────────────────────────────────
SERVER_PORT="${PORT:-8000}"
echo "[PHP] Starting server → http://0.0.0.0:${SERVER_PORT}"
echo "========================================"
exec php -S "0.0.0.0:${SERVER_PORT}" -t "$WORKDIR/public"
