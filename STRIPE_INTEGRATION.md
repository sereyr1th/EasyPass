# Stripe Payment Integration for EasyPass EMS

This document describes the Stripe payment integration implemented in the EasyPass Event Management System.

## Overview

The Stripe payment system has been integrated to provide secure, modern payment processing for event tickets. The integration uses Stripe's Payment Intents API with Stripe Elements for a seamless checkout experience.

## Features

- **Secure Payment Processing**: PCI DSS compliant payment handling
- **Modern UI**: Stripe Elements with customizable styling
- **Multiple Payment Methods**: Credit cards, debit cards, and digital wallets
- **Real-time Status**: Webhook-based payment confirmation
- **Admin Interface**: Manual payment confirmation for testing
- **Mobile Responsive**: Optimized for all devices

## Architecture

### Backend Components

1. **PaymentController** (`backend/app/Http/Controllers/API/PaymentController.php`)
   - Creates Stripe PaymentIntents
   - Handles payment status checking and confirmation
   - Processes Stripe webhooks
   - Provides payment history and cancellation functionality

2. **Database Schema Updates**
   - Updated `ticket_purchases` table with Stripe-specific fields:
     - `stripe_payment_intent_id` (String): Stripe PaymentIntent ID
     - `stripe_payment_method_id` (String): Stripe PaymentMethod ID
     - `stripe_client_secret` (String): Client secret for frontend
     - `stripe_metadata` (JSON): Additional Stripe metadata

3. **API Routes** (`backend/routes/api.php`)
   - `POST /api/payments/create-intent`: Create Stripe PaymentIntent
   - `POST /api/payments/status`: Check payment status
   - `POST /api/payments/confirm`: Manual payment confirmation (admin)
   - `POST /api/payments/cancel`: Cancel pending payment
   - `GET /api/payments/history`: Get user payment history
   - `POST /api/payments/webhook/stripe`: Stripe webhook endpoint

### Frontend Components

1. **StripePayment Component** (`frontend/src/components/payment/StripePayment.vue`)
   - Multi-step payment interface (form → payment → success/error)
   - Stripe Elements integration with dark theme
   - Real-time payment processing
   - Responsive design with modern animations

2. **Payment Store** (`frontend/src/stores/payments.ts`)
   - Centralized payment state management
   - Stripe API interaction methods
   - Payment history and statistics

3. **Views Updated**
   - **EventDetailView**: Updated to use StripePayment component
   - **PaymentHistoryView**: Shows Stripe payment information
   - **AdminPaymentsView**: Admin interface for manual payment confirmation

## Payment Flow

1. **Intent Creation**: User clicks "Purchase Ticket" and PaymentIntent is created
2. **Payment Form**: Stripe Elements renders secure payment form
3. **Payment Processing**: User enters payment details and confirms
4. **Webhook Processing**: Stripe sends webhook to confirm payment
5. **Ticket Creation**: Upon confirmation, ticket is generated with QR code

## Configuration

### Required Environment Variables

#### Backend (.env)
```bash
# Stripe Configuration
STRIPE_KEY=pk_test_your_stripe_publishable_key_here
STRIPE_SECRET=sk_test_your_stripe_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

#### Frontend (.env)
```bash
# Stripe Configuration
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_publishable_key_here
```

### Setup Instructions

1. **Get Stripe Keys**:
   - Sign up at [stripe.com](https://stripe.com)
   - Get your publishable and secret keys from the dashboard
   - Create a webhook endpoint for payment confirmations

2. **Backend Setup**:
   ```bash
   cd backend
   composer require stripe/stripe-php
   php artisan migrate
   ```

3. **Frontend Setup**:
   ```bash
   cd frontend
   npm install @stripe/stripe-js
   ```

4. **Webhook Configuration**:
   - Set webhook URL to: `https://yourdomain.com/api/payments/webhook/stripe`
   - Enable these events:
     - `payment_intent.succeeded`
     - `payment_intent.payment_failed`
     - `payment_intent.canceled`

