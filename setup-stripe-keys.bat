@echo off
echo.
echo ========================================
echo   EasyPass Stripe Setup Helper
echo ========================================
echo.
echo The payment system needs REAL Stripe API keys to work.
echo.
echo Step 1: Opening Stripe registration page...
start https://dashboard.stripe.com/register
echo.
echo Step 2: After creating your account:
echo   1. Go to Developers ^> API keys
echo   2. Make sure you're in TEST mode
echo   3. Copy your Publishable key (pk_test_...)
echo   4. Copy your Secret key (sk_test_...)
echo.
echo Step 3: Update these files with your keys:
echo.
echo   Frontend (.env):
echo   VITE_STRIPE_PUBLISHABLE_KEY=pk_test_YOUR_KEY_HERE
echo.
echo   Backend (.env):
echo   STRIPE_KEY=pk_test_YOUR_KEY_HERE
echo   STRIPE_SECRET=sk_test_YOUR_KEY_HERE
echo.
echo Step 4: Restart your servers:
echo   Backend: php artisan serve
echo   Frontend: npm run dev
echo.
echo ========================================
echo   Test Cards (once keys are set):
echo   Success: 4242424242424242
echo   Decline: 4000000000000002
echo   Expiry: 12/25, CVC: 123
echo ========================================
echo.
pause
