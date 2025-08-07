#!/bin/sh

# wait-for-mysql.sh
set -e

host="$1"
shift
cmd="$@"

until mysql -h "$host" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e 'SELECT 1'; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 3
done

>&2 echo "MySQL is up - executing command"
exec $cmd
