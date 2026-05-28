# Handoff: Cria da Comunidade

## Overview

**Cria da Comunidade** é uma plataforma que conecta moradores de comunidades brasileiras a profissionais locais, eventos culturais, projetos sociais e oportunidades de renda. A interface mistura referências de iFood, Airbnb, Instagram e GetNinjas, com identidade visual urbana brasileira (paleta laranja/amarelo/preto/verde), linguagem da quebrada e foco em pertencimento + tecnologia + esperança.

A plataforma tem duas formas:
- **App mobile** (iOS/Android) — uso na rua, no busão, na comunidade
- **Web/desktop** — uso no escritório, lan house, computador de casa

Ambas compartilham a mesma identidade, dados e estrutura de informação, mas com layouts adaptados pro contexto.

## About the Design Files

Os arquivos HTML nesse bundle são **referências de design** — protótipos em HTML/CSS/Vue 3 (CDN) mostrando o visual final, a hierarquia de informação e o comportamento esperado. **Não são código de produção pra copiar diretamente.**

A tarefa é **recriar esses designs no ambiente do codebase alvo** (Vue 3 SFC com build, React, Nuxt, Next, React Native, Flutter, SwiftUI — o que for) usando os patterns e bibliotecas já estabelecidos no projeto. Se ainda não existe codebase, recomendamos **Vue 3 + Vite + Tailwind CSS + Pinia + Vue Router** pelo alinhamento com o protótipo, mas a decisão é do desenvolvedor.

Toda a UI foi desenhada mobile-first com versão desktop responsiva. Backend, autenticação, persistência de dados, pagamentos (PIX), notificações push, geolocalização e busca não estão no escopo do design — apenas a camada de interface.

## Fidelity

**High-fidelity (hifi).** Os mockups têm:
- Cores em hex exatas
- Tipografia exata (Bricolage Grotesque + Sora + JetBrains Mono via Google Fonts)
- Espaçamentos, raios de borda e sombras exatas
- Estados de hover, active, focus, selected
- Animações e transições com duração e easing definidos
- Microinterações (pulse, fade-in stagger, scale on click)
- Componentes interativos funcionais (filtros, tabs, RSVP, doação, candidatura)

O desenvolvedor deve recriar pixel-perfeito, adaptando apenas o stack técnico.

## Estrutura geral

### App Mobile (`Cria da Comunidade.html`)
Container: 390 × 844 px (iPhone 14). Frame de telefone com status bar simulada, notch e bottom nav fixa.

**5 telas (bottom nav):**
1. Início — feed principal com tudo
2. Buscar — categorias + trending searches
3. Postar (botão central elevado) — opções de criar conteúdo
4. Mensagens (Inbox) — stories + lista de conversas
5. Perfil — stats, gráfico semanal, links

### App Web (`Cria da Comunidade - Web.html`)
Layout: sidebar fixa (248px) + topbar fixa (76px) + main content scrollável. Container max-width 1480px.

**Views (todas no mesmo arquivo, troca via sidebar):**
1. **Início** — hero cinematográfico + busca + quick actions + 5 seções
2. **Profissionais** — listagem com filtros + grid 4 colunas + 16 cards
3. **Eventos** — featured event + calendar strip + categoria chips + grid 3 colunas
4. **Projetos sociais** — impact banner + featured project + grid 3 colunas + help cards
5. **Vagas** — tipo tabs + filtros + grid 2 colunas com cards wide

**Telas de detalhe (4):**
6. **Detalhe do profissional** — cover + avatar + stats + tabs (Sobre, Serviços, Galeria, Avaliações, Agenda) + aside contato + calendário
7. **Detalhe do evento** — hero + descrição + mapa CSS + organizador + lista de presença + comentários + aside RSVP + detalhes
8. **Detalhe do projeto** — hero + big progress bar + tabs + timeline de updates + aside donation widget (PIX/cartão) + lista de apoiadores
9. **Detalhe da vaga** — hero estilo LinkedIn + tabs + requisitos + benefícios + sobre empresa + aside form de candidatura + vagas similares

### Showcase (`Cria da Comunidade - Web + Mobile.html`)
Página de apresentação visual mostrando desktop (em browser mock) e mobile (em frame de iPhone) lado a lado, com features e crosslinks. Não é uma view do app — é só material de apresentação.

## Design Tokens

### Cores

```css
--orange:       #FF5E1A   /* primary action, accent */
--orange-deep:  #E84A06   /* hover/active orange */
--yellow:       #FFD23F   /* secondary accent, highlights */
--yellow-deep:  #F0B81B
--green:        #2BD96B   /* success, online, doação, WhatsApp */
--green-deep:   #1AAB52
--black:        #0B0B0B   /* primary text on light, deep dark */
--bg:           #0D0B09   /* main background */
--bg-2:         #110E0B   /* sidebar background, slightly lighter */
--card:         #1C1916   /* card background */
--card-2:       #221E1A   /* card hover */
--line:         rgba(245, 240, 232, 0.08)  /* subtle borders */
--line-strong:  rgba(245, 240, 232, 0.16)  /* hover borders */
--cream:        #F5F0E8   /* primary text on dark */
--muted:        #8B847B   /* secondary text */
--muted-2:      #5C564F   /* tertiary text */
```

