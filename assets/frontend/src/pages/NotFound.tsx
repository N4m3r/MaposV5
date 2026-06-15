import { CCard, CCardBody, CButton } from '@coreui/react';
import { Link } from 'react-router-dom';
import CIcon from '@coreui/icons-react';

export default function NotFound() {
    return (
        <CCard>
            <CCardBody className="text-center p-5">
                <h1 className="display-1 text-primary">404</h1>
                <h3>Pagina nao encontrada</h3>
                <p className="text-muted">A rota solicitada nao existe na nova UI (React + CoreUI).</p>
                <div className="d-flex gap-2 justify-content-center mt-4">
                    <Link to="/dashboard" className="btn btn-primary">
                        <CIcon icon="cilSpeedometer" className="me-2" />
                        Ir para o Dashboard
                    </Link>
                    <a href="/index.php/mapos" className="btn btn-outline-secondary">
                        <CIcon icon="cilArrowLeft" className="me-2" />
                        Sistema legado (PHP)
                    </a>
                </div>
            </CCardBody>
        </CCard>
    );
}
