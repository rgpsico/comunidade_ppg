<template>
  <div class="lojas-page">
    <!-- Hero -->
    <div class="lojas-hero">
      <div class="hero-content">
        <div class="hero-eyebrow">🏪 Comércio local</div>
        <h1 class="hero-title">Lojas da<br><span class="hero-accent">Comunidade</span></h1>
        <p class="hero-sub">Compre de quem é da quebrada. Cada compra fortalece o nosso bairro.</p>
        <div class="hero-stats">
          <div class="hstat">
            <div class="hstat-num">{{ data.loading ? '…' : data.lojas.length }}</div>
            <div class="hstat-label">Lojas</div>
          </div>
          <div class="hstat-sep"></div>
          <div class="hstat">
            <div class="hstat-num">{{ data.loading ? '…' : totalProdutos }}</div>
            <div class="hstat-label">Produtos</div>
          </div>
          <div class="hstat-sep"></div>
          <div class="hstat">
            <div class="hstat-num">{{ categorias.length }}</div>
            <div class="hstat-label">Categorias</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Busca + filtros -->
    <div class="filter-bar">
      <div class="search-wrap">
        <svg class="s-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input
          v-model="ui.lojasFilters.search"
          class="search-input"
          placeholder="Buscar loja ou produto…"
        />
      </div>

      <div class="cat-chips">
        <button
          v-for="cat in ['Todas', ...categorias]"
          :key="cat"
          class="chip"
          :class="{ active: ui.lojasFilters.catActive === cat }"
          @click="ui.lojasFilters.catActive = cat"
        >{{ cat }}</button>
      </div>
    </div>

    <!-- Loja destaque (verificada) -->
    <template v-if="!data.loading && featuredLoja">
      <div class="section-label">⭐ Destaque</div>
      <div class="featured-loja" @click="ui.openLoja(featuredLoja)">
        <div class="fl-capa" :style="flCapaStyle">
          <div class="fl-overlay"></div>
          <div class="fl-verified">✓ Loja Verificada</div>
        </div>
        <div class="fl-body">
          <img :src="featuredLoja.logoUrl" :alt="featuredLoja.nome" class="fl-logo" />
          <div class="fl-text">
            <div class="fl-cat">{{ featuredLoja.categoria }}</div>
            <div class="fl-nome">{{ featuredLoja.nome }}</div>
            <div class="fl-desc">{{ featuredLoja.descricao }}</div>
            <div class="fl-actions">
              <button class="btn-fl" :style="{ background: featuredLoja.cor1 }">
                Ver produtos ({{ featuredLoja.produtosCount }}) →
              </button>
              <a
                v-if="featuredLoja.whatsapp"
                :href="`https://wa.me/${featuredLoja.whatsapp}`"
                target="_blank"
                rel="noopener"
                class="btn-fl-wpp"
                @click.stop
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Grid de lojas -->
    <div class="section-label">
      {{ ui.lojasFilters.catActive === 'Todas' && !ui.lojasFilters.search ? 'Todas as lojas' : 'Resultados' }}
      <span class="count-tag">{{ filteredLojas.length }}</span>
    </div>

    <!-- Skeleton -->
    <div v-if="data.loading" class="lojas-grid">
      <div v-for="i in 6" :key="i" class="loja-skeleton"></div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!filteredLojas.length" class="empty-state">
      <div class="empty-icon">🏪</div>
      <div class="empty-title">Nenhuma loja encontrada</div>
      <div class="empty-sub">Tente outro filtro ou busca</div>
    </div>

    <!-- Cards -->
    <div v-else class="lojas-grid">
      <LojaCard
        v-for="loja in filteredLojas"
        :key="loja.id"
        :loja="loja"
        @click="ui.openLoja(loja)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useDataStore } from '../../stores/data'
import { useUiStore } from '../../stores/ui'
import LojaCard from '../../components/ui/LojaCard.vue'

const data = useDataStore()
const ui = useUiStore()

const categorias = computed(() => {
  const set = new Set(data.lojas.map(l => l.categoria))
  return Array.from(set).sort()
})

const totalProdutos = computed(() =>
  data.lojas.reduce((acc, l) => acc + l.produtosCount, 0)
)

const filteredLojas = computed(() => {
  let list = data.lojas
  if (ui.lojasFilters.catActive !== 'Todas') {
    list = list.filter(l => l.categoria === ui.lojasFilters.catActive)
  }
  if (ui.lojasFilters.search.trim()) {
    const q = ui.lojasFilters.search.toLowerCase()
    list = list.filter(l =>
      l.nome.toLowerCase().includes(q) ||
      l.descricao.toLowerCase().includes(q) ||
      l.categoria.toLowerCase().includes(q)
    )
  }
  return list
})

const featuredLoja = computed(() =>
  data.lojas.find(l => l.verificado) ?? null
)

