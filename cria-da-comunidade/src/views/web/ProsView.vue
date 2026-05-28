<template>
  <div class="pros-page fade-up">
    <div class="page-head">
      <div>
        <div class="eyebrow">⭐ profissionais</div>
        <h1 class="page-title display">Profissionais perto de você</h1>
        <p class="page-sub">2.413 verificados · Complexo do Alemão e arredores</p>
      </div>
      <div class="view-toggle">
        <button class="toggle-btn active">Grade</button>
        <button class="toggle-btn">Lista</button>
        <button class="toggle-btn">Mapa</button>
      </div>
    </div>

    <!-- Categorias -->
    <div class="cat-chips">
      <button
        v-for="cat in categories"
        :key="cat.label"
        class="cat-chip"
        :class="{ active: ui.prosFilters.catActive === cat.label }"
        @click="ui.prosFilters.catActive = cat.label"
      >
        <span class="cat-icon" :style="{ background: cat.color + '22' }">{{ cat.icon }}</span>
        <span>{{ cat.label }}</span>
        <span class="cat-count">{{ cat.count }}</span>
      </button>
    </div>

    <!-- Filtros -->
    <div class="filter-bar">
      <div class="filter-pills">
        <span class="filter-label">Filtros:</span>
        <button class="filter-pill">Distância · 1km ⌄</button>
        <button class="filter-pill">Preço · R$ 0–200 ⌄</button>
        <button class="filter-pill">Avaliação · 4.5+ ⌄</button>
        <button
          class="filter-pill toggle"
          :class="{ active: ui.prosFilters.verificado }"
          @click="ui.prosFilters.verificado = !ui.prosFilters.verificado"
        >
          <span class="toggle-dot" :class="{ on: ui.prosFilters.verificado }"></span>
          Verificado
        </button>
        <button
          class="filter-pill toggle"
          :class="{ active: ui.prosFilters.atendeEmCasa }"
          @click="ui.prosFilters.atendeEmCasa = !ui.prosFilters.atendeEmCasa"
        >
          <span class="toggle-dot" :class="{ on: ui.prosFilters.atendeEmCasa }"></span>
          Atende em casa
        </button>
        <button class="filter-clear" @click="clearFilters">limpar</button>
      </div>
      <div class="sort-control">
        Ordenar: <button class="sort-btn">{{ ui.prosFilters.sort }} ⌄</button>
      </div>
    </div>

    <!-- Meta -->
    <div class="result-meta">
      <span>{{ filteredPros.length }} resultados · {{ ui.prosFilters.catActive }}</span>
      <span class="online-meta">
        <span class="live-dot"></span>
        148 online agora
      </span>
    </div>

    <!-- Grid -->
    <div class="pros-grid">
      <ProCard
        v-for="pro in filteredPros"
        :key="pro.id"
        :pro="pro"
        @click="ui.openPro(pro)"
      />
    </div>

    <div class="load-more">
      <button class="load-btn">Ver mais profissionais ⌄</button>
      <span class="load-meta">mostrando {{ filteredPros.length }} de 2.413</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import ProCard from '../../components/ui/ProCard.vue'

const ui = useUiStore()
const data = useDataStore()

const CATEGORY_META: Record<string, { icon: string; color: string }> = {
  'Todos':       { icon: '🔍', color: '#FF5E1A' },
  'Beleza':      { icon: '💅', color: '#FF5E1A' },
  'Construção':  { icon: '🔨', color: '#FFD23F' },
  'Casa':        { icon: '🏠', color: '#2BD96B' },
  'Transporte':  { icon: '🏍️', color: '#FFD23F' },
  'Eventos':     { icon: '🎉', color: '#FF5E1A' },
  'Saúde':       { icon: '💊', color: '#2BD96B' },
  'Alimentação': { icon: '🍔', color: '#F59E0B' },
  'Pet':         { icon: '🐾', color: '#8B5CF6' },
  'Serviços':    { icon: '🛠️', color: '#3B82F6' },
}

