#!/bin/bash

# Vue to Livewire Migration Cleanup Script
# This script helps automate the removal of Vue.js files and dependencies after migration to Livewire

echo "🧹 Starting Vue.js cleanup process..."

# Create backup directory
BACKUP_DIR="./vue-backup-$(date +%Y%m%d%H%M%S)"
echo "📦 Creating backup directory: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"
mkdir -p "$BACKUP_DIR/components"
mkdir -p "$BACKUP_DIR/tests"
mkdir -p "$BACKUP_DIR/config"

# Function to backup and remove files
backup_and_remove() {
    local SOURCE=$1
    local DEST=$2
    
    if [ -e "$SOURCE" ]; then
        echo "🔄 Backing up $SOURCE to $DEST"
        cp -r "$SOURCE" "$DEST"
        echo "🗑️ Removing $SOURCE"
        rm -rf "$SOURCE"
    else
        echo "⚠️ Warning: $SOURCE not found, skipping"
    fi
}

# 1. Backup and remove Vue components
echo "⚙️ Processing Vue components..."
backup_and_remove "resources/js/components" "$BACKUP_DIR/components"
backup_and_remove "resources/js/pages" "$BACKUP_DIR/components/pages"
backup_and_remove "resources/js/views" "$BACKUP_DIR/components/views"

# 2. Backup and remove Vue tests
echo "⚙️ Processing Vue tests..."
backup_and_remove "resources/js/tests" "$BACKUP_DIR/tests"
backup_and_remove "tests/Unit/js" "$BACKUP_DIR/tests/unit"
backup_and_remove "tests/JavaScript" "$BACKUP_DIR/tests/javascript"

# 3. Backup and remove Vue configuration
echo "⚙️ Processing Vue configuration..."
backup_and_remove "resources/js/app.js" "$BACKUP_DIR/config"
backup_and_remove "resources/js/bootstrap.js" "$BACKUP_DIR/config"
backup_and_remove "resources/js/router.js" "$BACKUP_DIR/config"
backup_and_remove "resources/js/store" "$BACKUP_DIR/config/store"

# 4. Backup and update package.json to remove Vue dependencies
echo "⚙️ Updating package.json..."
cp package.json "$BACKUP_DIR/config/"

# List of Vue-related dependencies to remove
VUE_PACKAGES=(
    "vue"
    "vue-router"
    "vuex"
    "@vue/test-utils"
    "@vue/compiler-sfc"
    "vue-loader"
    "vue-template-compiler"
    "vuex-persistedstate"
    "jest"
    "vue-jest"
    "babel-jest"
    "@babel/preset-env"
    "vitest"
    "jsdom"
)

# Create a temporary package.json without Vue dependencies
echo "🔄 Removing Vue dependencies from package.json"
for package in "${VUE_PACKAGES[@]}"; do
    # Check if the package exists in dependencies or devDependencies
    if grep -q "\"$package\":" package.json; then
        echo "   Removing $package"
        # Use jq to remove the package if it exists
        if command -v jq &> /dev/null; then
            # Try to remove from dependencies
            jq "if .dependencies[\"$package\"] then .dependencies[\"$package\"] = null else . end" package.json > temp.json
            mv temp.json package.json
            # Try to remove from devDependencies
            jq "if .devDependencies[\"$package\"] then .devDependencies[\"$package\"] = null else . end" package.json > temp.json
            mv temp.json package.json
            # Clean up null entries
            jq 'del(.dependencies | nulls) | del(.devDependencies | nulls)' package.json > temp.json
            mv temp.json package.json
        else
            echo "⚠️ jq command not found. Please manually edit package.json to remove Vue dependencies."
        fi
    fi
done

# 5. Backup and update webpack.mix.js or vite.config.js
if [ -f webpack.mix.js ]; then
    echo "⚙️ Backing up and updating webpack.mix.js..."
    cp webpack.mix.js "$BACKUP_DIR/config/"
    echo "⚠️ Please manually update webpack.mix.js to remove Vue-specific configurations"
fi

if [ -f vite.config.js ]; then
    echo "⚙️ Backing up and updating vite.config.js..."
    cp vite.config.js "$BACKUP_DIR/config/"
    echo "⚠️ Please manually update vite.config.js to remove Vue-specific configurations"
fi

# 6. Remove Jest configuration if it exists
if [ -f jest.config.js ]; then
    echo "⚙️ Backing up and removing Jest configuration..."
    cp jest.config.js "$BACKUP_DIR/config/"
    rm jest.config.js
fi

if [ -f jest.setup.js ]; then
    cp jest.setup.js "$BACKUP_DIR/config/"
    rm jest.setup.js
fi

# 7. Final steps
echo "✅ Vue.js files and dependencies have been backed up and removed."
echo "📦 Backup files are stored in: $BACKUP_DIR"
echo ""
echo "Next steps:"
echo "1. Install any new dependencies with 'npm install'"
echo "2. Rebuild assets with 'npm run build'"
echo "3. Run all Livewire tests with 'php artisan test --filter=Livewire'"
echo "4. Check the application functionality in the browser"
echo ""
echo "If everything works correctly, you can delete the backup directory."
echo "If issues occur, you can restore files from the backup directory."
echo ""
echo "Cleanup process completed!" 