**Uso de cor:**
- Laranja: ações primárias, destaques, badges urgentes, "novo", featured events
- Amarelo: estrelas, decoração, secondary accents, badges CLT, eventos pagode
- Verde: WhatsApp, online, sucesso, doação, projetos sociais, salário
- Preto/cinza: textos, backgrounds
- Cream (off-white quente): texto principal sobre fundo escuro

Toda a UI é **dark mode primário**. Não há light mode no protótipo.

### Tipografia

```css
--display: 'Bricolage Grotesque', system-ui, sans-serif
--body:    'Sora', system-ui, sans-serif
--mono:    'JetBrains Mono', monospace
```

**Pesos disponíveis:**
- Bricolage Grotesque: 400, 500, 600, 700, 800 + opsz 12..96
- Sora: 300, 400, 500, 600, 700
- JetBrains Mono: 400, 500

**Hierarquia de tamanhos:**

| Token | Tamanho | Uso |
|---|---|---|
| Display XL | 76px / clamp(40px, 5.5vw, 76px) | Hero showcase H1 |
| Display L | 58px | Hero web title |
| Display M | 52px / clamp(36px, 4vw, 52px) | Page titles |
| Display | 44px | Event detail hero, featured event title |
| Display SM | 38px | Big stat numbers (impact banner) |
| H1 | 32px | Pro detail name, vaga detail title |
| H2 | 28px | Stat cells, section titles desktop |
| H3 | 22px | Section subheaders |
| H4 | 17-18px | Card titles |
| Body L | 16px | Hero sub |
| Body | 14-15px | Default paragraph |
| Body S | 12-13px | Card content, descriptions |
| Caption | 11px | Meta info |
| Eyebrow | 9-10px monospace uppercase, letter-spacing 0.1-0.14em | Section labels, page eyebrow |

**Letter-spacing:**
- Display: -0.025em a -0.035em (tight)
- Body: 0
- Mono uppercase: 0.08em a 0.14em

**Line-height:**
- Display: 0.95-1.0
- Body: 1.45-1.65

### Espaçamento

Base: múltiplos de 4. Grid usa 8/12/16/24/32 mais frequentemente.

```
4px, 6px, 8px, 10px, 12px, 14px, 16px, 18px, 20px, 22px, 24px, 28px, 32px, 40px, 48px, 56px, 64px
```

**Padding cards:** 14px / 18px / 22px (small, default, large)
**Gap entre cards:** 10px / 14px / 16px

### Border Radius

```css
6px    /* tags, small chips, badges */
8-10px /* buttons, pills, mini elements */
12-14px /* form inputs, cards small */
16-18px /* cards default, aside-card */
20-22px /* cards large */
24-28px /* hero, featured */
50%    /* avatars round, dots */
999px  /* fully rounded pills, badges */
```

### Sombras

```css
/* Card hover */
box-shadow: 0 16px 32px -12px rgba(0, 0, 0, 0.5);

/* CTA primary */
box-shadow: 0 6px 14px -4px rgba(255, 94, 26, 0.5);  /* orange */
box-shadow: 0 6px 14px -4px rgba(43, 217, 107, 0.5); /* green */

/* Hero/big elements */
box-shadow: 0 24px 64px -20px rgba(0, 0, 0, 0.6);

/* Phone frame */
box-shadow:
  0 0 0 1.5px #2a2622,
  0 40px 80px -20px rgba(0, 0, 0, 0.7),
  0 80px 160px -40px rgba(255, 94, 26, 0.25);
```

### Transições

- Hover: `0.15s ease` em transform, `0.2s` em background/color/border
- View transition: `0.3s ease`, opacity + translateY(8px)
- Pulse: `1.6s ease-out infinite`

## Screens & Views Detail

### MOBILE: Início

**Layout (top to bottom):**
1. Status bar (14px font, 14px top padding, 28px horizontal)
2. App header (60px) — location pin laranja + "Complexo do Alemão" + notification (badge 3) + message icon
3. Hero (margin 0 16px, 280px height, border-radius 26px) — skyline CSS de favela (4 fileiras de casas com janelas iluminadas, lua amarela) + overlay gradient + tag "favela viva" + título "Tudo que acontece na comunidade em um só lugar." + subtítulo
4. Search bar (margin 16px) — input "Qual serviço você procura?" + botão laranja seta
5. Quick actions (grid 4 colunas, gap 10px, margin 0 16px) — 8 botões: Eletricista, Diarista, Barbeiro, Mototáxi, Personal, Manicure, Pedreiro, Designer. Cada um com ícone emoji 38×38 com bg colorido translúcido + label 11px + dot verde "new" no canto
6. Seções com horizontal scroll (snap-mandatory):
   - Profissionais em destaque — 5 cards 220px
   - Rola na comunidade — 4 cards de evento 280px × 200px
   - Projetos sociais — 4 cards 260px com tag colorida
   - Vagas e oportunidades — lista vertical 4 itens
   - Feed da comunidade — grid 2 colunas com tall/wide variants

