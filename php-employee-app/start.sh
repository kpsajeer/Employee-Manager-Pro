#!/usr/bin/env bash
# ============================================================
# Employee Manager – Replit startup script
# Initialises MySQL, imports schema (once), then serves PHP.
# ============================================================
set -euo pipefail

WORKDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MYSQL_DATA="/home/runner/mysql_data"
MYSQL_SOCK="/tmp/mysql.sock"
MYSQL_LOG="/tmp/mysql_error.log"

echo "========================================"
echo "  Employee Manager – Starting up"
echo "========================================"

# ── MySQL: initialise data directory (first run only) ────────
if [ ! -d "$MYSQL_DATA" ]; then
    echo "[MySQL] Initialising data directory (first run)…"
    mkdir -p "$MYSQL_DATA"
    mysqld \
        --initialize-insecure \
        --datadir="$MYSQL_DATA" \
        --user="$(whoami)" \
        2>>"$MYSQL_LOG"
    echo "[MySQL] Data directory ready."
fi

# ── MySQL: start server if not already running ────────────────
if ! mysqladmin --socket="$MYSQL_SOCK" -u root ping --silent 2>/dev/null; then
    echo "[MySQL] Starting server…"
    mysqld \
        --datadir="$MYSQL_DATA" \
        --socket="$MYSQL_SOCK" \
        --pid-file=/tmp/mysql.pid \
        --user="$(whoami)" \
        --bind-address=127.0.0.1 \
        --port=3306 \
        --log-error="$MYSQL_LOG" \
        --daemonize=ON \
        2>>"$MYSQL_LOG"

    echo "[MySQL] Waiting for server to accept connections…"
    for i in $(seq 1 30); do
        if mysqladmin --socket="$MYSQL_SOCK" -u root ping --silent 2>/dev/null; then
            echo "[MySQL] Ready after ${i}s."
            break
        fi
        sleep 1
        if [ "$i" -eq 30 ]; then
            echo "[MySQL] ERROR: server did not start in time. Check $MYSQL_LOG"
            cat "$MYSQL_LOG" | tail -20
            exit 1
        fi
    done

    # Ensure root uses mysql_native_password so PHP PDO can connect
    mysql --socket="$MYSQL_SOCK" -u root \
        -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;" \
        2>/dev/null || true
fi

# ── MySQL: create database + import schema (once) ────────────
if ! mysql --socket="$MYSQL_SOCK" -u root \
        -e "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='employee_manager';" \
        2>/dev/null | grep -q employee_manager; then
    echo "[MySQL] Creating database and importing schema + seed data…"
    mysql --socket="$MYSQL_SOCK" -u root < "$WORKDIR/database.sql"
    echo "[MySQL] Done — 20 sample employees loaded."
fi

# ── Composer: install dependencies if vendor/ is not ready ───
if [ ! -d "$WORKDIR/vendor/composer" ]; then
    echo "[Composer] Installing PHP dependencies…"
    cd "$WORKDIR" && composer install --no-interaction --prefer-dist --quiet
    echo "[Composer] Done."
fi

# ── PHP built-in server ───────────────────────────────────────
SERVER_PORT="${PORT:-8000}"
echo "[PHP] Starting server → http://0.0.0.0:${SERVER_PORT}"
echo "========================================"
exec php -S "0.0.0.0:${SERVER_PORT}" -t "$WORKDIR/public"
