#!/bin/bash

echo "🚀 EasyPass Stripe Setup Script"
echo "================================"
echo ""

# Check if we're in the right directory
if [[ ! -d "frontend" || ! -d "backend" ]]; then
    echo "❌ Please run this script from the EasyPass-EMS root directory"
    exit 1
fi

echo "📝 Setting up Stripe configuration..."

# Prompt for Stripe keys
echo ""
echo "Please enter your Stripe keys (get them from https://dashboard.stripe.com/apikeys):"
echo ""

read -p "Enter your Stripe Publishable Key (pk_test_...): " STRIPE_PK
read -p "Enter your Stripe Secret Key (sk_test_...): " STRIPE_SK

# Validate keys
if [[ ! $STRIPE_PK == pk_test_* ]]; then
    echo "⚠️  Warning: Publishable key should start with 'pk_test_' for testing"
fi

if [[ ! $STRIPE_SK == sk_test_* ]]; then
    echo "⚠️  Warning: Secret key should start with 'sk_test_' for testing"
fi

# Update frontend .env
echo "🔧 Updating frontend/.env..."
cat > frontend/.env << EOF
VITE_API_URL=http://127.0.0.1:8000
VITE_STRIPE_PUBLISHABLE_KEY=$STRIPE_PK
EOF

# Update backend .env
echo "🔧 Updating backend/.env..."
if ! grep -q "STRIPE_KEY" backend/.env; then
    cat >> backend/.env << EOF

# Stripe Configuration
STRIPE_KEY=$STRIPE_PK
STRIPE_SECRET=$STRIPE_SK
STRIPE_WEBHOOK_SECRET=whsec_placeholder_for_now
EOF
else
    echo "⚠️  Stripe configuration already exists in backend/.env"
    echo "   Please update it manually with your keys"
fi

echo ""
echo "✅ Stripe configuration complete!"
echo ""
echo "🔄 Next steps:"
echo "1. Restart your Laravel server: cd backend && php artisan serve"
echo "2. Restart your Vue dev server: cd frontend && npm run dev"
echo "3. Test payment on any event page"
echo ""
echo "🧪 Test cards:"
echo "   Success: 4242424242424242"
echo "   Decline: 4000000000000002"
echo "   Use any future expiry (12/25) and any CVC (123)"
echo ""
echo "🎉 Happy coding!"
