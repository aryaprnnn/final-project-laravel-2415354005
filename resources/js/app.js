import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

// Import layout logic
import appLayout, { notificationComponent, confirmComponent } from './layouts/app';

// Import page logic
import customersPage from './pages/customers';
import servicesPage from './pages/services';
import subscriptionsPage from './pages/subscriptions';
import invoicesPage from './pages/invoices';

window.Alpine = Alpine;

// Helper for Lucide icons
window.lucide = {
    createIcons: () => createIcons({
        icons,
        attrs: {
            class: ['lucide-icon']
        }
    })
};

// Register Alpine Data
Alpine.data('appLayout', appLayout);
Alpine.data('notificationComponent', notificationComponent);
Alpine.data('confirmComponent', confirmComponent);
Alpine.data('customersPage', customersPage);
Alpine.data('servicesPage', servicesPage);
Alpine.data('subscriptionsPage', subscriptionsPage);
Alpine.data('invoicesPage', invoicesPage);

Alpine.start();

// Initialize icons on load
document.addEventListener('DOMContentLoaded', () => {
    window.lucide.createIcons();
});

// Re-initialize icons after Alpine re-renders (if necessary)
document.addEventListener('alpine:initialized', () => {
    window.lucide.createIcons();
});


