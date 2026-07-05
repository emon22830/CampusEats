#!/bin/bash
set -euo pipefail

for f in /sql-source/migrations/*.sql; do
    echo "Applying migration: $f"
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < "$f"
done

for f in /sql-source/seed/*.sql; do
    echo "Applying seed: $f"
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < "$f"
done
