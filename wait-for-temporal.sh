#!/bin/sh
# wait-for-temporal.sh

set -e

shift
cmd="$@"

until tctl --namespace default namespace describe; do
  >&2 echo "Temporal namespace is unavailable - sleeping"
  sleep 10
done

>&2 echo "Temporal is up - executing command"
exec $cmd