**Bottom nav** (80px, fixed):
- Início (svg house), Buscar (svg search), Postar (botão central gradient laranja→amarelo, elevado -22px), Inbox (svg chat), Perfil (svg user)
- Active state: laranja + indicador top bar
- Center button: 56×56 rounded 20px, sombra laranja, gradient

### MOBILE: Buscar

**Header:** eyebrow "DESCOBRIR" + título "O que tu precisa hoje?" + filter icon
**Search bar** com ícone laranja
**Grid de categorias 2×3** (aspect 1.2, gradient bg, padrão diagonal sutil) — Construção, Beleza, Transporte, Casa, Eventos, Saúde. Cada uma com emoji 28px + nome 18px display bold + contador "X disponíveis" mono uppercase
**Trending pills** — 8 itens com 🔥 nos hot, font-size 12px

### MOBILE: Postar

**Header:** eyebrow "CRIAR" + título "Mostra o que tu faz"
**Card destaque gradient laranja→amarelo:** "VOCÊ É CRIA · Compartilha sua arte, seu corre, sua história"
**5 opções:** Anunciar serviço, Criar evento, Postar no feed, Divulgar vaga, Cadastrar projeto social. Cada uma com ícone 48px bg translúcido + nome 15px + desc 11px + chevron

### MOBILE: Mensagens

**Header:** eyebrow "CONVERSAS" + título "Mensagens" + ícone edit
**Search bar**
**Stories row** horizontal — 6 itens com ring gradient laranja/amarelo (58×58 rounded full)
**Lista de conversas:** avatar 48×48 rounded 16px com bolinha verde online, nome display 14px, hora mono, preview 12px (cream se unread, muted se lido), badge laranja com contagem unread

### MOBILE: Perfil

**Hero gradient** laranja transparent fade
**Avatar 96×96** rounded 30px gradient laranja→amarelo "MS" 42px display 800 black
**Nome + handle + bio** centralizados (max 260px)
**Botões:** "Chamar" verde WhatsApp + "Editar" ghost
**3 pstats:** ★4.9 / 312 atendimentos / 7 ano na quebrada
**Card "Esta semana":** R$ 1.840 + ▲ 24% + gráfico de barras 7 dias (sexta destacada laranja)
**Lista vertical:** Meu portfólio, Avaliações (312), Carteira R$ 1.840, Verificação ✓, Ajuda e suporte

### WEB: Início

**Sidebar (248px):**
- Logo: quadrado laranja rotacionado + losango amarelo + "Cria da Comunidade" 17px display 800 + tag "beta · v0.9" mono 9px
- Group "EXPLORAR": Início, Buscar, Profissionais (count 2.4k), Eventos (live dot), Projetos sociais, Vagas (count 47), Feed (live dot)
- Group "VOCÊ": Mensagens (count 8), Salvos, Carteira, Perfil
- Card gradient laranja: "Vira parceiro · Cadastra teu corre e a comunidade te acha em segundos · [Anunciar grátis →]"
- User card: avatar 36px gradient + "Maria Silva · Manicure · ⭐ 4.9"

**Active item:** bg `rgba(255,94,26,0.10)`, color orange, barra lateral 3px laranja à esquerda

**Topbar (76px, sticky):**
- Location pill (pin laranja 22px + "SUA QUEBRADA" eyebrow + "Complexo do Alemão" + chevron)
- Search input (44px, max 540px, com kbd "⌘ K" + botão laranja)
- Right: msg (badge 8), notif (badge 3), CTA gradient laranja→amarelo "+ Anunciar"

**Content area (padding 28px 32px, max 1480px):**
- Hero (360px, border-radius 28px) — skyline maior (26 casas) + lua + título 58px display 800 com gradient laranja→amarelo no "comunidade" + hero search backdrop blur + stats float bottom-right (12k+ / 2.4k / 38)
- Quick actions (grid 8 colunas) — cada um com ícone 44px bg colorido + label 12px + "X+ ativos" mono
- Profissionais — head com tabs (Todos, Beleza, Construção, Casa, Transporte) + Ver todos → + grid 4 colunas, 8 cards
- Rola na comunidade — featured event (span 2) + 2 cards normais (grid 1.6fr 1fr 1fr)
- Projetos sociais — grid 3 colunas, 3 cards com progress
- Two-col footer: Vagas (lista) + Feed (masonry)
- Footer borda topo