const categories = computed(() => {
  const counts: Record<string, number> = {}
  for (const pro of data.pros) {
    counts[pro.category] = (counts[pro.category] ?? 0) + 1
  }

  const cats = [
    { label: 'Todos', icon: '🔍', count: String(data.pros.length), color: '#FF5E1A' },
  ]

  // categorias que têm profissionais no banco
  const existentes = Object.keys(counts).sort()
  for (const label of existentes) {
    const meta = CATEGORY_META[label] ?? { icon: '⚡', color: '#FF5E1A' }
    cats.push({ label, icon: meta.icon, count: String(counts[label]), color: meta.color })
  }

  // adiciona categorias sem profissionais ainda (count 0)
  for (const label of Object.keys(CATEGORY_META)) {
    if (label !== 'Todos' && !counts[label]) {
      const meta = CATEGORY_META[label]
      cats.push({ label, icon: meta.icon, count: '0', color: meta.color })
    }
  }

  return cats
})

const filteredPros = computed<typeof data.pros[0][]>(() => {
  let result = [...data.pros]
  if (ui.prosFilters.catActive !== 'Todos') {
    result = result.filter(p => p.category === ui.prosFilters.catActive)
  }
  if (ui.prosFilters.verificado) result = result.filter(p => p.verified)
  if (ui.prosFilters.atendeEmCasa) result = result.filter(p => p.tags.some(t => t.toLowerCase().includes('casa')))
  return result
})

function clearFilters() {
  ui.prosFilters.catActive = 'Todos'
  ui.prosFilters.verificado = false
  ui.prosFilters.atendeEmCasa = false
}
</script>

<style scoped>
.pros-page { padding: 28px 32px; max-width: 1480px; }

.page-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}
.page-title { font-size: clamp(28px, 3.5vw, 48px); font-weight: 800; margin: 6px 0 4px; }
.page-sub { font-size: 14px; color: var(--muted); }

.view-toggle {
  display: flex;
  border: 1px solid var(--line);
  border-radius: 10px;
  overflow: hidden;
  background: var(--card);
}
.toggle-btn {
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  color: var(--muted);
  border-right: 1px solid var(--line);
  transition: background 0.15s, color 0.15s;
}
.toggle-btn:last-child { border-right: none; }
.toggle-btn.active { background: rgba(255,94,26,0.12); color: var(--orange); }
.toggle-btn:hover:not(.active) { background: var(--card-2); color: var(--cream); }

.cat-chips {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 4px;
  margin-bottom: 16px;
}
.cat-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid var(--line);
  background: var(--card);
  white-space: nowrap;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  color: var(--muted);
  transition: all 0.15s;
  flex-shrink: 0;
}
.cat-chip:hover { border-color: var(--line-strong); color: var(--cream); }
.cat-chip.active { background: rgba(255,94,26,0.10); border-color: rgba(255,94,26,0.3); color: var(--orange); }
.cat-icon { font-size: 16px; }
.cat-count { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-2); }

.filter-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 14px;
  padding: 12px 16px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
}
.filter-pills { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-label { font-size: 12px; color: var(--muted); }
.filter-pill {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 5px 12px;
  border-radius: 999px;
  border: 1px solid var(--line);
  background: var(--card-2);
  font-size: 11px;
  color: var(--cream);
  cursor: pointer;
  transition: all 0.15s;
}
.filter-pill:hover { border-color: var(--line-strong); }
.filter-pill.active { border-color: rgba(255,94,26,0.3); background: rgba(255,94,26,0.08); }
.toggle-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--muted-2);
  transition: background 0.15s;
}
.toggle-dot.on { background: var(--green); }
.filter-clear { font-size: 11px; color: var(--muted); cursor: pointer; padding: 5px; }
.filter-clear:hover { color: var(--cream); }

.sort-control { font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 6px; }
.sort-btn { color: var(--cream); font-size: 12px; font-weight: 600; }

.result-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  color: var(--muted);
  margin-bottom: 18px;
}
.online-meta { display: flex; align-items: center; gap: 6px; color: var(--green); font-weight: 600; }
.live-dot {
  width: 7px; height: 7px;
  background: var(--green);
  border-radius: 50%;
  animation: pulse-dot 1.6s ease-out infinite;
  color: var(--green);
}

.pros-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }

.load-more {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin-top: 32px;
  padding-bottom: 40px;
}
.load-btn {
  padding: 12px 28px;
  border-radius: 12px;
  border: 1px solid var(--line);
  background: var(--card);
  font-size: 13px;
  font-weight: 600;
  color: var(--cream);
  cursor: pointer;
  transition: all 0.15s;
}
.load-btn:hover { border-color: var(--line-strong); background: var(--card-2); }
.load-meta { font-size: 11px; color: var(--muted); }

@media (max-width: 768px) {
  .pros-page { padding: 16px; }
  .pros-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .filter-row { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; }
}
</style>
