# 🌟 Projeto Marketplace Connector: Configuração Simplificada e Operação Eficiente 🚀

Bem-vindo ao guia completo do Projeto Marketplace Connector! Este documento detalha os passos para configurar o ambiente de desenvolvimento com Docker, iniciar os serviços essenciais e como realizar o debug de importações. Vamos nessa! 💪

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
* [🎉 Próximos Passos](#-próximos-passos)

---

## 🌍 Visão Geral

O Marketplace Connector é uma aplicação robusta projetada para integrar diferentes marketplaces. Este guia foca em colocar seu ambiente de desenvolvimento em funcionamento rapidamente usando Docker.

---

## 📋 Pré-requisitos

Antes de começar, certifique-se de que você tem os seguintes softwares instalados:

* **Docker:** [Instruções de Instalação](https://docs.docker.com/get-docker/)
* **Docker Compose (Opcional, mas recomendado):** [Instruções de Instalação](https://docs.docker.com/compose/install/)
* Um arquivo `mocketplace.json` na raiz do seu projeto para o serviço de mock.

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

## 🎉 Pronto!

Seu ambiente Docker está configurado e os principais serviços estão funcionando. Agora é só codar e brilhar! 💻✨

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