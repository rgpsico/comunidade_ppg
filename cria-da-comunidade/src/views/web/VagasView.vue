<template>
  <div class="vagas-page fade-up">
    <div class="page-head">
      <div>
        <div class="eyebrow">💼 vagas</div>
        <h1 class="page-title display">Vagas perto de você</h1>
        <p class="page-sub">47 novas hoje · CLT, freela, diária e divulgação</p>
      </div>
      <button class="ghost-btn">+ Anunciar vaga</button>
    </div>

    <!-- Tabs -->
    <div class="vaga-tabs">
      <button
        v-for="tab in tabs"
        :key="tab.label"
        class="tab-btn"
        :class="{ active: ui.vagasFilters.tabActive === tab.label }"
        @click="ui.vagasFilters.tabActive = tab.label"
      >
        {{ tab.label }}
        <span class="tab-count">{{ tab.count }}</span>
      </button>
    </div>

    <!-- Filtros -->
    <div class="filter-bar">
      <div class="filter-pills">
        <span class="filter-label">Filtros:</span>
        <button class="filter-pill">Área ⌄</button>
        <button class="filter-pill">Salário ⌄</button>
        <button class="filter-pill">Distância ⌄</button>
        <button
          class="filter-pill toggle"
          :class="{ active: ui.vagasFilters.semExp }"
          @click="ui.vagasFilters.semExp = !ui.vagasFilters.semExp"
        >
          <span class="toggle-dot" :class="{ on: ui.vagasFilters.semExp }"></span>
          Sem experiência
        </button>
        <button
          class="filter-pill toggle"
          :class="{ active: ui.vagasFilters.mesmoDia }"
          @click="ui.vagasFilters.mesmoDia = !ui.vagasFilters.mesmoDia"
        >
          <span class="toggle-dot" :class="{ on: ui.vagasFilters.mesmoDia }"></span>
          Mesmo dia
        </button>
        <button class="filter-clear" @click="clearFilters">limpar</button>
      </div>
      <div class="sort-control">
        <button class="sort-btn">Mais recentes ⌄</button>
      </div>
    </div>

    <!-- Meta -->
    <div class="result-meta">
      <span>{{ filteredVagas.length }} vagas · {{ ui.vagasFilters.tabActive }}</span>
      <span class="urgent-meta">
        <span class="urgent-dot"></span>
        8 urgentes
      </span>
    </div>

    <!-- Banner banco de talentos -->
    <div class="talent-banner" @click="auth.isAuthenticated ? ui.goTo('curriculos') : ui.goTo('login')">
      <div class="tb-icon">📄</div>
      <div class="tb-body">
        <div class="tb-title">Cadastre seu currículo e seja encontrado automaticamente</div>
        <div class="tb-sub">Quando uma nova vaga for cadastrada, seu contato é enviado direto para o recrutador — de graça.</div>
      </div>
      <div class="tb-cta">{{ auth.isAuthenticated ? 'Cadastrar currículo →' : 'Entrar grátis →' }}</div>
    </div>

    <!-- Grid de vagas -->
    <div class="vagas-grid">
      <VagaCard
        v-for="vaga in filteredVagas"
        :key="vaga.id"
        :vaga="vaga"
        @click="ui.openVaga(vaga)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import { useAuthStore } from '../../stores/auth'
import VagaCard from '../../components/ui/VagaCard.vue'

const ui = useUiStore()
const data = useDataStore()
const auth = useAuthStore()

const tabs = [
  { label: 'Todas', count: '312' },
  { label: 'CLT', count: '87' },
  { label: 'Freela', count: '124' },
  { label: 'Diária', count: '68' },
  { label: 'Divulgação', count: '33' },
]

const filteredVagas = computed(() => {
  if (ui.vagasFilters.tabActive === 'Todas') return data.vagas
  return data.vagas.filter(v => v.type === ui.vagasFilters.tabActive)
})

function clearFilters() {
  ui.vagasFilters.tabActive = 'Todas'
  ui.vagasFilters.semExp = false
  ui.vagasFilters.mesmoDia = false
}
</script>

