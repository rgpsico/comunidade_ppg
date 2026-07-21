<template>
  <div class="artigos-view fade-up">
    <div class="page-header">
      <h1 class="page-title display">Artigos</h1>
      <p class="page-sub">Notícias e conteúdo da comunidade</p>
    </div>

    <!-- Filtro por categoria -->
    <div class="cats-bar">
      <button
        v-for="cat in categorias"
        :key="cat"
        class="cat-chip"
        :class="{ active: catAtiva === cat }"
        @click="catAtiva = cat"
      >{{ cat }}</button>
    </div>

    <div v-if="data.loading" class="loading-state">Carregando artigos…</div>

    <div v-else-if="!artigos.length" class="empty-state">
      <p>Nenhum artigo publicado ainda.</p>
    </div>

    <div v-else class="artigos-grid">
      <div
        v-for="artigo in artigos"
        :key="artigo.id"
        class="artigo-card"
        @click="ui.openArtigo(artigo)"
      >
        <div class="artigo-capa">
          <img v-if="artigo.imagemCapaUrl" :src="artigo.imagemCapaUrl" :alt="artigo.titulo" />
          <div v-else class="artigo-capa-placeholder" :style="{ background: capaGradient(artigo.categoria) }">
            <span class="capa-icon">📰</span>
          </div>
          <span class="artigo-cat">{{ artigo.categoria }}</span>
        </div>
        <div class="artigo-body">
          <h2 class="artigo-titulo">{{ artigo.titulo }}</h2>
          <p v-if="artigo.resumo" class="artigo-resumo">{{ artigo.resumo }}</p>
          <div class="artigo-meta">
            <span v-if="artigo.autor" class="artigo-autor">{{ artigo.autor }}</span>
            <span class="artigo-data">{{ formatData(artigo.publicadoEm ?? artigo.createdAt) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'

const ui   = useUiStore()
const data = useDataStore()

const catAtiva = ref('Todos')
const categorias = ['Todos', 'Notícia', 'Saúde', 'Educação', 'Cultura', 'Esporte', 'Economia', 'Tecnologia']

const artigos = computed(() =>
  catAtiva.value === 'Todos'
    ? data.artigos
    : data.artigos.filter(a => a.categoria === catAtiva.value)
)

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
  return new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.artigos-view { padding: 28px 32px; max-width: 1200px; }

.page-header { margin-bottom: 24px; }
.page-title { font-size: 32px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 4px; }
.page-sub { font-size: 14px; color: var(--muted); }

.cats-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
.cat-chip {
  padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 600;
  background: var(--card); border: 1px solid var(--line); color: var(--muted);
  cursor: pointer; transition: all 0.15s;
}
.cat-chip:hover { border-color: var(--orange); color: var(--orange); }
.cat-chip.active { background: var(--orange); border-color: var(--orange); color: var(--black); }

.loading-state, .empty-state { text-align: center; color: var(--muted); padding: 60px 0; font-size: 14px; }

.artigos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.artigo-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-2xl);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.15s, border-color 0.15s;
}
.artigo-card:hover { transform: translateY(-2px); border-color: var(--line-strong); }

.artigo-capa { position: relative; height: 180px; overflow: hidden; }
.artigo-capa img { width: 100%; height: 100%; object-fit: cover; }
.artigo-capa-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
}
.capa-icon { font-size: 48px; opacity: 0.6; }
.artigo-cat {
  position: absolute; top: 12px; left: 12px;
  background: rgba(15,14,12,0.75); backdrop-filter: blur(8px);
  color: var(--cream); font-size: 10px; font-weight: 700;
  font-family: var(--mono); text-transform: uppercase; letter-spacing: 0.08em;
  padding: 4px 10px; border-radius: 999px;
}

.artigo-body { padding: 18px; }
.artigo-titulo {
  font-family: var(--display); font-size: 17px; font-weight: 700;
  letter-spacing: -0.02em; line-height: 1.3; margin-bottom: 8px;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.artigo-resumo {
  font-size: 13px; color: var(--muted); line-height: 1.55; margin-bottom: 12px;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.artigo-meta { display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--muted-2); }
.artigo-autor { font-weight: 600; color: var(--muted); }
.artigo-data { font-family: var(--mono); }

@media (max-width: 768px) {
  .artigos-view { padding: 16px; }
  .artigos-grid { grid-template-columns: 1fr; }
}
</style>
