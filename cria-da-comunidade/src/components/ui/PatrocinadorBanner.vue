<template>
  <section v-if="patrocinador" class="banner-wrap fade-up">
    <a
      :href="patrocinador.link_url ?? undefined"
      :target="patrocinador.link_url ? '_blank' : undefined"
      rel="noopener"
      class="banner"
      :class="{ 'banner--clickable': !!patrocinador.link_url }"
      :style="bannerStyle"
    >
      <!-- Overlay escuro para legibilidade quando há imagem -->
      <div v-if="patrocinador.imagem_url" class="banner-overlay" />

      <div class="banner-body">
        <div class="banner-eyebrow">✦ Patrocinador Master</div>
        <div class="banner-nome">{{ patrocinador.nome }}</div>
        <p v-if="patrocinador.texto" class="banner-texto">{{ patrocinador.texto }}</p>
      </div>

      <div v-if="patrocinador.link_url" class="banner-cta">
        {{ patrocinador.texto_botao }}
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M7 17L17 7M17 7H7M17 7v10"/>
        </svg>
      </div>
    </a>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useDataStore } from '../../stores/data'

const data = useDataStore()
const patrocinador = computed(() => data.patrocinador)

const bannerStyle = computed(() => {
  if (!patrocinador.value?.imagem_url) return {}
  return { backgroundImage: `url(${patrocinador.value.imagem_url})` }
})
</script>

<style scoped>
.banner-wrap {
  margin-bottom: 40px;
}

.banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  position: relative;
  overflow: hidden;
  background: var(--card);
  border: 1px solid var(--line-strong);
  border-radius: var(--radius-xl);
  padding: 24px 28px;
  background-size: cover;
  background-position: center;
  min-height: 110px;
  text-decoration: none;
  transition: border-color 0.2s, transform 0.15s;
}

.banner--clickable:hover {
  border-color: var(--orange);
  transform: translateY(-1px);
}

.banner-overlay {
  position: absolute;
  inset: 0;
  background: rgba(10,8,6,0.72);
  border-radius: inherit;
  pointer-events: none;
}

.banner-body {
  position: relative;
  z-index: 1;
  min-width: 0;
  flex: 1;
}

.banner-eyebrow {
  font-family: var(--mono);
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--orange);
  margin-bottom: 6px;
}

.banner-nome {
  font-family: var(--display);
  font-size: 22px;
  font-weight: 900;
  color: var(--cream);
  letter-spacing: -0.02em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.banner-texto {
  font-size: 13px;
  color: var(--muted);
  margin-top: 4px;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.banner-cta {
  position: relative;
  z-index: 1;
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  background: var(--orange);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  border-radius: var(--radius-full);
  transition: filter 0.15s;
  white-space: nowrap;
}

.banner--clickable:hover .banner-cta {
  filter: brightness(1.15);
}

@media (max-width: 600px) {
  .banner {
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
    min-height: auto;
  }
  .banner-nome { font-size: 18px; white-space: normal; }
  .banner-cta { align-self: flex-start; }
}
</style>