<style scoped>
.vagas-page { padding: 28px 32px; max-width: 1480px; }

.page-head {
  display: flex; align-items: flex-end; justify-content: space-between;
  margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
}
.page-title { font-size: clamp(28px, 3.5vw, 48px); font-weight: 800; margin: 6px 0 4px; }
.page-sub { font-size: 14px; color: var(--muted); }
.ghost-btn {
  padding: 10px 18px; border-radius: 10px;
  border: 1px solid var(--line); background: transparent;
  color: var(--cream); font-size: 13px; font-weight: 600; cursor: pointer;
  transition: all 0.15s;
}
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card); }

.vaga-tabs {
  display: flex; gap: 4px; margin-bottom: 16px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 4px;
  width: fit-content;
}
.tab-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 8px;
  font-size: 13px; font-weight: 600; color: var(--muted);
  cursor: pointer; transition: all 0.15s;
}
.tab-btn:hover { color: var(--cream); }
.tab-btn.active { background: rgba(255,94,26,0.12); color: var(--orange); }
.tab-count {
  font-family: var(--mono); font-size: 10px;
  padding: 2px 6px; border-radius: 999px;
  background: var(--card-2); color: var(--muted-2);
}
.tab-btn.active .tab-count { background: rgba(255,94,26,0.15); color: var(--orange); }

.filter-bar {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 10px;
  margin-bottom: 14px;
  padding: 12px 16px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
}
.filter-pills { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-label { font-size: 12px; color: var(--muted); }
.filter-pill {
  display: flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 999px;
  border: 1px solid var(--line); background: var(--card-2);
  font-size: 11px; color: var(--cream); cursor: pointer;
  transition: all 0.15s;
}
.filter-pill:hover { border-color: var(--line-strong); }
.filter-pill.active { border-color: rgba(255,94,26,0.3); background: rgba(255,94,26,0.08); }
.toggle-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--muted-2); transition: background 0.15s; }
.toggle-dot.on { background: var(--green); }
.filter-clear { font-size: 11px; color: var(--muted); cursor: pointer; padding: 5px; }
.filter-clear:hover { color: var(--cream); }
.sort-control { font-size: 12px; }
.sort-btn { color: var(--cream); font-size: 12px; font-weight: 600; cursor: pointer; }

.result-meta {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 12px; color: var(--muted); margin-bottom: 18px;
}
.urgent-meta { display: flex; align-items: center; gap: 6px; color: var(--orange); font-weight: 600; }
.urgent-dot {
  width: 7px; height: 7px;
  background: var(--orange); border-radius: 50%;
  animation: pulse-dot 1.6s ease-out infinite; color: var(--orange);
}

/* Banner banco de talentos */
.talent-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  background: linear-gradient(135deg, rgba(255,94,26,0.10), rgba(255,210,63,0.06));
  border: 1px solid rgba(255,94,26,0.25);
  border-radius: 14px;
  padding: 16px 20px;
  margin-bottom: 20px;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
}
.talent-banner:hover { border-color: var(--orange); background: rgba(255,94,26,0.14); }
.tb-icon { font-size: 28px; flex-shrink: 0; }
.tb-body { flex: 1; min-width: 0; }
.tb-title { font-size: 14px; font-weight: 700; color: var(--cream); margin-bottom: 3px; }
.tb-sub { font-size: 12px; color: var(--muted); line-height: 1.4; }
.tb-cta {
  white-space: nowrap;
  font-size: 13px;
  font-weight: 700;
  color: var(--orange);
  flex-shrink: 0;
}
@media (max-width: 600px) {
  .talent-banner { flex-wrap: wrap; gap: 10px; }
  .tb-cta { width: 100%; text-align: right; }
}

.vagas-grid {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
  padding-bottom: 40px;
}

@media (max-width: 768px) {
  .vagas-page { padding: 16px; }
  .vagas-grid { grid-template-columns: 1fr; }
  .tab-bar { overflow-x: auto; flex-wrap: nowrap; }
  .filter-pills { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; }
}
</style>
