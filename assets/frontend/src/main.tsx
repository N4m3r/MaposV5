import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import './styles/app.css';

// Em produção, a view PHP injeta APP_CONFIG na window antes do bundle
declare global {
    interface Window {
        APP_CONFIG?: {
            baseUrl: string;
            userName: string;
            userEmail: string;
            userAvatar?: string;
            permissions: string[];
            theme: string;
        };
    }
}

const root = document.getElementById('root');
if (!root) {
    throw new Error('Elemento #root nao encontrado');
}

ReactDOM.createRoot(root).render(
    <React.StrictMode>
        <BrowserRouter>
            <App />
        </BrowserRouter>
    </React.StrictMode>,
);
