# Sistema de Técnicos - MAPOS

Documentação resumida do sistema de gestão de técnicos do MAPOS.

---

## Visão Geral

O **Sistema de Técnicos** é um módulo do MAPOS que permite gerenciar técnicos de campo, suas ordens de serviço, estoque e performance.

### Funcionalidades Principais

| Funcionalidade | Descrição | Status |
|:--|:--|:--:|
| Dashboard Técnico | Visão geral de OS e estatísticas | Ativo |
| Minhas OS | Gerenciamento de ordens de serviço | Ativo |
| Meu Estoque | Controle de produtos no veículo | Ativo |
| Perfil do Técnico | Dados pessoais e configurações | Ativo |
| Relatórios | Performance e atendimentos | Ativo |

---

## Guia de Estilos (Tema MAPOS)

### Cores Principais

| Cor | Hex | Uso |
|:--|:--|:--|
| Primary | `#2c88ff` | Botões primários, links |
| Success | `#4caf50` | Status concluído, sucesso |
| Warning | `#f57c00` | Alertas, status aguardando |
| Danger | `#e74c3c` | Erros, exclusões |
| Info | `#3498db` | Informações |
| Background | `#f5f6fa` | Fundo da página |
| Card | `#ffffff` | Fundo dos cards |
| Text Primary | `#2c3e50` | Títulos |
| Text Secondary | `#7f8c8d` | Descrições |

### Tipografia

| Elemento | Tamanho | Peso |
|:--|--:|:--:|
| H1 | 1.8rem | 700 |
| H2 | 1.5rem | 600 |
| H3 | 1.2rem | 600 |
| Body | 0.9rem | 400 |
| Small | 0.75rem | 500 |

---

## Estrutura de Arquivos

```
application/
├── controllers/
│   └── Tecnicos.php          # Controller principal
├── models/
│   └── Tecnicos_model.php    # Model de dados
└── views/
    ├── tecnicos/
    │   ├── dashboard.php     # Painel do técnico
    │   ├── login.php         # Login exclusivo
    │   ├── minhas_os.php     # Lista de OS
    │   ├── executar_os.php   # Execução de OS
    │   ├── meu_estoque.php   # Estoque no veículo
    │   └── perfil.php        # Perfil do técnico
    ├── tecnicos_admin/
    │   ├── dashboard.php     # Dashboard admin
    │   ├── tecnicos_list.php # Lista de técnicos
    │   └── tecnico_form.php  # Form de cadastro
    └── tema/
        └── menu_tecnico.php  # Menu lateral
```

---

## Permissões

| Permissão | Descrição |
|:--|:--|
| vTecnicoDashboard | Visualizar Dashboard |
| vTecnicoOS | Visualizar Minhas OS |
| aTecnicoOS | Executar OS |
| vTecnicoEstoque | Visualizar Estoque |
| eTecnicoEstoque | Editar Estoque |
| vRelatorioTecnicos | Relatório de Performance |
| vRelatorioAtendimentos | Relatório de Atendimentos |

---

## API Endpoints

### Técnico (Mobile/Portal)

| Método | Endpoint | Descrição |
|:--:|:--|:--|
| GET | /tecnicos/dashboard | Dashboard do técnico |
| GET | /tecnicos/minhas_os | Listar minhas OS |
| GET | /tecnicos/executar_os/{id} | Executar OS específica |
| POST | /tecnicos/atualizar_os/{id} | Atualizar status da OS |
| GET | /tecnicos/meu_estoque | Ver estoque no veículo |
| POST | /tecnicos/atualizar_estoque | Atualizar quantidades |
| GET | /tecnicos/perfil | Ver perfil |
| POST | /tecnicos/atualizar_perfil | Atualizar dados |

### Admin

| Método | Endpoint | Descrição |
|:--:|:--|:--|
| GET | /tecnicos_admin | Dashboard admin |
| GET | /tecnicos_admin/listar | Listar todos técnicos |
| POST | /tecnicos_admin/adicionar | Cadastrar técnico |
| POST | /tecnicos_admin/editar/{id} | Editar técnico |
| POST | /tecnicos_admin/excluir/{id} | Excluir técnico |
| GET | /tecnicos_admin/relatorio/{id} | Relatório individual |

---

## Status das OS

| Status | Cor | Classe CSS |
|:--|:--|:--|
| Aberto | Azul | .os-status.aberto |
| Em Andamento | Verde | .os-status.em_andamento |
| Aguardando Peças | Laranja | .os-status.aguardando |
| Aguardando Cliente | Amarelo | .os-status.aguardando |
| Finalizado | Cinza | .os-status.finalizado |

---

## Performance - Otimizações

1. **Cache**
   - Service Worker para assets estáticos
   - Cache de consultas frequentes (OS do dia)
   - Lazy loading de imagens

2. **Responsividade**
   - Breakpoints: 480px, 768px, 1024px
   - Grid flexível para cards
   - Touch-friendly para mobile

3. **Boas Práticas**
   - Imagens otimizadas
   - CSS inline crítico
   - JavaScript assíncrono
   - Consultas SQL otimizadas com índices

---

## Changelog

| Versão | Data | Alterações |
|:--|:--|:--|
| 1.0.0 | 2024-04-18 | Versão inicial do sistema |

---

**MAPOS - Sistema de Ordens de Serviço**  
Versão 5.0