## API Usage Examples

### Create Payment Intent
```javascript
POST /api/payments/create-intent
{
  "event_id": 1,
  "amount": 25.00
}
```

### Check Payment Status
```javascript
POST /api/payments/status
{
  "transaction_id": "TXN-ABC123"
}
```

### Manual Confirmation (Admin)
```javascript
POST /api/payments/confirm
{
  "transaction_id": "TXN-ABC123"
}
```

## Testing

### Test Cards (Stripe Test Mode)

- **Successful Payment**: `4242424242424242`
- **Declined Card**: `4000000000000002`
- **Insufficient Funds**: `4000000000009995`
- **Expired Card**: `4000000000000069`

### Testing Flow

1. **Start Payment**: Navigate to event detail page and click "Purchase Ticket"
2. **Fill Payment Form**: Use test card numbers above
3. **Complete Payment**: Payment should process immediately in test mode
4. **Verify Ticket**: Check that ticket is created and accessible
5. **Admin Testing**: Use `/admin/payments` for manual confirmations

## Security Features

- **PCI DSS Compliance**: Stripe handles all sensitive card data
- **Secure Tokenization**: Card details never touch your servers
- **Webhook Verification**: All webhooks are cryptographically verified
- **HTTPS Required**: All Stripe communication requires SSL/TLS
- **3D Secure**: Automatic 3D Secure authentication when required

## Production Deployment

### Stripe Configuration

1. **Switch to Live Mode**:
   - Replace test keys with live keys
   - Update webhook endpoints
   - Test with real (small) transactions

2. **Webhook Security**:
   - Verify webhook signatures in production
   - Use HTTPS for all webhook endpoints
   - Monitor webhook delivery in Stripe dashboard

3. **Error Handling**:
   - Implement proper error logging
   - Set up monitoring and alerts
   - Handle edge cases (network failures, etc.)

### Performance Optimization

- **Caching**: Cache Stripe customer data where appropriate
- **Async Processing**: Use queues for non-critical operations
- **Monitoring**: Set up application performance monitoring
- **Backup**: Regular database backups including payment data

## Troubleshooting

### Common Issues

1. **"Invalid API Key"**:
   - Verify environment variables are set correctly
   - Check if using test vs live keys appropriately

2. **"Webhook signature verification failed"**:
   - Ensure webhook secret is correctly configured
   - Verify webhook URL is accessible from Stripe

3. **"Payment requires authentication"**:
   - 3D Secure authentication required
   - Stripe Elements handles this automatically

### Debugging

1. **Check Stripe Dashboard**: View payment attempts and webhook deliveries
2. **Laravel Logs**: Check `storage/logs/laravel.log` for errors
3. **Browser Console**: Check for JavaScript errors in payment flow
4. **Network Tab**: Monitor API requests and responses

## File Structure

```
backend/
├── app/Http/Controllers/API/PaymentController.php (updated)
├── app/Models/TicketPurchase.php (updated)
├── config/services.php (updated)
├── database/migrations/2025_08_30_145954_update_ticket_purchases_for_stripe.php
└── routes/api.php (updated)

frontend/
├── src/components/payment/StripePayment.vue (new)
├── src/stores/payments.ts (updated)
├── src/views/EventDetailView.vue (updated)
└── package.json (updated dependencies)
```

## Support and Resources

- **Stripe Documentation**: [stripe.com/docs](https://stripe.com/docs)
- **Stripe Elements**: [stripe.com/docs/stripe-js](https://stripe.com/docs/stripe-js)
- **Payment Intents**: [stripe.com/docs/payments/payment-intents](https://stripe.com/docs/payments/payment-intents)
- **Webhooks**: [stripe.com/docs/webhooks](https://stripe.com/docs/webhooks)

For questions or issues:
1. Check Stripe dashboard for payment details
2. Review Laravel logs for backend errors
3. Test with Stripe's test cards
4. Use Stripe's webhook testing tools
