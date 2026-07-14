<template>
  <div class="app">
    <AppSidebar />
    <div class="main-wrap">
      <AppTopbar />
      <main class="main-content">
        <Transition name="view" mode="out-in">
          <component :is="currentView" :key="ui.activeView" />
        </Transition>
      </main>
    </div>
  </div>
  <BottomNav />
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useUiStore } from './stores/ui'
import { useDataStore } from './stores/data'
import { useAuthStore } from './stores/auth'
import AppSidebar from './components/layout/AppSidebar.vue'
import AppTopbar from './components/layout/AppTopbar.vue'
import HomeView from './views/web/HomeView.vue'
import ProsView from './views/web/ProsView.vue'
import EventosView from './views/web/EventosView.vue'
import ProjetosView from './views/web/ProjetosView.vue'
import VagasView from './views/web/VagasView.vue'
import ProDetail from './views/web/details/ProDetail.vue'
import EventDetail from './views/web/details/EventDetail.vue'
import ProjDetail from './views/web/details/ProjDetail.vue'
import VagaDetail from './views/web/details/VagaDetail.vue'
import LojasView from './views/web/LojasView.vue'
import LojaDetail from './views/web/details/LojaDetail.vue'
import LoginView from './views/web/LoginView.vue'
import ProfileView from './views/web/ProfileView.vue'
import BottomNav from './components/layout/BottomNav.vue'

const ui = useUiStore()
const data = useDataStore()
const auth = useAuthStore()

const views = {
  inicio: HomeView,
  profissionais: ProsView,
  eventos: EventosView,
  projetos: ProjetosView,
  vagas: VagasView,
  proDetail: ProDetail,
  eventDetail: EventDetail,
  projDetail: ProjDetail,
  vagaDetail: VagaDetail,
  lojas: LojasView,
  lojaDetail: LojaDetail,
  login: LoginView,
  perfil: ProfileView,
}

const currentView = computed(() => views[ui.activeView])

function resolveDeepLink() {
  const path   = window.location.pathname
  const params = new URLSearchParams(window.location.search)

  // Slug-prefixed paths: /{slug}/module/{id}
  const sluggedDetail = path.match(/^\/[a-z0-9_-]+\/(profissionais|eventos|vagas|lojas)\/(\d+)$/)
  if (sluggedDetail) {
    const [, module, id] = sluggedDetail
    if (module === 'profissionais') {
      const p = data.pros.find(p => p.id === id)
      if (p) { ui.openPro(p); return }
    } else if (module === 'eventos') {
      const ev = data.events.find(e => e.id === id)
      if (ev) { ui.openEvent(ev); return }
    } else if (module === 'vagas') {
      const v = data.vagas.find(v => v.id === id)
      if (v) { ui.openVaga(v); return }
    } else if (module === 'lojas') {
      const l = data.lojas.find(l => l.id === id)
      if (l) { ui.openLoja(l); return }
    }
  }

  // Bare path-based: /profissionais/3, /eventos/3, /vagas/3, /lojas/3
  const profissionalPath = path.match(/^\/profissionais\/(\d+)$/)
  if (profissionalPath) {
    const p = data.pros.find(p => p.id === profissionalPath[1])
    if (p) { ui.openPro(p); return }
  }

  const eventoPath = path.match(/^\/eventos\/(\d+)$/)
  if (eventoPath) {
    const ev = data.events.find(e => e.id === eventoPath[1])
    if (ev) { ui.openEvent(ev); return }
  }
  const vagaPath = path.match(/^\/vagas\/(\d+)$/)
  if (vagaPath) {
    const v = data.vagas.find(v => v.id === vagaPath[1])
    if (v) { ui.openVaga(v); return }
  }
  const lojaPath = path.match(/^\/lojas\/(\d+)$/)
  if (lojaPath) {
    const l = data.lojas.find(l => l.id === lojaPath[1])
    if (l) { ui.openLoja(l); return }
  }

  // Query params legados: ?vaga=ID, ?loja=ID, ?evento=ID
  const vagaId = params.get('vaga')
  if (vagaId) {
    const v = data.vagas.find(v => v.id === vagaId)
    if (v) { ui.openVaga(v); return }
  }
  const lojaId = params.get('loja')
  if (lojaId) {
    const l = data.lojas.find(l => l.id === lojaId)
    if (l) { ui.openLoja(l); return }
  }
  const eventoId = params.get('evento')
  if (eventoId) {
    const ev = data.events.find(e => e.id === eventoId)
    if (ev) { ui.openEvent(ev); return }
  }
}

onMounted(async () => {
  // Carrega comunidades e usuário em paralelo
  await Promise.all([data.fetchComunidades(), auth.fetchMe()])

  // Resolve comunidade a partir do slug na URL (antes do fetchAll para filtrar corretamente)
  const urlPath = window.location.pathname
  const urlSlugMatch = urlPath.match(/^\/([a-z0-9_-]+)\//)
  if (urlSlugMatch) {
    const urlCommunity = data.communities.find(c => c.slug === urlSlugMatch[1])
    if (urlCommunity) {
      data.activeComunidadeId = urlCommunity.id
    } else if (auth.user?.comunidade_id) {
      data.activeComunidadeId = auth.user.comunidade_id
    }
  } else if (auth.user?.comunidade_id) {
    data.activeComunidadeId = auth.user.comunidade_id
  }

  await data.fetchAll()

  resolveDeepLink()

  // Botão Voltar do browser restaura a view correta
  window.addEventListener('popstate', () => {
    let p = window.location.pathname
    // Strip community slug prefix if recognized
    const slugMatch = p.match(/^\/([a-z0-9_-]+)(\/.+)$/)
    if (slugMatch && data.communities.some(c => c.slug === slugMatch[1])) {
      p = slugMatch[2]
    }
    if (p === '/' || p === '/inicio') ui.activeView = 'inicio'
    else if (p.startsWith('/eventos') && !p.match(/\/\d+$/)) ui.activeView = 'eventos'
    else if (p.startsWith('/vagas') && !p.match(/\/\d+$/)) ui.activeView = 'vagas'
    else if (p.startsWith('/lojas') && !p.match(/\/\d+$/)) ui.activeView = 'lojas'
    else if (p.startsWith('/profissionais') && !p.match(/\/\d+$/)) ui.activeView = 'profissionais'
    else if (p.startsWith('/projetos') && !p.match(/\/\d+$/)) ui.activeView = 'projetos'
  })
})
</script>

<style>
.app {
  display: grid;
  grid-template-columns: var(--sb-w) 1fr;
  min-height: 100vh;
}

.main-wrap {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  overflow: hidden;
}

.main-content {
  flex: 1;
  overflow-y: auto;
}

@media (max-width: 768px) {
  .app {
    grid-template-columns: 1fr;
  }
  .main-content {
    padding-bottom: var(--bn-h);
  }
}

/* View transitions */
.view-enter-active,
.view-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.view-enter-from {
  opacity: 0;
  transform: translateY(8px);
}
.view-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
