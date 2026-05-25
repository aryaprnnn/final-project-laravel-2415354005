<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $query = Subscription::with(['customer', 'service']);

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->query('customer_id'));
        }

        if ($request->has('service_id')) {
            $query->where('service_id', $request->query('service_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->wantsJson()) {
            $subscriptions = $query->latest()->get();
            return response()->json([
                'success' => true,
                'message' => 'Subscriptions retrieved successfully',
                'data' => $subscriptions,
            ]);
        }

        $subscriptions = $query->latest()->paginate(20);
        return view('subscriptions', compact('subscriptions'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:active,inactive,trial,isolir,dismantle'],
        ]);

        $subscription = Subscription::create($validated);

        // Generate Invoice Automatically when subscription is created
        $service = \App\Models\Service::find($validated['service_id']);
        Invoice::create([
            'customer_id' => $validated['customer_id'],
            'subscription_id' => $subscription->id,
            'amount' => $service->price,
            'due_date' => now()->addDays(7),
            'payment_status' => 'unpaid',
            'invoice_number' => 'INV-' . strtoupper(bin2hex(random_bytes(4)))
        ]);

        $subscription->load(['customer', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $subscription = Subscription::with(['customer', 'service'])->find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription detail retrieved successfully',
            'data' => $subscription,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        $validated = $request->validate([
            'customer_id' => ['sometimes', 'required', 'integer', 'exists:customers,id'],
            'service_id' => ['sometimes', 'required', 'integer', 'exists:services,id'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive,trial,isolir,dismantle'],
        ]);

        $subscription->update($validated);

        $subscription->load(['customer', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'data' => $subscription,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        // Cek jika status masih aktif
        if ($subscription->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Data langganan aktif tidak dapat dihapus. Silakan ubah status menjadi non-aktif atau dismantle terlebih dahulu.',
            ], 422);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully',
        ]);
    }
}