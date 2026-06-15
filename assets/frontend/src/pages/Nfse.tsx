/**
 * Pagina NFS-e (Nota Fiscal de Servico Eletronica).
 * Lista NFS-e emitidas, com link para detalhes/baixa do XML.
 */
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
    autorizada:  { label: 'Autorizada',  color: 'success' },
    cancelada:   { label: 'Cancelada',   color: 'danger' },
    pendente:    { label: 'Pendente',    color: 'warning' },
    rejeitada:   { label: 'Rejeitada',   color: 'danger' },
    erro:        { label: 'Erro',        color: 'danger' },
};

const columns: ColumnDef<Row>[] = [
    { key: 'numero',       label: 'Numero',      width: '120px', sortable: true, className: 'fw-bold' },
    { key: 'data_emissao', label: 'Emissao',     width: '120px', sortable: true, render: (r) => fmtDate(r.data_emissao) },
    { key: 'cliente_nome', label: 'Tomador',     render: (r) => String(r.cliente_nome || r.tomador_nome || '-') },
    { key: 'os_id',        label: 'OS',          width: '80px' },
    { key: 'valor',        label: 'Valor',       width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valor) },
    { key: 'status',       label: 'Status',      width: '130px', render: (r) => <StatusBadge value={String(r.status || '').toLowerCase()} map={STATUS_MAP} /> },
];

export default function NfsePage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilReceipt" className="me-2" />
                    NFS-e Emitidas
                </h2>
                <button type="button" className="btn btn-sm btn-success">
                    <CIcon icon="cilPlus" className="me-1" />Emitir NFS-e
                </button>
            </div>
            <DataTable<Row>
                controller="nfse_os"
                title=""
                columns={columns}
                initialPageSize={50}
                renderActions={(r) => (
                    <>
                        <button className="btn btn-sm btn-link p-0 me-2" title="Visualizar" aria-label="Visualizar">
                            <CIcon icon="cilEye" />
                        </button>
                        {r.chave_acesso ? (
                            <a
                                className="btn btn-sm btn-link p-0"
                                title="Baixar XML"
                                aria-label="Baixar XML"
                                href={`/index.php/nfse_os/baixar_xml/${r.id}`}
                            >
                                <CIcon icon="cilCloudDownload" />
                            </a>
                        ) : null}
                    </>
                )}
            />
        </>
    );
}
