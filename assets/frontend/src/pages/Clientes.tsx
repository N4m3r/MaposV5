import { useEffect, useState } from 'react';
import { CCard, CCardBody, CCardHeader, CTable, CSpinner, CBadge } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { getClientes } from '../api/clientes';
import type { Cliente } from '../types';

export default function Clientes() {
    const [clientes, setClientes] = useState<Cliente[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        let mounted = true;
        getClientes()
            .then((data) => { if (mounted) setClientes(data); })
            .catch((err) => mounted && setError(err.message))
            .finally(() => mounted && setLoading(false));
        return () => { mounted = false; };
    }, []);

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: 400 }}>
                <CSpinner color="primary" />
            </div>
        );
    }

    if (error) {
        return <CCard className="border-danger"><CCardBody>Erro: {error}</CCardBody></CCard>;
    }

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilPeople" className="me-2" />
                    Clientes
                </h2>
                <button className="btn btn-primary">
                    <CIcon icon="cilPlus" className="me-1" />
                    Novo Cliente
                </button>
            </div>

            <CCard>
                <CCardHeader>
                    <strong>{clientes.length} cliente(s) cadastrado(s)</strong>
                </CCardHeader>
                <CCardBody className="p-0">
                    <CTable hover responsive className="mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Documento</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {clientes.map((c) => (
                                <tr key={c.id}>
                                    <td>{c.id}</td>
                                    <td>{c.nomeCliente}</td>
                                    <td>{c.documento || '-'}</td>
                                    <td>{c.email || '-'}</td>
                                    <td>{c.celular || c.telefone || '-'}</td>
                                    <td>
                                        <CBadge color={c.ativo ? 'success' : 'secondary'}>
                                            {c.ativo ? 'Ativo' : 'Inativo'}
                                        </CBadge>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </CTable>
                </CCardBody>
            </CCard>
        </>
    );
}
