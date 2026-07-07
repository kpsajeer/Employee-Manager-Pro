---
name: PHP Employee Manager – PostgreSQL migration
description: Key decisions and gotchas from migrating the PHP employee app from MySQL to Replit's built-in PostgreSQL.
---

## What changed

- **Database**: MySQL (self-managed daemon via start.sh) → Replit built-in PostgreSQL (DATABASE_URL)
- **Connection**: config/database.php parses DATABASE_URL with parse_url(); credentials decoded with rawurldecode(); URL query params (sslmode etc.) forwarded into DSN
- **Schema bootstrap**: setup.php (PHP/PDO) runs on start; idempotent via IF NOT EXISTS + ON CONFLICT DO NOTHING
- **start.sh**: stripped all mysqld init code; now just checks DATABASE_URL, runs composer if needed, runs setup.php, then exec php -S

## PostgreSQL-specific SQL changes in Employee.php

| MySQL | PostgreSQL |
|---|---|
| `LIKE` | `ILIKE` (case-insensitive) |
| `SUM(status = 'active')` | `COUNT(*) FILTER (WHERE status = 'active')` |
| SQLSTATE `23000` | SQLSTATE `23505` (unique_violation) |
| `lastInsertId()` | `INSERT ... RETURNING id` + `fetchColumn()` |
| `ATTR_EMULATE_PREPARES => false` | `ATTR_EMULATE_PREPARES => true` (required for repeated named params like `:search` × 3) |

## DSN construction (critical)

**Why:** parse_url() drops query string params (e.g. sslmode=require). Must explicitly re-append them.
**How:**
```php
parse_str($parsed['query'] ?? '', $extraOptions);
$dsn = "pgsql:host=...;port=...;dbname=...";
foreach ($extraOptions as $k => $v) { $dsn .= ";$k=$v"; }
```
Credentials must also be rawurldecode()'d before passing to PDO.

## Nix packages

mysql80 removed from replit.nix; pdo_pgsql already included in pkgs.php83 — no extra Nix package needed.

## Deployment note

With PostgreSQL, deployment type can be Autoscale (no local daemon needed). Production config is in artifact.toml [services.production].
