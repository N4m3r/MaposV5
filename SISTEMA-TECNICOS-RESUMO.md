# Sistema de Gestão de Técnicos - Mapos OS

## Resumo da Implementação

Este documento resume o sistema completo de gestão de técnicos implementado no Mapos OS, projetado para empresas de instalação e manutenção de sistemas de segurança eletrônica.

---

## 📁 Estrutura de Arquivos Criada

### Controllers
- `application/controllers/Tecnicos.php` - Portal do técnico (mobile)
- `application/controllers/Tecnicos_admin.php` - Área administrativa

### Models
- `application/models/Tecnicos_model.php` - Gestão de técnicos e estoque
- `application/models/Tec_os_model.php` - Execução de OS e serviços
- `application/models/Obras_model.php` - Gestão de obras

### Views (Portal do Técnico)
- `application/views/tecnicos/login.php` - Login com foto e geolocalização
- `application/views/tecnicos/dashboard.php` - Painel do técnico
- `application/views/tecnicos/minhas_os.php` - Lista de OS
- `application/views/tecnicos/executar_os.php` - Execução de OS (checklists, fotos, assinatura)
- `application/views/tecnicos/meu_estoque.php` - Estoque do veículo
- `application/views/tecnicos/perfil.php` - Perfil do técnico

### Views (Administrativo)
- `application/views/tecnicos_admin/tecnico_form.php` - Formulário de cadastro

### Assets
- `assets/tecnicos/manifest.json` - Configuração PWA
- `assets/tecnicos/sw.js` - Service Worker para offline

### Database
- `database/sql/banco.sql` - SQL completo com novas tabelas
- `application/database/migrations/20260413000003_sistema_tecnicos_completo.php` - Migration CI

### Documentação
- `tec.md` - Especificação técnica completa
- `IMPLEMENTACAO-GUIA.md` - Guia de implementação
- `INTEGRACAO-MENU.md` - Instruções de integração

---

## 🗄️ Estrutura de Banco de Dados

### Campos Adicionados em `usuarios`
- `is_tecnico` - Flag de técnico
- `nivel_tecnico` - I, II, III, IV
- `especialidades` - JSON de especialidades
- `veiculo_placa`, `veiculo_tipo` - Dados do veículo
- `coordenadas_base_lat/lng` - Localização base
- `raio_atuacao_km` - Raio máximo de atuação
- `plantao_24h` - Disponibilidade plantão
- `app_tecnico_instalado` - Flag de instalação
- `token_app`, `token_expira` - Autenticação mobile
- `ultimo_acesso_app` - Último login
- `foto_tecnico` - Foto do perfil

### Novas Tabelas

1. **`servicos_catalogo`** - Catálogo de serviços
   - INS, MP, MC, CT, TR, UP, URG
   - Checklist JSON padronizado

2. **`os_servicos`** - Ligação OS <-> Serviços
   - Status de execução
   - Checklist específico

3. **`tec_os_execucao`** - Execução detalhada
   - Check-in/check-out com GPS
   - Fotos (check-in, checkout, galeria)
   - Assinatura digital
   - Tempo total
   - Checklist executado

4. **`tec_checklist_template`** - Templates reutilizáveis

5. **`tec_estoque_veiculo`** - Estoque por técnico

6. **`tec_rotas_tracking`** - Rastreamento GPS

7. **`obras`**, `obra_etapas`, `obra_diario` - Gestão de obras

---

## 📱 Funcionalidades do Portal do Técnico

### Login
- ✅ Login com e-mail e senha
- ✅ Captura de foto do técnico
- ✅ Geolocalização obrigatória
- ✅ Token JWT para sessão

### Dashboard
- ✅ Estatísticas de OS (hoje, pendentes, semana)
- ✅ Lista de OS do dia
- ✅ Alertas de estoque baixo
- ✅ Acesso rápido às principais funções

### Execução de OS
- ✅ Check-in com GPS e foto
- ✅ Checklist passo a passo
- ✅ Galeria de fotos (antes/depois/problemas)
- ✅ Registro de materiais utilizados
- ✅ Assinatura digital do cliente
- ✅ Check-out com tempo total

### Estoque
- ✅ Visualização de itens no veículo
- ✅ Histórico de movimentações
- ✅ Alertas de estoque baixo

### Perfil
- ✅ Dados pessoais e profissionais
- ✅ Alteração de foto
- ✅ Estatísticas de trabalho

---

## 🔧 Funcionalidades Administrativas

### Gestão de Técnicos
- ✅ Cadastro com especialidades
- ✅ Níveis (Aprendiz a Coordenador)
- ✅ Configuração de raio de atuação
- ✅ Plantão 24h
- ✅ Veículo atribuído

### Catálogo de Serviços
- ✅ Tipos padronizados (INS, MP, MC, etc.)
- ✅ Checklists configuráveis
- ✅ Especialidades requeridas

### Relatórios
- ✅ OS executadas por período
- ✅ Tempo médio por serviço
- ✅ Rotas dos técnicos
- ✅ Produtividade

