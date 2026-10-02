import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../services/api'
import { useDataStore } from './data'
import type { Pelada, PeladaForm, PeladaResumo, Quadra } from '../types'

export const useFutebolStore = defineStore('futebol', () => {
  const peladas = ref<PeladaResumo[]>([])
  const loadingList = ref(false)
  const atual = ref<Pelada | null>(null)
  const loadingAtual = ref(false)
  const notFound = ref(false)
  const quadras = ref<Quadra[]>([])
  const loadingQuadras = ref(false)

  async function fetchPeladas() {
    const data = useDataStore()
    loadingList.value = true
    try {
      const cid = data.activeComunidadeId
      const qs = cid ? `?comunidade_id=${cid}` : ''
      const res = await api.get<{ data: PeladaResumo[] }>(`/peladas${qs}`)
      peladas.value = res.data
    } catch {
      peladas.value = []
    } finally {
      loadingList.value = false
    }
  }

  async function fetchQuadras() {
    const data = useDataStore()
    loadingQuadras.value = true
    try {
      const cid = data.activeComunidadeId
      const qs = cid ? `?comunidade_id=${cid}` : ''
      const res = await api.get<{ data: Quadra[] }>(`/quadras${qs}`)
      quadras.value = res.data
    } catch {
      quadras.value = []
    } finally {
      loadingQuadras.value = false
    }
  }

  /** silent=true é usado pelo polling: não mostra "carregando" nem derruba a tela em erro de rede. */
  async function fetchPelada(id: number, silent = false) {
    if (!silent) {
      if (atual.value?.id !== id) atual.value = null
      loadingAtual.value = true
      notFound.value = false
    }
    try {
      atual.value = await api.get<Pelada>(`/peladas/${id}`)
    } catch {
      if (!silent) notFound.value = true
    } finally {
      if (!silent) loadingAtual.value = false
    }
  }

  async function criar(form: PeladaForm): Promise<Pelada> {
    const data = useDataStore()
    const created = await api.post<Pelada>('/peladas', {
      ...form,
      local: form.local || null,
      quando: form.quando || null,
      comunidade_id: data.activeComunidadeId,
    })
    return created
  }

  async function acao(request: Promise<Pelada>): Promise<void> {
    atual.value = await request
  }

  const entrar = (id: number, goleiro: boolean) => acao(api.post<Pelada>(`/peladas/${id}/entrar`, { goleiro }))
  const sair = (id: number) => acao(api.delete<Pelada>(`/peladas/${id}/sair`))
  const voltarFila = (id: number) => acao(api.post<Pelada>(`/peladas/${id}/voltar-fila`, {}))
  const addConvidado = (id: number, nome: string, goleiro: boolean) =>
    acao(api.post<Pelada>(`/peladas/${id}/convidados`, { nome, goleiro }))
  const removerJogador = (id: number, jogadorId: number) =>
    acao(api.delete<Pelada>(`/peladas/${id}/jogadores/${jogadorId}`))
  const rodizio = (id: number, time: number) => acao(api.post<Pelada>(`/peladas/${id}/rodizio`, { time }))
  const zerar = (id: number) => acao(api.post<Pelada>(`/peladas/${id}/zerar`, {}))

  async function excluir(id: number) {
    await api.delete(`/peladas/${id}`)
    atual.value = null
    peladas.value = peladas.value.filter(p => p.id !== id)
  }

  return {
    peladas, loadingList, atual, loadingAtual, notFound, quadras, loadingQuadras,
    fetchPeladas, fetchQuadras, fetchPelada, criar, entrar, sair, voltarFila, addConvidado, removerJogador, rodizio, zerar, excluir,
  }
})
