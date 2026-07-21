import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { WebView, MobileScreen, Pro, Event, Project, Vaga, Loja, Artigo } from '../types'
import { trackPageView, trackClick } from '../services/analytics'
import { useDataStore } from './data'

export const useUiStore = defineStore('ui', () => {
  const activeView = ref<WebView>('inicio')
  const mobileScreen = ref<MobileScreen>('home')

  const selectedPro = ref<Pro | null>(null)
  const selectedEvent = ref<Event | null>(null)
  const selectedProj = ref<Project | null>(null)
  const selectedVaga = ref<Vaga | null>(null)
  const selectedLoja = ref<Loja | null>(null)
  const selectedArtigo = ref<Artigo | null>(null)

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
  const lojasFilters = ref({ catActive: 'Todas', search: '' })

  const VIEW_PATHS: Partial<Record<WebView, string>> = {
    inicio: '/',
    eventos: '/eventos',
    vagas: '/vagas',
    lojas: '/lojas',
    profissionais: '/profissionais',
    projetos: '/projetos',
    artigos: '/artigos',
  }

  function slug(): string {
    const data = useDataStore()
    return data.activeComunidade?.slug ?? ''
  }

  function prefixedPath(path: string): string {
    const s = slug()
    return s ? `/${s}${path}` : path
  }

  function goTo(view: WebView) {
    activeView.value = view
    trackPageView(view)
    window.scrollTo({ top: 0, behavior: 'smooth' })
    const viewPath = VIEW_PATHS[view]
    if (viewPath) history.pushState({}, '', prefixedPath(viewPath))
  }

  function openPro(pro: Pro) {
    selectedPro.value = pro
    trackClick('profissional', pro.id, pro.name, 'proDetail')
    activeView.value = 'proDetail'
    window.scrollTo({ top: 0, behavior: 'smooth' })
    history.pushState({}, '', prefixedPath(`/profissionais/${pro.id}`))
  }

  function openEvent(event: Event) {
    selectedEvent.value = event
    trackClick('evento', event.id, event.title, 'eventDetail')
    activeView.value = 'eventDetail'
    window.scrollTo({ top: 0, behavior: 'smooth' })
    history.pushState({}, '', prefixedPath(`/eventos/${event.id}`))
  }

  function openProj(proj: Project) {
    selectedProj.value = proj
    trackClick('projeto', proj.id, proj.name, 'projDetail')
    activeView.value = 'projDetail'
    window.scrollTo({ top: 0, behavior: 'smooth' })
    history.pushState({}, '', prefixedPath(`/projetos/${proj.id}`))
  }

  function openVaga(vaga: Vaga) {
    selectedVaga.value = vaga
    trackClick('vaga', vaga.id, vaga.title, 'vagaDetail')
    activeView.value = 'vagaDetail'
    window.scrollTo({ top: 0, behavior: 'smooth' })
    history.pushState({}, '', prefixedPath(`/vagas/${vaga.id}`))
  }

  function openLoja(loja: Loja) {
    selectedLoja.value = loja
    trackClick('loja', loja.id, loja.nome, 'lojaDetail')
    activeView.value = 'lojaDetail'
    window.scrollTo({ top: 0, behavior: 'smooth' })
    history.pushState({}, '', prefixedPath(`/lojas/${loja.id}`))
  }

  function openArtigo(artigo: Artigo) {
    selectedArtigo.value = artigo
    activeView.value = 'artigoDetail'
    window.scrollTo({ top: 0, behavior: 'smooth' })
    history.pushState({}, '', prefixedPath(`/artigos/${artigo.slug}`))
  }

  return {
    activeView, mobileScreen,
    selectedPro, selectedEvent, selectedProj, selectedVaga, selectedLoja, selectedArtigo,
    prosFilters, eventsFilters, projectsFilters, vagasFilters, lojasFilters,
    goTo, openPro, openEvent, openProj, openVaga, openLoja, openArtigo,
  }
})
