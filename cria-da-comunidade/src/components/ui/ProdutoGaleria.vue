<template>
  <!-- Galeria do produto no estilo Mercado Livre.
       1 imagem: só a foto (clique amplia). 2+ imagens: carrossel com miniaturas,
       setas, arrastar no celular, contador e tela cheia. -->
  <div class="pg" :class="{ multi }">
    <!-- Miniaturas (desktop: coluna à esquerda; celular: escondidas, usa os pontos) -->
    <div v-if="multi" class="pg-thumbs" role="tablist" aria-label="Fotos do produto">
      <button
        v-for="(img, i) in imagens"
        :key="img + i"
        type="button"
        class="pg-thumb"
        :class="{ active: atual === i }"
        :aria-label="`Foto ${i + 1} de ${imagens.length}`"
        :aria-selected="atual === i"
        @mouseenter="ir(i)"
        @focus="ir(i)"
        @click="ir(i)"
      >
        <img :src="img" :alt="''" loading="lazy" />
      </button>
    </div>

    <div class="pg-main">
      <div
        ref="trilho"
        class="pg-track"
        @scroll.passive="aoRolar"
        @keydown.left.prevent="anterior"
        @keydown.right.prevent="proxima"
        tabindex="0"
        aria-roledescription="carrossel"
        :aria-label="`Fotos de ${nome}`"
      >
        <div v-for="(img, i) in imagens" :key="img + i" class="pg-slide" @click="abrirTela(i)">
          <img :src="img" :alt="`${nome} — foto ${i + 1}`" :loading="i === 0 ? 'eager' : 'lazy'" draggable="false" />
        </div>
      </div>

      <slot name="badge" />

      <template v-if="multi">
        <button type="button" class="pg-seta esq" :disabled="atual === 0" aria-label="Foto anterior" @click="anterior">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button type="button" class="pg-seta dir" :disabled="atual === imagens.length - 1" aria-label="Próxima foto" @click="proxima">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <span class="pg-contador">{{ atual + 1 }} / {{ imagens.length }}</span>
        <div class="pg-pontos" aria-hidden="true">
          <span v-for="(_, i) in imagens" :key="i" :class="{ on: atual === i }" />
        </div>
      </template>
    </div>

    <!-- Tela cheia -->
    <Teleport to="body">
      <div v-if="telaCheia" class="pg-lb" role="dialog" aria-modal="true" :aria-label="`Fotos de ${nome}`" @click.self="fecharTela">
        <button type="button" class="pg-lb-fechar" aria-label="Fechar" @click="fecharTela">✕</button>
        <img class="pg-lb-img" :src="imagens[atual]" :alt="`${nome} — foto ${atual + 1}`" @touchstart.passive="toqueIni" @touchend.passive="toqueFim" />
        <template v-if="multi">
          <button type="button" class="pg-lb-seta esq" :disabled="atual === 0" aria-label="Foto anterior" @click="anterior">‹</button>
          <button type="button" class="pg-lb-seta dir" :disabled="atual === imagens.length - 1" aria-label="Próxima foto" @click="proxima">›</button>
          <span class="pg-lb-contador">{{ atual + 1 }} / {{ imagens.length }}</span>
        </template>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onBeforeUnmount } from 'vue'

const props = defineProps<{ imagens: string[]; nome: string }>()

const atual = ref(0)
const telaCheia = ref(false)
const trilho = ref<HTMLElement | null>(null)
const multi = computed(() => props.imagens.length > 1)

// Produto trocado: volta para a primeira foto.
watch(() => props.imagens, () => {
  atual.value = 0
  trilho.value?.scrollTo({ left: 0 })
})

function ir(i: number) {
  const n = Math.max(0, Math.min(props.imagens.length - 1, i))
  atual.value = n
  const el = trilho.value
  if (el) el.scrollTo({ left: n * el.clientWidth, behavior: 'smooth' })
}
const anterior = () => ir(atual.value - 1)
const proxima  = () => ir(atual.value + 1)

// Arrastar no celular (scroll-snap) atualiza o índice.
let raf = 0
function aoRolar() {
  cancelAnimationFrame(raf)
  raf = requestAnimationFrame(() => {
    const el = trilho.value
    if (el && el.clientWidth) atual.value = Math.round(el.scrollLeft / el.clientWidth)
  })
}

function teclas(e: KeyboardEvent) {
  if (e.key === 'Escape') fecharTela()
  else if (e.key === 'ArrowLeft') anterior()
  else if (e.key === 'ArrowRight') proxima()
}
function abrirTela(i: number) {
  atual.value = i
  telaCheia.value = true
  document.addEventListener('keydown', teclas)
  document.body.style.overflow = 'hidden'
}
function fecharTela() {
  telaCheia.value = false
  document.removeEventListener('keydown', teclas)
  document.body.style.overflow = ''
  ir(atual.value)
}

