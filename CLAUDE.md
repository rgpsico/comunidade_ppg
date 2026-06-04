# Cria da Comunidade — Contexto do Projeto

## O que é

Plataforma comunitária (tipo "favela conectada") com profissionais, eventos, projetos sociais, vagas, lojas e marketplace. Deployed em produção.

## Estrutura de pastas

```
comunidadeppg/
├── cria-da-comunidade/        # Frontend Vue 3
├── cria-da-comunidade-api/    # Backend Laravel
├── docker/                    # Configs nginx, Dockerfile
└── docker-compose.yml
```

---

## Backend — Laravel

- **Laravel** 13.12 + **Filament v3** (admin panel)
- **PHP** 8.4 (dentro do container Docker)
- **MySQL** 8.0
- **Resend** para e-mails (candidatura de vagas)
- Admin em: `https://api.comunidadeppg.com.br/admin`
- Admin login: `admin@criadacomunidade.com` / `password`

### Modelos principais
| Model | Tabela |
|---|---|
| Profissional | profissionais |
| Evento | eventos |
| Projeto | projetos |
| ProjetoAtividade | projeto_atividades |
| ProjetoMembro | projeto_membros |
| Vaga | vagas |
| Loja | lojas |
| Produto | produtos |
| Comunidade | comunidades |
| User | users |

### Regras importantes do Filament v3
- ❌ `BadgeColumn` não existe → usar `TextColumn->badge()`
- ❌ `heroicon-o-crown` não existe → usar `heroicon-o-star`
- ❌ `Tables\Actions\Action` não existe → usar `Actions\Action`
- ❌ `Tables\Actions\BulkAction` não existe → usar `Actions\BulkAction`
- ✅ RelationManagers ficam em `app/Filament/Resources/{Model}Resource/RelationManagers/`
- ✅ Sections usam `Filament\Schemas\Components\Section`

### Migrations
- Prefixo de data: `2026_06_02_000X_...`
- Sempre rodar no container: `docker compose exec -T api php artisan migrate --force`

---

## Frontend — Vue 3

- **Vue 3** + **Pinia** + **TypeScript** estrito
- **Vite** para build
- SPA (Single Page App) — sem router, navegação via `useUiStore`
- Pasta: `cria-da-comunidade/src/`

### Stores
| Store | Arquivo |
|---|---|
| `useUiStore` | `stores/ui.ts` — navegação, modais, filtros |
| `useDataStore` | `stores/data.ts` — todos os dados da API, mappers |
| `useAuthStore` | `stores/auth.ts` — login, user, token |

### Padrão de navegação
```typescript
ui.goTo('profissionais')   // muda a view
ui.openPro(pro)            // abre detalhe
ui.selectedLoja            // item selecionado
```

### API
- Base URL: `https://api.comunidadeppg.com.br/api`
- Serviço: `src/services/api.ts`
- Métodos: `api.get`, `api.post`, `api.put`, `api.delete`, `api.postForm`, `api.patchForm`
- Auth: Bearer token no localStorage (`auth_token`)

### Tipos principais: `src/types/index.ts`
`Pro`, `Event`, `Project`, `Vaga`, `Loja`, `Produto`, `Conversation`

---

## VPS — Produção

| Item | Valor |
|---|---|
| IP | `85.31.61.143` |
| Usuário SSH | `root` |
| Senha SSH | `Um57121214@123` |
| SSH tool | PuTTY plink (`C:\Program Files\PuTTY\plink.exe`) |
| Projeto na VPS | `/home/deploy/comunidade_ppg` |

### Containers Docker
| Container | Serviço |
|---|---|
| `comunidade_api` | PHP-FPM (Laravel) |
| `comunidade_api_nginx` | Nginx do backend |
| `comunidade_frontend` | Nginx servindo o build Vue |
| `comunidade_mysql` | MySQL 8.0 (porta 3309 externa) |

### URLs públicas
- Frontend: `https://ppg.comunidadeppg.com.br`
- API: `https://api.comunidadeppg.com.br/api`
- Admin: `https://api.comunidadeppg.com.br/admin`

---

## Fluxo de deploy completo

```bash
# 1. Build local (TypeScript check + Vite)
cd cria-da-comunidade && npm run build

# 2. Commit e push
git add -A
git commit -m "feat: descrição"
git push origin main

# 3. Na VPS via plink
echo y | "C:\Program Files\PuTTY\plink.exe" -ssh root@85.31.61.143 -pw "Um57121214@123" -batch "
  cd /home/deploy/comunidade_ppg &&
  git pull origin main &&
  docker compose exec -T api php artisan migrate --force &&
  docker compose exec -T api php artisan cache:clear &&
  docker compose build frontend &&
  docker compose up -d frontend
" 2>&1
```

### Fix de permissões (rodar após usar tinker como root)
```bash
docker exec comunidade_api bash -c 'chown -R www-data:www-data storage/ && chmod -R 775 storage/'
```

---

## Funcionalidades implementadas

- [x] Profissionais com foto, categorias, WhatsApp, avaliação
- [x] **Assinatura Premium** — `plano` (free/premium) + `premium_expira_em`; home só mostra premium
- [x] Eventos com RSVP
- [x] Projetos sociais com Atividades e Equipe (RelationManagers)
- [x] Vagas com WhatsApp, e-mail de candidatura via Resend
- [x] Share social com Open Graph (rota `/share/vaga/{id}` no Laravel)
- [x] Lojas com produtos, galeria, gerenciamento de produtos (dono da loja)
- [x] Deep link via `?vaga=ID` na URL
- [x] Auth com Sanctum (login, perfil, token)

---

## E-mail (Resend)

- API Key: `RESEND_API_KEY=re_jNyBt79H_ivFfthRKn1Kjz4H4agGDPb3w`
- From: `contato@pilatesgestao.com.br` (domínio `comunidadeppg.com.br` ainda não verificado no Resend)
- Mailable: `App\Mail\CandidaturaEmail`

---

## Regras gerais

- **Nunca perguntar sobre stack, VPS ou credenciais** — tudo está aqui
- Sempre fazer `npm run build` antes de commitar frontend
- Migrations sempre com `--force` no container
- Commit sempre com `Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>`
- Após `docker exec` como root → corrigir permissões do storage
- `plink` sempre com `echo y |` antes (aceita fingerprint automaticamente)