const flCapaStyle = computed(() => {
  if (!featuredLoja.value) return {}
  const l = featuredLoja.value
  return l.capaUrl
    ? { backgroundImage: `url(${l.capaUrl})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : { background: `linear-gradient(135deg, ${l.cor1}, ${l.cor2})` }
})
</script>

<style scoped>
.lojas-page {
  padding: 0 0 80px;
  max-width: 900px;
  margin: 0 auto;
}

/* Hero */
.lojas-hero {
  padding: 48px 32px 32px;
  background: linear-gradient(180deg, rgba(255,94,26,0.06) 0%, transparent 100%);
  border-bottom: 1px solid var(--line);
  margin-bottom: 0;
}
.hero-eyebrow {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--orange);
  margin-bottom: 10px;
}
.hero-title {
  font-family: var(--display);
  font-size: 40px;
  font-weight: 900;
  line-height: 1.0;
  letter-spacing: -0.03em;
  color: var(--cream);
  margin-bottom: 10px;
}
.hero-accent { color: var(--orange); }
.hero-sub {
  font-size: 15px;
  color: var(--muted);
  max-width: 400px;
  line-height: 1.5;
  margin-bottom: 24px;
}
.hero-stats {
  display: flex;
  align-items: center;
  gap: 16px;
}
.hstat { text-align: center; }
.hstat-num {
  font-family: var(--display);
  font-size: 24px;
  font-weight: 800;
  color: var(--cream);
  line-height: 1;
}
.hstat-label {
  font-size: 11px;
  color: var(--muted);
  margin-top: 2px;
}
.hstat-sep {
  width: 1px;
  height: 28px;
  background: var(--line);
}

/* Filters */
.filter-bar {
  padding: 16px 32px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  border-bottom: 1px solid var(--line);
}
.search-wrap {
  position: relative;
  max-width: 380px;
}
.s-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  pointer-events: none;
}
.search-input {
  width: 100%;
  padding: 9px 14px 9px 36px;
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 10px;
  color: var(--cream);
  font-size: 13px;
  transition: border-color 0.15s;
}
.search-input:focus { outline: none; border-color: var(--orange); }
.search-input::placeholder { color: var(--muted); }

.cat-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.chip {
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
.chip:hover { border-color: var(--orange); color: var(--orange); }
.chip.active {
  background: var(--orange);
  border-color: var(--orange);
  color: #fff;
  font-weight: 700;
}

/* Section label */
.section-label {
  padding: 20px 32px 10px;
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--muted-2);
  display: flex;
  align-items: center;
  gap: 8px;
}
.count-tag {
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 20px;
  padding: 1px 8px;
  font-size: 10px;
  color: var(--muted);
}

/* Featured loja */
.featured-loja {
  margin: 0 32px 20px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 18px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.18s, box-shadow 0.18s;
}
.featured-loja:hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 40px -8px rgba(0,0,0,0.4);
}
.fl-capa {
  height: 140px;
  position: relative;
}
.fl-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.6) 100%);
}
.fl-verified {
  position: absolute;
  bottom: 12px;
  left: 16px;
  background: rgba(43,217,107,0.9);
  color: #000;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
}
.fl-body {
  display: flex;
  gap: 16px;
  padding: 0 20px 20px;
  align-items: flex-start;
  margin-top: -32px;
}
.fl-logo {
  width: 64px;
  height: 64px;
  border-radius: 14px;
  border: 3px solid var(--card);
  object-fit: cover;
  flex-shrink: 0;
  background: var(--bg-2);
}
.fl-text {
  flex: 1;
  padding-top: 36px;
}
.fl-cat {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--orange);
  margin-bottom: 4px;
}
.fl-nome {
  font-size: 20px;
  font-weight: 800;
  color: var(--cream);
  margin-bottom: 4px;
}
.fl-desc {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.5;
  margin-bottom: 14px;
  max-width: 500px;
}
.fl-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}
.btn-fl {
  padding: 9px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  color: #fff;
  cursor: pointer;
  transition: filter 0.15s;
}
.btn-fl:hover { filter: brightness(1.1); }
.btn-fl-wpp {
  width: 38px; height: 38px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 10px;
  background: rgba(43,217,107,0.15);
  color: var(--green);
  border: 1px solid rgba(43,217,107,0.3);
  transition: background 0.15s;
  text-decoration: none;
  flex-shrink: 0;
}
.btn-fl-wpp:hover { background: rgba(43,217,107,0.25); }

/* Grid */
.lojas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
  padding: 0 32px;
}

/* Skeleton */
.loja-skeleton {
  height: 320px;
  border-radius: 16px;
  background: var(--card);
  border: 1px solid var(--line);
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* Empty */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-title { font-size: 18px; font-weight: 700; color: var(--cream); margin-bottom: 6px; }
.empty-sub { font-size: 14px; color: var(--muted); }

@media (max-width: 768px) {
  .lojas-hero { padding: 28px 20px 24px; }
  .hero-title { font-size: 30px; }
  .filter-bar { padding: 12px 20px; }
  .section-label { padding: 16px 20px 8px; }
  .featured-loja { margin: 0 20px 16px; }
  .lojas-grid { padding: 0 20px; grid-template-columns: 1fr; }
}
</style>