// Deslizar na tela cheia.
let x0 = 0
const toqueIni = (e: TouchEvent) => { x0 = e.changedTouches[0]?.clientX ?? 0 }
function toqueFim(e: TouchEvent) {
  const dx = (e.changedTouches[0]?.clientX ?? 0) - x0
  if (Math.abs(dx) > 40) (dx < 0 ? proxima : anterior)()
}

onBeforeUnmount(() => {
  cancelAnimationFrame(raf)
  document.removeEventListener('keydown', teclas)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.pg { display: grid; gap: 10px; }
.pg.multi { grid-template-columns: 64px 1fr; align-items: start; }

/* Miniaturas */
.pg-thumbs { display: flex; flex-direction: column; gap: 8px; max-height: 380px; overflow-y: auto; scrollbar-width: none; }
.pg-thumbs::-webkit-scrollbar { display: none; }
.pg-thumb {
  width: 64px; height: 64px; flex-shrink: 0; padding: 0; cursor: pointer;
  border-radius: 10px; overflow: hidden; background: #fff;
  border: 2px solid var(--line); opacity: .65; transition: opacity .15s, border-color .15s;
}
.pg-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pg-thumb:hover, .pg-thumb.active { opacity: 1; border-color: var(--orange); }

/* Principal */
.pg-main { position: relative; border-radius: 18px; overflow: hidden; background: #fff; min-width: 0; }
.pg-track {
  display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none;
  outline: none; overscroll-behavior-x: contain;
}
.pg-track::-webkit-scrollbar { display: none; }
.pg-track:focus-visible { box-shadow: inset 0 0 0 2px var(--orange); }
.pg-slide { flex: 0 0 100%; scroll-snap-align: center; height: 380px; cursor: zoom-in; display: flex; align-items: center; justify-content: center; }
.pg-slide img { max-width: 100%; max-height: 100%; object-fit: contain; display: block; user-select: none; }

.pg-seta {
  position: absolute; top: 50%; transform: translateY(-50%);
  width: 40px; height: 40px; border-radius: 50%; border: none; cursor: pointer;
  background: rgba(255,255,255,.95); color: #222; box-shadow: 0 2px 8px rgba(0,0,0,.18);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .15s;
}
.pg-main:hover .pg-seta:not(:disabled), .pg-seta:focus-visible { opacity: 1; }
.pg-seta.esq { left: 10px; }
.pg-seta.dir { right: 10px; }

.pg-contador {
  position: absolute; left: 12px; top: 12px;
  background: rgba(0,0,0,.55); color: #fff; font-size: 12px; font-weight: 700;
  padding: 3px 9px; border-radius: 20px; pointer-events: none;
}
.pg-pontos { display: none; }

/* Tela cheia */
.pg-lb { position: fixed; inset: 0; z-index: 2000; background: rgba(0,0,0,.92); display: flex; align-items: center; justify-content: center; }
.pg-lb-img { max-width: 92vw; max-height: 86vh; object-fit: contain; user-select: none; }
.pg-lb-fechar {
  position: absolute; top: 16px; right: 16px; width: 44px; height: 44px; border-radius: 50%;
  border: none; background: rgba(255,255,255,.12); color: #fff; font-size: 18px; cursor: pointer;
}
.pg-lb-seta {
  position: absolute; top: 50%; transform: translateY(-50%); width: 52px; height: 52px; border-radius: 50%;
  border: none; background: rgba(255,255,255,.12); color: #fff; font-size: 34px; line-height: 1; cursor: pointer;
}
.pg-lb-seta:disabled { opacity: .25; cursor: default; }
.pg-lb-seta.esq { left: 16px; }
.pg-lb-seta.dir { right: 16px; }
.pg-lb-contador { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); color: #fff; font-size: 14px; font-weight: 700; }

@media (max-width: 768px) {
  .pg.multi { grid-template-columns: 1fr; }
  .pg-thumbs { display: none; }
  .pg-slide { height: 300px; }
  .pg-seta { display: none; }
  .pg-pontos { display: flex; justify-content: center; gap: 6px; position: absolute; bottom: 10px; left: 0; right: 0; pointer-events: none; }
  .pg-pontos span { width: 6px; height: 6px; border-radius: 50%; background: rgba(0,0,0,.25); transition: width .2s, background .2s; }
  .pg-pontos span.on { width: 16px; border-radius: 3px; background: var(--orange); }
  .pg-lb-seta { display: none; }
}
@media (prefers-reduced-motion: reduce) { .pg-track { scroll-behavior: auto; } }
</style>
