/**
 * Pagina Arquivos com listagem e download.
 * Upload e CRUD completo estao fora do escopo deste sprint;
 * aqui mantemos a listagem e link de download.
 */
import CIcon from '@coreui/icons-react';
import { DataTable } from '../components/ui/DataTable';
import { StatusBadge } from '../components/ui/DataTable';
import type { Row, ColumnDef } from '../types';

function fmtSize(v: unknown): string {
    const n = Number(v || 0);
    if (n <= 0) return '-';
    if (n < 1024) return `${n} B`;
    if (n < 1024 * 1024) return `${(n / 1024).toFixed(1)} KB`;
    if (n < 1024 * 1024 * 1024) return `${(n / 1024 / 1024).toFixed(1)} MB`;
    return `${(n / 1024 / 1024 / 1024).toFixed(1)} GB`;
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const columns: ColumnDef<Row>[] = [
    { key: 'id',         label: '#',        width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'nome',       label: 'Nome',     sortable: true },
    { key: 'descricao',  label: 'Descricao', render: (r) => String(r.descricao || '-') },
    { key: 'categoria',  label: 'Categoria', width: '140px' },
    { key: 'tamanho',    label: 'Tamanho',  width: '110px', className: 'text-end', render: (r) => fmtSize(r.tamanho) },
    { key: 'data_upload', label: 'Upload',  width: '120px', sortable: true, render: (r) => fmtDate(r.data_upload) },
    { key: 'tipo',       label: 'Tipo',     width: '100px', render: (r) => <StatusBadge value={String(r.tipo || '-')} map={{
        pdf:  { label: 'PDF',  color: 'danger' },
        img:  { label: 'IMG',  color: 'info' },
        doc:  { label: 'DOC',  color: 'primary' },
        xls:  { label: 'XLS',  color: 'success' },
        other:{ label: 'OUT',  color: 'secondary' },
    }} /> },
];

export default function ArquivosPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilFolderOpen" className="me-2" />
                    Arquivos
                </h2>
                <a href="/index.php/arquivos/adicionarArquivo" className="btn btn-sm btn-success">
                    <CIcon icon="cilCloudUpload" className="me-1" />Upload (sistema legado)
                </a>
            </div>
            <DataTable<Row>
                controller="arquivos"
                title="Arquivos"
                icon="cilFolderOpen"
                columns={columns}
                initialPageSize={50}
                renderActions={(r) => (
                    <a
                        className="btn btn-sm btn-link p-0"
                        title="Baixar"
                        aria-label="Baixar"
                        href={`/index.php/arquivos/download/${r.id}`}
                    >
                        <CIcon icon="cilCloudDownload" />
                    </a>
                )}
            />
        </>
    );
}
