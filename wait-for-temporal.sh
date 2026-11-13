#!/bin/sh
# wait-for-temporal.sh

set -e

if [ "$#" -eq 0 ]; then
  >&2 echo "Usage: wait-for-temporal.sh command [arguments...]"
  exit 2
fi

attempt=1
until temporal operator namespace describe --namespace default --command-timeout 5s >/dev/null; do
  if [ "$attempt" -ge 60 ]; then
    >&2 echo "Temporal namespace is unavailable after 60 attempts - exiting"
    exit 1
  fi
  >&2 echo "Temporal namespace is unavailable - sleeping"
  sleep 2
  attempt=$((attempt + 1))
done

>&2 echo "Temporal is up - executing command"
exec "$@"