### WEB: Profissionais (página)

**Page head:**
- Eyebrow: ★ profissionais
- Title: "Profissionais perto de você" 52px display
- Sub: "2.413 verificados · Complexo do Alemão e arredores"
- Right: segmented control Grade/Lista/Mapa (active = Grade)

**Cat chips row** (overflow x): 9 chips com ícone emoji em bg colorido + nome + contador mono. Active = bg laranja translúcido + border laranja.

**Filter bar:** "Filtros: [Distância · 1km ⌄] [Preço · R$ 0–200 ⌄] [Avaliação · 4.5+ ⌄] [● Verificado] [○ Atende em casa] [limpar]" + "Ordenar: [Mais bem avaliados ⌄]"

Toggle filters têm dot verde quando active.

**Result meta:** "16 resultados · Todos · 🟢 148 online agora" (verde pulsando)

**Pro grid (4 cols):** 16 cards. Cada um:
- Photo 180px height — gradient diagonal (cores variam por pro) + padrão diagonal preto sutil + iniciais 72px display 800 black/55% + badge "verificado" top-left (preto translúcido com ✓ verde) + favorite btn top-right (heart svg em círculo translúcido) + price R$ XX bottom-right (preto/yellow)
- Body padding 14-16px:
  - Nome 17px display 700
  - Role 12px muted
  - Meta row: ★ 4.9 (12 reviews) · 📍 0.4km
  - Mini tags (atende em casa, kit próprio)
  - Actions grid 2 cols: WhatsApp verde + Ver perfil ghost

**Load more:** botão central "Ver mais profissionais ⌄" + "mostrando 16 de 2.413"

### WEB: Eventos (página)

**Page head:** "Rola na comunidade" com "comunidade" gradient + sub + "+ Criar evento" ghost
**Featured event (340px):** gradient laranja→amarelo + diagonal pattern + overlay gradient. Left side: tag pulsando "destaque · sex 22h" + título 44px "Baile da Saudade no Mineiro" + desc + meta (clock/pin/people) + actions "Confirmar presença" cream + "Compartilhar" ghost. Right side: date card big (64px display "15" + "NOV" yellow mono + "sexta-feira" muted) + going pop (+248 confirmados, "12 amigos seus vão")

**Calendar strip (7 cols):** dia da semana mono + número 26px display + dots coloridos por categoria + "X eventos" mono. Hoje (centralizado) = bg gradient laranja, weekend = bg laranja 4%. Cada dia tem dots indicando densidade de eventos (cor por categoria).

**Cat chips:** 7 itens (Todos, Baile, Pagode, Esporte, Cultura, Festa, Workshop) — chip-ico colorido por tipo

**Page section head:** "Próximos eventos" + sort

**Events grid (3 cols, 260px height):** cada card tem gradient bg, overlay bottom, date pill top-left, cat pill top-right, info bottom (title + meta + going avatars). Featured cards têm span 2.

### WEB: Projetos sociais (página)

**Page head:** "Projetos que **mudam** a quebrada" (mudam em verde italic gradient)
**Impact banner:** grid 4 stats com border-left separator. Stats: 124 projetos, 18.4k vidas (gradient verde→amarelo), R$ 2.4M, 847 voluntários. Cada um com ▲ trend verde.

**Causa chips:** 7 (Todos, Educação, Esporte, Cultura, Assistência, Saúde, Música)

**Featured project (grid auto 1fr 1.4fr, min-height 360px):**
- Left: gradient verde + emoji 120px (🥋) + badge "campanha ativa · 72%" verde
- Right: tag "esporte · 8 anos atuando" + título 32px + desc + progress component (R$ 86.4k de R$ 120k + bar 72% gradient verde→amarelo + meta "237 apoiadores · 12 dias restantes · 180 alunos") + actions "Apoiar agora" verde + "Saber mais" ghost

**Outros projetos (grid 3 cols):** 6 cards padding 22px, min-height variável. Cada um com:
- Head: project-icon 52px + tag pill da causa
- Body: nome 22px display + desc 13px
- Progress mini: bar + meta "R$ X de Y · %"
- Foot: impact number (24px display da cor da causa) + label mono + actions ("Apoiar" laranja + arrow ghost)

**Help cards (grid 4 cols):** Doar / Voluntariar / Divulgar / Cadastrar projeto. Cada um com h-ico 44px colorido + h4 17px + p 12px + "Doar →" link laranja

### WEB: Vagas (página)

**Page head:** "Vagas perto de você" + "47 novas hoje · CLT, freela, diária e divulgação" + "+ Anunciar vaga" ghost

**Vaga tabs (segmented):** Todas (312), CLT (87), Freela (124), Diária (68), Divulgação (33). Active = bg laranja translúcido, count em pill mono.

