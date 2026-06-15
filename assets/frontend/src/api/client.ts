/**
 * Wrapper Axios com baseURL do CodeIgniter e CSRF automatico.
 * O CodeIgniter 3 valida csrf_token_name em todo POST/AJAX.
 */
import axios, { type AxiosInstance, type InternalAxiosRequestConfig } from 'axios';
import { getConfig } from '../config';

function getCsrfToken(): { name: string; value: string } {
    if (typeof document === 'undefined') return { name: '', value: '' };

    // Tenta ler do cookie (csrf_cookie_name)
    const cookieName = getConfig().baseUrl ? 'csrf_cookie_name' : 'csrf_cookie_name';
    const match = document.cookie.match(new RegExp('(^| )' + cookieName + '=([^;]+)'));
    const token = match ? match[2] : '';

    return {
        name: 'csrf_token_name', // csrf_token_name configurado no CI
        value: token,
    };
}

function createApi(): AxiosInstance {
    const config = getConfig();
    const api = axios.create({
        baseURL: config.baseUrl,
        timeout: 30000,
        withCredentials: true,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    // Interceptor: injeta CSRF em todas as requisicoes
    api.interceptors.request.use((req: InternalAxiosRequestConfig) => {
        const { name, value } = getCsrfToken();
        if (name && value && ['post', 'put', 'patch', 'delete'].includes(req.method || '')) {
            if (req.headers) {
                (req.headers as Record<string, string>)[name] = value;
            }
        }
        return req;
    });

    // Interceptor: tratamento de erro global
    api.interceptors.response.use(
        (r) => r,
        (err) => {
            if (err.response?.status === 401) {
                window.location.href = '/index.php/login';
            }
            if (err.response?.status === 403) {
                console.warn('[API] Acesso negado:', err.config?.url);
            }
            return Promise.reject(err);
        },
    );

    return api;
}

export const api = createApi();
