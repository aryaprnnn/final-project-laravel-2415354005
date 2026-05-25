import { getCsrfToken, notify, reloadPage } from '../utils';

export default () => ({
    showModal: false, 
    isEdit: false,
    formData: { id: '', customer_id: '', service_id: '', start_date: '', end_date: '', status: 'active' },
    loading: false,
    customers: [],
    services: [],

    async init() {
        try {
            const [crees, sres] = await Promise.all([
                fetch('/api/customers', { headers: { 'Accept': 'application/json' } }),
                fetch('/api/services', { headers: { 'Accept': 'application/json' } })
            ]);
            const cdata = await crees.json();
            const sdata = await sres.json();
            this.customers = cdata.data;
            this.services = sdata.data;
        } catch (e) { 
            console.error('Failed to load customers or services:', e); 
        }
    },
    
    async submitForm() {
        this.loading = true;
        const url = this.isEdit ? `/api/subscriptions/${this.formData.id}` : '/api/subscriptions';
        const method = this.isEdit ? 'PUT' : 'POST';
        
        try {
            const res = await fetch(url, {
                method: method,
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(this.formData)
            });
            const data = await res.json();
            
            if (res.ok) {
                notify(data.message, 'success');
                reloadPage();
            } else {
                const errorMsg = data.errors ? Object.values(data.errors)[0][0] : data.message;
                notify(errorMsg, 'error');
            }
        } catch (e) {
            notify('Something went wrong', 'error');
        } finally {
            this.loading = false;
        }
    },

    async deleteData(id) {
        window.confirmAction({
            title: 'Hapus Langganan?',
            message: 'Apakah Anda yakin ingin menghapus data langganan ini? Tindakan ini tidak dapat dibatalkan.',
            confirmLabel: 'Ya, Hapus',
            onConfirm: async () => {
                try {
                    const res = await fetch(`/api/subscriptions/${id}`, { 
                        method: 'DELETE', 
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        } 
                    });
                    const data = await res.json();
                    if(res.ok) {
                        notify(data.message, 'success');
                        reloadPage();
                    } else {
                        notify(data.message, 'error');
                    }
                } catch (e) { 
                    notify('Gagal menghapus data', 'error');
                }
            }
        });
    },

    openAdd() {
        this.isEdit = false;
        this.formData = { id: '', customer_id: '', service_id: '', start_date: '', end_date: '', status: 'active' };
        this.showModal = true;
    },

    openEdit(sub) {
        this.isEdit = true;
        this.formData = { 
            id: sub.id, 
            customer_id: sub.customer_id, 
            service_id: sub.service_id, 
            start_date: sub.start_date.split('T')[0], 
            end_date: sub.end_date.split('T')[0], 
            status: sub.status 
        };
        this.showModal = true;
    }
});