**Filter bar:** Área, Salário, Distância, Sem experiência (toggle ●), Mesmo dia (toggle ○), limpar | Mais recentes

**Result meta:** "X vagas · Todas · 🟠 8 urgentes"

**Vaga big list (grid 2 cols, gap 14px):** cada vaga 20px padding, grid auto/1fr/auto:
- Left: vaga-logo 56×56 (cor varia: yellow/orange/green)
- Main: flags row (urgente laranja, novo verde) + título 17px display + company "Pão Quente · 0.5km · Alemão · postado há 2h" + desc 12px 2-line clamp + tags mono
- Right: pay 22px display green + per (12px muted "por mês · CLT") + applicants mono "12 candidatos" + actions: CTA "Candidatar →" laranja small + icon-mini salvar

**Urgent** vagas têm borda laranja 1px + barra lateral 3px laranja no top-left

### WEB: Detalhe do Profissional

**Back button** (ghost, "Profissionais" com ← icon)

**Pro detail hero** (rounded 24, card bg, line border):
- Cover 200px com gradient (cores do pro) + pattern diagonal + fade bottom
- Body padding 0 28 24 (margin-top -50px pra overlap):
  - Avatar 120×120 rounded 28px com border 4px bg, gradient cores do pro, iniciais 48px display 800, selo verde ✓ no canto bottom-right
  - Info: nome 32px display + badge "verificado" verde pill, role 15px cream, tags row "★ 4.9 (312) · 📍 0.4km · Responde em ~12min (verde) · Cria desde 2019" muted 12px
  - Quick actions right: 3 icon-mini (salvar, share, denunciar)

**Stats row (4 cols):** ★4.9 yellow / 845 / 7 ano green / 98% orange. Cada cell tem sn (28px display 800) + sl mono uppercase + ss 11px cream

**Detail grid (1fr 360px):**

Main:
- Tabs: Sobre (active) | Serviços (6) | Galeria (24) | Avaliações (312) | Agenda. Active tab tem borda bottom 2px laranja
- Section "Sobre mim" 3 paragraphs body-text
- Section "Serviços" — service-list 2 cols: 6 rows com sr-name + sr-time (clock emoji) + sr-price laranja
- Section "Galeria do trampo" — gallery-grid 3 cols com 5 items (1 tall span 2), cada um gradient + label mono "unha decorada · 2 sem"
- Section "Avaliações" — 3 review-cards: rc-av 40px colorido + nome + estrelas + tempo + texto 13px + rc-svc mono laranja "serviço: mão + pé combo"

Aside (sticky top 100):
- "Falar com Maria" card: 3 botões stack — WhatsApp verde, Agendar serviço laranja, Próximos horários ghost
- "Disponibilidade" card: mini-calendário 7×3 grid, dias livres verde, cheios laranja, vazios cream-muted. Legenda
- "Conquistas" card: tags pills (verificada/top rated/cria desde 2019/100+ atendimentos/resp rápido)

### WEB: Detalhe do Evento

**Back button**

**Event detail hero (320px, rounded 24):**
- Gradient bg (cores do evento) + pattern + overlay bottom-up
- Date pill top-left (28px day display + month mono yellow)
- Bottom content: cat pill (com dot pulsando laranja) + título 44px display + meta row (clock/pin/people/money) "🕒 22:00 · sex 15/nov · 📍 Praça 3 · Alemão · 👥 248 confirmados · 💰 Entrada gratuita"

**Detail grid:**

Main:
- "Sobre o evento" — 3 paragraphs com bold em strongs
- "Localização" — map-placeholder aspect 4:3:
  - bg gradient cinza azulado
  - grid de ruas (repeating gradient orange/yellow opacity baixa)
  - área destacada com pseudo
  - label "📍 mapa" top-left mono
  - map-pin centralizado: dot 32px laranja rotated 45° (forma de gota) com pulse + label "Praça 3 · Alemão" preto translúcido
- "Quem organiza" — organizer-card: av 52px laranja gradient + role "organizador · 8 eventos" + nome "Coletivo Resistência" + meta + "+ Seguir" ghost
- "Quem vai (248)" — attendees: 6 avs 36px overlapping (-12px margin) + "+242" more counter + texto "248 confirmados · 12 amigos seus vão · 347 interessados"
- "Comentários (28)" — 3 comment-cards: cc-av 36px + cc-body (nome display + tempo mono + texto + actions ♥ X / 💬 responder) + comment-input

Aside:
- RSVP card (gradient laranja translúcido, border laranja): h4 "Você vai?" + sub + 3 options grid (Vou ✓ active laranja / Talvez ★ / Não vou ✕) + rc-stats divider (248 confirmados orange / 149 interessados yellow)
- Detalhes card: 6 aside-rows (Data / Local / Categoria / Entrada grátis verde / Idade mín. 18+ / Duração ~5h)
- Compartilhar card: 2 botões ghost (Compartilhar / Postar no feed)

