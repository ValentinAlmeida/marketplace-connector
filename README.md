# 🌟 Projeto Marketplace Connector: Configuração Simplificada e Operação Eficiente 🚀

Bem-vindo ao guia completo do Projeto Marketplace Connector! Este documento detalha os passos para configurar o ambiente de desenvolvimento com Docker, iniciar os serviços essenciais, como realizar o debug de importações e como interagir com a API. Vamos nessa! 💪

---

## 📚 Índice

* [Visão Geral](#-visão-geral)
* [Pré-requisitos](#-pré-requisitos)
* [🚀 Configuração do Ambiente Docker](#-configuração-do-ambiente-docker)
  * [1. Crie a Imagem do Backend](#-1-crie-a-imagem-do-backend)
  * [2. Inicie o Container do Backend](#-2-inicie-o-container-do-backend)
  * [3. Crie uma Rede Docker Dedicada](#-3-crie-uma-rede-docker-dedicada)
  * [4. Inicie os Containers do Banco de Dados](#-4-inicie-os-containers-do-banco-de-dados)
    * [🐘 PostgreSQL](#-postgresql)
  * [5. Conecte o Backend à Rede](#-5-conecte-o-backend-à-rede)
  * [6. Crie o Container do Redis](#-6-crie-o-container-do-redis)
  * [7. Crie o Container do Mockoon (Serviço de Mock)](#-7-crie-o-container-do-mockoon-serviço-de-mock)
* [🛠️ Operações Comuns](#️-operações-comuns)
  * [🔍 Debugando Importações](#-debugando-importações)
* [📖 Documentação da API](#-documentação-da-api)
  * [Agendar Nova Importação](#agendar-nova-importação)
* [📈 Pontos de Melhoria e Próximas Etapas (Checklist)](#-pontos-de-melhoria-e-próximas-etapas-checklist)
* [🎉 Ambiente Pronto!](#-ambiente-pronto)

---

## 🌍 Visão Geral

O Marketplace Connector é uma aplicação robusta projetada para integrar diferentes marketplaces. Este guia foca em colocar seu ambiente de desenvolvimento em funcionamento rapidamente usando Docker e detalha como interagir com sua API.

---

## 📋 Pré-requisitos

Antes de começar, certifique-se de que você tem os seguintes softwares instalados:

* **Docker:** [Instruções de Instalação](https://docs.docker.com/get-docker/)
* **Docker Compose (Opcional, mas recomendado):** [Instruções de Instalação](https://docs.docker.com/compose/install/)
* Um arquivo `mocketplace.json` na raiz do seu projeto para o serviço de mock.
* Uma ferramenta para realizar requisições HTTP (como cURL, Postman, Insomnia).

---

## 🚀 Configuração do Ambiente Docker

Siga estas etapas para configurar todos os serviços necessários.

### 🛠️ 1. Crie a Imagem do Backend

Vamos começar construindo a imagem Docker para a sua aplicação backend. Este comando utiliza o `Dockerfile.dev` para criar uma imagem otimizada para desenvolvimento.

```bash
docker build -t marketplace-connector -f Dockerfile.dev .
```
* `-t marketplace-connector`: Define o nome e a tag da imagem.
* `-f Dockerfile.dev`: Especifica o Dockerfile a ser usado.
* `.`: Define o contexto de build como o diretório atual.

---

### 🛳️ 2. Inicie o Container do Backend

Com a imagem criada, vamos iniciar o container do backend. Ele ficará acessível na porta **8000** da sua máquina local.

```bash
docker run --name marketplace-connector -d -p 8000:8000 -v $HOME/.ssh:/root/.ssh -v $(pwd):/application marketplace-connector
```
* `--name marketplace-connector`: Nomeia o container para fácil referência.
* `-d`: Executa o container em modo detached (em segundo plano).
* `-p 8000:8000`: Mapeia a porta 8000 do host para a porta 8000 do container.
* `-v $HOME/.ssh:/root/.ssh`: Monta suas chaves SSH no container (útil para dependências privadas).
* `-v $(pwd):/application`: Monta o diretório atual do projeto no diretório `/application` dentro do container, permitindo live-reloading das suas alterações de código.

---

### 🔗 3. Crie uma Rede Docker Dedicada

Para que os containers possam se comunicar de forma isolada e segura, criaremos uma rede Docker específica para este projeto.

```bash
docker network create marketplace-network
```
* `marketplace-network`: Nome da rede criada.

---

### 🗄️ 4. Inicie os Containers do Banco de Dados

#### 🐘 PostgreSQL:

Configure e inicie o container do banco de dados PostgreSQL. As credenciais são definidas através de variáveis de ambiente.

```bash
docker run -d --name db-marketplace --net marketplace-network -e POSTGRES_PASSWORD=root postgres
```
* `--name db-marketplace`: Nome do container do banco de dados.
* `--net marketplace-network`: Conecta o container à rede criada anteriormente.
* `-e POSTGRES_PASSWORD=root`: Define a senha do superusuário `postgres` como `root`. **Atenção:** Use senhas seguras em ambientes de produção.
* `postgres`: Utiliza a imagem oficial do PostgreSQL.

---

### 🔗 5. Conecte o Backend à Rede

Agora, vamos garantir que o container do backend possa se comunicar com os outros serviços (como o banco de dados) conectando-o à `marketplace-network`.

```bash
docker network connect marketplace-network marketplace-connector
```

---

### ♨️ 6. Crie o Container do Redis

O Redis é utilizado para caching e gerenciamento de filas. Vamos criar um container para ele e conectá-lo à nossa rede.

```bash
docker run -d --name redis \
  --network marketplace-network \
  -p 6379:6379 \
  -v redis_data:/data \
  redis:alpine
```
* `--name redis`: Nome do container do Redis.
* `--network marketplace-network`: Conecta o container à rede da aplicação.
* `-p 6379:6379`: Mapeia a porta padrão do Redis.
* `-v redis_data:/data`: Cria um volume chamado `redis_data` para persistir os dados do Redis.
* `redis:alpine`: Utiliza a imagem oficial do Redis baseada no Alpine Linux (mais leve).

---

### 🎭 7. Crie o Container do Mockoon (Serviço de Mock)

Para simular APIs externas ou endpoints durante o desenvolvimento, utilizaremos o Mockoon. Este container servirá os mocks definidos no arquivo `mocketplace.json`.

```bash
docker run -d --name mockoon-service \
  --network marketplace-network \
  --mount type=bind,source=./mocketplace.json,target=/data/mocketplace.json,readonly \
  -p 3000:3000 \
  mockoon/cli:latest -d /data/mocketplace.json -p 3000
```
* `--name mockoon-service`: Nome do container do Mockoon.
* `--network marketplace-network`: Conecta o container à rede da aplicação.
* `--mount type=bind,source=./mocketplace.json,target=/data/mocketplace.json,readonly`: Monta o arquivo `mocketplace.json` do seu host (localizado na raiz do projeto) para dentro do container em modo somente leitura. **Certifique-se de que este arquivo existe!**
* `-p 3000:3000`: Mapeia a porta 3000 do host para a porta 3000 do container, onde o Mockoon estará escutando.
* `mockoon/cli:latest`: Utiliza a imagem oficial da CLI do Mockoon.
* `-d /data/mocketplace.json`: Informa ao Mockoon qual arquivo de dados (mock) utilizar dentro do container.
* `-p 3000`: Especifica a porta que o Mockoon deve usar dentro do container.

---

## 🛠️ Operações Comuns

### 🔍 Debugando Importações

Se você precisar iniciar e monitorar uma importação específica manualmente (por exemplo, para depurar um problema ou testar uma nova funcionalidade), você pode usar o comando Artisan `import:start`.

**Para executar o comando de importação:**

1.  Acesse o shell do container do backend:
    ```bash
    docker exec -it marketplace-connector sh
    ```

2.  Dentro do container, execute o comando Artisan:
    ```bash
    php artisan import:start <ID_DA_IMPORTACAO> [opções]
    ```

    **Parâmetros:**
    * `<ID_DA_IMPORTACAO>`: (Obrigatório) O ID numérico da importação que você deseja processar.
    * `--timeout=<segundos>`: (Opcional) Tempo máximo em segundos para esperar pela conclusão da importação. O padrão é `300` segundos (5 minutos).
    * `--poll=<segundos>`: (Opcional) Intervalo em segundos entre as verificações de status da importação. O padrão é `5` segundos.

    **Exemplo:**
    Para iniciar a importação com ID `123`, com um timeout de 10 minutos e verificando o status a cada 10 segundos:
    ```bash
    php artisan import:start 123 --timeout=600 --poll=10
    ```

    **Importante sobre as Filas:**
    O comando `import:start` irá despachar um job para a fila `imports_control`. Para que a importação seja processada, você **precisa ter workers de fila rodando**. Em um terminal separado (ou dentro de outra sessão `docker exec`), inicie os workers para as filas relevantes:
    ```bash
    # Dentro do container do backend
    php artisan queue:work --queue=imports_control,imports_ids,imports_details,imports_send,default
    ```
    O comando `import:start` irá monitorar o progresso e exibir informações como status, total de itens, itens processados e falhados. Ele também exibirá detalhes completos da importação ao final ou em caso de erro/timeout.

    **Verificando Logs:**
    Os logs da aplicação, incluindo detalhes de erros de importação, podem ser encontrados no sistema de logging configurado (geralmente em `storage/logs/laravel.log` dentro do container ou na saída do Docker se configurado para tal). O comando também loga informações usando `Log::info` e `Log::error`.

    **Estados Finais:**
    * `COMPLETED`: A importação foi concluída com sucesso.
    * `FAILED`: A importação falhou.
    * Outros status podem indicar que a importação terminou, mas não necessariamente com sucesso completo (ex: `PARTIALLY_COMPLETED` se existir tal status).

    O comando `displayImportDetails` é chamado internamente para mostrar um resumo no console ao final da execução.

---

## 📖 Documentação da API

Esta seção detalha como interagir com os endpoints da API do Marketplace Connector.

### Agendar Nova Importação

* **Endpoint:** `POST /api/imports`
    *(Nota: O prefixo `/api` é comum em aplicações Laravel. Ajuste conforme a configuração do seu projeto.)*
* **Método:** `POST`
* **Descrição:** Agenda uma nova importação de dados para ser processada posteriormente pelo sistema.
* **Autenticação:** (Verifique se há middlewares de autenticação globais ou específicos para esta rota. O `ImportCreateRequest` em si permite acesso não autenticado com `authorize(): bool { return true; }`, mas a autenticação pode ser tratada em um nível anterior.)

* **Corpo da Requisição (`application/json`):**
    ```json
    {
        "description": "Importação de produtos da coleção de inverno",
        "scheduled_at": "2025-07-15 10:00:00"
    }
    ```
    **Campos:**
    * `description` (string, opcional, max: 255): Uma descrição textual para identificar a importação. Se não fornecido, será nulo.
    * `scheduled_at` (string, obrigatório): Data e hora em que a importação deve ser agendada para execução.
        * **Formato Requerido:** Deve corresponder ao formato definido na constante `App\Constants\Format::DATE_TIME` (por exemplo, `Y-m-d H:i:s`). Consulte esta constante no código para o formato exato.
        * **Validação:** O valor deve ser uma data/hora válida e igual ou posterior à data/hora atual no momento da requisição.

* **Exemplo de Requisição (usando cURL):**
    ```bash
    curl -X POST http://localhost:8000/api/imports \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "description": "Importação de novos usuários - Maio/2025",
        "scheduled_at": "2025-05-30 14:30:00"
    }'
    ```
    *(Lembre-se de ajustar `http://localhost:8000` se o seu backend estiver rodando em uma porta ou host diferente.)*

* **Resposta de Sucesso:**
    * **Código:** `201 Created`
    * **Corpo:** O corpo da resposta será uma string JSON contendo a mensagem de sucesso.
        ```json
        "Importação agendada com sucesso!"
        ```

* **Respostas de Erro Comuns:**
    * **Código:** `422 Unprocessable Entity`
        * **Descrição:** Ocorre se os dados enviados na requisição falharem nas regras de validação.
        * **Corpo (Exemplo):**
            ```json
            {
                "message": "The given data was invalid. (Ou uma mensagem de erro traduzida)",
                "errors": {
                    "scheduled_at": [
                        "O campo scheduled_at deve ser uma data igual ou posterior a agora.",
                        "O campo scheduled_at não corresponde ao formato Y-m-d H:i:s."
                    ],
                    "description": [
                        "O campo description não pode ser superior a 255 caracteres."
                    ]
                }
            }
            ```
    * **Outros Códigos:**
        * `401 Unauthorized` / `403 Forbidden`: Se a autenticação for necessária e falhar.
        * `500 Internal Server Error`: Em caso de erros inesperados no servidor.

---

## 📈 Pontos de Melhoria e Próximas Etapas (Checklist)

Este projeto está em constante evolução. Aqui estão alguns pontos que podem ser considerados para futuras melhorias e implementações:

* [ ] **Melhorias de Codigo:**
    * [ ] Utilizar UUID ao inves de Identificadores na camada interna do servidor, e utilizar o ID apenas para relacionamento.
* [ ] **Utilizar Docker Compose:**
    * [ ] Criar um arquivo `docker-compose.yml` para orquestrar todos os serviços (backend, banco de dados, Redis, Mockoon).
    * [ ] Simplificar os comandos de `build` e `run` para um único `docker-compose up`.
    * [ ] Facilitar a configuração de rede e volumes.
* [ ] **Gerenciamento de Configuração e Segredos:**
    * [ ] Externalizar configurações sensíveis (como senhas de banco de dados) do `Dockerfile` e comandos `run` para variáveis de ambiente em um arquivo `.env` (usado pelo Docker Compose) ou um sistema de gerenciamento de segredos (como HashiCorp Vault, AWS Secrets Manager, etc.).
    * [ ] Criar arquivos de configuração de exemplo (ex: `.env.example`).
* [ ] **Otimização do Dockerfile e Imagens:**
    * [ ] Implementar multi-stage builds no `Dockerfile.dev` e criar um `Dockerfile` otimizado para produção (menor tamanho, menos camadas, remoção de dependências de desenvolvimento).
    * [ ] Analisar e reduzir o tamanho final das imagens Docker.
* [ ] **Testes Automatizados:**
    * [ ] Configurar e integrar testes unitários.
    * [ ] Implementar testes de integração para os principais fluxos.
    * [ ] Adicionar um script ou comando para rodar os testes facilmente no ambiente Docker.
* [ ] **Integração Contínua / Entrega Contínua (CI/CD):**
    * [ ] Configurar um pipeline de CI (ex: GitHub Actions, GitLab CI, Jenkins) para buildar e testar a aplicação automaticamente a cada push/merge.
    * [ ] Configurar um pipeline de CD para deploy automatizado em ambientes de staging/produção.
* [ ] **Logging e Monitoramento Avançado:**
    * [ ] Centralizar logs dos containers (ex: ELK Stack, Grafana Loki, Datadog).
    * [ ] Adicionar métricas de aplicação e monitoramento de performance (ex: Prometheus, Grafana, New Relic).
* [ ] **Documentação Detalhada:**
    * [ ] Documentar a arquitetura da aplicação.
    * [ ] Detalhar as principais APIs e seus endpoints (talvez usando Swagger/OpenAPI).
    * [ ] Criar guias para troubleshooting de problemas comuns.
* [ ] **Segurança:**
    * [ ] Realizar varreduras de vulnerabilidades nas imagens Docker e dependências.
    * [ ] Implementar boas práticas de segurança no código da aplicação.
* [ ] **Linters e Formatadores de Código:**
    * [ ] Integrar ferramentas como PHP CS Fixer, ESLint/Prettier (se houver frontend) para garantir a consistência do código.
    * [ ] Adicionar hooks de pre-commit para rodar linters/formatadores automaticamente.
* [ ] **Melhorar o Comando `import:start`:**
    * [ ] Adicionar opção para reprocessar apenas itens falhados de uma importação anterior.
    * [ ] Permitir a especificação de mais de uma fila para os workers diretamente no comando de monitoramento ou como sugestão.

---

## 🎉 Ambiente Pronto!

Seu ambiente Docker está configurado, os principais serviços estão funcionando e você tem a documentação inicial para interagir com a API. Agora é só codar e brilhar! 💻✨

Lembre-se de verificar os logs dos containers caso encontre algum problema:
```bash
docker logs <nome_do_container>
```
Exemplo:
```bash
docker logs marketplace-connector
docker logs db-marketplace
docker logs redis
docker logs mockoon-service
```

Happy coding! 🚀