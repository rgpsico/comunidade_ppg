<template>
  <article class="chamado-card" :class="`urgencia-${chamado.urgencia}`" @click="$emit('click')">
    <div class="card-top">
      <span class="badge-tipo" :class="chamado.tipo">
        {{ chamado.tipo === 'problema' ? '⚠️ Problema' : '🔧 Serviço' }}
      </span>
      <span class="badge-urgencia" :class="chamado.urgencia">
        {{ urgenciaLabel }}
      </span>
      <span class="badge-status" :class="chamado.status">
        {{ statusLabel }}
      </span>
    </div>

    <h3 class="card-titulo">{{ chamado.titulo }}</h3>
    <p class="card-desc">{{ chamado.descricao }}</p>

    <div class="card-meta">
      <span class="meta-chip">📂 {{ chamado.categoria }}</span>
      <span v-if="chamado.local" class="meta-chip">📍 {{ chamado.local }}</span>
      <span v-if="chamado.estimativa_valor" class="meta-chip">💰 {{ chamado.estimativa_valor }}</span>
    </div>

    <div class="card-footer">
      <span class="card-user">👤 {{ chamado.user?.name ?? 'Usuário' }}</span>
      <span class="card-date">{{ formatDate(chamado.created_at) }}</span>
    </div>
  </article>
</template>

<script setup lang="ts">
import type { Chamado } from '../../types'

const props = defineProps<{ chamado: Chamado }>()
defineEmits<{ click: [] }>()

const urgenciaLabel = {
  normal: 'Normal',
  urgente: '⚡ Urgente',
  critico: '🚨 Crítico',
}[props.chamado.urgencia] ?? props.chamado.urgencia

const statusLabel = {
  aberto: 'Aberto',
  aceito: 'Aceito',
  em_andamento: 'Em andamento',
  resolvido: 'Resolvido',
  cancelado: 'Cancelado',
}[props.chamado.status] ?? props.chamado.status

function formatDate(iso: string): string {
  const d = new Date(iso)
  const diff = Math.floor((Date.now() - d.getTime()) / 86400000)
  if (diff === 0) return 'hoje'
  if (diff === 1) return 'ontem'
  return `${diff}d atrás`
}
</script>

<style scoped>
.chamado-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  padding: 18px;
  cursor: pointer;
  transition: border-color 0.2s, transform 0.15s;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.chamado-card:hover {
  border-color: var(--orange);
  transform: translateY(-2px);
}

.chamado-card.urgencia-urgente { border-left: 3px solid var(--yellow); }
.chamado-card.urgencia-critico { border-left: 3px solid #EF4444; }

.card-top {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.badge-tipo,
.badge-urgencia,
.badge-status {
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: var(--radius-full);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.badge-tipo.problema { background: #FF5E1A22; color: var(--orange); }
.badge-tipo.servico  { background: #2BD96B22; color: var(--green); }

.badge-urgencia.normal  { background: #2BD96B18; color: var(--green); }
.badge-urgencia.urgente { background: #FFD23F22; color: var(--yellow); }
.badge-urgencia.critico { background: #EF444422; color: #EF4444; }

.badge-status.aberto       { background: #3B82F622; color: #60A5FA; }
.badge-status.aceito       { background: #FFD23F22; color: var(--yellow); }
.badge-status.em_andamento { background: #8B5CF622; color: #A78BFA; }
.badge-status.resolvido    { background: #2BD96B22; color: var(--green); }
.badge-status.cancelado    { background: #6B728022; color: var(--muted); }

.card-titulo {
  font-size: 15px;
  font-weight: 700;
  color: var(--cream);
  line-height: 1.3;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-desc {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.5;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.meta-chip {
  font-size: 11px;
  color: var(--muted);
  background: var(--bg);
  padding: 3px 8px;
  border-radius: var(--radius-full);
  border: 1px solid var(--line);
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 4px;
}

.card-user {
  font-size: 12px;
  color: var(--muted);
}

.card-date {
  font-size: 11px;
  color: var(--muted);
  font-family: var(--mono);
}
</style>
