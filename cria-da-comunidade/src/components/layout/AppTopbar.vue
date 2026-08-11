<template>
  <header class="topbar">

    <!-- Community picker -->
    <div class="location-wrap" ref="pillRef">
      <button class="location-pill" @click="toggleDropdown" :class="{ open: dropdownOpen }">
        <svg width="16" height="16" fill="none" stroke="var(--orange)" stroke-width="2" viewBox="0 0 24 24">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        <div class="loc-text">
          <div class="loc-eyebrow">Comunidade</div>
          <div class="loc-name">{{ data.activeComunidade?.nome ?? 'Todas as comunidades' }}</div>
        </div>
        <svg class="loc-chevron" :class="{ rotated: dropdownOpen }" width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </button>

      <!-- Dropdown -->
      <Transition name="dropdown">
        <div v-if="dropdownOpen" class="community-dropdown">
          <div class="dropdown-header">Escolha a comunidade</div>

          <!-- All communities option -->
          <button
            class="community-option"
            :class="{ active: data.activeComunidadeId === null }"
            @click="select(null)"
          >
            <div class="option-av all-av">🗺</div>
            <div class="option-info">
              <div class="option-name">Todas as comunidades</div>
              <div class="option-sub">Ver tudo, sem filtro</div>
            </div>
            <div v-if="data.activeComunidadeId === null" class="option-check">✓</div>
          </button>

          <div class="dropdown-divider"></div>

          <!-- Loading state -->
          <div v-if="data.communities.length === 0" class="dropdown-empty">
            Carregando comunidades…
          </div>

          <!-- Community list -->
          <button
            v-for="c in data.communities"
            :key="c.id"
            class="community-option"
            :class="{ active: data.activeComunidadeId === c.id }"
            @click="select(c.id)"
          >
            <div class="option-av">{{ c.nome.slice(0, 2).toUpperCase() }}</div>
            <div class="option-info">
              <div class="option-name">{{ c.nome }}</div>
              <div class="option-sub" v-if="c.cidade || c.estado">
                {{ [c.cidade, c.estado].filter(Boolean).join(' · ') }}
              </div>
            </div>
            <div v-if="data.activeComunidadeId === c.id" class="option-check">✓</div>
          </button>
        </div>
      </Transition>
    </div>

    <!-- Logo mobile -->
    <div class="mobile-brand">
      <div class="mobile-mark"></div>
      <span class="mobile-name">Cria da Comunidade</span>
    </div>

    <!-- Search -->
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

    <!-- Avatar mobile -->
    <button class="mobile-avatar" @click="onAvatarClick">{{ auth.initials }}</button>
  </header>

  <AnunciarModal v-if="showAnunciar" @close="showAnunciar = false" />
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import AnunciarModal from '../ui/AnunciarModal.vue'

const auth = useAuthStore()
const ui = useUiStore()
const data = useDataStore()
const showAnunciar = ref(false)
const dropdownOpen = ref(false)
const pillRef = ref<HTMLElement | null>(null)

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function select(id: number | null) {
  dropdownOpen.value = false
  data.setComunidade(id)
}

function onClickOutside(e: MouseEvent) {
  if (pillRef.value && !pillRef.value.contains(e.target as Node)) {
    dropdownOpen.value = false
  }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside))

function openAnunciar() {
  if (!auth.isAuthenticated) {
    ui.goTo('login')
    return
  }
  showAnunciar.value = true
}

function onAvatarClick() {
  ui.goTo(auth.isAuthenticated ? 'perfil' : 'login')
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

/* ── Location pill ── */
.location-wrap {
  position: relative;
  flex-shrink: 0;
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
  transition: border-color 0.2s, background 0.2s;
  white-space: nowrap;
}
.location-pill:hover,
.location-pill.open {
  border-color: var(--orange);
  background: rgba(255,94,26,0.06);
}

.loc-text { text-align: left; }
.loc-eyebrow {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted);
  line-height: 1;
}
.loc-name { font-size: 12px; font-weight: 600; margin-top: 2px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; }

.loc-chevron { transition: transform 0.2s; flex-shrink: 0; }
.loc-chevron.rotated { transform: rotate(180deg); }

/* ── Dropdown ── */
.community-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  width: 280px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 14px;
  box-shadow: 0 16px 40px -8px rgba(0,0,0,0.6);
  overflow: hidden;
  z-index: 200;
}

.dropdown-header {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--muted-2);
  padding: 12px 16px 8px;
}

.dropdown-divider {
  height: 1px;
  background: var(--line);
  margin: 4px 0;
}

.dropdown-empty {
  padding: 16px;
  font-size: 12px;
  color: var(--muted);
  text-align: center;
}

.community-option {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 10px 14px;
  text-align: left;
  cursor: pointer;
  transition: background 0.15s;
  border-radius: 0;
}
.community-option:hover { background: rgba(245,240,232,0.04); }
.community-option.active { background: rgba(255,94,26,0.08); }

.option-av {
  width: 34px; height: 34px;
  border-radius: 9px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display);
  font-weight: 800;
  font-size: 12px;
  color: var(--black);
  flex-shrink: 0;
}
.all-av {
  background: var(--card-2);
  border: 1px solid var(--line);
  font-size: 16px;
}

.option-info { flex: 1; min-width: 0; }
.option-name {
  font-size: 13px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.option-sub { font-size: 11px; color: var(--muted); margin-top: 1px; }

.option-check {
  font-size: 12px;
  color: var(--orange);
  font-weight: 700;
  flex-shrink: 0;
}

/* Dropdown transition */
.dropdown-enter-active { transition: opacity 0.15s ease, transform 0.15s ease; }
.dropdown-leave-active { transition: opacity 0.1s ease, transform 0.1s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-6px); }

/* ── Search ── */
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

/* ── Actions ── */
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

/* ── Mobile brand ── */
.mobile-brand {
  display: none;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.mobile-mark {
  width: 28px; height: 28px;
  background: var(--orange);
  border-radius: 8px;
  transform: rotate(-4deg);
  box-shadow: 0 4px 12px -3px rgba(255,94,26,0.45);
  position: relative;
  flex-shrink: 0;
}
.mobile-mark::before {
  content: "";
  width: 11px; height: 11px;
  background: var(--yellow);
  border-radius: 2px;
  position: absolute;
  transform: rotate(45deg);
  top: 8px; left: 8px;
}
.mobile-name {
  font-family: var(--display);
  font-weight: 800;
  font-size: 14px;
  letter-spacing: -0.02em;
  color: var(--cream);
  white-space: nowrap;
}

/* Avatar mobile */
.mobile-avatar {
  display: none;
  width: 36px; height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  color: var(--black);
  font-weight: 800;
  font-size: 13px;
  cursor: pointer;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  transition: transform 0.15s;
}
.mobile-avatar:hover { transform: scale(1.06); }

/* ── Mobile ── */
@media (max-width: 768px) {
  .topbar { padding: 0 14px; gap: 10px; height: 60px; }
  .location-wrap { display: none; }
  .search-kbd { display: none; }
  .tb-icon-btn { display: none; }
  .cta-btn { display: none; }
  .search-wrap { max-width: 100%; }
  .mobile-brand { display: flex; }
  .mobile-avatar { display: flex; }
}
</style>
