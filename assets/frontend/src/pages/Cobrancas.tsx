/**
 * Pagina Cobrancas com CRUD completo e filtro por status.
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import { StatusBadge } from '../components/ui/DataTable';
import type { FieldDef } from '../components/ui/FormModal';
import type { Row, ColumnDef } from '../types';

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    pendente:  { label: 'Pendente',  color: 'warning' },
    pago:      { label: 'Pago',      color: 'success' },
    atrasado:  { label: 'Atrasado',  color: 'danger' },
    cancelado: { label: 'Cancelado', color: 'secondary' },
    enviado:   { label: 'Enviado',   color: 'info' },
};

const columns: ColumnDef<Row>[] = [
    { key: 'id',              label: '#',         width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'cliente_nome',    label: 'Cliente',   render: (r) => String(r.cliente_nome || r.nomeCliente || '-') },
    { key: 'descricao',       label: 'Descricao', render: (r) => String(r.descricao || '-') },
    { key: 'data_vencimento', label: 'Vencimento', width: '120px', sortable: true, render: (r) => fmtDate(r.data_vencimento) },
    { key: 'valor',           label: 'Valor',     width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valor) },
    { key: 'status',          label: 'Status',    width: '120px', render: (r) => <StatusBadge value={String(r.status || 'pendente').toLowerCase()} map={STATUS_MAP} /> },
];

const fields: FieldDef[] = [
    { key: 'cliente_id',      label: 'Cliente',         type: 'number', required: true, help: 'ID do cliente' },
    { key: 'descricao',       label: 'Descricao',       type: 'text',   required: true },
    { key: 'valor',           label: 'Valor (R$)',      type: 'number', required: true, step: '0.01', min: 0 },
    { key: 'data_vencimento', label: 'Vencimento',      type: 'date',   required: true },
    { key: 'data_pagamento',  label: 'Pagamento',       type: 'date' },
    { key: 'status',          label: 'Status',          type: 'select', required: true, options: [
        { value: 'pendente',  label: 'Pendente' },
        { value: 'pago',      label: 'Pago' },
        { value: 'atrasado',  label: 'Atrasado' },
        { value: 'cancelado', label: 'Cancelado' },
        { value: 'enviado',   label: 'Enviado' },
    ] },
    { key: 'observacao',      label: 'Observacao',      type: 'textarea', rows: 2 },
];

const FILTROS = [
    { id: '',         label: 'Todos' },
    { id: 'pendente', label: 'Pendentes' },
    { id: 'pago',     label: 'Pagos' },
    { id: 'atrasado', label: 'Atrasados' },
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
                </div>
            </div>
            <CrudTable<Row>
                controller="cobrancas"
                title="Cobrancas"
                icon="cilCreditCard"
                columns={columns}
                fields={fields}
                defaultValue={{ cliente_id: 0, descricao: '', valor: 0, data_vencimento: new Date().toISOString().slice(0, 10), status: 'pendente' }}
                entityName="Cobranca"
                fixedParams={status ? { status } : undefined}
            />
        </>
    );
}
