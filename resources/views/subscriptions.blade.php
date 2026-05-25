@extends('layouts.app')

@section('content')
    <div x-data="subscriptionsPage()">
        <div class="page-header">
            <div>
                <h2 class="page-title">Subscriptions</h2>
                <p class="page-subtitle">Manage customer active services and billing cycles.</p>
            </div>
            <button @click="openAdd()" class="btn-primary">
                <i data-lucide="plus" class="mr-2 w-4 h-4"></i> New Subscription
            </button>
        </div>

        <div class="card-table">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse($subscriptions as $sub)
                            <tr>
                                <td>
                                    <div class="text-sm font-semibold text-[var(--text-main)]">{{ $sub->customer->name }}</div>
                                    <div class="text-[10px] text-[var(--text-muted)]">{{ $sub->customer->customer_id }}</div>
                                </td>
                                <td>
                                    <div class="text-sm font-bold text-[var(--primary)]">{{ $sub->service->name }}</div>
                                </td>
                                <td class="text-xs">
                                    <div class="font-medium text-[var(--text-main)]">{{ $sub->start_date->format('d M Y') }}</div>
                                    <div class="text-[var(--text-muted)]">- {{ $sub->end_date->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <span class="{{ $sub->status === 'active' ? 'badge-success' : 'badge-orange' }}">
                                        {{ strtoupper($sub->status) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="openEdit({{ json_encode($sub) }})" class="btn-icon-primary">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="deleteData({{ $sub->id }})" class="btn-icon-danger">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-10 text-[var(--text-muted)]">No active subscriptions.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <div x-show="showModal" 
             class="fixed inset-0 z-[150] flex items-center justify-center p-4 md:p-6" 
             style="display: none;">
            
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false" 
                 class="modal-backdrop"></div>

            <div x-show="showModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                 class="modal-content w-full max-w-lg relative z-[160] max-h-[90vh] flex flex-col">
                
                <div class="modal-header shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-[var(--text-main)]" x-text="isEdit ? 'Edit Subscription' : 'New Subscription'"></h3>
                        <p class="text-[10px] text-[var(--text-muted)] font-medium">Link a customer to a service plan.</p>
                    </div>
                    <button @click="showModal = false" class="p-2 text-[var(--text-muted)] hover:text-rose-500 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="flex flex-col overflow-hidden">
                    <div class="p-8 space-y-6 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Customer</label>
                                <select x-model="formData.customer_id" required class="form-select">
                                    <option value="">Select Customer</option>
                                    <template x-for="cust in customers" :key="cust.id">
                                        <option :value="cust.id" x-text="cust.name" :selected="cust.id == formData.customer_id"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Service Plan</label>
                                <select x-model="formData.service_id" required class="form-select">
                                    <option value="">Select Service</option>
                                    <template x-for="serv in services" :key="serv.id">
                                        <option :value="serv.id" x-text="serv.name" :selected="serv.id == formData.service_id"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Activation Date</label>
                                <input type="date" x-model="formData.start_date" required class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Expiry Date</label>
                                <input type="date" x-model="formData.end_date" required class="form-input">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Status</label>
                            <select x-model="formData.status" required class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="trial">Trial</option>
                                <option value="isolir">Isolir</option>
                                <option value="dismantle">Dismantle</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="showModal = false" class="btn-secondary-sm">Cancel</button>
                        <button type="submit" :disabled="loading" class="btn-primary-sm">
                            <span x-show="!loading" x-text="isEdit ? 'Update Plan' : 'Activate Plan'"></span>
                            <span x-show="loading">Activating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
