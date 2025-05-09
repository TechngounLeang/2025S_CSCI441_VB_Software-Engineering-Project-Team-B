@echo off
echo =============================================
echo TESTING FRAMEWORK DEMONSTRATION
echo =============================================
echo.

echo Running Unit Tests
echo Unit tests verify individual components in isolation
echo.
php artisan test --testsuite=Unit
echo.
echo Press Enter to continue...
pause > nul

echo Running Integration Tests (Feature Tests)
echo Integration tests verify how components work together
echo.
php artisan test --testsuite=Feature
echo.
echo Press Enter to continue...
pause > nul

echo Running API Tests
echo API tests verify API endpoints and responses
echo.
php artisan test --testsuite=API
echo.
echo Press Enter to continue...
pause > nul

echo Running All Tests
echo This shows the full test suite execution
echo.
php artisan test
echo.

echo =============================================
echo TESTING FRAMEWORK DEMONSTRATION COMPLETE
echo =============================================