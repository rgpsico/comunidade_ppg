<template>
  <div class="ci-wrap">
    <div class="ci-card">
      <div class="ci-av" :style="{ background: userColor(comentario.user.name) }">
        {{ initials(comentario.user.name) }}
      </div>
      <div class="ci-body">
        <div class="ci-head">
          <span class="ci-name">{{ comentario.user.name }}</span>
          <span class="ci-time">{{ timeAgo(comentario.createdAt) }}</span>
        </div>
        <p class="ci-text">{{ comentario.corpo }}</p>
        <div class="ci-actions">
          <button v-if="auth.isAuthenticated" class="ci-action" @click="emit('reply', comentario)">
            Responder
          </button>
          <button v-if="comentario.isMine" class="ci-action danger" @click="emit('delete', comentario.id)">
            Excluir
          </button>
        </div>
      </div>
    </div>

    <!-- Respostas -->
    <div v-if="comentario.respostas.length" class="ci-respostas">
      <div v-for="r in comentario.respostas" :key="r.id" class="ci-card resposta">
        <div class="ci-av sm" :style="{ background: userColor(r.user.name) }">
          {{ initials(r.user.name) }}
        </div>
        <div class="ci-body">
          <div class="ci-head">
            <span class="ci-name">{{ r.user.name }}</span>
            <span class="ci-time">{{ timeAgo(r.createdAt) }}</span>
          </div>
          <p class="ci-text">{{ r.corpo }}</p>
          <div class="ci-actions">
            <button v-if="r.isMine" class="ci-action danger" @click="emit('delete', r.id, comentario.id)">
              Excluir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Comentario } from '../../types'
import { useAuthStore } from '../../stores/auth'

defineProps<{ comentario: Comentario }>()
const emit = defineEmits<{
  reply:  [comentario: Comentario]
  delete: [id: string, parentId?: string]
}>()

const auth = useAuthStore()

function initials(name: string): string {
  return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

const COLORS = ['#FF5E1A', '#2BD96B', '#FFD23F', '#5E5AFF', '#FF2D78', '#00C2C7']
function userColor(name: string): string {
  let h = 0
  for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) & 0xffffffff
  return COLORS[Math.abs(h) % COLORS.length]
}

function timeAgo(iso: string): string {
  const diff = Date.now() - new Date(iso).getTime()
  const m = Math.floor(diff / 60000)
  if (m < 60)  return `há ${m || 1}min`
  const h = Math.floor(m / 60)
  if (h < 24)  return `há ${h}h`
  const d = Math.floor(h / 24)
  if (d < 30)  return `há ${d}d`
  return `há ${Math.floor(d / 30)} meses`
}
</script>

<style scoped>
.ci-wrap { display: flex; flex-direction: column; gap: 6px; }

.ci-card {
  display: flex;
  gap: 10px;
  padding: 12px;
  background: var(--card-2);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
}
.ci-card.resposta {
  background: var(--card);
  border-radius: var(--radius-md);
}

.ci-av {
  width: 34px; height: 34px;
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display); font-weight: 700; font-size: 12px; color: var(--black);
  flex-shrink: 0;
}
.ci-av.sm { width: 28px; height: 28px; font-size: 10px; border-radius: 7px; }

.ci-body { flex: 1; min-width: 0; }
.ci-head { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; }
.ci-name { font-size: 13px; font-weight: 600; }
.ci-time { font-family: var(--mono); font-size: 10px; color: var(--muted); text-transform: uppercase; margin-left: auto; }
.ci-text { font-size: 13px; color: var(--muted); line-height: 1.55; margin-bottom: 6px; }

.ci-actions { display: flex; gap: 12px; }
.ci-action { font-size: 11px; color: var(--muted); cursor: pointer; padding: 2px 0; }
.ci-action:hover { color: var(--cream); }
.ci-action.danger:hover { color: var(--red, #e53); }

.ci-respostas { display: flex; flex-direction: column; gap: 4px; padding-left: 24px; }
</style>
