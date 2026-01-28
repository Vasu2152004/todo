#!/bin/bash

echo "Generating composer.lock file..."
echo ""

# Check if composer is available
if ! command -v composer &> /dev/null; then
    echo "ERROR: Composer is not installed or not in PATH"
    echo "Please install Composer from https://getcomposer.org/"
    exit 1
fi

echo "Running composer install to generate composer.lock..."
composer install --no-interaction

if [ $? -eq 0 ]; then
    echo ""
    echo "SUCCESS: composer.lock file generated!"
    echo ""
    echo "Next steps:"
    echo "1. git add composer.lock"
    echo "2. git commit -m 'Add composer.lock for Wasmer deployment'"
    echo "3. git push"
    echo "4. Redeploy on Wasmer"
else
    echo ""
    echo "ERROR: Failed to generate composer.lock"
    echo "Please check the error messages above"
    exit 1
fi
