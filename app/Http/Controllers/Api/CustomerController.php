<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $status = $request->query('status');
        $showDeleted = $request->query('deleted', 'hide');
        
        $query = Customer::query();

        if ($showDeleted === 'only') {
            $query->onlyTrashed();
        } elseif ($showDeleted === 'show') {
            $query->withTrashed();
        }

        if ($status !== null) {
            $query->where('status', $status === 'active');
        }

        if ($request->wantsJson()) {
            $customers = $query->latest()->get();
            return response()->json([
                'success' => true,
                'message' => 'Customers retrieved successfully',
                'data' => $customers,
            ]);
        }

        $customers = $query->latest()->paginate(20);
        return view('customers', compact('customers'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'string', 'unique:customers,customer_id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $customer = Customer::with('subscriptions.service')->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer detail retrieved successfully',
            'data' => $customer,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $validated = $request->validate([
            'customer_id' => ['sometimes', 'required', 'string', 'unique:customers,customer_id,' . $id],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'unique:customers,email,' . $id],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', 'boolean'],
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        // Soft delete will handle this safely without breaking relationships
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer has been archived (Soft Delete)',
        ]);
    }

    public function restore(string $id): JsonResponse
    {
        $customer = Customer::withTrashed()->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->restore();

        return response()->json([
            'success' => true,
            'message' => 'Customer restored successfully',
        ]);
    }

    public function activate(string $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->update(['status' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Customer activated successfully',
            'data' => $customer,
        ]);
    }

    public function deactivate(string $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->update(['status' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Customer deactivated successfully',
            'data' => $customer,
        ]);
    }
}