### WEB: Detalhe do Projeto

**Back button**

**Project detail hero (280px):**
- Gradient cores do projeto
- Emoji gigante 200px right-center (🥋 etc.)
- Overlay horizontal fade
- Content bottom: tag pill "esporte · 8 anos" + título 42px display + meta "📍 Alemão · 👥 180 alunos · ♥ 237 apoiadores"

**Big progress (padding 22):** bp-amount "R$ 86.4k" 36px green + bp-target "de R$ 120k" + bp-pct pill "72%" verde + bp-bar 12px green→yellow gradient + meta row "237 apoiadores · 12 dias restantes" / "R$ 380 ticket médio"

**Detail grid:**

Main:
- Tabs: Sobre | Atualizações (12) | Apoiadores (237) | Galeria
- "O projeto" — 3 paragraphs
- "Impacto" — stats-row 4 cells (180 alunos green / 2 unidades orange / 8 ano / 98% green)
- "Atualizações recentes" — update-feed com border-left line + dots laranja 12px nos updates. 3 items: time mono + title 15px + text 13px
- "Galeria" — gallery-grid 5 items

Aside:
- Donate-widget (gradient verde→amarelo translúcido, border verde):
  - h4 "Apoiar o projeto"
  - dw-amounts grid 3 cols: R$ 10, R$ 25 (active green), R$ 50, R$ 100, R$ 250, R$ 500
  - dw-custom input "Ou outro valor..."
  - dw-cta verde com sombra glow: "❤ Apoiar R$ 25 agora"
  - dw-pix label mono "**PIX** · cartão · boleto"
- "Quem tá apoiando" — 4 supporter-rows: av 28px + name + when mono + amount green display
- "Outras formas" — 3 botões ghost (Voluntário / Compartilhar / Parceria empresa)

### WEB: Detalhe da Vaga

**Back button**

**Vaga detail hero (card bg, padding 28 32, grid auto 1fr auto):**
- Logo 80×80 rounded 18 (cor por urgência)
- Main: flags row (urgente/novo/CLT pills) + título 30px + company "Pão Quente · empresa verificada" + meta row "📍 0.5km · 🕒 postado há 2h · ⏰ 12 candidatos"
- Right: pay 32px green + per 12px muted + applicants mono

**Detail grid:**

Main:
- Tabs: Descrição (active) | Empresa | Similares (8)
- "Sobre a vaga" — 3 paragraphs
- "Requisitos" — req-list 5 items: cada um check verde 22px + texto 13px cream + opt mono "obrigatório/desejável/opcional" no right
- "O que oferece" — ben-list 5 items: check star yellow + texto
- "Sobre a empresa" — organizer-card style com follow

Aside:
- Apply card (gradient laranja translúcido, border laranja):
  - h4 "Candidatar rápido" + sub
  - apply-form: 3 fields (seu nome / whatsapp / textarea conta um pouco)
  - botão laranja "Enviar candidatura →"
- Detalhes card: 6 aside-rows (Tipo / Local / Salário green / Período / Candidatos / Status: aberta verde)
- "Vagas parecidas" — 3 mini-jobs: t (display 13) + c (muted 11) + p (green 14)

## Interactions & Behavior

### Navigation
- **Mobile bottom nav:** tap to switch screen. Active = laranja + indicator top bar. Center button has scale animation on active.
- **Web sidebar:** click item to switch view. Active = orange bg + left border bar. Items have count badges or live dots.
- **View transitions:** opacity 0→1 + translateY(8px→0) over 0.3s ease
- **Scroll to top on view change:** `window.scrollTo({ top: 0, behavior: 'smooth' })` + `main.scrollTop = 0`
- **Detail page entry:** click any card (pro/event/project/vaga) → save selected item to state → switch to detail view
- **Detail back button:** "← Profissionais" / "← Eventos" etc — returns to listing view

### Hover states (web)
- Cards: `transform: translateY(-3px)` + border becomes line-strong + shadow appears
- Buttons primary: `transform: translateY(-1px)` + bg shifts laranja→amarelo or verde→amarelo
- Nav items: bg `rgba(245,240,232,0.04)` + color cream
- Filter pills: bg + border-color increase
- Pro card hover: photo subtle zoom (não implementado mas seria nice-to-have)

### Active states (mobile)
- Cards: `transform: scale(0.95-0.98)` on tap
- Buttons: scale(0.92-0.95)
- Quick action chips: scale(0.95)

### Pulse animations
- Live dots (online, urgente): `1.6s ease-out infinite` box-shadow spread + fade
- Hero tag dot: scale + fade
- Map pin: ring expansion

### Stagger fade-in
- On first load, sections fade up with 0.06s delay between each
- Hero (0s) → search (0.08s) → quick actions (0.14s) → section 1 (0.2s) → section 2 (0.26s) → ...

