/**
 * Tela de login React (standalone).
 *
 * Carregada em /login (rota publica) — nao usa AppShell, renderiza
 * layout proprio full-screen com glassmorphism e gradient background.
 *
 * Endpoint:
 *   POST /index.php/mine/login?ajax=true
 *   body: { email, senha, csrf_token }
 *   res:  { result: bool, message?: string, MAPOS_TOKEN?: string }
 *
 * Em sucesso, redireciona para /index.php/mine/painel (entra no portal
 * tradicional de cliente PHP).
 */
import { useState, FormEvent } from 'react';
import {
    CButton,
    CCard,
    CCardBody,
    CForm,
    CFormInput,
    CFormLabel,
    CSpinner,
} from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { toast } from '../components/ui/Toast';

declare global {
    interface Window {
        MAPOS_CONFIG?: {
            csrfName: string;
            csrfHash: string;
            baseUrl: string;
        };
    }
}

function getMeta(name: string): string {
    const el = document.querySelector(`meta[name="${name}"]`);
    return el ? el.getAttribute('content') || '' : '';
}

export default function Login() {
    const config = window.MAPOS_CONFIG ?? {
        csrfName: getMeta('csrf-token-name'),
        csrfHash: getMeta('csrf-hash'),
        baseUrl: document.querySelector<HTMLBaseElement>('base')?.href ?? '/',
    };
    const csrfName = config.csrfName || 'csrf_test_name';
    const baseUrl = config.baseUrl.replace(/\/+$/, '');

    const [email, setEmail] = useState('');
    const [senha, setSenha] = useState('');
    const [loading, setLoading] = useState(false);
    const [showPwd, setShowPwd] = useState(false);

    async function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!email || !senha) {
            toast.warning('Preencha email e senha.');
            return;
        }

        setLoading(true);
        try {
            const form = new FormData();
            form.append('email', email);
            form.append('senha', senha);
            form.append(csrfName, config.csrfHash);

            const res = await fetch(`${baseUrl}/index.php/mine/login?ajax=true`, {
                method: 'POST',
                body: form,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            if (data.result === true) {
                toast.success('Login realizado com sucesso');
                window.location.href = `${baseUrl}/index.php/mine/painel`;
                return;
            }

            toast.error(data.message || 'Dados de acesso incorretos.');
            // Atualiza CSRF token devolvido pelo servidor
            if (data.MAPOS_TOKEN) {
                config.csrfHash = data.MAPOS_TOKEN;
            }
        } catch (err) {
            toast.error(err instanceof Error ? err.message : 'Falha na requisição');
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="login-react-page">
            <div className="login-react-bg">
                <div className="login-react-blob login-react-blob-1" />
                <div className="login-react-blob login-react-blob-2" />
                <div className="login-react-blob login-react-blob-3" />
            </div>

            <div className="login-react-grid">
                <div className="login-react-hero d-none d-lg-flex">
                    <div className="login-react-hero-inner">
                        <h1 className="login-react-title">Área do Cliente</h1>
                        <p className="login-react-subtitle">
                            Acompanhe suas Ordens de Serviço, Vendas, Financeiro,
                            Cobranças e Documentos Fiscais em um só lugar.
                        </p>
                        <ul className="login-react-features">
                            <li><CIcon icon="cilCheckCircle" /> Ordens de serviço em tempo real</li>
                            <li><CIcon icon="cilCheckCircle" /> Download de NFS-e e Boletos</li>
                            <li><CIcon icon="cilCheckCircle" /> Histórico financeiro completo</li>
                            <li><CIcon icon="cilCheckCircle" /> Suporte via WhatsApp integrado</li>
                        </ul>
                    </div>
                </div>

                <CCard className="login-react-card">
                    <CCardBody className="login-react-card-body">
                        <div className="login-react-brand">
                            <div className="login-react-logo">
                                <CIcon icon="cilSettings" size="3xl" />
                            </div>
                            <h2 className="login-react-brand-title">Mapos OS</h2>
                            <p className="login-react-brand-sub">Acesso do Cliente</p>
                        </div>

                        <CForm onSubmit={handleSubmit} className="login-react-form" noValidate>
                            <div className="mb-3">
                                <CFormLabel htmlFor="login-email">Email</CFormLabel>
                                <div className="login-react-input">
                                    <CIcon icon="cilUser" className="login-react-input-icon" />
                                    <CFormInput
                                        id="login-email"
                                        type="email"
                                        placeholder="seu@email.com"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        autoComplete="email"
                                        required
                                    />
                                </div>
                            </div>

                            <div className="mb-4">
                                <CFormLabel htmlFor="login-senha">Senha</CFormLabel>
                                <div className="login-react-input">
                                    <CIcon icon="cilLockLocked" className="login-react-input-icon" />
                                    <CFormInput
                                        id="login-senha"
                                        type={showPwd ? 'text' : 'password'}
                                        placeholder="••••••••"
                                        value={senha}
                                        onChange={(e) => setSenha(e.target.value)}
                                        autoComplete="current-password"
                                        required
                                    />
                                    <button
                                        type="button"
                                        className="login-react-pwd-toggle"
                                        onClick={() => setShowPwd((v) => !v)}
                                        aria-label={showPwd ? 'Ocultar senha' : 'Mostrar senha'}
                                    >
                                        <CIcon icon={showPwd ? 'cilEyeSlash' : 'cilEye'} />
                                    </button>
                                </div>
                            </div>

                            <CButton
                                type="submit"
                                color="primary"
                                className="login-react-submit"
                                disabled={loading}
                            >
                                {loading ? (
                                    <>
                                        <CSpinner size="sm" className="me-2" />
                                        Entrando...
                                    </>
                                ) : (
                                    <>
                                        <CIcon icon="cilAccountLogout" className="me-2" />
                                        Acessar
                                    </>
                                )}
                            </CButton>

                            <div className="login-react-links">
                                <a href={`${baseUrl}/index.php/mine/cadastrar`} className="login-react-link">
                                    <CIcon icon="cilUserFollow" /> Cadastre-se
                                </a>
                                <a href={`${baseUrl}/index.php/mine/resetarSenha`} className="login-react-link">
                                    <CIcon icon="cilLockUnlocked" /> Esqueceu a senha?
                                </a>
                            </div>
                        </CForm>

                        <p className="login-react-footer">
                            Mapos OS &copy; {new Date().getFullYear()}
                        </p>
                    </CCardBody>
                </CCard>
            </div>
        </div>
    );
}
