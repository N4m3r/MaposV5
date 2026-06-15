/**
 * Pagina Financeiro.
 * Lista lancamentos (contas a pagar e receber) com filtros por tipo e status.
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

const TIPO_MAP: Record<string, { label: string; color: string }> = {
    receita:  { label: 'Receita',  color: 'success' },
    despesa:  { label: 'Despesa',  color: 'danger' },
};

const PAGO_MAP: Record<string, { label: string; color: string }> = {
    '1': { label: 'Pago',    color: 'success' },
    '0': { label: 'Aberto',  color: 'warning' },
};

const columns: ColumnDef<Row>[] = [
    { key: 'data_vencimento', label: 'Vencimento', width: '120px', sortable: true, render: (r) => fmtDate(r.data_vencimento || r.dataVencimento) },
    { key: 'descricao',       label: 'Descricao',  render: (r) => String(r.descricao || '-') },
    { key: 'tipo',            label: 'Tipo',       width: '110px', render: (r) => <StatusBadge value={String(r.tipo || '').toLowerCase()} map={TIPO_MAP} /> },
    { key: 'valor',           label: 'Valor',      width: '140px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valor) },
    { key: 'pago',            label: 'Status',     width: '110px', render: (r) => <StatusBadge value={String(r.pago ?? '0')} map={PAGO_MAP} /> },
];

const FILTROS = [
    { id: '',         label: 'Todos' },
    { id: 'receita',  label: 'Receitas' },
    { id: 'despesa',  label: 'Despesas' },
];

export default function FinanceiroPage() {
    const [tipo, setTipo] = useState('');
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 className="mb-0">
                    <CIcon icon="cilWallet" className="me-2" />
                    Financeiro
                </h2>
                <div className="d-flex gap-2 align-items-center flex-wrap">
                    {FILTROS.map((f) => (
                        <button
                            key={f.id}
                            type="button"
                            className={`btn btn-sm ${tipo === f.id ? 'btn-primary' : 'btn-outline-secondary'}`}
                            onClick={() => setTipo(f.id)}
                        >
                            {f.label}
                        </button>
                    ))}
                    <button type="button" className="btn btn-sm btn-success">
                        <CIcon icon="cilPlus" className="me-1" />Novo Lancamento
                    </button>
                </div>
            </div>
            <DataTable<Row>
                controller="financeiro"
                title=""
                columns={columns}
                fixedParams={tipo ? { tipo } : undefined}
                initialPageSize={50}
            />
        </>
    );
}
