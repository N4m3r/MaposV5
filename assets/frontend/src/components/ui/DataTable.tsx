/**
 * DataTable generica.
 *
 * Recebe: controller (string), columns, mapeamento de id opcional.
 * Encapsula: busca, paginacao, ordenacao, loading, error.
 *
 * Uso:
 *   <DataTable
 *     controller="clientes"
 *     title="Clientes"
 *     icon="cilPeople"
 *     columns={[
 *       { key: 'nomeCliente', label: 'Nome' },
 *       { key: 'documento', label: 'CPF/CNPJ' },
 *       { key: 'ativo', label: 'Status', render: r => r.ativo ? 'Ativo' : 'Inativo' },
 *     ]}
 *   />
 */
import { useEffect, useState, useCallback, useMemo, type ReactNode } from 'react';
import { CCard, CCardBody, CCardHeader, CTable, CSpinner, CPagination, CPaginationItem, CFormInput, CBadge } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { list as crudList, type ListParams } from '../../api/crud';
import type { ColumnDef, Row } from '../../types';

interface DataTableProps<R extends Row = Row> {
    controller: string;
    title: string;
    icon?: string;
    columns: ColumnDef<R>[];
    /** Nome da coluna id (default 'id') */
    idKey?: string;
    /** Params fixos pra sempre enviar (ex: status='Aberto') */
    fixedParams?: ListParams;
    /** Renderiza algo na coluna de acoes */
    renderActions?: (row: R) => ReactNode;
    /** Botao customizado no header */
    headerButton?: ReactNode;
    /** Extrai id da row (caso queira uma chave custom) */
    getRowId?: (row: R) => number | string;
    /** Pagina inicial */
    initialPageSize?: number;
    /** Estado inicial do search */
    initialSearch?: string;
}

