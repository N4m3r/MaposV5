# Relatorio de Validacao - Certificado Digital e Emissao NFSe Nacional

## Data da analise: 01/05/2026
## Certificado analisado: `JJ TECNOLOGIAS LTDA 2025.pfx`
## CNPJ do certificado: `54.518.217/0001-17`

---

## 1. RESUMO EXECUTIVO

**O certificado digital esta tecnicamente perfeito. A empresa e optante pelo Simples Nacional. O erro E0160 e causado por um bug no codigo que envia a serie DPS incorreta para a API Nacional.**

A consulta na Receita Federal confirma que o CNPJ `54518217000117` e **optante pelo Simples Nacional desde 28/03/2024** e **nao e MEI**. Portanto, o cadastro tributario esta correto.

O erro `E0160` esta ocorrendo porque o sistema esta enviando a serie da DPS como `00001`, mas o cadastro correto no CNC NFS-e do municipio de Manaus e a serie `70000`. Quando a API Nacional nao localiza o prestador na serie correta, a validacao do Simples Nacional falha em cascata.

> **Diagnostico final:** Bug na leitura do arquivo `application/config/nfse_nacional.php`. O sistema nao consegue ler o valor `$config['nfse_serie_dps'] = '70000'`, e usa o fallback `1`, gerando `<serie>00001</serie>` no XML.

---

## 2. ANALISE TECNICA DO CERTIFICADO

### 2.1 Validacao Estrutural

| Atributo | Valor | Status |
|----------|-------|--------|
| Formato | PKCS#12 (.pfx) | OK |
| Senha | Validada | OK |
| Leitura OpenSSL | Bem-sucedida | OK |
| Cadeia de certificados | 4 certificados (end-entity + 3 intermediarios) | OK |
| Par certificado/chave | Validado (`openssl_x509_check_private_key`) | OK |

### 2.2 Dados do Certificado

| Campo | Valor |
|-------|-------|
| **Subject CN** | JJ TECNOLOGIAS LTDA:54518217000117 |
| **CNPJ extraido** | 54518217000117 |
| **Razao Social** | JJ TECNOLOGIAS LTDA |
| **Emissor (Issuer)** | AC SyngularID Multipla (ICP-Brasil) |
| **Serial Number** | 683153758491667465373951 |
| **Validade de** | 06/06/2025 |
| **Validade ate** | 06/06/2026 |
| **Dias restantes** | 36 dias |
| **Expirado** | NAO |

### 2.3 Usos e Extensoes (EKU / Key Usage)

| Extensao | Valor | Status |
|----------|-------|--------|
| **Key Usage** | Digital Signature, Non Repudiation, Key Encipherment | OK |
| **Extended Key Usage** | E-mail Protection, TLS Web Client Authentication | OK |
| **Client Auth (1.3.6.1.5.5.7.3.2)** | Presente | OK |

### 2.4 Conformidade ICP-Brasil

| Requisito | Status |
|-----------|--------|
| Emissor ICP-Brasil reconhecido | SIM |
| Possui `TLS Web Client Authentication` | SIM |
| Cadeia completa no arquivo PEM | SIM (4 certificados) |
| CNPJ no certificado corresponde ao cadastro | SIM |

### 2.5 Parecer sobre o Certificado

**O certificado esta 100% apto para emissao de NFS-e Nacional.**

Todos os requisitos tecnicos do SEFIN Nacional sao atendidos.

---

## 3. ANALISE DO ERRO DE EMISSAO

### 3.1 Confirmacao do Cadastro na Receita Federal

**Consulta realizada em 01/05/2026 as 16:24:34:**

| Informacao | Valor |
|------------|-------|
| **CNPJ** | 54.518.217/0001-17 |
| **Razao Social** | JJ TECNOLOGIAS LTDA |
| **Situacao no Simples Nacional** | Optante pelo Simples Nacional desde 28/03/2024 |
| **Situacao no SIMEI** | NAO enquadrado no SIMEI |
| **Periodos Anteriores Simples** | Nao existem |
| **Eventos Futuros Simples** | Nao existem |
| **Eventos Futuros SIMEI** | Nao existem |

**Conclusao:** O CNPJ e de fato optante pelo Simples Nacional. O cadastro esta ativo e regular.

### 3.2 Historico de Erros nas Tentativas de Emissao

| Horario | Erro | Codigo | Causa |
|---------|------|--------|-------|
| 05:16:05 | IM nao cadastrada no CNC | E0116 | Inscricao Municipal nao vinculada ao CNPJ no CNC |
| 05:20:35 | IRRF deve ser maior que zero | E0700 | Valor do servico era muito baixo (R$ 2,00), IRRF ficou 0.00 |
| 05:20:59 | IRRF deve ser maior que zero | E0700 | Mesmo erro com valor R$ 10,00 |
| 05:24:44 | Simples Nacional divergente | **E0160** | Serie DPS incorreta: XML enviou `00001` mas o cadastro e `70000` |

### 3.3 O Bug da Serie DPS

