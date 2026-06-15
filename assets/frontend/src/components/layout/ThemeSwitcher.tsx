import { useState } from 'react';
import { CDropdown, CDropdownToggle, CDropdownMenu, CDropdownItem } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { useTheme } from './AppShell';

const THEMES = [
    { id: 'white',       label: 'Claro',          icon: 'cilSun' },
    { id: 'puredark',    label: 'Claro Escuro',   icon: 'cilMoon' },
    { id: 'whitegreen',  label: 'Claro Verde',    icon: 'cilLeaf' },
    { id: 'whiteblack',  label: 'Claro Preto',    icon: 'cilContrast' },
    { id: 'darkviolet',  label: 'Escuro Violeta', icon: 'cilStar' },
    { id: 'darkorange',  label: 'Escuro Laranja', icon: 'cilFire' },
];

/**
 * Switcher de tema que usa o sistema consolidado em mapos.css Bloco 1.
 * Apenas troca body[data-theme] - sem reload de CSS.
 */
export function ThemeSwitcher() {
    const { theme, setTheme } = useTheme();
    const [open, setOpen] = useState(false);

    const current = THEMES.find((t) => t.id === theme) || THEMES[0];

    function pickTheme(id: string) {
        setTheme(id);
        setOpen(false);
        // Persiste no CodeIgniter via API (silencioso, sem redirect)
        fetch('/index.php/notificacoes/trocar_tema', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `tema=${encodeURIComponent(id)}`,
        }).catch(() => {
            // Silencia erros (tema eh visual, nao bloqueia)
        });
    }

    return (
        <CDropdown alignment="end" visible={open}>
            <CDropdownToggle caret={false} color="transparent" className="app-icon-btn" onClick={() => setOpen(!open)}>
                <CIcon icon={current.icon} size="lg" />
            </CDropdownToggle>
            <CDropdownMenu>
                {THEMES.map((t) => (
                    <CDropdownItem
                        key={t.id}
                        active={t.id === theme}
                        onClick={() => pickTheme(t.id)}
                    >
                        <CIcon icon={t.icon} className="me-2" />
                        {t.label}
                    </CDropdownItem>
                ))}
            </CDropdownMenu>
        </CDropdown>
    );
}
