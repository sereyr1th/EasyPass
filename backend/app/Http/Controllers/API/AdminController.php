<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Admin Controller
 * Handles admin-specific functionality like user management, analytics, etc.
 */
class AdminController extends Controller
{
    /**
     * Get admin dashboard analytics
     */
    public function dashboard(): JsonResponse
    {
        try {
            $totalUsers = User::count();
            $totalAdmins = User::where('role', 'admin')->count();
            $totalEvents = Event::count();
            $activeEvents = Event::where('event_date', '>=', Carbon::now())->count();
            $totalTickets = Ticket::count();
            $totalRevenue = (float) TicketPurchase::where('payment_status', 'completed')
                ->sum('amount_paid') ?: 0;

            // Recent activity
            $recentUsers = User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'created_at']);
            $recentEvents = Event::latest()->take(5)->get(['id', 'title', 'category', 'event_date', 'created_at']);
            $recentTickets = Ticket::with('event:id,title', 'user:id,name')
                ->latest()->take(5)->get();

            // Monthly stats for charts
            $monthlyUsers = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $monthlyRevenue = TicketPurchase::selectRaw('MONTH(created_at) as month, COALESCE(SUM(amount_paid), 0) as revenue')
                ->where('payment_status', 'completed')
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'stats' => [
                        'totalUsers' => $totalUsers,
                        'totalAdmins' => $totalAdmins,
                        'totalEvents' => $totalEvents,
                        'activeEvents' => $activeEvents,
                        'totalTickets' => $totalTickets,
                        'totalRevenue' => $totalRevenue,
                    ],
                    'recent' => [
                        'users' => $recentUsers,
                        'events' => $recentEvents,
                        'tickets' => $recentTickets,
                    ],
                    'charts' => [
                        'monthlyUsers' => $monthlyUsers,
                        'monthlyRevenue' => $monthlyRevenue,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users with pagination
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $role = $request->get('role');

            $query = User::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($role) {
                $query->where('role', $role);
            }

            $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|in:user,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['name', 'email', 'role']);
            
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        try {
            // Don't allow deleting the last admin
            if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete the last admin user'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all events for admin management
     */
    public function getEvents(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status');

            $query = Event::with('creator:id,name');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            }

            if ($status === 'active') {
                $query->where('event_date', '>=', Carbon::now());
            } elseif ($status === 'past') {
                $query->where('event_date', '<', Carbon::now());
            }

            $events = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch events: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete event (admin only)
     */
    public function deleteEvent($id): JsonResponse
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        try {
            // Check if event has any paid tickets (completed purchases)
            $paidTicketCount = Ticket::where('event_id', $id)
                ->whereHas('purchase', function($query) {
                    $query->where('payment_status', 'completed');
                })
                ->count();
            
            \Log::info("Attempting to delete event {$id}. Paid ticket count: {$paidTicketCount}");
            
            if ($paidTicketCount > 0) {
                \Log::warning("Cannot delete event {$id} - has {$paidTicketCount} paid tickets");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete event with existing paid tickets. Refunds must be processed first.'
                ], 400);
            }

            // Clean up any unpaid tickets for this event
            $unpaidTickets = Ticket::where('event_id', $id)
                ->whereDoesntHave('purchase', function($query) {
                    $query->where('payment_status', 'completed');
                })
                ->get();
            
            foreach ($unpaidTickets as $ticket) {
                $ticket->delete();
            }
            
            \Log::info("Deleted " . $unpaidTickets->count() . " unpaid tickets for event {$id}");

            // Delete event image if exists
            if ($event->image_url) {
                $imagePath = str_replace('/storage/', '', $event->image_url);
                \Storage::disk('public')->delete($imagePath);
                \Log::info("Deleted image for event {$id}: {$imagePath}");
            }

            $event->delete();
            \Log::info("Successfully deleted event {$id}");

            return response()->json([
                'status' => 'success',
                'message' => 'Event deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all tickets for admin management
     */
    public function getTickets(): JsonResponse
    {
        try {
            $tickets = Ticket::with([
                'event:id,title,category,event_date,price',
                'user:id,name,email',
                'purchase:id,amount_paid,payment_method,payment_status,created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            return response()->json([
                'status' => 'success',
                'data' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive system analytics
     */
    public function getAnalytics(): JsonResponse
    {
        try {
            // User registration trends (last 12 months)
            $userTrends = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Revenue trends (last 12 months)
            $revenueTrends = TicketPurchase::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COALESCE(SUM(amount_paid), 0) as revenue')
                ->where('payment_status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Ticket sales trends (last 12 months)
            $ticketTrends = Ticket::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Event categories distribution
            $categoryStats = Event::selectRaw('category, COUNT(*) as events_count, COALESCE(SUM(current_attendees), 0) as total_attendees')
                ->groupBy('category')
                ->get();

            // Top events by ticket sales
            $topEvents = Event::withCount('tickets')
                ->with('creator:id,name')
                ->orderBy('tickets_count', 'desc')
                ->take(10)
                ->get(['id', 'title', 'category', 'event_date', 'price', 'created_by']);

            // Payment method distribution
            $paymentMethods = TicketPurchase::selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(amount_paid), 0) as total_revenue')
                ->where('payment_status', 'completed')
                ->groupBy('payment_method')
                ->get();

            // Event status distribution
            $eventStatusStats = Event::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            // Ticket status distribution
            $ticketStatusStats = Ticket::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            // Daily stats for the last 30 days
            $dailyStats = TicketPurchase::selectRaw('DATE(created_at) as date, COUNT(*) as tickets_sold, COALESCE(SUM(amount_paid), 0) as daily_revenue')
                ->where('payment_status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top performing event creators
            $topCreators = User::withCount('createdEvents as events_count')
                ->having('events_count', '>', 0)
                ->orderBy('events_count', 'desc')
                ->take(10)
                ->get(['id', 'name', 'email']);

            // Revenue by event category
            $categoryRevenue = Event::selectRaw('category, COALESCE(SUM(tp.amount_paid), 0) as revenue, COUNT(tp.id) as tickets_sold')
                ->leftJoin('ticket_purchases as tp', function($join) {
                    $join->on('events.id', '=', 'tp.event_id')
                         ->where('tp.payment_status', '=', 'completed');
                })
                ->groupBy('category')
                ->get();

            // Recent activity summary
            $recentActivity = [
                'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
                'new_events_today' => Event::whereDate('created_at', Carbon::today())->count(),
                'tickets_sold_today' => Ticket::whereDate('created_at', Carbon::today())->count(),
                'revenue_today' => TicketPurchase::where('payment_status', 'completed')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('amount_paid') ?: 0,
                'active_events' => Event::where('status', 'active')
                    ->where('event_date', '>', Carbon::now())
                    ->count(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'trends' => [
                        'users' => $userTrends,
                        'revenue' => $revenueTrends,
                        'tickets' => $ticketTrends,
                        'daily' => $dailyStats,
                    ],
                    'distributions' => [
                        'categories' => $categoryStats,
                        'paymentMethods' => $paymentMethods,
                        'eventStatus' => $eventStatusStats,
                        'ticketStatus' => $ticketStatusStats,
                        'categoryRevenue' => $categoryRevenue,
                    ],
                    'topPerformers' => [
                        'events' => $topEvents,
                        'creators' => $topCreators,
                    ],
                    'summary' => $recentActivity,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics: ' . $e->getMessage()
            ], 500);
        }
    }
}