export function DataTable<R extends Row = Row>({
    controller,
    title,
    icon,
    columns,
    idKey = 'id',
    fixedParams,
    renderActions,
    headerButton,
    getRowId,
    initialPageSize = 25,
    initialSearch = '',
}: DataTableProps<R>) {
    const [rows, setRows] = useState<R[]>([]);
    const [total, setTotal] = useState(0);
    const [page, setPage] = useState(1);
    const [limit] = useState(initialPageSize);
    const [search, setSearch] = useState(initialSearch);
    const [searchInput, setSearchInput] = useState(initialSearch);
    const [orderBy, setOrderBy] = useState<string | null>(null);
    const [orderDir, setOrderDir] = useState<'asc' | 'desc'>('desc');
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const totalPages = useMemo(() => Math.max(1, Math.ceil(total / limit)), [total, limit]);

    const load = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const params: ListParams = { page, limit, search, ...(fixedParams || {}) };
            if (orderBy) {
                params.orderBy = orderBy;
                params.orderDir = orderDir;
            }
            const r = await crudList<R>(controller, params);
            setRows(r.data);
            setTotal(r.total);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Erro ao carregar');
        } finally {
            setLoading(false);
        }
    }, [controller, page, limit, search, orderBy, orderDir, fixedParams]);

    useEffect(() => { load(); }, [load]);

    // Debounce da busca
    useEffect(() => {
        const t = setTimeout(() => { setSearch(searchInput); setPage(1); }, 350);
        return () => clearTimeout(t);
    }, [searchInput]);

    function handleSort(col: ColumnDef<R>) {
        if (!col.sortable) return;
        if (orderBy === col.key) {
            setOrderDir(orderDir === 'asc' ? 'desc' : 'asc');
        } else {
            setOrderBy(col.key);
            setOrderDir('asc');
        }
    }

    function getRowKey(row: R, i: number): React.Key {
        if (getRowId) return getRowId(row);
        const v = row[idKey];
        return (typeof v === 'number' || typeof v === 'string') ? v : i;
    }

    return (
        <CCard>
            <CCardHeader className="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    {icon && <CIcon icon={icon} className="me-2" />}
                    <strong>{title}</strong>
                    <span className="text-muted ms-2">({total})</span>
                </div>
                <div className="d-flex gap-2 align-items-center flex-wrap" style={{ minWidth: 280 }}>
                    <CFormInput
                        type="search"
                        size="sm"
                        placeholder="Buscar..."
                        value={searchInput}
                        onChange={(e) => setSearchInput(e.target.value)}
                        style={{ minWidth: 220 }}
                    />
                    {headerButton}
                </div>
            </CCardHeader>
            <CCardBody className="p-0">
                {loading && (
                    <div className="d-flex justify-content-center align-items-center" style={{ minHeight: 200 }}>
                        <CSpinner color="primary" />
                    </div>
                )}
                {error && !loading && (
                    <div className="p-3 text-danger">
                        Erro: {error} <button className="btn btn-link btn-sm" onClick={load}>Tentar novamente</button>
                    </div>
                )}
                {!loading && !error && rows.length === 0 && (
                    <div className="text-center text-muted p-4">Nenhum registro encontrado.</div>
                )}
                {!loading && !error && rows.length > 0 && (
                    <div className="table-responsive">
                        <CTable hover striped small className="mb-0 align-middle">
                            <thead>
                                <tr>
                                    {columns.map((c) => (
                                        <th
                                            key={c.key}
                                            style={c.width ? { width: c.width } : undefined}
                                            className={c.className}
                                            onClick={() => handleSort(c)}
                                            role={c.sortable ? 'button' : undefined}
                                        >
                                            {c.label}
                                            {c.sortable && orderBy === c.key && (
                                                <CIcon
                                                    icon={orderDir === 'asc' ? 'cilArrowTop' : 'cilArrowBottom'}
                                                    size="sm"
                                                    className="ms-1"
                                                />
                                            )}
                                        </th>
                                    ))}
                                    {renderActions && <th style={{ width: 100 }} className="text-end">Acoes</th>}
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((row, i) => (
                                    <tr key={getRowKey(row, i)}>
                                        {columns.map((c) => (
                                            <td key={c.key} className={c.className}>
                                                {c.render ? c.render(row) : String((row as Record<string, unknown>)[c.key] ?? '-')}
                                            </td>
                                        ))}
                                        {renderActions && <td className="text-end">{renderActions(row)}</td>}
                                    </tr>
                                ))}
                            </tbody>
                        </CTable>
                    </div>
                )}
            </CCardBody>
            {totalPages > 1 && (
                <CCardBody className="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small className="text-muted">
                        Pagina {page} de {totalPages} - {total} registro(s)
                    </small>
                    <CPagination size="sm" className="mb-0">
                        <CPaginationItem disabled={page <= 1} onClick={() => setPage(1)}>&laquo;</CPaginationItem>
                        <CPaginationItem disabled={page <= 1} onClick={() => setPage(page - 1)}>&lsaquo;</CPaginationItem>
                        {Array.from({ length: Math.min(7, totalPages) }, (_, k) => {
                            const n = Math.max(1, Math.min(page - 3, totalPages - 6)) + k;
                            if (n > totalPages) return null;
                            return (
                                <CPaginationItem key={n} active={n === page} onClick={() => setPage(n)}>
                                    {n}
                                </CPaginationItem>
                            );
                        })}
                        <CPaginationItem disabled={page >= totalPages} onClick={() => setPage(page + 1)}>&rsaquo;</CPaginationItem>
                        <CPaginationItem disabled={page >= totalPages} onClick={() => setPage(totalPages)}>&raquo;</CPaginationItem>
                    </CPagination>
                </CCardBody>
            )}
        </CCard>
    );
}

export function StatusBadge({ value, map }: { value: string | number | null | undefined; map?: Record<string, { label: string; color: string }> }) {
    const s = String(value ?? '');
    const def = map?.[s] || { label: s || '-', color: 'secondary' };
    return <CBadge color={def.color}>{def.label}</CBadge>;
}
