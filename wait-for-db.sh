echo "Waiting for database to be ready..."
until nc -z database 3306; do
  echo "Database is unavailable - sleeping"
  sleep 1
done

echo "Database is ready! Executing command..."

exec "$@"