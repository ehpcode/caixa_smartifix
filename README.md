# Caixa SmartiFix 📱💼

O **Caixa SmartiFix** é um sistema completo e robusto de controle de fluxo de caixa, gestão financeira, ordens de serviço (OS) e controle de vendas, desenvolvido especificamente para atender às necessidades de uma assistência técnica de dispositivos eletrônicos (como celulares, tablets, etc.). 

O projeto é estruturado sob uma arquitetura **MVC (Model-View-Controller)** personalizada em PHP puro, sem dependência de frameworks externos pesados, garantindo alta velocidade de carregamento, facilidade de manutenção e segurança.

---

## 🚀 Principais Funcionalidades

### 1. 📊 Dashboard Inteligente
*   Resumo financeiro diário, mensal e por período.
*   Gráficos e estatísticas de vendas, faturamento de ordens de serviço (OS) e despesas.
*   Indicadores visuais de saldo consolidado de contas financeiras.

### 2. 💰 Controle de Caixa Diário
*   **Fluxo de Caixa:** Abertura e fechamento de caixa diário por operador.
*   **Saldos por Conta:** Fechamento preciso que computa os saldos iniciais e finais em cada conta.
*   **Suprimentos e Sangrias:** Registro de entradas de troco e retiradas de valores com justificativa.
*   **Reabertura de Caixa:** Permissão especial de reabertura com justificativa obrigatória registrada em logs de auditoria.

### 3. 💸 Movimentações Financeiras
*   Registro detalhado de **Entradas** (Venda de produtos, serviços de OS, serviços avulsos, xerox, aportes) e **Saídas** (Custos com fornecedores, contas de luz/água, comissões de funcionários, retiradas).
*   Filtros avançados por data, tipo, conta, natureza financeira e forma de pagamento.
*   Cancelamento de movimentações integradas ao controle de saldos.

### 4. 🛠️ Ordens de Serviço (OS) e Serviços
*   Associação direta de recebimentos de serviços com a respectiva Ordem de Serviço.
*   **Custos Operacionais:** Gestão fina de custos por OS (peças de reposição, fornecedor específico, mão de obra) para cálculo automático da margem de lucro real de cada serviço.

### 5. 📦 Vendas
*   Registro rápido de vendas de produtos, acessórios ou aparelhos celulares/tablets diretamente associados ao caixa ativo.

### 6. 👤 Perfis de Acesso e Permissões Granulares
*   Sistema dinâmico de controle de acesso baseado em JSON.
*   Perfis configuráveis (Administrador, Técnico, Atendente, Caixa).
*   Permissões detalhadas por ação (ex: `caixa:abrir`, `caixa:fechar`, `caixa:reabrir`, `sangria:criar`, `movimentacao:cancelar`).

### 7. 🛡️ Logs de Auditoria
*   Histórico detalhado de todas as operações críticas do sistema.
*   Grava dados anteriores e novos em formato JSON (facilitando auditorias de edições ou cancelamentos).
*   Registro automático do IP de origem e do usuário que realizou a ação.

---

## 🛠️ Tecnologias Utilizadas

*   **Backend:** PHP (PHP >= 7.4 ou 8.x) com arquitetura MVC nativa.
*   **Banco de Dados:** MySQL / MariaDB (com chaves estrangeiras e relacionamentos lógicos estruturados).
*   **Frontend:** HTML5, CSS3 personalizado (visual moderno e responsivo) com auxílio de Bootstrap e FontAwesome.
*   **Servidor:** Apache (redirecionamento de rotas amigáveis via `.htaccess`).

---

## 📂 Estrutura do Projeto

```text
caixa_smartifix/
├── app/                  # Núcleo da Aplicação (MVC)
│   ├── Controllers/     # Controladores (Regras de Negócio e Rotas)
│   ├── Core/            # Classes Base (Router, Controller, Model, Database)
│   ├── Models/          # Modelos de Acesso ao Banco de Dados (PDO)
│   └── Views/           # Telas e Templates (Interface do Usuário)
├── config/               # Configurações do Sistema
│   └── database.php     # Conexão com o banco e constantes globais
├── public/               # Pasta Pública (Ponto de entrada do Servidor)
│   ├── assets/          # Arquivos de Estilo (CSS, JS, Imagens, FontAwesome)
│   ├── .htaccess        # Regras de URL amigável do Apache
│   └── index.php        # Bootstrapper e definição de rotas
├── database.sql          # Dump do banco de dados MySQL
└── README.md             # Documentação do projeto
```

