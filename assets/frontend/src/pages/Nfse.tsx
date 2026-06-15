/**
 * Pagina NFS-e com CRUD completo.
 * Inclui link de download do XML na coluna de acoes.
 */
import CIcon from '@coreui/icons-react';
import { DataTable } from '../components/ui/DataTable';
import { StatusBadge } from '../components/ui/DataTable';
import { FormModal, type FieldDef } from '../components/ui/FormModal';
import { useCrudForm } from '../hooks/useCrudForm';
import { remove as crudRemove } from '../api/crud';
import type { Row, ColumnDef } from '../types';

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    autorizada: { label: 'Autorizada',  color: 'success' },
    cancelada:  { label: 'Cancelada',   color: 'danger' },
    pendente:   { label: 'Pendente',    color: 'warning' },
    rejeitada:  { label: 'Rejeitada',   color: 'danger' },
    erro:       { label: 'Erro',        color: 'danger' },
};

const columns: ColumnDef<Row>[] = [
    { key: 'numero',       label: 'Numero',      width: '120px', sortable: true, className: 'fw-bold' },
    { key: 'data_emissao', label: 'Emissao',     width: '120px', sortable: true, render: (r) => fmtDate(r.data_emissao) },
    { key: 'cliente_nome', label: 'Tomador',     render: (r) => String(r.cliente_nome || r.tomador_nome || '-') },
    { key: 'os_id',        label: 'OS',          width: '80px' },
    { key: 'valor',        label: 'Valor',       width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valor) },
    { key: 'status',       label: 'Status',      width: '130px', render: (r) => <StatusBadge value={String(r.status || '').toLowerCase()} map={STATUS_MAP} /> },
];

const fields: FieldDef[] = [
    { key: 'numero',       label: 'Numero',      type: 'text' },
    { key: 'data_emissao', label: 'Emissao',     type: 'date' },
    { key: 'os_id',        label: 'OS',          type: 'number' },
    { key: 'cliente_id',   label: 'Cliente',     type: 'number', help: 'ID do cliente (tomador)' },
    { key: 'valor',        label: 'Valor (R$)',  type: 'number', step: '0.01', min: 0 },
    { key: 'status',       label: 'Status',      type: 'select', options: [
        { value: 'pendente',  label: 'Pendente' },
        { value: 'autorizada',label: 'Autorizada' },
        { value: 'cancelada', label: 'Cancelada' },
        { value: 'rejeitada', label: 'Rejeitada' },
        { value: 'erro',      label: 'Erro' },
    ] },
    { key: 'observacao',   label: 'Observacao',  type: 'textarea', rows: 2 },
];

export default function NfsePage() {
    const { form, setForm, editing, open, loading, error, openCreate, openEdit, close, submit } = useCrudForm<Row>({
        controller: 'nfse_os',
        defaultValue: { data_emissao: new Date().toISOString().slice(0, 10), status: 'pendente', valor: 0 },
    });

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilReceipt" className="me-2" />
                    NFS-e Emitidas
                </h2>
                <button type="button" className="btn btn-sm btn-success" onClick={openCreate}>
                    <CIcon icon="cilPlus" className="me-1" />Nova NFS-e
                </button>
            </div>
            <DataTable<Row>
                controller="nfse_os"
                title="NFS-e Emitidas"
                icon="cilReceipt"
                columns={columns}
                initialPageSize={50}
                renderActions={(r) => (
                    <>
                        {r.chave_acesso ? (
                            <a
                                className="btn btn-sm btn-link p-0 me-2"
                                title="Baixar XML"
                                aria-label="Baixar XML"
                                href={`/index.php/nfse_os/baixar_xml/${r.id}`}
                            >
                                <CIcon icon="cilCloudDownload" />
                            </a>
                        ) : null}
                        <button
                            className="btn btn-sm btn-link p-0 me-2"
                            title="Editar"
                            aria-label="Editar"
                            onClick={() => openEdit(r.id as number)}
                        >
                            <CIcon icon="cilPencil" />
                        </button>
                        <button
                            className="btn btn-sm btn-link p-0 text-danger"
                            title="Excluir"
                            aria-label="Excluir"
                            onClick={async () => {
                                if (window.confirm('Excluir esta NFS-e?')) {
                                    await crudRemove('nfse_os', r.id as number);
                                    window.location.reload();
                                }
                            }}
                        >
                            <CIcon icon="cilTrash" />
                        </button>
                    </>
                )}
            />
            <FormModal
                visible={open}
                title={editing ? 'Editar NFS-e' : 'Nova NFS-e'}
                fields={fields}
                value={form}
                onChange={setForm}
                onClose={close}
                onSubmit={submit}
                loading={loading}
                error={error}
            />
        </>
    );
}
