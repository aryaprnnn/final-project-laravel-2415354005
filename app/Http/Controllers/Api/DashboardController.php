<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_services' => Service::where('status', true)->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'unpaid_invoices' => Invoice::where('payment_status', 'unpaid')->count(),
        ];

        $recentInvoices = Invoice::with([
            'subscription.customer' => function ($query) {
                $query->withTrashed();
            },
            'subscription.service'
        ])->latest()->take(10)->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_invoices' => $recentInvoices
                ]
            ]);
        }

        return view('dashboard', compact('stats', 'recentInvoices'));
    }
}
