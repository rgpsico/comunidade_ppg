<template>
  <header class="topbar">
    <button class="location-pill">
      <svg width="16" height="16" fill="none" stroke="var(--orange)" stroke-width="2" viewBox="0 0 24 24">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
        <circle cx="12" cy="10" r="3"/>
      </svg>
      <div>
        <div class="loc-eyebrow">Sua quebrada</div>
        <div class="loc-name">Complexo do Alemão</div>
      </div>
      <svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>

    <div class="search-wrap">
      <svg width="15" height="15" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input class="search-input" type="text" placeholder="O que tu precisa hoje?" />
      <kbd class="search-kbd">⌘ K</kbd>
      <button class="search-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="5" y1="12" x2="19" y2="12"/>
          <polyline points="12 5 19 12 12 19"/>
        </svg>
      </button>
    </div>

    <div class="tb-actions">
      <button class="tb-icon-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
        </svg>
        <span class="tb-badge">8</span>
      </button>
      <button class="tb-icon-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <span class="tb-badge">3</span>
      </button>
      <button class="cta-btn" @click="openAnunciar">+ Anunciar</button>
    </div>
  </header>

  <AnunciarModal v-if="showAnunciar" @close="showAnunciar = false" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useUiStore } from '../../stores/ui'
import AnunciarModal from '../ui/AnunciarModal.vue'

const auth = useAuthStore()
const ui = useUiStore()
const showAnunciar = ref(false)

function openAnunciar() {
  if (!auth.isAuthenticated) {
    ui.goTo('login')
    return
  }
  showAnunciar.value = true
}
</script>

<style scoped>
.topbar {
  position: sticky;
  top: 0;
  height: var(--tb-h);
  background: rgba(13,11,9,0.85);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--line);
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 0 32px;
  z-index: 40;
}

.location-pill {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: var(--card);
  cursor: pointer;
  transition: border-color 0.2s;
  flex-shrink: 0;
}
.location-pill:hover { border-color: var(--line-strong); }
.loc-eyebrow {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted);
  line-height: 1;
}
.loc-name { font-size: 12px; font-weight: 600; margin-top: 2px; }

.search-wrap {
  flex: 1;
  max-width: 540px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 14px;
  height: 44px;
  border-radius: 12px;
  border: 1px solid var(--line);
  background: var(--card);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.search-wrap:focus-within {
  border-color: var(--orange);
  box-shadow: 0 0 0 3px rgba(255,94,26,0.12);
}
.search-input {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  font-size: 13px;
  color: var(--cream);
}
.search-input::placeholder { color: var(--muted); }
.search-kbd {
  font-family: var(--mono);
  font-size: 10px;
  color: var(--muted-2);
  background: var(--card-2);
  border: 1px solid var(--line);
  padding: 2px 6px;
  border-radius: 4px;
}
.search-btn {
  width: 28px; height: 28px;
  background: var(--orange);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: white;
  flex-shrink: 0;
  transition: background 0.15s;
}
.search-btn:hover { background: var(--orange-deep); }

.tb-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: auto;
}

.tb-icon-btn {
  position: relative;
  width: 38px; height: 38px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: var(--card);
  display: flex; align-items: center; justify-content: center;
  color: var(--muted);
  transition: color 0.15s, border-color 0.2s;
}
.tb-icon-btn:hover { color: var(--cream); border-color: var(--line-strong); }
.tb-badge {
  position: absolute;
  top: -4px; right: -4px;
  min-width: 16px; height: 16px;
  background: var(--orange);
  border-radius: 999px;
  font-family: var(--mono);
  font-size: 9px;
  font-weight: 500;
  color: white;
  display: flex; align-items: center; justify-content: center;
  padding: 0 3px;
}

.cta-btn {
  height: 38px;
  padding: 0 16px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  color: var(--black);
  font-weight: 700;
  font-size: 13px;
  box-shadow: var(--shadow-cta-orange);
  transition: transform 0.15s, box-shadow 0.15s;
}
.cta-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 18px -4px rgba(255,94,26,0.6); }

@media (max-width: 768px) {
  .topbar { padding: 0 16px; gap: 10px; height: 60px; }
  .location-pill { display: none; }
  .search-kbd { display: none; }
  .tb-icon-btn { display: none; }
  .cta-btn { display: none; }
  .search-wrap { max-width: 100%; }
}
</style>
