# 🌟 Projeto Marketplace Connector: Configuração Simplificada 🚀

Bem-vindo ao guia de configuração do Projeto Marketplace Connector! Aqui você encontrará as etapas necessárias para configurar e iniciar rapidamente o ambiente Docker. Vamos nessa! 💪

---

## 📦 1. Crie a Imagem do Backend

Vamos começar construindo a imagem Docker para o backend! 🛠️

```bash
docker build -t marketplace-connector -f Dockerfile.dev .
```

---

## 🛳️ 2. Inicie o Container do Backend

Hora de iniciar o backend! Ele será acessível na porta **8000**. 🌐

```bash
docker run --name marketplace-connector -d -p 8000:8000 -v $HOME/.ssh:/root/.ssh -v $(pwd):/application marketplace-connector
```

---

## 🔗 3. Crie uma Rede Docker

Vamos criar uma rede dedicada para nossos containers se comunicarem. 📡

```bash
docker network create marketplace-network
```

---

## 🗄️ 4. Inicie os Containers do Banco de Dados

### 🐘 Container PostgreSQL:
Configure e inicie o banco de dados com as credenciais necessárias. 🔒

```bash
docker run -d --name db-marketplace --net marketplace-network -e POSTGRES_PASSWORD=root postgres
```

---

## 🌐 5. Conecte o Backend à Rede

Agora, conecte o backend à rede criada no passo 3. 🚦

```bash
docker network connect marketplace-network marketplace-connector
```

---

🎉 **Pronto!** Seu ambiente Docker está configurado e funcionando. Agora é só codar e brilhar! 💻✨