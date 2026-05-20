# MarkSports — Back-end

API Rest do sistema MarkSports.

## Tecnologias

- Laravel
- MySQL
- PestV

## Como rodar localmente

### 1. Clone o repositório

```bash
git clone <https://github.com/MiguelMartini/MarkSPORTS-BackEnd.git>
```

### 2. Acesse a pasta do projeto

```bash
cd marksports
```

### 3. Instale as dependências

```bash
compser install
cp .env.example .env
php artisan key:generate
```

### 5. Realize as Migrations
```bash
php artisan migrate
```

### 5. Inicie o servidor de desenvolvimento

```bash
php artisan serve
```
