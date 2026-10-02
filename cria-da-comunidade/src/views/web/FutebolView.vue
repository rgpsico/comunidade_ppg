<template>
  <div class="futebol fade-up">
    <div class="view-header">
      <div>
        <div class="eyebrow">⚽ bola rolando</div>
        <h1 class="view-title display">Futebol <span class="text-gradient-orange">da Comunidade</span></h1>
        <p class="view-sub">Entre na fila da pelada direto do celular e acompanhe quem joga a seguir.</p>
      </div>
      <button v-if="aba === 'peladas'" class="btn-primary" @click="criarPelada">+ Criar pelada</button>
    </div>

    <div class="tabs" role="tablist">
      <button role="tab" class="tab" :class="{ active: aba === 'peladas' }" :aria-selected="aba === 'peladas'" @click="aba = 'peladas'">⚽ Peladas</button>
      <button role="tab" class="tab" :class="{ active: aba === 'quadras' }" :aria-selected="aba === 'quadras'" @click="aba = 'quadras'">🏟️ Quadras e horários</button>
    </div>

    <QuadrasPanel v-if="aba === 'quadras'" />

    <template v-else>
    <div v-if="futebol.loadingList" class="loading-msg">Carregando peladas...</div>
    <div v-else-if="futebol.peladas.length === 0" class="empty-state">
      <div class="empty-icon">⚽</div>
      <p>Nenhuma pelada cadastrada ainda.</p>
      <button class="btn-secondary" @click="criarPelada">Criar a primeira pelada</button>
    </div>
    <div v-else class="peladas-grid">
      <PeladaCard
        v-for="p in futebol.peladas"
        :key="p.id"
        :pelada="p"
        @click="ui.openPelada(p.id)"
      />
    </div>
    </template>

    <FutebolSetupWizard
      v-if="showWizard"
      @close="showWizard = false"
      @created="onCreated"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useAuthStore } from '../../stores/auth'
import { useDataStore } from '../../stores/data'
import { useFutebolStore } from '../../stores/futebol'
import PeladaCard from '../../components/ui/PeladaCard.vue'
import FutebolSetupWizard from '../../components/ui/FutebolSetupWizard.vue'
import QuadrasPanel from '../../components/ui/QuadrasPanel.vue'

const ui = useUiStore()
const auth = useAuthStore()
const data = useDataStore()
const futebol = useFutebolStore()
const showWizard = ref(false)
const aba = ref<'peladas' | 'quadras'>('peladas')

onMounted(() => futebol.fetchPeladas())
watch(() => data.activeComunidadeId, () => futebol.fetchPeladas())

function criarPelada() {
  if (!auth.isAuthenticated) {
    ui.goTo('login')
    return
  }
  showWizard.value = true
}

function onCreated(id: number) {
  showWizard.value = false
  ui.openPelada(id)
}
</script>

<style scoped>
.futebol { padding: 40px 48px; max-width: 1200px; margin: 0 auto; }

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 32px;
  flex-wrap: wrap;
}
.eyebrow {
  font-family: var(--mono);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--orange);
  margin-bottom: 8px;
}
.view-title { font-size: 32px; font-weight: 900; color: var(--cream); margin: 0 0 8px; }
.view-sub { font-size: 14px; color: var(--muted); margin: 0; }

.tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
.tab {
  min-height: 44px;
  padding: 0 18px;
  border-radius: var(--radius-full);
  background: transparent;
  border: 1px solid var(--line);
  color: var(--muted);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}
.tab.active, .tab:hover { background: var(--orange); border-color: var(--orange); color: #fff; }

.peladas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
}

.loading-msg, .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
.empty-icon { font-size: 48px; margin-bottom: 12px; }

.btn-primary {
  min-height: 44px;
  padding: 0 22px;
  background: var(--orange);
  color: #fff;
  border: none;
  border-radius: var(--radius-full);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
}
.btn-primary:hover { filter: brightness(1.1); }
.btn-secondary {
  min-height: 44px;
  padding: 0 22px;
  background: transparent;
  color: var(--muted);
  border: 1px solid var(--line);
  border-radius: var(--radius-full);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}
.btn-secondary:hover { border-color: var(--muted); color: var(--cream); }

@media (max-width: 768px) {
  .futebol { padding: 20px 16px; }
  .peladas-grid { grid-template-columns: 1fr; }
}
</style>
