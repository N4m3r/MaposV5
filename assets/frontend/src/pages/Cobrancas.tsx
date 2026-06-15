/**
 * Pagina Cobrancas.
 * Lista cobrancas geradas com filtros por status.
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { DataTable, StatusBadge } from '../components/ui/DataTable';
import type { Row, ColumnDef } from '../types';

function fmtCurrency(v: unknown): string {
    const n = Number(v || 0);
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n);
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    pendente: { label: 'Pendente', color: 'warning' },
    pago:     { label: 'Pago',     color: 'success' },
    atrasado: { label: 'Atrasado', color: 'danger' },
    cancelado:{ label: 'Cancelado',color: 'secondary' },
    enviado:  { label: 'Enviado',  color: 'info' },
};

const columns: ColumnDef<Row>[] = [
    { key: 'id',           label: '#',         width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'cliente_nome', label: 'Cliente',   render: (r) => String(r.cliente_nome || r.nomeCliente || '-') },
    { key: 'descricao',    label: 'Descricao', render: (r) => String(r.descricao || '-') },
    { key: 'data_vencimento', label: 'Vencimento', width: '120px', sortable: true, render: (r) => fmtDate(r.data_vencimento) },
    { key: 'valor',        label: 'Valor',     width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valor) },
    { key: 'status',       label: 'Status',    width: '120px', render: (r) => <StatusBadge value={String(r.status || 'pendente').toLowerCase()} map={STATUS_MAP} /> },
];

const FILTROS = [
    { id: '',          label: 'Todos' },
    { id: 'pendente',  label: 'Pendentes' },
    { id: 'pago',      label: 'Pagos' },
    { id: 'atrasado',  label: 'Atrasados' },
];

export default function CobrancasPage() {
    const [status, setStatus] = useState('');
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 className="mb-0">
                    <CIcon icon="cilCreditCard" className="me-2" />
                    Cobrancas
                </h2>
                <div className="d-flex gap-2 align-items-center flex-wrap">
                    {FILTROS.map((f) => (
                        <button
                            key={f.id}
                            type="button"
                            className={`btn btn-sm ${status === f.id ? 'btn-primary' : 'btn-outline-secondary'}`}
                            onClick={() => setStatus(f.id)}
                        >
                            {f.label}
                        </button>
                    ))}
                    <button type="button" className="btn btn-sm btn-success">
                        <CIcon icon="cilPlus" className="me-1" />Nova Cobranca
                    </button>
                </div>
            </div>
            <DataTable<Row>
                controller="cobrancas"
                title=""
                columns={columns}
                fixedParams={status ? { status } : undefined}
                initialPageSize={50}
            />
        </>
    );
}
