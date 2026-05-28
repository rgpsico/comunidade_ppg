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
  login: LoginView,
  perfil: ProfileView,
}

const currentView = computed(() => views[ui.activeView])

onMounted(async () => {
  // Carrega comunidades e usuário em paralelo
  await Promise.all([data.fetchComunidades(), auth.fetchMe()])
  // Usa a comunidade do usuário logado como filtro padrão
  if (auth.user?.comunidade_id) {
    data.activeComunidadeId = auth.user.comunidade_id
  }
  data.fetchAll()
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
