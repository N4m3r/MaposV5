/**
 * Toast global (singleton). Uma unica instancia para a app toda.
 * Encapsula useToast em um modulo, nao precisa passar via contexto.
 */
import { useEffect, useState, useCallback } from 'react';
import { CToast, CToastBody, CToastHeader, CToaster } from '@coreui/react';
import CIcon from '@coreui/icons-react';

export type ToastColor = 'success' | 'danger' | 'warning' | 'info';

interface Toast {
    id: number;
    message: string;
    color: ToastColor;
    title?: string;
}

let nextId = 1;
type Listener = (toasts: Toast[]) => void;
let toasts: Toast[] = [];
const listeners = new Set<Listener>();

function emit() {
    listeners.forEach((l) => l([...toasts]));
}

export const toast = {
    show(message: string, color: ToastColor = 'info', title?: string) {
        const t: Toast = { id: nextId++, message, color, title };
        toasts = [...toasts, t];
        emit();
        // Auto-dismiss depois de 4s (erros duram mais)
        const ms = color === 'danger' ? 6000 : 4000;
        setTimeout(() => toast.dismiss(t.id), ms);
        return t.id;
    },
    success(msg: string) { return this.show(msg, 'success'); },
    error(msg: string)   { return this.show(msg, 'danger'); },
    warning(msg: string) { return this.show(msg, 'warning'); },
    info(msg: string)    { return this.show(msg, 'info'); },
    dismiss(id: number) {
        toasts = toasts.filter((t) => t.id !== id);
        emit();
    },
};

/**
 * Componente que renderiza o singleton. Colocar 1x no App.tsx.
 */
export function ToastContainer() {
    const [items, setItems] = useState<Toast[]>([]);
    useEffect(() => {
        const l: Listener = (t) => setItems(t);
        listeners.add(l);
        setItems([...toasts]);
        return () => { listeners.delete(l); };
    }, []);

    return (
        <CToaster placement="top-end" className="position-fixed" style={{ zIndex: 1100, top: '80px', right: '16px' }}>
            {items.map((t) => (
                <CToast key={t.id} color={t.color} onClose={() => toast.dismiss(t.id)} visible>
                    <CToastHeader closeButton>
                        <CIcon
                            icon={t.color === 'success' ? 'cilCheckCircle'
                                : t.color === 'danger' ? 'cilXCircle'
                                : t.color === 'warning' ? 'cilWarning'
                                : 'cilInfo'}
                            className="me-2"
                        />
                        <strong className="me-auto">{t.title || 'Mapos'}</strong>
                    </CToastHeader>
                    <CToastBody>{t.message}</CToastBody>
                </CToast>
            ))}
        </CToaster>
    );
}
