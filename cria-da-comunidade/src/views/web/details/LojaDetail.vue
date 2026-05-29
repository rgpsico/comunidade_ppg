<template>
  <div class="loja-detail fade-up" v-if="loja">
    <button class="back-btn" @click="ui.goTo('lojas')">← Lojas</button>

    <!-- Hero capa -->
    <div class="loja-hero" :style="heroStyle">
      <div class="lh-overlay"></div>
      <div class="lh-content">
        <img :src="loja.logoUrl" :alt="loja.nome" class="lh-logo" />
        <div class="lh-info">
          <div class="lh-cat">
            <span class="cat-badge">{{ loja.categoria }}</span>
            <span v-if="loja.verificado" class="verified-badge">✓ Verificada</span>
          </div>
          <h1 class="lh-nome">{{ loja.nome }}</h1>
          <div v-if="loja.endereco" class="lh-endereco">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ loja.endereco }}
          </div>
        </div>
      </div>
    </div>

    <!-- Stats strip -->
    <div class="stats-strip">
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: loja.cor1 }">{{ loja.produtosCount }}</div>
        <div class="stat-l">produtos</div>
      </div>
      <div class="stat-sep"></div>
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: loja.cor1 }">{{ disponiveisProdutos }}</div>
        <div class="stat-l">disponíveis</div>
      </div>
      <div class="stat-sep"></div>
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: loja.cor1 }">{{ categoriasProd.length || '—' }}</div>
        <div class="stat-l">categorias</div>
      </div>
    </div>

    <!-- Actions bar -->
    <div class="actions-bar">
      <a
        v-if="loja.whatsapp"
        :href="`https://wa.me/${loja.whatsapp}`"
        target="_blank"
        rel="noopener"
        class="btn-wpp-main"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Falar no WhatsApp
      </a>
    </div>

    <div class="detail-layout">
      <div class="detail-main">
        <!-- Tabs -->
        <div class="tabs">
          <button
            v-for="tab in tabs"
            :key="tab"
            class="tab"
            :class="{ active: activeTab === tab }"
            @click="activeTab = tab"
          >{{ tab }}</button>
        </div>

        <!-- Tab: Produtos -->
        <div v-if="activeTab === 'Produtos'" class="tab-content">
          <!-- Categoria filter -->
          <div v-if="categoriasProd.length > 1" class="prod-cats">
            <button
              v-for="cat in ['Todos', ...categoriasProd]"
              :key="cat"
              class="pcat-chip"
              :class="{ active: prodCatActive === cat }"
              :style="prodCatActive === cat ? { background: loja.cor1, borderColor: loja.cor1 } : {}"
              @click="prodCatActive = cat"
            >{{ cat }}</button>
          </div>

          <!-- Produtos em destaque -->
          <template v-if="destaques.length && prodCatActive === 'Todos'">
            <div class="prod-section-label">⭐ Em destaque</div>
            <div class="prods-grid">
              <div
                v-for="p in destaques"
                :key="p.id"
                class="prod-card"
                @click="openedProduto = p"
              >
                <ProdutoCard :produto="p" :cor="loja.cor1" />
              </div>
            </div>
          </template>

          <div v-if="destaques.length && prodCatActive === 'Todos'" class="prod-section-label">Todos os produtos</div>

          <div v-if="prodsFiltrados.length" class="prods-grid">
            <div
              v-for="p in prodsFiltrados"
              :key="p.id"
              class="prod-card"
              @click="openedProduto = p"
            >
              <ProdutoCard :produto="p" :cor="loja.cor1" />
            </div>
          </div>

          <div v-else-if="!loja.produtos?.length" class="empty-prod">
            <div class="ep-icon">📦</div>
            <div class="ep-text">Esta loja ainda não adicionou produtos</div>
          </div>
        </div>

        <!-- Tab: Sobre -->
        <div v-if="activeTab === 'Sobre'" class="tab-content">
          <h3 class="content-title">Sobre a loja</h3>
          <p class="body-text">{{ loja.descricao || 'Sem descrição disponível.' }}</p>

          <div class="sobre-info-grid">
            <div v-if="loja.whatsapp" class="sobre-info-item">
              <div class="sii-label">WhatsApp</div>
              <a :href="`https://wa.me/${loja.whatsapp}`" target="_blank" class="sii-value link">{{ loja.whatsapp }}</a>
            </div>
            <div v-if="loja.endereco" class="sobre-info-item">
              <div class="sii-label">Endereço</div>
              <div class="sii-value">{{ loja.endereco }}</div>
            </div>
            <div v-if="loja.comunidade" class="sobre-info-item">
              <div class="sii-label">Comunidade</div>
              <div class="sii-value">{{ loja.comunidade.nome }}</div>
            </div>
            <div class="sobre-info-item">
              <div class="sii-label">Categoria</div>
              <div class="sii-value">{{ loja.categoria }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Produto modal -->
    <Transition name="modal">
      <div v-if="openedProduto" class="prod-modal-overlay" @click.self="openedProduto = null">
        <div class="prod-modal">
          <button class="modal-close" @click="openedProduto = null">✕</button>

          <!-- Imagens -->
          <div v-if="openedProduto.imagens.length" class="pm-gallery">
            <img
              v-for="(img, i) in openedProduto.imagens"
              :key="i"
              :src="img"
              :alt="openedProduto.nome"
              class="pm-img"
              :class="{ active: galleryIdx === i }"
              @click="galleryIdx = i"
            />
          </div>
          <div v-else class="pm-no-img" :style="{ background: `linear-gradient(135deg, ${loja.cor1}33, ${loja.cor2}33)` }">
            <span style="font-size: 48px">📦</span>
          </div>

          <div class="pm-body">
            <div v-if="openedProduto.categoria" class="pm-cat">{{ openedProduto.categoria }}</div>
            <h2 class="pm-nome">{{ openedProduto.nome }}</h2>
            <div class="pm-preco-row">
              <span v-if="openedProduto.precoPromocional" class="pm-de">{{ fmtMoney(openedProduto.preco) }}</span>
              <span class="pm-por" :style="{ color: loja.cor1 }">{{ fmtMoney(openedProduto.precoPromocional ?? openedProduto.preco) }}</span>
              <span v-if="openedProduto.precoPromocional" class="pm-off">
                -{{ Math.round((1 - openedProduto.precoPromocional / openedProduto.preco) * 100) }}%
              </span>
            </div>
            <p v-if="openedProduto.descricao" class="pm-desc">{{ openedProduto.descricao }}</p>

            <a
              v-if="loja.whatsapp"
              :href="prodWppLink"
              target="_blank"
              rel="noopener"
              class="btn-pm-wpp"
              :style="{ background: loja.cor1 }"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Pedir pelo WhatsApp
            </a>
          </div>
        </div>
      </div>
    </Transition>
  </div>

  <div v-else class="no-loja">
    <button class="back-btn" @click="ui.goTo('lojas')">← Lojas</button>
    <p>Loja não encontrada.</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUiStore } from '../../../stores/ui'
