@extends('layouts.app')

@section('content')
    <div x-data="servicesPage()">
        <div class="page-header">
            <div>
                <h2 class="page-title">Services</h2>
                <p class="page-subtitle">Manage service catalog and plans.</p>
            </div>
            <button @click="openAdd()" class="btn-primary">
                <i data-lucide="plus" class="mr-2 w-4 h-4"></i> Add Service
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
                <div class="card p-5 flex flex-col {{ !$service->status ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-sm font-bold text-[var(--text-main)] flex-1 pr-4">{{ $service->name }}</h3>
                        <span class="{{ $service->status ? 'badge-success' : 'badge-danger' }} whitespace-nowrap">
                            {{ $service->status ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-[var(--text-muted)] leading-relaxed mb-6 flex-1">{{ $service->description ?? 'No description available for this service.' }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-[var(--border)]">
                        <div class="text-lg font-bold text-[var(--text-main)]">Rp {{ number_format($service->price, 0, ',', '.') }}<span class="text-[10px] text-[var(--text-muted)] ml-1">/mo</span></div>
                        <div class="flex space-x-1.5">
                            <button @click="openEdit({{ json_encode($service) }})" class="btn-icon-primary p-2">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button @click="deleteData({{ $service->id }})" class="btn-icon-danger p-2 text-rose-500">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center text-[var(--text-muted)]">No services found.</div>
            @endforelse
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
                        <h3 class="text-lg font-bold text-[var(--text-main)]" x-text="isEdit ? 'Edit Service' : 'Add New Service'"></h3>
                        <p class="text-[10px] text-[var(--text-muted)] font-medium">Define your service package details.</p>
                    </div>
                    <button @click="showModal = false" class="p-2 text-[var(--text-muted)] hover:text-rose-500 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="flex flex-col overflow-hidden">
                    <div class="p-8 space-y-6 overflow-y-auto">
                        <div>
                            <label class="form-label">Service Name</label>
                            <input type="text" x-model="formData.name" required class="form-input" placeholder="e.g. Fiber Home 50Mbps">
                        </div>
                        <div>
                            <label class="form-label">Monthly Price (IDR)</label>
                            <input type="number" x-model="formData.price" required class="form-input" placeholder="350000">
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <textarea x-model="formData.description" rows="3" class="form-input resize-none" placeholder="Describe the service features..."></textarea>
                        </div>
                        <div class="flex items-center space-x-3 pt-2">
                            <label class="form-switch">
                                <input type="checkbox" x-model="formData.status" class="sr-only peer">
                                <div class="form-switch-track">
                                    <div class="form-switch-thumb"></div>
                                </div>
                                <span class="ml-3 text-sm font-bold text-[var(--text-main)]">Active in Catalog</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="showModal = false" class="btn-secondary-sm">Cancel</button>
                        <button type="submit" :disabled="loading" class="btn-primary-sm">
                            <span x-show="!loading" x-text="isEdit ? 'Update Service' : 'Save Service'"></span>
                            <span x-show="loading">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
