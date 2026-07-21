<template>
  <div class="artigo-detail fade-up" v-if="artigo">
    <button class="back-btn" @click="ui.goTo('artigos')">← Artigos</button>

    <!-- Capa -->
    <div class="artigo-hero">
      <div v-if="artigo.imagemCapaUrl" class="hero-img-wrap">
        <img :src="artigo.imagemCapaUrl" :alt="artigo.titulo" class="hero-img" />
      </div>
      <div v-else class="hero-gradient" :style="{ background: capaGradient(artigo.categoria) }">
        <span style="font-size:64px;opacity:.4">📰</span>
      </div>

      <div class="hero-overlay">
        <span class="cat-badge">{{ artigo.categoria }}</span>
        <h1 class="hero-titulo display">{{ artigo.titulo }}</h1>
        <div class="hero-meta">
          <span v-if="artigo.autor">✍️ {{ artigo.autor }}</span>
          <span>📅 {{ formatData(artigo.publicadoEm ?? artigo.createdAt) }}</span>
        </div>
      </div>
    </div>

    <!-- Resumo destacado -->
    <div v-if="artigo.resumo" class="artigo-resumo-block">
      {{ artigo.resumo }}
    </div>

    <!-- Corpo HTML -->
    <article class="artigo-corpo" v-html="artigo.corpo" />

    <!-- Avaliações e comentários -->
    <div class="artigo-interacoes">
      <AvaliacaoSection tipo="artigos" :id="artigo.id" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUiStore } from '../../../stores/ui'
import AvaliacaoSection from '../../../components/ui/AvaliacaoSection.vue'

const ui = useUiStore()
const artigo = computed(() => ui.selectedArtigo)

const gradients: Record<string, string> = {
  'Notícia':    'linear-gradient(135deg, #FF5E1A, #FFD23F)',
  'Saúde':      'linear-gradient(135deg, #2BD96B, #00C2C7)',
  'Educação':   'linear-gradient(135deg, #5E5AFF, #2BD96B)',
  'Cultura':    'linear-gradient(135deg, #FF2D78, #FF5E1A)',
  'Esporte':    'linear-gradient(135deg, #00C2C7, #5E5AFF)',
  'Economia':   'linear-gradient(135deg, #FFD23F, #FF5E1A)',
  'Tecnologia': 'linear-gradient(135deg, #5E5AFF, #FF2D78)',
}

function capaGradient(cat: string): string {
  return gradients[cat] ?? 'linear-gradient(135deg, #FF5E1A, #FFD23F)'
}

function formatData(iso: string | null): string {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })
}
</script>

<style scoped>
.artigo-detail { padding: 28px 32px; max-width: 760px; }
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 4px; }
.back-btn:hover { color: var(--cream); }

.artigo-hero { position: relative; border-radius: var(--radius-2xl); overflow: hidden; margin-bottom: 24px; min-height: 280px; display: flex; align-items: flex-end; }
.hero-img-wrap { position: absolute; inset: 0; }
.hero-img { width: 100%; height: 100%; object-fit: cover; }
.hero-gradient { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }

.hero-overlay {
  position: relative; z-index: 1; width: 100%;
  background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 60%, transparent 100%);
  padding: 32px 28px 28px;
}
.cat-badge {
  display: inline-block; background: var(--orange); color: var(--black);
  font-size: 10px; font-weight: 800; font-family: var(--mono);
  text-transform: uppercase; letter-spacing: 0.1em;
  padding: 4px 10px; border-radius: 999px; margin-bottom: 10px;
}
.hero-titulo {
  font-size: 28px; font-weight: 800; letter-spacing: -0.03em; line-height: 1.2;
  color: #fff; margin-bottom: 12px;
}
.hero-meta { display: flex; gap: 16px; font-size: 12px; color: rgba(255,255,255,0.7); }

.artigo-resumo-block {
  background: var(--card-2); border-left: 3px solid var(--orange);
  border-radius: 0 var(--radius-md) var(--radius-md) 0;
  padding: 14px 18px; margin-bottom: 28px;
  font-size: 15px; color: var(--cream); line-height: 1.6; font-style: italic;
}

/* Corpo do artigo */
.artigo-corpo {
  font-size: 15px; line-height: 1.75; color: var(--muted);
  margin-bottom: 40px;
}
.artigo-corpo :deep(h2) {
  font-family: var(--display); font-size: 22px; font-weight: 700;
  letter-spacing: -0.02em; color: var(--cream); margin: 28px 0 12px;
}
.artigo-corpo :deep(h3) {
  font-family: var(--display); font-size: 17px; font-weight: 700;
  color: var(--cream); margin: 20px 0 8px;
}
.artigo-corpo :deep(p) { margin-bottom: 16px; }
.artigo-corpo :deep(strong) { color: var(--cream); font-weight: 700; }
.artigo-corpo :deep(em) { font-style: italic; }
.artigo-corpo :deep(a) { color: var(--orange); text-decoration: underline; }
.artigo-corpo :deep(ul), .artigo-corpo :deep(ol) { padding-left: 20px; margin-bottom: 16px; }
.artigo-corpo :deep(li) { margin-bottom: 6px; }
.artigo-corpo :deep(blockquote) {
  border-left: 3px solid var(--orange); padding-left: 16px;
  color: var(--cream); font-style: italic; margin: 20px 0;
}

.artigo-interacoes {
  border-top: 1px solid var(--line); padding-top: 32px;
}

@media (max-width: 768px) {
  .artigo-detail { padding: 16px; }
  .hero-titulo { font-size: 22px; }
  .artigo-hero { min-height: 200px; }
}
</style>
