<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Public event routes
Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::get('/{id}', [EventController::class, 'show']);
    Route::get('/categories/list', [EventController::class, 'categories']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });

    // Event management routes
    Route::prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
        Route::get('/my/events', [EventController::class, 'myEvents']);
    });

    // Ticket routes
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{id}', [TicketController::class, 'show']);
        Route::post('/validate', [TicketController::class, 'validate']);
        Route::put('/{id}/cancel', [TicketController::class, 'cancel']);
        Route::get('/{id}/download', [TicketController::class, 'download']);
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiatePayment']);
        Route::post('/status', [PaymentController::class, 'checkPaymentStatus']);
        Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
        Route::post('/cancel', [PaymentController::class, 'cancelPayment']);
        Route::get('/history', [PaymentController::class, 'paymentHistory']);
    });

    // Contact/Support route
    Route::post('/contact', function (Request $request) {
        // Simple contact endpoint - in real implementation, this would send email
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Log the contact message (in real app, send email to developers)
        \Log::info('Contact form submission', [
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Your message has been sent successfully. We will get back to you soon.'
        ]);
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'EasyPass API is running',
        'timestamp' => now(),
    ]);
});

// Public webhook routes (outside auth middleware)
Route::prefix('payments/webhook')->group(function () {
    Route::post('/khqr', [PaymentController::class, 'handleKHQRWebhook']);
});

// Test payment endpoint (public - for debugging only)
Route::post('/test-payment', function (Request $request) {
    \Log::info('Test payment request received', $request->all());
    return response()->json([
        'status' => 'success',
        'message' => 'Test payment endpoint working',
        'received_data' => $request->all(),
        'timestamp' => now(),
    ]);
});