import type { Produto } from '../../../types'
import ProdutoCard from '../../../components/ui/ProdutoCard.vue'

const ui = useUiStore()
const loja = computed(() => ui.selectedLoja)

const tabs = ['Produtos', 'Sobre']
const activeTab = ref('Produtos')
const prodCatActive = ref('Todos')
const openedProduto = ref<Produto | null>(null)
const galleryIdx = ref(0)

const heroStyle = computed(() => {
  if (!loja.value) return {}
  const l = loja.value
  return l.capaUrl
    ? { backgroundImage: `url(${l.capaUrl})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : { background: `linear-gradient(135deg, ${l.cor1}, ${l.cor2})` }
})

const disponiveisProdutos = computed(() =>
  loja.value?.produtos?.filter(p => p.disponivel).length ?? 0
)

const categoriasProd = computed(() => {
  const set = new Set(loja.value?.produtos?.filter(p => p.disponivel).map(p => p.categoria).filter(Boolean) ?? [])
  return Array.from(set) as string[]
})

const destaques = computed(() =>
  loja.value?.produtos?.filter(p => p.disponivel && p.destaque) ?? []
)

const prodsFiltrados = computed(() => {
  const prods = loja.value?.produtos?.filter(p => p.disponivel) ?? []
  if (prodCatActive.value === 'Todos') return prods
  return prods.filter(p => p.categoria === prodCatActive.value)
})

const prodWppLink = computed(() => {
  if (!loja.value?.whatsapp || !openedProduto.value) return '#'
  const msg = encodeURIComponent(`Olá! Tenho interesse no produto: ${openedProduto.value.nome}`)
  return `https://wa.me/${loja.value.whatsapp}?text=${msg}`
})

function fmtMoney(val: number): string {
  return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>

<style scoped>
.loja-detail { padding: 0 0 80px; max-width: 860px; margin: 0 auto; }
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin: 20px 32px 0;
  font-size: 13px;
  color: var(--muted);
  cursor: pointer;
  transition: color 0.15s;
  background: none;
  border: none;
  padding: 0;
}
.back-btn:hover { color: var(--cream); }

/* Hero */
.loja-hero {
  margin: 16px 32px 0;
  height: 200px;
  border-radius: 18px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
}
.lh-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 30%, rgba(0,0,0,0.7) 100%);
}
.lh-content {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: flex-end;
  gap: 14px;
  padding: 16px 20px;
}
.lh-logo {
  width: 64px; height: 64px;
  border-radius: 14px;
  border: 3px solid rgba(255,255,255,0.2);
  object-fit: cover;
  flex-shrink: 0;
}
.lh-info { flex: 1; }
.lh-cat {
  display: flex;
  gap: 6px;
  margin-bottom: 4px;
}
.cat-badge {
  background: rgba(0,0,0,0.5);
  backdrop-filter: blur(6px);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 20px;
}
.verified-badge {
  background: rgba(43,217,107,0.9);
  color: #000;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 20px;
}
.lh-nome {
  font-size: 22px;
  font-weight: 900;
  color: #fff;
  font-family: var(--display);
  letter-spacing: -0.02em;
  text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.lh-endereco {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: rgba(255,255,255,0.7);
  margin-top: 4px;
}

/* Stats strip */
.stats-strip {
  display: flex;
  align-items: center;
  gap: 0;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 14px;
  margin: 12px 32px;
  overflow: hidden;
}
.stat-cell {
  flex: 1;
  text-align: center;
  padding: 14px 0;
}
.stat-n {
  font-family: var(--display);
  font-size: 22px;
  font-weight: 800;
  line-height: 1;
}
.stat-l {
  font-size: 10px;
  color: var(--muted);
  margin-top: 2px;
  font-family: var(--mono);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.stat-sep {
  width: 1px;
  height: 36px;
  background: var(--line);
}

/* Actions */
.actions-bar {
  padding: 0 32px 12px;
  display: flex;
  gap: 10px;
}
.btn-wpp-main {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: rgba(43,217,107,0.15);
  color: var(--green);
  border: 1px solid rgba(43,217,107,0.35);
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.15s;
}
.btn-wpp-main:hover { background: rgba(43,217,107,0.25); }

/* Detail layout */
.detail-layout {
  padding: 0 32px;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 4px;
  border-bottom: 1px solid var(--line);
  margin-bottom: 20px;
}
.tab {
  padding: 10px 16px;
  font-size: 13px;
  font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: color 0.15s, border-color 0.15s;
  background: none;
  border-top: none;
  border-left: none;
  border-right: none;
  margin-bottom: -1px;
}
.tab.active { color: var(--orange); border-bottom-color: var(--orange); }
.tab:hover:not(.active) { color: var(--cream); }

/* Tab content */
.tab-content {}

/* Prod cats */
.prod-cats {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 16px;
}
.pcat-chip {
  padding: 5px 12px;
  border-radius: 20px;
  border: 1px solid var(--line);
  font-size: 12px;
  font-weight: 500;
  color: var(--muted);
  cursor: pointer;
  transition: all 0.15s;
  background: transparent;
}
.pcat-chip:hover:not(.active) { border-color: var(--orange); color: var(--orange); }
.pcat-chip.active { color: #fff; font-weight: 700; }

/* Prod section label */
.prod-section-label {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--muted-2);
  margin-bottom: 10px;
  margin-top: 4px;
}

/* Prods grid */
.prods-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}
.prod-card { cursor: pointer; }

/* Empty */
.empty-prod {
  text-align: center;
  padding: 60px 20px;
}
.ep-icon { font-size: 40px; margin-bottom: 12px; }
.ep-text { font-size: 14px; color: var(--muted); }

/* Sobre */
.content-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--cream);
  margin-bottom: 10px;
}
.body-text {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.7;
}
.sobre-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-top: 20px;
}
.sobre-info-item {
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 12px 14px;
}
.sii-label {
  font-size: 10px;
  font-family: var(--mono);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted-2);
  margin-bottom: 4px;
}
.sii-value { font-size: 14px; color: var(--cream); font-weight: 600; }
.sii-value.link { color: var(--green); text-decoration: none; }
.sii-value.link:hover { text-decoration: underline; }

