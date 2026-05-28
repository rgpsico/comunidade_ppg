import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { WebView, MobileScreen, Pro, Event, Project, Vaga } from '../types'
import { trackPageView, trackClick } from '../services/analytics'

export const useUiStore = defineStore('ui', () => {
  const activeView = ref<WebView>('inicio')
  const mobileScreen = ref<MobileScreen>('home')

  const selectedPro = ref<Pro | null>(null)
  const selectedEvent = ref<Event | null>(null)
  const selectedProj = ref<Project | null>(null)
  const selectedVaga = ref<Vaga | null>(null)

  const prosFilters = ref({
    catActive: 'Todos',
    sort: 'Mais bem avaliados',
    distance: '1km',
    price: 'R$ 0–200',
    rating: '4.5+',
    verificado: false,
    atendeEmCasa: false,
  })

  const eventsFilters = ref({ catActive: 'Todos' })
  const projectsFilters = ref({ causaActive: 'Todos' })
  const vagasFilters = ref({ tabActive: 'Todas', semExp: false, mesmoDia: false })

  function goTo(view: WebView) {
    activeView.value = view
    trackPageView(view)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  function openPro(pro: Pro) {
    selectedPro.value = pro
    trackClick('profissional', pro.id, pro.name, 'proDetail')
    goTo('proDetail')
  }

  function openEvent(event: Event) {
    selectedEvent.value = event
    trackClick('evento', event.id, event.title, 'eventDetail')
    goTo('eventDetail')
  }

  function openProj(proj: Project) {
    selectedProj.value = proj
    trackClick('projeto', proj.id, proj.name, 'projDetail')
    goTo('projDetail')
  }

  function openVaga(vaga: Vaga) {
    selectedVaga.value = vaga
    trackClick('vaga', vaga.id, vaga.title, 'vagaDetail')
    goTo('vagaDetail')
  }

  return {
    activeView, mobileScreen,
    selectedPro, selectedEvent, selectedProj, selectedVaga,
    prosFilters, eventsFilters, projectsFilters, vagasFilters,
    goTo, openPro, openEvent, openProj, openVaga,
  }
})