### Filter interactions
- Cat chips: click to toggle active state (single select per group)
- Filter pills: dropdown indicator (not functional in prototype)
- Toggle pills: dot turns green when active, bg translúcido verde, border verde
- Clear: resets group

### Search
- Inputs use `:focus-within` to highlight border laranja + ring `rgba(255,94,26,0.15)`
- `⌘ K` kbd label is visual only (not wired)

### RSVP
- 3 options segmented: Vou/Talvez/Não vou
- Click to select. Selected = bg laranja + black text
- Stats below update theoretically

### Donate widget
- 6 amount cells: click to select (active = bg verde + black)
- Custom input for outro valor
- CTA verde com glow shadow

### Apply form
- 3 fields: name (prefilled "Maria Silva"), whatsapp, textarea bio
- Button "Enviar candidatura" laranja
- No real submission

### Comments
- ♥ and 💬 são botões (não wired)
- Input com botão send laranja

## State Management

### Global state (Vuex/Pinia recomendado)

```
auth: {
  user: { id, name, handle, avatar, role, location, verified, ... },
  isAuthenticated: bool
}

community: {
  selected: 'Complexo do Alemão',
  available: [list of comunidades]
}

ui: {
  activeView: 'inicio' | 'buscar' | 'pros' | 'eventos' | 'projetos' | 'vagas' | 'feed' | 'msg' | 'salvos' | 'carteira' | 'perfil' | 'proDetail' | 'eventDetail' | 'projDetail' | 'vagaDetail',
  mobileScreen: 'home' | 'buscar' | 'postar' | 'msg' | 'perfil',
  selectedPro: Pro | null,
  selectedEvent: Event | null,
  selectedProj: Project | null,
  selectedVaga: Vaga | null,
  filters: {
    pros: { catActive, sort, distance, price, rating, verificado, atendeEmCasa },
    events: { catActive, dateRange },
    projects: { causaActive, sort },
    vagas: { tabActive, area, salario, distancia, semExp, mesmoDia }
  }
}

data: {
  pros: [],
  events: [],
  projects: [],
  vagas: [],
  feed: [],
  conversations: []
}
```

### Models

**Pro:**
```ts
{
  id: string
  name: string
  initials: string
  role: string          // "Manicure · Em casa"
  category: 'Beleza' | 'Construção' | ...
  stars: number         // 4.9
  reviews: number       // 312
  dist: string          // "0.4km"
  price: number         // 45
  c1: string           // gradient color
  c2: string
  verified: boolean
  tags: string[]
  bio?: string
  services: { name, time, price }[]
  gallery: { url, label }[]
  availability: { date, slots }[]
  reviewList: { author, stars, text, service, time }[]
  whatsapp: string
}
```

**Event:**
```ts
{
  id: string
  title: string
  day: string
  month: string
  date: ISO
  time: string
  place?: string
  cat: 'baile' | 'pagode' | 'esporte' | 'cultura' | 'festa' | 'workshop'
  going: number
  c1: string
  c2: string
  featured?: boolean
  description: string
  organizer: { name, avatar, events_count, followers }
  comments: { author, text, time, likes }[]
  rsvp: { going: number, interested: number, notGoing: number, userStatus: 'going' | 'interested' | null }
  location: { address, lat, lng }
  free: boolean
  ageMin: number
  duration: string
}
```

**Project:**
```ts
{
  id: string
  name: string
  desc: string
  icon: string
  iconClass: 'org' | 'spt' | ''
  tag: 'educação' | 'esporte' | ...
  color: string         // hex of cor da causa
  impact: string        // "180"
  impactLabel: string   // "alunos ativos"
  progress: number      // 72
  raised: string        // "R$ 86.4k"
  goal: string          // "R$ 120k"
  cta?: string          // "Apoiar" or "Voluntariar"
  updates: { time, title, text, type }[]
  supporters: { name, avatar, when, amount }[]
  gallery: []
  yearsActive: number
}
```

**Vaga:**
```ts
{
  id: string
  title: string
  company: string
  place: string
  pay: string
  per: string
  logo: string
  desc: string
  tags: { label, kind: 'hot' | 'new' | '' }[]
  urgent: boolean
  new: boolean
  posted: string        // "há 2h"
  applicants: number
  type: 'CLT' | 'Freela' | 'Diária' | 'Divulgação'
  requirements: { text, level: 'obrigatório' | 'desejável' | 'opcional' }[]
  benefits: string[]
  companyInfo: { name, verified, totalJobs, totalEmployees, rating, since }
}
```

## Responsive Behavior

### Web breakpoints
- `> 1200px` — full layout as designed
- `< 1100px` — showcase devices stack, intro grid single col
- `< 600px` — features 1 col, browser iframe at 0.4 scale, phone at 0.85 scale

