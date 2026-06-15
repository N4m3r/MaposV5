/**
 * Sininho de notificacoes.
 * Polling a cada 60s no endpoint existente /notificacoes/listar.
 * Reaproveita o backend PHP que ja existe (application/controllers/Notificacoes.php).
 */
import { useState, useEffect } from 'react';
import { CDropdown, CDropdownToggle, CDropdownMenu, CDropdownItem, CBadge } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { api } from '../../api/client';

interface Notificacao {
    id: number;
    titulo: string;
    mensagem: string;
    data_notificacao: string;
    lida: number;
    icone: string;
    url?: string;
}

export function NotificationBell() {
    const [notifs, setNotifs] = useState<Notificacao[]>([]);
    const [naoLidas, setNaoLidas] = useState(0);
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const load = async () => {
            try {
                const { data } = await api.get<{ success: boolean; nao_lidas: number; notificacoes: Notificacao[] }>(
                    'notificacoes/listar',
                );
                if (data.success) {
                    setNotifs(data.notificacoes || []);
                    setNaoLidas(data.nao_lidas || 0);
                }
            } catch {
                // Silencia: tabela pode nao existir, sessao expirada, etc.
            }
        };
        load();
        const id = setInterval(load, 60000);
        return () => clearInterval(id);
    }, []);

    async function markRead(id?: number) {
        try {
            await api.post('notificacoes/marcar_lida', id ? { id } : {});
            setNaoLidas((n) => (id ? Math.max(0, n - 1) : 0));
            if (!id) setNotifs((arr) => arr.map((n) => ({ ...n, lida: 1 })));
        } catch { /* noop */ }
    }

    return (
        <CDropdown alignment="end" visible={open}>
            <CDropdownToggle caret={false} color="transparent" className="app-icon-btn position-relative" onClick={() => setOpen(!open)}>
                <CIcon icon="cilBell" size="lg" />
                {naoLidas > 0 && (
                    <CBadge color="danger" position="top-end" shape="rounded-pill" className="notif-badge-count">
                        {naoLidas > 99 ? '99+' : naoLidas}
                    </CBadge>
                )}
            </CDropdownToggle>
            <CDropdownMenu className="notif-dropdown">
                <div className="notif-header">
                    <strong>Notificacoes</strong>
                    {naoLidas > 0 && (
                        <button
                            className="btn btn-link btn-sm p-0"
                            onClick={(e) => {
                                e.preventDefault();
                                markRead();
                            }}
                        >
                            Marcar todas
                        </button>
                    )}
                </div>
                <div className="notif-list">
                    {notifs.length === 0 && (
                        <div className="text-center text-muted p-3">Nenhuma notificacao</div>
                    )}
                    {notifs.slice(0, 10).map((n) => (
                        <CDropdownItem
                            key={n.id}
                            className={n.lida ? '' : 'notif-unread'}
                            onClick={() => {
                                markRead(n.id);
                                if (n.url) window.location.href = n.url;
                            }}
                        >
                            <div className="notif-titulo">
                                <CIcon icon={n.icone || 'cilBell'} className="me-2" />
                                {n.titulo}
                            </div>
                            <div className="notif-mensagem small text-muted">{n.mensagem}</div>
                        </CDropdownItem>
                    ))}
                </div>
            </CDropdownMenu>
        </CDropdown>
    );
}
