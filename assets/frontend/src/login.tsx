/**
 * Entry-point standalone da página de login.
 *
 * Carregado por /index.php/mine/loginReact (login_react.php).
 * Bundle separado do app principal (nao inclui AppShell, dashboard, etc).
 */
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import Login from './pages/Login';
import { ToastContainer } from './components/ui/Toast';

const root = document.getElementById('root');
if (!root) {
    throw new Error('Elemento #root nao encontrado');
}

ReactDOM.createRoot(root).render(
    <React.StrictMode>
        <BrowserRouter>
            <Login />
            <ToastContainer />
        </BrowserRouter>
    </React.StrictMode>,
);
