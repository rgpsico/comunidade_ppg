<template>
  <div class="info-detail fade-up" v-if="inf">
    <button class="back-btn" @click="ui.goTo('informativos')">← Informativos</button>

    <!-- Header do aviso -->
    <div class="info-hero" :class="{ urgente: inf.urgente }">
      <div class="hero-icon">
        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path d="M22 8.5A2.5 2.5 0 0019.5 6h-15A2.5 2.5 0 002 8.5v7A2.5 2.5 0 004.5 18h3l2 3 2-3h8a2.5 2.5 0 002.5-2.5v-7z"/>
        </svg>
      </div>
      <div class="hero-content">
        <div class="hero-badges">
          <span v-if="inf.urgente" class="badge badge-urgente">⚠ Urgente</span>
          <span v-if="inf.fonte" class="badge badge-fonte">{{ inf.fonte }}</span>
        </div>
        <h1 class="hero-titulo display">{{ inf.titulo }}</h1>
        <p class="hero-data">📅 {{ formatData(inf.dataOcorrencia ?? inf.createdAt) }}</p>
      </div>
    </div>

    <!-- Corpo -->
    <div class="info-corpo">{{ inf.corpo }}</div>

    <!-- Compartilhar -->
    <div class="share-row">
      <span class="share-label">Compartilhar aviso:</span>
      <button class="share-btn" @click="copiarLink">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
          <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
        </svg>
        {{ copiado ? 'Link copiado!' : 'Copiar link' }}
      </button>
      <a :href="whatsappUrl" target="_blank" class="share-btn share-wa">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        WhatsApp
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useUiStore } from '../../../stores/ui'

const ui  = useUiStore()
const inf = computed(() => ui.selectedInformativo)
const copiado = ref(false)

const whatsappUrl = computed(() => {
  if (!inf.value) return '#'
  const url = window.location.href
  const txt = encodeURIComponent(`📢 *${inf.value.titulo}*\n\n${url}`)
  return `https://wa.me/?text=${txt}`
})

function formatData(iso: string | null): string {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })
}

function copiarLink() {
  navigator.clipboard.writeText(window.location.href)
  copiado.value = true
  setTimeout(() => copiado.value = false, 2500)
}
</script>

<style scoped>
.info-detail { padding: 28px 32px; max-width: 760px; }

.back-btn {
  font-size: 13px; color: var(--muted); cursor: pointer;
  margin-bottom: 24px; display: inline-flex; align-items: center; gap: 4px;
}
.back-btn:hover { color: var(--cream); }

.info-hero {
  display: flex; gap: 20px; align-items: flex-start;
  background: var(--card); border: 1px solid var(--line);
  border-radius: var(--radius-2xl); padding: 24px 28px;
  margin-bottom: 24px;
}
.info-hero.urgente {
  border-color: rgba(239,68,68,0.4);
  background: rgba(239,68,68,0.05);
}

.hero-icon {
  width: 56px; height: 56px; border-radius: 14px;
  background: rgba(255,94,26,0.12); color: var(--orange);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.info-hero.urgente .hero-icon {
  background: rgba(239,68,68,0.15); color: #ef4444;
}

.hero-content { flex: 1; }

.hero-badges { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
.badge {
  font-size: 11px; font-weight: 700; font-family: var(--mono);
  padding: 3px 10px; border-radius: 999px; letter-spacing: 0.04em;
}
.badge-urgente { background: rgba(239,68,68,0.15); color: #ef4444; }
.badge-fonte   { background: rgba(255,94,26,0.12); color: var(--orange); }

.hero-titulo {
  font-size: 24px; font-weight: 800; letter-spacing: -0.02em;
  line-height: 1.25; margin-bottom: 8px;
}
.hero-data { font-size: 12px; color: var(--muted-2); font-family: var(--mono); }

.info-corpo {
  background: var(--card); border: 1px solid var(--line);
  border-radius: var(--radius-2xl); padding: 24px 28px;
  font-size: 15px; line-height: 1.75; color: var(--muted);
  white-space: pre-line; margin-bottom: 24px;
}

.share-row {
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
  padding: 16px 0; border-top: 1px solid var(--line);
}
.share-label { font-size: 12px; color: var(--muted-2); }

.share-btn {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 600;
  padding: 7px 14px; border-radius: 999px;
  background: var(--card); border: 1px solid var(--line);
  color: var(--muted); cursor: pointer; transition: all 0.15s;
}
.share-btn:hover { border-color: var(--orange); color: var(--orange); }

.share-wa { color: #25d366; border-color: rgba(37,211,102,0.3); text-decoration: none; }
.share-wa:hover { background: rgba(37,211,102,0.08); border-color: #25d366; color: #25d366; }

@media (max-width: 768px) {
  .info-detail { padding: 16px; }
  .hero-titulo { font-size: 20px; }
  .info-hero { flex-direction: column; gap: 12px; }
}
</style>