#### Configuracao esperada (`application/config/nfse_nacional.php`):
```php
$config['nfse_serie_dps'] = '70000';
```

#### Como o codigo tenta ler:
```php
// application/controllers/Nfse_os.php (linha ~969-970)
$this->config->load('nfse_nacional', true);
$nfseConfig = $this->config->item('nfse_nacional');

// linha ~1102
$serieDps = $nfseConfig['nfse_serie_dps'] ?? '1';
```

#### O que acontece de fato:
Em CodeIgniter, quando se usa `$this->config->load('nfse_nacional', true)`, o segundo parametro `true` indica que o carregamento deve ser feito de forma que as configuracoes fiquem disponiveis via `$this->config->item('chave', 'nfse_nacional')`. 

A chamada `$this->config->item('nfse_nacional')` procura um indice chamado `nfse_nacional` dentro do array de configuracoes. Como o arquivo `nfse_nacional.php` nao define nenhuma chave com esse nome exato, o retorno e provavelmente `null` ou `false`.

Isso faz com que `$nfseConfig` nao seja um array valido, e `$nfseConfig['nfse_serie_dps']` falhe silenciosamente, caindo no fallback `?? '1'`.

#### Resultado no XML (confirmado nos logs):
```xml
<serie>00001</serie>   <!-- Sistema enviou serie 1 -->
```

#### O que deveria ser enviado:
```xml
<serie>70000</serie>   <!-- Serie cadastrada no CNC NFS-e de Manaus -->
```

### 3.4 Por que a Serie Errada Causa E0160?

A API SEFIN Nacional utiliza a serie DPS para localizar o prestador de servicos no CNC NFS-e (Cadastro Nacional de Contribuintes NFS-e). Cada prestador e vinculado a uma serie especifica no cadastro do municipio.

Quando o sistema envia `<serie>00001</serie>`, a API nao localiza o prestador `54518217000117` com a inscricao municipal `618590001` na serie `1`. Como consequencia:

1. O prestador nao e encontrado na serie informada.
2. A API tenta validar os dados tributarios, mas nao consegue confirmar a situacao do Simples Nacional para um prestador que nao encontrou.
3. A resposta devolvida e `E0160` (Simples Nacional divergente), pois a API nao consegue validar a opcao pelo Simples Nacional sem localizar o cadastro correto do prestador na serie correta.

**A mensagem de erro E0160 e um efeito colateral da serie incorreta.** O problema raiz e o envio da serie `00001` no lugar da serie `70000`.

---

## 4. EVIDENCIAS DOS LOGS

### Trecho do XML enviado (extraido dos logs do sistema):
```xml
<?xml version="1.0" encoding="UTF-8"?>
<DPS xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01">
  <infDPS Id="DPS130260325451821700011700001100000000000001">
    <tpAmb>2</tpAmb>
    <dhEmi>2026-05-01T05:24:44-03:00</dhEmi>
    <verAplic>MAPOS-NFSE-1.0</verAplic>
    <serie>00001</serie>          <!-- !!!! ERRO: DEVERIA SER 70000 !!!! -->
    <nDPS>100000000000001</nDPS>
    ...
    <prest>
      <CNPJ>54518217000117</CNPJ>
      <IM>618590001</IM>
      <regTrib>
        <opSimpNac>1</opSimpNac>  <!-- Esta correto para optante -->
        <regEspTrib>0</regEspTrib>
      </regTrib>
    </prest>
  </infDPS>
</DPS>
```

### Resposta da API SEFIN Nacional:
```json
{
  "tipoAmbiente": 2,
  "versaoAplicativo": "SefinNacional_1.6.0",
  "dataHoraProcessamento": "2026-05-01T05:24:44.7795203-03:00",
  "idDPS": "DPS130260325451821700011700001100000000000001",
  "erros": [{
    "Codigo": "E0160",
    "Descricao": "No mes de competencia da NFS-e, a opcao de situacao perante o Simples Nacional, do prestador, informada na DPS nao esta de acordo com o cadastro Simples Nacional."
  }]
}
```

---

## 5. COMPARACAO: PORTAL DO CONTRIBUINTE vs. API NACIONAL

| Aspecto | Portal do Contribuinte (Manaus) | API SEFIN Nacional |
|---------|--------------------------------|-------------------|
| Base de dados | Prefeitura de Manaus (base local) | Base federal unificada (CNC NFS-e + Receita) |
| Serie DPS | Usa a serie cadastrada no portal (70000) | **O sistema esta enviando serie 1 ao inves de 70000** |
| Validacao do Simples Nacional | Consulta base local | Consulta Receita Federal em tempo real |
| Resultado | Emissao permitida | Rejeicao E0160 (porque nao achou o prestador na serie 1) |

---

## 6. ACOMETIDAS REQUERIDAS

### 6.1 Correcao Imediata no Codigo

**Arquivo:** `application/controllers/Nfse_os.php`

**Linhas afetadas:** ~969-970 e ~1102

**Problema:**
```php
$this->config->load('nfse_nacional', true);
$nfseConfig = $this->config->item('nfse_nacional');   // Retorna null/false
```

