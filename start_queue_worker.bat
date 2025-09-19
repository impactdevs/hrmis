@echo off
echo Starting Laravel Queue Worker...
echo This will process email notifications for new employees.
echo Press Ctrl+C to stop the worker.
echo.
php artisan queue:work --verbose --timeout=60
pause
