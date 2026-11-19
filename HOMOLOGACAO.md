# Ambiente de homologação

Endereço: **https://portaltestefront.comunidadeppg.com.br** (portal na raiz, API em `/api`, admin em `/admin`).
Tudo fica atrás de uma senha do navegador (usuário `homolog`) e fora do Google.

## Onde fica
- Código: `/home/deploy/comunidade_ppg_homolog` — git worktree do mesmo repositório, branch `homolog`.
- Produção continua em `/home/deploy/comunidade_ppg` (branch `main`). Nada aqui toca os containers, o banco ou os arquivos de produção.
- Containers: `comunidade_homolog_{api,api_nginx,frontend,mysql}` (`docker-compose.homolog.yml`).

## O que é diferente da produção (`cria-da-comunidade-api/.env`, fora do git)
- `APP_ENV=staging`, banco próprio (`comunidade_homolog_mysql`).
- E-mails não saem: `MAIL_MAILER=log` (aparecem em `storage/logs/laravel.log`).
- Facebook desligado (sem token): "publicar no Facebook" só retorna erro.
- Todos os usuários copiados da produção têm a mesma senha de teste.

## Fluxo de trabalho
1. Altere o código **aqui** (branch `homolog`).
2. Backend (PHP) vale na hora. Frontend: `docker compose -f docker-compose.homolog.yml up -d --build frontend`.
3. Migrations: `docker exec comunidade_homolog_api php artisan migrate --force`.
4. Testou e aprovou: commit no `homolog`, merge no `main`, e em `/home/deploy/comunidade_ppg` faça `git pull` + build do frontend de produção.

## Atualizar os dados com uma nova cópia da produção
```bash
docker exec comunidade_mysql sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --no-tablespaces comunidade' \
  | docker exec -i comunidade_homolog_mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" comunidade'
rsync -a ../comunidade_ppg/cria-da-comunidade-api/storage/app/ cria-da-comunidade-api/storage/app/
```
Depois, troque de novo as senhas dos usuários (a cópia traz as senhas reais) e apague `personal_access_tokens`.

## Como o acesso é protegido
- Páginas, `/admin`, `/storage`: senha do navegador (`docker/homolog/.htpasswd`, fora do git).
- `/api`: o portal usa `Authorization: Bearer` (login do usuário), que conflita com a senha do navegador.
  Por isso a `/api` exige um cookie secreto que o nginx entrega a quem passou pela senha
  (`docker/homolog/frontend.conf`, fora do git; modelo em `frontend.conf.example`).
