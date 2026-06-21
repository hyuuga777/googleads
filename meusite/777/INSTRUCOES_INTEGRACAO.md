# Guia de Integração: Google Tag Manager & Google Analytics 4

Este guia foi criado para orientar você no passo a passo de configuração das suas contas do Google, obtenção das credenciais e ativação do monitoramento automático da IA no seu site.

---

## 🚀 Passo 1: Criar a Conta do Google Tag Manager (GTM)
O GTM será o container centralizador de todas as suas tags de marketing e rastreamento.

1. Acesse o [Google Tag Manager](https://tagmanager.google.com/).
2. Clique em **Criar Conta**.
3. Insira o nome da sua empresa (ex: `Agência Cyborg`) e escolha o país.
4. Nome do Container: insira o domínio do seu site (ex: `agenciacyborg.com`).
5. Plataforma de Destino: selecione **Web**.
6. Aceite os termos.
7. Após criar, você verá o seu ID do Container no topo da tela, no formato **`GTM-XXXXXXX`**. Guarde este ID.

---

## 📈 Passo 2: Criar a Propriedade do Google Analytics 4 (GA4)
O GA4 registrará todas as visitas, cliques e conversões de leads do seu site.

1. Acesse o [Google Analytics](https://analytics.google.com/).
2. Vá em **Administrador** (ícone de engrenagem no canto inferior esquerdo).
3. Clique em **Criar Conta** (ou **Criar Propriedade** se já tiver uma conta).
4. Nome da propriedade: ex: `Site Cyborg`.
5. Preencha os detalhes do negócio e fuso horário.
6. Em "Coleta de dados", selecione **Web** como plataforma.
7. Insira a URL do seu site e o nome do fluxo (ex: `Fluxo Web Principal`).
8. Clique em **Criar fluxo**.
9. Você receberá um **ID de Medição** no formato **`G-XXXXXXXXXX`**. Guarde este ID.

---

## 🛡️ Passo 3: Autenticar o Google Analytics 4 para a IA
Como a política de segurança da sua organização no Google Cloud bloqueia a criação de chaves de contas de serviço (`iam.disableServiceAccountKeyCreation`), nós configuramos uma alternativa muito mais simples e segura: **reutilizar as credenciais OAuth do Google Ads**.

Dessa forma, o painel do Google Analytics usará o seu próprio usuário para visualizar os dados, sem precisar criar contas de serviço nem chaves JSON de serviço.

### Como configurar em 2 passos:

1. **Ativar as APIs do Google Analytics no Google Cloud Console**:
   * Acesse o [Google Cloud Console](https://console.cloud.google.com/) e certifique-se de selecionar o projeto **`sharp-weft-499123-d4`** no topo da tela.
   * Vá em **APIs e Serviços > Biblioteca**.
   * Pesquise e **Ative** as duas APIs a seguir:
     * 🟢 **Google Analytics Admin API**
     * 🟢 **Google Analytics Data API**

2. **Gerar o Token de Acesso**:
   * Abra um terminal na pasta do projeto e execute o script que preparamos para você:
     ```bash
     python get_analytics_token.py
     ```
   * O script exibirá um link do Google. Copie e cole esse link no seu navegador.
   * Faça login com a sua conta do Google que tem acesso à propriedade do Google Analytics 4 (Propriedade `542079942`).
   * Após autorizar, o navegador tentará redirecionar para `http://localhost`. Copie o link completo ou o código da barra de endereços, cole de volta no terminal e aperte **Enter**.
   * O script gerará automaticamente o arquivo `google-analytics-credentials.json` no formato correto.

---

## ⚙️ Passo 4: Atualizar as Configurações do MCP
O arquivo de configuração do MCP já está preparado para receber as suas chaves e IDs reais no seguinte caminho:
`C:\Users\i\.gemini\antigravity-ide\mcp_config.json`

Abra este arquivo e edite a seção `"google-analytics-mcp"` com o seu ID de propriedade real do GA4:
```json
    "google-analytics-mcp": {
      "command": "uvx",
      "args": [
        "analytics-mcp"
      ],
      "env": {
        "GOOGLE_APPLICATION_CREDENTIALS": "Y:\\PROJETOS\\google ads\\v1\\google-analytics-credentials.json",
        "GOOGLE_PROJECT_ID": "SEU_PROJECT_ID_REAL",
        "GA_PROPERTY_ID": "SEU_PROPERTY_ID_REAL"
      }
    }
```

---

## 🛠️ Passo 5: Sincronizar as Tags no Site via Terminal
Criamos um script automático para injetar as tags do GTM e GA4 em todas as páginas públicas do site de uma só vez, eliminando o trabalho manual.

Quando você tiver seus IDs reais do Google, abra o terminal na pasta do projeto e execute:
```bash
python meusite/backend/inject_tags.py --gtm GTM-SEUIDREAL --ga4 G-SEUIDREAL
```

### Exemplo de execução:
```bash
python meusite/backend/inject_tags.py --gtm GTM-ABC1234 --ga4 G-XYZ56789
```

O script cuidará de limpar as tags de demonstração antigas e inserir as novas no local exato de cada um dos 23 arquivos HTML do site.
O painel administrativo (`technical.html`) atualizará os status automaticamente e passará a reportar tudo como **"Instalada"** e operando com baixa latência!

---

## 🎯 Passo 6: Configurar Conversões Automáticas (Google Ads & GTM)
Junto com o GTM, nosso injetor instalou um **rastreador de conversões inteligente** em todas as páginas. Ele captura cliques em botões e envios de formulários e os repassa ao GTM usando eventos personalizados no `dataLayer`.

Para conectar esses eventos ao **Google Ads**:

1. **Cliques no botão de WhatsApp**:
   * O script dispara o evento personalizado: **`click_whatsapp`**.
   * No GTM: Crie um Acionador (Trigger) do tipo **Evento Personalizado**, com o nome exato: `click_whatsapp`.
   * Crie uma Tag de **Acompanhamento de Conversões do Google Ads**, insira o seu ID de Conversão e a Label correspondente (gerada na criação da conversão de WhatsApp no Google Ads), e defina o acionador para disparar neste evento.

2. **Envio de Formulários de Contato (Leads)**:
   * O script dispara o evento personalizado: **`lead_conversion`**.
   * No GTM: Crie um Acionador (Trigger) do tipo **Evento Personalizado**, com o nome exato: `lead_conversion`.
   * Crie uma Tag de **Acompanhamento de Conversões do Google Ads**, configure com a Label de conversão de Leads correspondente, e vincule a este acionador.

Dessa forma, qualquer novo botão ou formulário que você criar no futuro será rastreado automaticamente, sem necessidade de alterar código!
