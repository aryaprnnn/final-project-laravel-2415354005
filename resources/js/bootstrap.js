import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Global Axios Response Interceptor for Error Handling
 */
window.axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        const errorResponse = error.response;
        
        // Handle common error cases
        if (errorResponse) {
            console.error('Frontend API Error:', {
                status: errorResponse.status,
                data: errorResponse.data
            });

            // You could show a toast or notification here
            // Example message based on the status code:
            if (errorResponse.status === 401) {
                console.warn('Redirecting to login or showing session expired message.');
            }
        } else {
            console.error('Frontend Network Error:', error.message);
        }

        return Promise.reject(error);
    }
);

