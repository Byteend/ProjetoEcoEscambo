# Backend (PHP) para ProjetoEcoEscambo

Como usar (local, SQLite):

1. Aceda à pasta `backend` e execute a migração:

```bash
php migrate.php
```

2. Os endpoints principais (retornam JSON):

Nota: os endpoints foram reorganizados em `backend/api/` e os ficheiros de suporte estão em `backend/lib/`. O ficheiro de migração está em `backend/migrations/migrations.sql` e o runner `migrate.php` permanece em `backend/`.

3. Observações:
- A aplicação usa SQLite (ficheiro em `backend/data/database.sqlite`).
- Não há envio real de e-mail; `register.php` retorna o link de ativação para fins de teste.
- Para autenticação, use sessões PHP (cookies do browser). Use chamadas via formulário ou fetch/XHR mantendo cookies.
