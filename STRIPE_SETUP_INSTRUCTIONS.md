# 🚀 Stripe Setup Instructions for EasyPass

You're seeing errors because the Stripe API keys are not configured. Here's how to fix it:

## ⚠️ Current Errors

- **Frontend**: `Stripe publishable key not found`
- **Backend**: `400 Bad Request` when creating payment intent

## 🔧 Quick Fix

### 1. Get Your Stripe Keys

1. Go to [https://dashboard.stripe.com/register](https://dashboard.stripe.com/register)
2. Create a free Stripe account
3. Go to **Developers** → **API keys**
4. Copy your **Publishable key** and **Secret key** (use TEST keys for now)

### 2. Update Environment Files

#### Frontend (.env file)
```bash
VITE_API_URL=http://127.0.0.1:8000
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_YOUR_ACTUAL_PUBLISHABLE_KEY_HERE
```

#### Backend (.env file)
Add these lines to your backend `.env` file:
```bash
# Stripe Configuration
STRIPE_KEY=pk_test_YOUR_ACTUAL_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_ACTUAL_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET_HERE
```

### 3. Restart Your Servers

After updating the environment files:

```bash
# Stop both servers (Ctrl+C)
# Then restart:

# Backend
cd backend
php artisan serve

# Frontend (in new terminal)
cd frontend  
npm run dev
```

## 🧪 Test Cards for Development

Once configured, use these test card numbers:

- **Success**: `4242424242424242`
- **Declined**: `4000000000000002`
- **Insufficient Funds**: `4000000000009995`

**Expiry**: Any future date (e.g., `12/25`)
**CVC**: Any 3 digits (e.g., `123`)

## 🔒 For Production Later

1. Switch to **Live keys** in Stripe dashboard
2. Set up webhooks for automatic payment confirmation
3. Add your domain to Stripe's allowed domains

## 📞 Need Help?

If you're still having issues:

1. **Check console errors** in browser developer tools
2. **Check Laravel logs** in `backend/storage/logs/laravel.log`
3. **Verify environment variables** are loaded correctly

## 🎯 Quick Test

After setup, go to any event page and click "Purchase Ticket". You should see the Stripe payment form instead of errors.

---

**💡 Tip**: Keep your Secret keys private and never commit them to version control!
