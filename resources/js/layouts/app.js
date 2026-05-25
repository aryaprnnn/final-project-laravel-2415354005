export default () => ({
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches), 
    sidebarOpen: true,

    init() {
        this.$watch('darkMode', val => {
            localStorage.setItem('theme', val ? 'dark' : 'light');
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });

        // Global Lucide initialization
        if (window.lucide) window.lucide.createIcons();
        
        document.addEventListener('alpine:initialized', () => {
            if (window.lucide) window.lucide.createIcons();
        });
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
    }
});

// Toast notification component logic
export const notificationComponent = () => ({
    show: false, 
    message: '', 
    type: 'success',
    init() {
        window.addEventListener('notify', (e) => {
            this.message = e.detail.message;
            this.type = e.detail.type || 'success';
            this.show = true;
            setTimeout(() => { this.show = false }, 4000);
        });
    }
});

// Confirmation modal component logic
export const confirmComponent = () => ({
    show: false, 
    title: '', 
    message: '', 
    confirmLabel: 'Konfirmasi',
    cancelLabel: 'Batal',
    onConfirm: null,
    loading: false,

    init() {
        window.confirmAction = (options) => {
            this.title = options.title || 'Konfirmasi Tindakan';
            this.message = options.message || 'Apakah Anda yakin ingin melanjutkan?';
            this.confirmLabel = options.confirmLabel || 'Ya, Lanjutkan';
            this.onConfirm = options.onConfirm;
            this.show = true;
        };
    },

    async proceed() {
        if(this.onConfirm) {
            this.loading = true;
            try {
                await this.onConfirm();
            } catch (e) {
                console.error('Action failed:', e);
            }
            this.loading = false;
        }
        this.show = false;
    }
});
