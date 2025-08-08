#!/bin/sh
set -e

host="$1"
user="$2"
pass="$3"
shift 3
cmd="$@"

until mysql -h "$host" -u"$user" -p"$pass" -e 'SELECT 1'; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 3
done

>&2 echo "MySQL is up - executing command"
exec $cmd
