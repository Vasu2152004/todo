@echo off
echo Generating composer.lock file...
echo.

REM Check if composer is available
where composer >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer is not installed or not in PATH
    echo Please install Composer from https://getcomposer.org/
    pause
    exit /b 1
)

echo Running composer install to generate composer.lock...
composer install --no-interaction

if %ERRORLEVEL% EQU 0 (
    echo.
    echo SUCCESS: composer.lock file generated!
    echo.
    echo Next steps:
    echo 1. git add composer.lock
    echo 2. git commit -m "Add composer.lock for Wasmer deployment"
    echo 3. git push
    echo 4. Redeploy on Wasmer
) else (
    echo.
    echo ERROR: Failed to generate composer.lock
    echo Please check the error messages above
)

pause
