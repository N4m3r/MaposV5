/**
 * Pagina Produtos.
 * Lista produtos com estoque e preco.
 */
import CIcon from '@coreui/icons-react';
import { DataTable } from '../components/ui/DataTable';
import type { Row, ColumnDef } from '../types';

function fmtCurrency(v: unknown): string {
    const n = Number(v || 0);
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n);
}

const columns: ColumnDef<Row>[] = [
    { key: 'idProdutos',  label: '#',         width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'descricao',   label: 'Descricao', sortable: true },
    { key: 'unidade',     label: 'Un.',       width: '80px' },
    { key: 'codDeBarra',  label: 'Codigo',    width: '140px' },
    { key: 'estoque',     label: 'Estoque',   width: '110px', sortable: true, className: 'text-end',
      render: (r) => {
          const n = Number(r.estoque || 0);
          const min = Number(r.estoqueMinimo || 0);
          const color = min > 0 && n <= min ? 'text-danger fw-bold' : (n <= 0 ? 'text-danger' : '');
          return <span className={color}>{n}</span>;
      }
    },
    { key: 'precoVenda',  label: 'Preco',     width: '120px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.precoVenda) },
];

export default function ProdutosPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilBox" className="me-2" />
                    Produtos
                </h2>
                <button type="button" className="btn btn-sm btn-success">
                    <CIcon icon="cilPlus" className="me-1" />Novo Produto
                </button>
            </div>
            <DataTable<Row>
                controller="produtos"
                title=""
                columns={columns}
                initialPageSize={50}
                renderActions={(r) => (
                    <>
                        <button className="btn btn-sm btn-link p-0 me-2" title="Visualizar" aria-label="Visualizar">
                            <CIcon icon="cilEye" />
                        </button>
                        <button className="btn btn-sm btn-link p-0" title="Editar" aria-label="Editar">
                            <CIcon icon="cilPencil" />
                        </button>
                    </>
                )}
            />
        </>
    );
}