### Gestão de Obras
- ✅ Cadastro de obras grandes
- ✅ Etapas estruturadas
- ✅ Diário de obra
- ✅ Equipe alocada
- ✅ Fotos documentais

---

## 🚀 Como Usar

### Instalação

1. **Executar Migration** (opcional para novas instalações):
```bash
php index.php migrate
```

Ou importar diretamente:
```bash
mysql -u usuario -p banco < database/sql/banco.sql
```

2. **Configurar Permissões da Pasta**:
```bash
chmod -R 755 assets/tecnicos/fotos/
chown -R www-data:www-data assets/tecnicos/fotos/
```

3. **Adicionar Menu** (ver `INTEGRACAO-MENU.md`)

### Primeiro Acesso

1. **Acesse o admin**: `https://seudominio.com/tecnicos_admin`
2. **Cadastre um técnico** com e-mail e senha
3. **Configure o catálogo de serviços**
4. **Acesse o portal**: `https://seudominio.com/tecnicos/login`

### Fluxo de Trabalho

1. **Admin** cria OS e atribui ao técnico
2. **Técnico** recebe notificação (se push configurado)
3. **Técnico** faz login no portal
4. **Técnico** inicia execução (check-in com foto+GPS)
5. **Técnico** executa checklist e tira fotos
6. **Técnico** registra materiais usados
7. **Técnico** coleta assinatura do cliente
8. **Técnico** finaliza OS (check-out)
9. **Admin** acompanha em tempo real

---

## 📲 PWA (Progressive Web App)

O portal do técnico funciona como um app instalável:

### Recursos
- ✅ Instalação na tela inicial
- ✅ Funcionamento offline básico
- ✅ Cache de assets
- ✅ Background sync (para fotos)
- ✅ Push notifications (pronto para implementar)

### Instalação
1. Acesse `/tecnicos/login` no Chrome/Safari
2. Toque em "Adicionar à tela inicial"
3. Use como um app nativo

---

## 🔒 Segurança

- ✅ Senhas hasheadas (bcrypt)
- ✅ Tokens com expiração
- ✅ Validação de GPS (anti-fake)
- ✅ Fotos com metadados
- ✅ Assinatura digital verificável
- ✅ Permissões granulares

---

## 🎨 Personalização

### Cores
Edite as variáveis CSS nas views:
```css
/* Tema principal */
--primary-color: #667eea;
--secondary-color: #764ba2;
```

### Logo
Substitua os arquivos em `assets/tecnicos/icon-*.png`

### Checklists
Adicione templates em `tec_checklist_template`

---

## 📊 Tipos de Serviço Suportados

| Código | Descrição | Uso |
|--------|-----------|-----|
| INS | Instalação | Nova instalação |
| MP | Manutenção Preventiva | Revisão periódica |
| MC | Manutenção Corretiva | Reparo de defeito |
| CT | Consultoria | Avaliação técnica |
| TR | Treinamento | Capacitação cliente |
| UP | Upgrade | Modernização |
| URG | Urgência | Chamado emergencial |

---

## 🔧 Níveis de Técnico

| Nível | Título | Responsabilidades |
|-------|--------|-------------------|
| I | Aprendiz | Acompanhado, tarefas simples |
| II | Técnico | Executa serviços padrão |
| III | Especialista | Serviços complexos, liderança |
| IV | Coordenador | Gestão de equipe, decisões |

---

## 🐛 Troubleshooting

### Problema: Login não funciona
**Solução**: Verifique se o técnico tem `is_tecnico = 1`

### Problema: GPS não é capturado
**Solução**: Site precisa estar em HTTPS (obrigatório para geolocation)

### Problema: Fotos não aparecem
**Solução**: Verifique permissões da pasta `assets/tecnicos/fotos/`

### Problema: Câmera não abre
**Solução**: Permita acesso à câmera nas configurações do navegador

---

## 📈 Próximos Passos (Roadmap)

### Versão 1.1
- [ ] Notificações push de novas OS
- [ ] Chat em tempo entre técnico e admin
- [ ] Relatórios em PDF
- [ ] Integração com WhatsApp

### Versão 1.2
- [ ] Face ID/Touch ID para login
- [ ] OCR para leitura de placas/serial
- [ ] Realidade aumentada para instalação
- [ ] Dashboard analítico avançado

### Versão 2.0
- [ ] App nativo Android/iOS
- [ ] Integração com APIs de roteamento (Google Maps)
- [ ] Predição de falhas com IA
- [ ] Automação de estoque

---

## 📞 Suporte

Para suporte e dúvidas:
1. Consulte a documentação técnica (`tec.md`)
2. Verifique logs em `application/logs/`
3. Abra issue no repositório

---

## 📄 Licença

Este sistema é parte do Mapos OS e segue a mesma licença do projeto principal.

---

**Data de Implementação**: 13/04/2026  
**Versão**: 1.0.0  
**Desenvolvido por**: Claude Code (Anthropic)
