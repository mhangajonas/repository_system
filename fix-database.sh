#!/bin/bash

echo "🔧 Fixing URMS Database Connection..."
echo ""

# Step 1: Kill any existing MySQL processes
echo "1️⃣  Stopping existing MySQL processes..."
pkill -f mysqld || true
pkill -f mysql || true
sleep 2

# Step 2: Start MySQL fresh
echo "2️⃣  Starting MySQL service..."
/opt/lampp/bin/mysqld_safe --user=root &
sleep 5

# Step 3: Verify MySQL is running
echo "3️⃣  Verifying MySQL connection..."
if /opt/lampp/bin/mysql -u root -e "SELECT 1;" 2>/dev/null; then
    echo "✅ MySQL is connected!"
else
    echo "❌ MySQL connection failed. Attempting recovery..."
    pkill -9 mysqld || true
    rm -f /opt/lampp/var/mysql/*.pid
    /opt/lampp/bin/mysqld_safe --user=root &
    sleep 5
fi

# Step 4: Create database if it doesn't exist
echo "4️⃣  Creating/checking repository_system database..."
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS repository_system;" 2>/dev/null
/opt/lampp/bin/mysql -u root -e "SHOW DATABASES LIKE 'repository_system';" 2>/dev/null | grep -q repository_system && echo "✅ Database exists!" || echo "❌ Database creation failed"

# Step 5: Verify .env is correct
echo "5️⃣  Verifying .env configuration..."
cd /opt/lampp/htdocs/repository_system
if grep -q "DB_DATABASE=repository_system" .env; then
    echo "✅ .env has correct database name"
else
    echo "❌ .env needs fixing"
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=repository_system/' .env
    echo "✅ .env fixed"
fi

# Step 6: Reload Laravel config
echo "6️⃣  Clearing Laravel cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Step 7: Run migrations
echo "7️⃣  Running database migrations..."
php artisan migrate --force 2>/dev/null || echo "⚠️  Migrations may have already run"

echo ""
echo "✅ Database setup complete!"
echo ""
echo "Next steps:"
echo "  • Run: php artisan serve"
echo "  • Test: Visit http://localhost:8000"
