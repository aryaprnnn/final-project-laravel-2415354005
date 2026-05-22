<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['subscription.customer', 'subscription.service']);

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->query('payment_status'));
        }

        $invoices = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscription_id' => ['required', 'integer', 'exists:subscriptions,id'],
        ]);

        $subscription = \App\Models\Subscription::with('service')->find($validated['subscription_id']);

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad((string)$subscription->id, 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'subscription_id' => $subscription->id,
            'invoice_number'  => $invoiceNumber,
            'amount'          => $subscription->service->price,
            'payment_status'  => 'unpaid',
        ]);

        $invoice->load(['subscription.customer', 'subscription.service']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully',
            'data' => $invoice,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $invoice = Invoice::with(['subscription.customer', 'subscription.service'])->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice detail retrieved successfully',
            'data' => $invoice,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        $validated = $request->validate([
            'payment_status' => ['required', 'string', 'in:unpaid,paid,expired'],
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['paid_at'] = now();
        } else {
            $validated['paid_at'] = null;
        }

        $invoice->update($validated);
        $invoice->load(['subscription.customer', 'subscription.service']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice payment status updated successfully',
            'data' => $invoice,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully',
        ]);
    }
}