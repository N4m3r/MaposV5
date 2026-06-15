/**
 * Pagina Produtos com CRUD completo.
 * Destaque visual para estoque baixo.
 */
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import type { FieldDef } from '../components/ui/FormModal';
import type { Row, ColumnDef } from '../types';

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
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

const fields: FieldDef[] = [
    { key: 'descricao',     label: 'Descricao',     type: 'text',     required: true },
    { key: 'unidade',       label: 'Unidade',       type: 'text',     placeholder: 'UN, KG, LT...' },
    { key: 'codDeBarra',    label: 'Codigo Barras', type: 'text' },
    { key: 'precoVenda',    label: 'Preco Venda',   type: 'number',   step: '0.01', min: 0 },
    { key: 'precoCusto',    label: 'Preco Custo',   type: 'number',   step: '0.01', min: 0 },
    { key: 'estoque',       label: 'Estoque Atual', type: 'number',   min: 0 },
    { key: 'estoqueMinimo', label: 'Estoque Minimo', type: 'number',  min: 0 },
];

export default function ProdutosPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilBox" className="me-2" />
                    Produtos
                </h2>
            </div>
            <CrudTable<Row>
                controller="produtos"
                title="Produtos"
                icon="cilBox"
                columns={columns}
                fields={fields}
                defaultValue={{ descricao: '', unidade: 'UN', precoVenda: 0, precoCusto: 0, estoque: 0, estoqueMinimo: 0 }}
                idKey="idProdutos"
                entityName="Produto"
            />
        </>
    );
}
