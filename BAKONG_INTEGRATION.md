# Bakong Payment Integration for EasyPass EMS

This document describes the Bakong payment integration implemented in the EasyPass Event Management System.

## Overview

The Bakong payment system has been integrated to provide secure, QR code-based payments for event tickets. The integration uses the `bakong-khqr` library to generate compliant KHQR (Khmer QR) codes for payments.

## Features

- **QR Code Generation**: Dynamic QR codes for each payment transaction
- **Payment Status Tracking**: Real-time payment status monitoring
- **Payment History**: Complete transaction history for users
- **Admin Interface**: Manual payment confirmation for testing
- **Secure Processing**: Transaction-based payment flow with expiration times

## Architecture

### Backend Components

1. **PaymentController** (`backend/app/Http/Controllers/API/PaymentController.php`)
   - Handles payment initiation, status checking, and confirmation
   - Generates transaction IDs and manages payment data
   - Provides payment history and cancellation functionality

2. **Database Schema Updates**
   - Added Bakong-specific fields to `ticket_purchases` table:
     - `bakong_qr_data` (JSON): Stores QR code generation parameters
     - `bakong_payment_reference` (String): Bakong payment reference
     - `payment_expires_at` (Timestamp): Payment expiration time

3. **API Routes** (`backend/routes/api.php`)
   - `POST /api/payments/initiate`: Start a new payment
   - `POST /api/payments/status`: Check payment status
   - `POST /api/payments/confirm`: Confirm payment (manual/webhook)
   - `POST /api/payments/cancel`: Cancel pending payment
   - `GET /api/payments/history`: Get user payment history

### Frontend Components

1. **BakongPayment Component** (`frontend/src/components/payment/BakongPayment.vue`)
   - Multi-step payment interface (form → QR → success/error)
   - Real-time QR code generation using `bakong-khqr` library
   - Countdown timer for payment expiration
   - Automatic status checking

2. **Payment Store** (`frontend/src/stores/payments.ts`)
   - Centralized payment state management
   - API interaction methods
   - Payment history and statistics

3. **Views**
   - **PaymentHistoryView**: User payment transaction history
   - **AdminPaymentsView**: Admin interface for manual payment confirmation
   - **EventDetailView**: Updated with Bakong payment modal

## Payment Flow

1. **Initiation**: User clicks "Purchase Ticket" and fills payment form
2. **QR Generation**: System generates Bakong KHQR code with transaction details
3. **Payment**: User scans QR code with Bakong app and completes payment
4. **Confirmation**: Payment status is confirmed (manual or automatic)
5. **Ticket Creation**: Upon confirmation, ticket is generated with QR code

## Configuration

### Required Information for Payments

- **Merchant Name**: Business name for the payment
- **Merchant City**: City where business is located
- **Bakong Account**: Bakong account identifier (e.g., "yourname@wing")
- **Phone Number**: Contact phone number
- **Amount**: Payment amount in USD
- **Expiration Time**: 15 minutes from initiation

### Environment Setup

1. **Backend Dependencies**:
   ```bash
   cd backend
   composer require guzzlehttp/guzzle
   ```

2. **Frontend Dependencies**:
   ```bash
   cd frontend
   npm install bakong-khqr qrcode
   ```

3. **Database Migration**:
   ```bash
   php artisan migrate
   ```

## API Usage Examples

### Initiate Payment
```javascript
POST /api/payments/initiate
{
  "event_id": 1,
  "merchant_name": "EasyPass Events",
  "merchant_city": "Phnom Penh",
  "bakong_account": "easypass@wing",
  "phone_number": "+85512345678"
}
```

### Check Payment Status
```javascript
POST /api/payments/status
{
  "transaction_id": "TXN-ABC123"
}
```

### Confirm Payment (Admin/Webhook)
```javascript
POST /api/payments/confirm
{
  "transaction_id": "TXN-ABC123",
  "payment_reference": "BAKONG_REF_123"
}
```

## Testing

### Manual Testing Flow

1. **Start Payment**: Navigate to event detail page and click "Purchase Ticket"
2. **Fill Form**: Enter merchant details and submit
3. **View QR Code**: QR code is generated and displayed
4. **Admin Confirmation**: Use `/admin/payments` to manually confirm payment
5. **Verify Ticket**: Check that ticket is created and accessible

### Admin Interface

Access the admin payment interface at `/admin/payments` to:
- View all pending payments
- Manually confirm payments for testing
- Cancel expired or invalid payments

## Security Considerations

- Transaction IDs are unique and generated server-side
- Payment expiration prevents indefinite pending states
- All payment data is validated before processing
- QR codes contain transaction-specific information

## Integration Notes

### Bakong API Access

The current implementation uses the `bakong-khqr` library for QR code generation. For production use with real Bakong API integration:

1. Register for developer token at: https://api-bakong.nbc.gov.kh/register
2. Implement webhook endpoints for automatic payment confirmation
3. Add proper error handling for API failures
4. Implement payment reconciliation processes

### Production Considerations

- Replace manual confirmation with webhook integration
- Add comprehensive logging for payment transactions
- Implement payment retry mechanisms
- Add fraud detection and prevention measures
- Set up monitoring and alerting for payment failures

## File Structure

```
backend/
├── app/Http/Controllers/API/PaymentController.php
├── app/Models/TicketPurchase.php (updated)
├── database/migrations/2025_08_28_163713_add_bakong_fields_to_ticket_purchases_table.php
└── routes/api.php (updated)

frontend/
├── src/components/payment/BakongPayment.vue
├── src/stores/payments.ts
├── src/views/PaymentHistoryView.vue
├── src/views/AdminPaymentsView.vue
├── src/views/EventDetailView.vue (updated)
└── src/router/index.ts (updated)
```

## Support

For questions or issues related to the Bakong integration:
1. Check the payment logs in Laravel logs
2. Verify QR code generation in browser console
3. Test payment flow with admin interface
4. Review API responses for error details