/* Produto modal */
.prod-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.75);
  backdrop-filter: blur(4px);
  z-index: 200;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.prod-modal {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 20px;
  max-width: 480px;
  width: 100%;
  overflow: hidden;
  position: relative;
  max-height: 90vh;
  overflow-y: auto;
}
.modal-close {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 32px; height: 32px;
  background: rgba(0,0,0,0.5);
  border-radius: 50%;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  z-index: 1;
  transition: background 0.15s;
}
.modal-close:hover { background: rgba(0,0,0,0.8); }

.pm-gallery {
  display: flex;
  gap: 4px;
  overflow-x: auto;
  background: var(--bg-2);
}
.pm-img {
  width: 100%;
  height: 240px;
  object-fit: cover;
  flex-shrink: 0;
  cursor: pointer;
  opacity: 0.6;
  transition: opacity 0.15s;
}
.pm-img:first-child { opacity: 1; }
.pm-img.active { opacity: 1; }

.pm-no-img {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pm-body {
  padding: 20px;
}
.pm-cat {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--muted-2);
  margin-bottom: 6px;
}
.pm-nome {
  font-size: 22px;
  font-weight: 800;
  color: var(--cream);
  font-family: var(--display);
  letter-spacing: -0.02em;
  margin-bottom: 8px;
}
.pm-preco-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.pm-de {
  font-size: 14px;
  color: var(--muted);
  text-decoration: line-through;
}
.pm-por {
  font-size: 24px;
  font-weight: 800;
  font-family: var(--display);
}
.pm-off {
  background: rgba(43,217,107,0.15);
  color: var(--green);
  font-size: 11px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 20px;
}
.pm-desc {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.6;
  margin-bottom: 16px;
}
.btn-pm-wpp {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 12px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
  transition: filter 0.15s;
}
.btn-pm-wpp:hover { filter: brightness(1.1); }

/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-active .prod-modal,
.modal-leave-active .prod-modal {
  transition: transform 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from .prod-modal,
.modal-leave-to .prod-modal {
  transform: scale(0.95) translateY(10px);
}

.fade-up {
  animation: fade-up 0.3s ease both;
}
@keyframes fade-up {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

.no-loja { padding: 40px; color: var(--muted); }

@media (max-width: 768px) {
  .back-btn { margin: 16px 20px 0; }
  .loja-hero { margin: 12px 20px 0; }
  .stats-strip { margin: 10px 20px; }
  .actions-bar { padding: 0 20px 10px; }
  .detail-layout { padding: 0 20px; }
  .prods-grid { grid-template-columns: repeat(2, 1fr); }
  .sobre-info-grid { grid-template-columns: 1fr; }
}
</style>
