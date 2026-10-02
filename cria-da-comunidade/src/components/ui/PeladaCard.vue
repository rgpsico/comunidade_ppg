<template>
  <article class="pelada-card" @click="$emit('click')">
    <div class="card-top">
      <span class="badge-tipo">{{ TIPO_LABEL[pelada.tipo] }}</span>
      <span class="badge-fila">👥 {{ pelada.jogadores_count }} na lista</span>
    </div>

    <h3 class="card-nome">{{ pelada.nome }}</h3>

    <div class="card-meta">
      <span class="meta-chip">⚽ {{ formato }}</span>
      <span class="meta-chip">⏱ {{ pelada.tempo_jogo_min }} min</span>
      <span v-if="pelada.quando" class="meta-chip">🗓 {{ pelada.quando }}</span>
      <span v-if="pelada.local" class="meta-chip">📍 {{ pelada.local }}</span>
    </div>

    <div class="card-footer">
      <span class="card-user">Organiza: {{ pelada.organizador?.name ?? '—' }}</span>
      <span class="card-cta">Ver lista →</span>
    </div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { PeladaResumo, PeladaTipo } from '../../types'

const props = defineProps<{ pelada: PeladaResumo }>()
defineEmits<{ click: [] }>()

const TIPO_LABEL: Record<PeladaTipo, string> = {
  quadra: 'Quadra',
  society: 'Society',
  campo: 'Campo',
  racha: 'Racha',
}

const formato = computed(() =>
  props.pelada.goleiros_por_time > 0
    ? `${props.pelada.jogadores_linha} na linha + gol`
    : `${props.pelada.jogadores_linha} na linha`
)
</script>

<style scoped>
.pelada-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg, 16px);
  padding: 18px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: border-color 0.15s, transform 0.15s;
}
.pelada-card:hover { border-color: var(--orange); transform: translateY(-2px); }

.card-top { display: flex; gap: 8px; align-items: center; justify-content: space-between; }
.badge-tipo {
  font-family: var(--mono);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--green);
}
.badge-fila { font-size: 12px; color: var(--muted); }

.card-nome { font-size: 18px; font-weight: 800; color: var(--cream); margin: 0; }

.card-meta { display: flex; gap: 6px; flex-wrap: wrap; }
.meta-chip {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  background: var(--bg);
  border: 1px solid var(--line);
  color: var(--muted);
}

.card-footer { display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--muted); }
.card-cta { color: var(--orange); font-weight: 700; }
</style>