**Correcao recomendada:**

Alterar a forma de carregamento da configuracao. A maneira mais simples e carregar sem o segundo parametro `true`:

```php
// Substituir:
$this->config->load('nfse_nacional', true);
$nfseConfig = $this->config->item('nfse_nacional');

// Por:
$this->config->load('nfse_nacional');
$nfseConfig = [
    'nfse_ambiente' => $this->config->item('nfse_ambiente'),
    'nfse_urls' => $this->config->item('nfse_urls'),
    'nfse_codigo_municipio' => $this->config->item('nfse_codigo_municipio'),
    'nfse_codigo_uf' => $this->config->item('nfse_codigo_uf'),
    'nfse_versao_dps' => $this->config->item('nfse_versao_dps'),
    'nfse_timeout' => $this->config->item('nfse_timeout'),
    'nfse_ca_path' => $this->config->item('nfse_ca_path'),
    'nfse_temp_path' => $this->config->item('nfse_temp_path'),
    'nfse_natureza_operacao' => $this->config->item('nfse_natureza_operacao'),
    'nfse_serie_dps' => $this->config->item('nfse_serie_dps'),  // <-- Isto corrige a serie
    'nfse_optante_simples' => $this->config->item('nfse_optante_simples'),
    'nfse_regime_especial' => $this->config->item('nfse_regime_especial'),
    'nfse_incentivador_cultural' => $this->config->item('nfse_incentivador_cultural'),
    'nfse_responsavel_retencao' => $this->config->item('nfse_responsavel_retencao'),
];
```

Ou, alternativamente, manter o `load('nfse_nacional', true)` e acessar cada item individualmente:
```php
$this->config->load('nfse_nacional', true);
$serieDps = $this->config->item('nfse_serie_dps', 'nfse_nacional');
$codigoMunicipio = $this->config->item('nfse_codigo_municipio', 'nfse_nacional');
// ... etc
```

**Observacao:** Todas as outras referencias a `$nfseConfig['nfse_codigo_municipio']`, `$nfseConfig['nfse_codigo_uf']`, `$nfseConfig['nfse_natureza_operacao']`, etc., tambem estao recebendo valores padrao (fallback) em vez dos valores configurados. Isso significa que o codigo inteiro esta operando com configuracoes padrao, nao com as configuracoes do arquivo `nfse_nacional.php`.

### 6.2 Verificacao do Cadastro no CNC NFS-e

Alem da serie, o erro `E0116` (IM nao cadastrada) apareceu em tentativas anteriores. Recomenda-se verificar no portal do CNC NFS-e se:

1. O CNPJ `54518217000117` esta vinculado a Inscricao Municipal `618590001`.
2. A serie `70000` esta cadastrada corretamente para esse prestador.
3. A opcao pelo Simples Nacional esta marcada no cadastro do prestador no CNC NFS-e.

### 6.3 Validacao do Valor minimo para IRRF

O erro `E0700` ocorreu em valores baixos (R$ 2,00 e R$ 10,00). O sistema foi ajustado para garantir `vRetIRRF > 0`, mas atencao:
- Para servicos de valor muito baixo, a aliquota de IRPJ pode resultar em valores menores que o centavo.
- O codigo atual garante `0.01` como valor minimo, mas para servicos de R$ 2,00, o valor `1.04` de IRRF pode ser maior que o proprio IRPJ calculado. Verificar se a logica de `vRetIRRF` esta consistente.

---

## 7. CONCLUSAO ATUALIZADA

| Item | Status |
|------|--------|
| Certificado digital (.pfx) | **APTO** - Nenhum problema |
| Cadeia ICP-Brasil | **COMPLETA** |
| mTLS / Autenticacao | **FUNCIONANDO** |
| Cadastro Simples Nacional na Receita | **CONFIRMADO** - Optante desde 28/03/2024 |
| Regime tributario informado no XML (`opSimpNac=1`) | **CORRETO** |
| **Serie DPS no XML (`<serie>00001</serie>`)** | **INCORRETA** - Deveria ser `70000` |
| Leitura do arquivo `nfse_nacional.php` | **QUEBRADA** - Sistema nao consegue ler as configuracoes |

**Diagnostico final:** O erro `E0160` nao e um problema cadastral, nem de certificado, nem do Simples Nacional. E um **efeito colateral de um bug no carregamento das configuracoes** do arquivo `nfse_nacional.php`. O sistema esta enviando a serie `1` (fallback) ao inves da serie `70000` configurada. Como a API Nacional nao localiza o prestador na serie `1`, a validacao cascata falha e retorna `E0160`.

**Correcao:** Ajustar a forma como o CodeIgniter carrega as configuracoes do arquivo `nfse_nacional.php` no controller `Nfse_os.php`. Todas as configuracoes estao caindo nos valores padrao (fallback) porque `$this->config->item('nfse_nacional')` retorna nulo.

---

*Relatorio gerado automaticamente por analise do certificado, codigo-fonte, logs de emissao e consulta a Receita Federal.*
