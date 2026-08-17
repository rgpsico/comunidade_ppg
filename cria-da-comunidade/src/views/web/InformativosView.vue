<template>
  <div class="info-view fade-up">
    <div class="page-header">
      <h1 class="page-title display">Informativos</h1>
      <p class="page-sub">Avisos oficiais da comunidade</p>
    </div>

    <div v-if="data.loading" class="empty-state">Carregando…</div>

    <div v-else-if="!data.informativos.length" class="empty-state">
      <p>Nenhum informativo publicado ainda.</p>
    </div>

    <div v-else class="info-list">
      <div
        v-for="inf in data.informativos"
        :key="inf.id"
        class="info-card"
        :class="{ urgente: inf.urgente }"
        @click="ui.openInformativo(inf)"
        style="cursor:pointer"
      >
        <div class="info-top">
          <div class="info-badges">
            <span v-if="inf.urgente" class="badge badge-urgente">⚠ Urgente</span>
            <span v-if="inf.fonte" class="badge badge-fonte">{{ inf.fonte }}</span>
          </div>
          <span class="info-data">{{ formatData(inf.dataOcorrencia ?? inf.createdAt) }}</span>
        </div>

        <h2 class="info-titulo">{{ inf.titulo }}</h2>
        <p class="info-corpo">{{ inf.corpo }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useDataStore } from '../../stores/data'
import { useUiStore } from '../../stores/ui'

const data = useDataStore()
const ui   = useUiStore()

function formatData(iso: string | null): string {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })
}
</script>

<style scoped>
.info-view { padding: 28px 32px; max-width: 800px; }

.page-header { margin-bottom: 28px; }
.page-title { font-size: 32px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 4px; }
.page-sub { font-size: 14px; color: var(--muted); }

.empty-state { text-align: center; color: var(--muted); padding: 60px 0; font-size: 14px; }

.info-list { display: flex; flex-direction: column; gap: 16px; }

.info-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-2xl);
  padding: 22px 24px;
  transition: border-color 0.15s;
}
.info-card.urgente {
  border-color: rgba(239, 68, 68, 0.4);
  background: rgba(239, 68, 68, 0.04);
}

.info-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}

.info-badges { display: flex; gap: 6px; flex-wrap: wrap; }

.badge {
  font-size: 11px;
  font-weight: 700;
  font-family: var(--mono);
  padding: 3px 10px;
  border-radius: 999px;
  letter-spacing: 0.04em;
}
.badge-urgente { background: rgba(239,68,68,0.15); color: #ef4444; }
.badge-fonte   { background: rgba(255,94,26,0.12); color: var(--orange); }

.info-data {
  font-size: 11px;
  font-family: var(--mono);
  color: var(--muted-2);
  white-space: nowrap;
}

.info-titulo {
  font-family: var(--display);
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.02em;
  line-height: 1.3;
  margin-bottom: 10px;
}

.info-corpo {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.65;
  white-space: pre-line;
}

@media (max-width: 768px) {
  .info-view { padding: 16px; }
}
</style>