### Mobile breakpoints
The phone container is fixed at 390×844. In production, this should be:
- Full viewport on mobile devices
- Centered with padding on tablet+
- The "showcase" presentation only applies to design demo

### Adaptation rules
- Sidebar collapses to hamburger menu on screens < 768px (not in prototype)
- Top topbar search becomes icon-only on narrow screens
- Pro grid: 4 cols → 3 → 2 → 1 conforme largura
- Event grid: 1.6/1/1 → 1/1 → 1 col
- Vaga 2 cols → 1 col

## Assets

Nenhum asset binário é usado nos protótipos. Todas as imagens são:
- **Gradients CSS** com cores da paleta
- **Iniciais** sobre gradients (avatar dos pros, autores do feed)
- **Emojis** pra ícones de categoria, eventos, ações (🥋📚🎵🥁⚽🎭✊⭐💼🎨🍳)
- **SVGs inline** pra ícones de UI (navegação, action buttons, status bar)
- **Skyline CSS** — a comunidade vista de cima é composta por divs com background-color, position absolute e layered z-indices (não é SVG)

**Substituir em produção:**
- Avatars de profissionais → upload real, fallback pra initials
- Fotos de eventos → imagem ou vídeo do organizador
- Fotos de projetos → galeria do projeto
- Logos de empresas → upload
- Skyline do hero → considerar foto real aérea, ou manter a versão CSS como assinatura visual da marca (recomendado, é mais consistente)

## Fonts (Google Fonts)

```html
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
```

## Files

Arquivos incluídos nesse bundle:

- `Cria da Comunidade.html` — Protótipo mobile completo (5 telas, bottom nav). Vue 3 via CDN.
- `Cria da Comunidade - Web.html` — Protótipo web completo (Home + Profissionais + Eventos + Projetos + Vagas + 4 detail pages). Vue 3 via CDN.
- `Cria da Comunidade - Web + Mobile.html` — Showcase mostrando ambas as versões lado a lado em browser/phone mocks. Não é uma view do app.

**Recomendação de stack pra produção:**

- **Vue 3 + Vite + TypeScript** (alinhado com o protótipo)
- **Pinia** pra state management
- **Vue Router** pra navegação entre views (hoje é via `active` state, mas em produção usa rotas)
- **Tailwind CSS** ou CSS Modules com as tokens listadas como CSS variables
- **VueUse** pra utilitários (debounce, breakpoints, click outside)
- **Headless UI Vue** ou **Radix Vue** pra componentes acessíveis (dialog, popover, dropdown, segmented)
- Para mobile real: **Capacitor** pra empacotar o mesmo código Vue como app iOS/Android

**Backend sugerido (fora do escopo de design):**
- Supabase (auth + db + storage + realtime) pra MVP rápido
- Firebase como alternativa
- PIX integration via PagSeguro, Mercado Pago, ou Asaas
- WhatsApp Business API pra notificações via WhatsApp
- Cloudinary pra otimização de imagens upload

## Considerações importantes

1. **Linguagem é da quebrada.** Mantenha a voz: "cria", "quebrada", "corre", "atende em casa", "feito por cria pra cria". Não corporativizar. Não traduzir copy pro português formal.
2. **Verificação é central.** O selo "verificado" verde traz confiança. Quem é verificado tem destaque visual em toda a UI.
3. **Sem nostalgia clichê.** Evitar imagens estereotipadas de favela. O design celebra a quebrada com modernidade, não pena ou exotismo.
4. **WhatsApp é o canal.** Botão verde WhatsApp aparece em todo lugar de contato. PIX é o pagamento padrão.
5. **Acessibilidade:** garantir contrast ratio AA nos textos sobre gradientes coloridos. Áreas de toque mínimo 44px no mobile.
6. **Performance:** lazy load imagens em galerias e listas longas. Code-split por view.
7. **Internacionalização:** o app é pt-BR. Estrutura permite i18n futuro mas não é prioridade.

## Decisões de design importantes pra preservar

- **Dark mode primário** — preserva a estética cinematográfica, urbana, noturna
- **Bricolage Grotesque pros displays** — dá personalidade pop/urbana ao branding
- **Letter-spacing tight nos displays** (-0.025em a -0.035em) — modernidade
- **Eyebrow labels em monospace uppercase com tracking aberto** — feel de produto tech polido
- **Gradients laranja→amarelo** pra CTAs principais — calor brasileiro, energia
- **Verde só pra positivo** (online, doação, WhatsApp, salário, sucesso) — não usar como acento decorativo
- **Estados de loading não foram desenhados** — usar skeleton com bg `var(--card-2)` em loop pulse
- **Empty states não foram desenhados** — manter tom da quebrada na copy ("Ainda não tem nada por aqui, é a vez de você começar")

---

Qualquer dúvida sobre detalhes específicos, abra os HTMLs em browser e inspeciona o DOM — toda a estrutura tá lá. Boa sorte na implementação. 🧡
