# MarkSports — Back-end

API Rest do sistema MarkSports.

## Tecnologias

- Laravel
- Composer
- MySQL
- PestV

## Como rodar localmente

### 1. Clone o repositório

```bash
git clone <https://github.com/MiguelMartini/MarkSPORTS-BackEnd.git>
```

### 2. Acesse a pasta do projeto

```bash
cd marksportsbackend
```

### 3. Instale as dependências
_Tenha instalado o composer para seguir_

```bash
composer install
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