---

## 🖥️ Requisitos do Sistema

1.  Servidor Web local como **XAMPP**, **WampServer**, **Laragon** ou similar.
2.  **PHP** na versão **7.4** ou superior (testado e compatível com PHP 8.x).
3.  Servidor de banco de dados **MySQL** / **MariaDB**.
4.  Módulo **`mod_rewrite`** habilitado no Apache (para funcionamento das URLs amigáveis).

---

## ⚙️ Instalação e Configuração

### Passo 1: Clonar ou Copiar o Projeto
Mova a pasta `caixa_smartifix` para o diretório de arquivos públicos do seu servidor local (geralmente `htdocs` no XAMPP ou `www` no WampServer).

### Passo 2: Importar o Banco de Dados
1.  Acesse o seu gerenciador de banco de dados (como **phpMyAdmin** em `http://localhost/phpmyadmin`).
2.  Crie um banco de dados chamado `caixa_smartifix` com a codificação `utf8_general_ci` (ou deixe o script criar automaticamente).
3.  Importe o arquivo [database.sql] localizado na raiz do projeto. Ele criará as tabelas necessárias e preencherá os dados iniciais.

### Passo 3: Configurar a Conexão
Abra o arquivo de configuração de banco de dados em [database.php] e ajuste as credenciais de acordo com o seu ambiente local:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Insira a senha do MySQL se houver
define('DB_NAME', 'caixa_smartifix');
define('BASE_URL', 'http://localhost/caixa_smartifix/public');
```

---

## 🔐 Credenciais de Acesso Padrão

Após a importação do banco de dados, utilize as seguintes credenciais para fazer o primeiro login:

*   **E-mail:** `admin@dominio.com.br`
*   **Senha:** `password`. O sistema utiliza hash de segurança `bcrypt` no banco de dados. Caso necessite redefinir a senha do administrador principal para um valor conhecido (como `admin` ou similar), você pode rodar o script abaixo para gerar o hash ou atualizar via SQL.

### Como Redefinir a Senha do Administrador

Se você precisar redefinir a senha do administrador padrão para `123456` (ou qualquer outra senha de sua preferência), execute os seguintes passos:

1. Gere o hash de senha utilizando o PHP via terminal:
   ```bash
   php -r "echo password_hash('sua_senha_aqui', PASSWORD_DEFAULT);"
   ```
2. Atualize o banco de dados via SQL no banco `caixa_smartifix`:
   ```sql
   UPDATE `usuarios` 
   SET `senha` = 'HASH_GERADO_NO_PASSO_1' 
   WHERE `email` = 'admin@dominio.com.br';
   ```

---

## 🎯 Rotas e Endpoints Principais

A definição de rotas amigáveis é realizada no arquivo [public/index.php] através de um roteador simples:

*   `GET /` - Tela de Login do operador.
*   `GET /dashboard` - Dashboard principal com gráficos e estatísticas.
*   `GET /caixa` - Painel de controle do caixa diário (abertura, fechamento, saldo).
*   `GET /movimentacoes` - Histórico completo de entradas e saídas de caixa.
*   `GET /os` - Controle de ordens de serviço e seus custos operacionais.
*   `GET /vendas` - Histórico e novos lançamentos de vendas rápidas.
*   `GET /config/perfis` - Gerenciamento de níveis de acesso e permissões.

---

## 🔒 Auditoria e Segurança

O sistema se destaca pela transparência administrativa:
*   A tabela `logs_auditoria` armazena o estado anterior e posterior das movimentações financeiras ao serem editadas ou canceladas.
*   O controle de permissões é robusto: caso o usuário tente acessar um recurso não autorizado, ele será redirecionado ou receberá um erro HTTP `403 Forbidden` (em requisições AJAX).