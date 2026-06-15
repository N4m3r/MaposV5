import { CCard, CCardBody } from '@coreui/react';
import { Link } from 'react-router-dom';

export default function NotFound() {
    return (
        <CCard>
            <CCardBody className="text-center p-5">
                <h1 className="display-1 text-primary">404</h1>
                <h3>Pagina nao encontrada</h3>
                <p className="text-muted">A rota solicitada nao existe.</p>
                <Link to="/dashboard" className="btn btn-primary">
                    Voltar ao Dashboard
                </Link>
            </CCardBody>
        </CCard>
    );
}
