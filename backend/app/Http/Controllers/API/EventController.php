<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * Event Controller
 * Handles CRUD operations for events, search functionality, and event management
 */
class EventController extends Controller
{
    /**
     * Display a listing of events with optional search and filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['creator', 'tickets'])
            ->where('status', 'active');

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('event_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('event_date', '<=', $request->get('date_to'));
        }

        // Sort by date (default) or price
        $sortBy = $request->get('sort_by', 'event_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $events = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'registration_deadline' => 'required|date|after:now|before:event_date',
            'price' => 'required|numeric|min:0',
            'max_attendees' => 'required|integer|min:1',
            'refundable' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $eventData = $request->except('image');
        $eventData['created_by'] = $request->user()->id;
        
        // Ensure refundable has a default value if not provided
        if (!isset($eventData['refundable'])) {
            $eventData['refundable'] = false;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $eventData['image_url'] = Storage::url($imagePath);
        }

        $event = Event::create($eventData);

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully',
            'data' => $event->load(['creator', 'tickets'])
        ], 201);
    }

    /**
     * Display the specified event
     */
    public function show(string $id): JsonResponse
    {
        $event = Event::with(['creator', 'tickets.user', 'ticketPurchases'])
            ->find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $event
        ]);
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        // Check if user owns the event
        if ($event->created_by !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to update this event'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'category' => 'string|max:100',
            'location' => 'string|max:255',
            'event_date' => 'date|after:now',
            'registration_deadline' => 'date|after:now|before:event_date',
            'price' => 'numeric|min:0',
            'max_attendees' => 'integer|min:1',
            'status' => 'in:active,cancelled,completed',
            'refundable' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image_url) {
                $oldImagePath = str_replace('/storage/', '', $event->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }

            $imagePath = $request->file('image')->store('events', 'public');
            $updateData['image_url'] = Storage::url($imagePath);
        }

        $event->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Event updated successfully',
            'data' => $event->load(['creator', 'tickets'])
        ]);
    }

    /**
     * Remove the specified event
     */
    public function destroy(string $id): JsonResponse
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }

        // Check if user owns the event or is an admin
        $user = request()->user();
        if ($event->created_by !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this event'
            ], 403);
        }

        // Check if event has tickets sold
        if ($event->tickets()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete event with existing ticket sales'
            ], 400);
        }

        // Delete event image
        if ($event->image_url) {
            $imagePath = str_replace('/storage/', '', $event->image_url);
            Storage::disk('public')->delete($imagePath);
        }

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Get event categories
     */
    public function categories(): JsonResponse
    {
        $categories = Event::select('category')
            ->distinct()
            ->pluck('category');

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get events created by the authenticated user
     */
    public function myEvents(Request $request): JsonResponse
    {
        $events = Event::with(['tickets', 'ticketPurchases'])
            ->where('created_by', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }
}
