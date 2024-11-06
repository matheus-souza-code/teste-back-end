# Projeto Laravel com Docker e Horizon

## Pré-requisitos

- **PHP** ^8.1
- **Node.js** >=12
- **Docker**

## Configuração Inicial

1. **Instalação de pacotes:**
   ```bash
   composer install
   npm install

2. **Configuração do Ambiente (.env):**

	Copie o arquivo .env.example e renomeie para .env.

	Atualize as variáveis principais no novo .env:

	```bash
	DB_CONNECTION=mysql
	DB_HOST=mysql
	DB_PORT=3306
	DB_DATABASE=laravel
	DB_USERNAME=sail
	DB_PASSWORD=password

	BROADCAST_DRIVER=log
	CACHE_DRIVER=file
	FILESYSTEM_DISK=local
	QUEUE_CONNECTION=redis
	SESSION_DRIVER=file
	SESSION_LIFETIME=120

	MEMCACHED_HOST=127.0.0.1

	REDIS_HOST=redis
	REDIS_PASSWORD=null
	REDIS_PORT=6379

3. **Iniciar Containers Docker:**

	```bash
	./vendor/bin/sail up

4. **Gerar chave da aplicação:**

	```bash
	./vendor/bin/sail artisan key:generate

5. **Migrar banco de dados:**

	```bash
	./vendor/bin/sail artisan migrate

6. **Executar frontend e Laravel Horizon:**

	Frontend: 
	```bash
	npm run dev
    ```
	Horizon:
    ```bash
	./vendor/bin/sail artisan horizon

7. **Importação de Produtos**
7.1 **Importar todos os produtos:**

	./vendor/bin/sail artisan products:import

7.2 **Importar um produto específico:**

	./vendor/bin/sail artisan products:import --id=1

8 **Execute os testes unitários com:**

	./vendor/bin/sail test