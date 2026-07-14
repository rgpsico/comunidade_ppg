import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../services/api'
import type { Avaliacao, AvaliacoesState, Comentario, ComentariosState } from '../types'

function mapAvaliacao(a: Record<string, unknown>): Avaliacao {
  return {
    id:       String(a.id),
    nota:     Number(a.nota),
    texto:    (a.texto as string | null) ?? null,
    user:     a.user as Avaliacao['user'],
    createdAt: a.created_at as string,
    isMine:   Boolean(a.is_mine),
  }
}

function mapComentario(c: Record<string, unknown>): Comentario {
  const respostas = Array.isArray(c.respostas)
    ? (c.respostas as Record<string, unknown>[]).map(mapComentario)
    : []
  return {
    id:       String(c.id),
    corpo:    c.corpo as string,
    user:     c.user as Comentario['user'],
    respostas,
    createdAt: c.created_at as string,
    isMine:   Boolean(c.is_mine),
  }
}

export const useInteracoesStore = defineStore('interacoes', () => {
  const avaliacoes   = ref<AvaliacoesState>({ data: [], media: 0, total: 0, minhaAvaliacao: null })
  const comentarios  = ref<ComentariosState>({ data: [], total: 0 })
  const loadingAv    = ref(false)
  const loadingCom   = ref(false)
  const errorAv      = ref<string | null>(null)
  const errorCom     = ref<string | null>(null)

  // ---------- Avaliações ----------

  async function fetchAvaliacoes(tipo: string, id: string) {
    loadingAv.value = true
    errorAv.value   = null
    try {
      const res = await api.get<Record<string, unknown>>(`/${tipo}/${id}/avaliacoes`)
      avaliacoes.value = {
        data:            (res.data as Record<string, unknown>[]).map(mapAvaliacao),
        media:           Number(res.media),
        total:           Number(res.total),
        minhaAvaliacao:  res.minha_avaliacao ? mapAvaliacao(res.minha_avaliacao as Record<string, unknown>) : null,
      }
    } catch (e) {
      errorAv.value = 'Erro ao carregar avaliações'
    } finally {
      loadingAv.value = false
    }
  }

  async function submitAvaliacao(tipo: string, id: string, nota: number, texto: string) {
    const res = await api.post<Record<string, unknown>>(`/${tipo}/${id}/avaliacoes`, { nota, texto })
    const av  = mapAvaliacao(res)

    // Actualiza minha avaliação e lista
    avaliacoes.value.minhaAvaliacao = av
    const idx = avaliacoes.value.data.findIndex(a => a.isMine)
    if (idx >= 0) avaliacoes.value.data[idx] = av
    else          avaliacoes.value.data.unshift(av)

    // Recalcula média localmente (fallback até próximo fetch)
    const total = avaliacoes.value.data.length
    const soma  = avaliacoes.value.data.reduce((s, a) => s + a.nota, 0)
    avaliacoes.value.media = total > 0 ? Math.round((soma / total) * 10) / 10 : 0
    avaliacoes.value.total = total

    return av
  }

  async function deleteAvaliacao(avaliacaoId: string) {
    await api.delete(`/avaliacoes/${avaliacaoId}`)
    avaliacoes.value.minhaAvaliacao = null
    avaliacoes.value.data = avaliacoes.value.data.filter(a => a.id !== avaliacaoId)
    const total = avaliacoes.value.data.length
    const soma  = avaliacoes.value.data.reduce((s, a) => s + a.nota, 0)
    avaliacoes.value.media = total > 0 ? Math.round((soma / total) * 10) / 10 : 0
    avaliacoes.value.total = total
  }

  // ---------- Comentários ----------

  async function fetchComentarios(tipo: string, id: string) {
    loadingCom.value = true
    errorCom.value   = null
    try {
      const res = await api.get<Record<string, unknown>>(`/${tipo}/${id}/comentarios`)
      comentarios.value = {
        data:  (res.data as Record<string, unknown>[]).map(mapComentario),
        total: Number(res.total),
      }
    } catch (e) {
      errorCom.value = 'Erro ao carregar comentários'
    } finally {
      loadingCom.value = false
    }
  }

  async function submitComentario(tipo: string, id: string, corpo: string, parentId?: string) {
    const res = await api.post<Record<string, unknown>>(`/${tipo}/${id}/comentarios`, {
      corpo,
      parent_id: parentId ?? null,
    })
    const com = mapComentario(res)

    if (parentId) {
      // Adiciona como resposta ao comentário pai
      const pai = comentarios.value.data.find(c => c.id === parentId)
      if (pai) pai.respostas.push(com)
    } else {
      comentarios.value.data.unshift(com)
      comentarios.value.total++
    }

    return com
  }

  async function deleteComentario(comentarioId: string, parentId?: string) {
    await api.delete(`/comentarios/${comentarioId}`)
    if (parentId) {
      const pai = comentarios.value.data.find(c => c.id === parentId)
      if (pai) pai.respostas = pai.respostas.filter(r => r.id !== comentarioId)
    } else {
      comentarios.value.data = comentarios.value.data.filter(c => c.id !== comentarioId)
      comentarios.value.total--
    }
  }

  function reset() {
    avaliacoes.value  = { data: [], media: 0, total: 0, minhaAvaliacao: null }
    comentarios.value = { data: [], total: 0 }
  }

  return {
    avaliacoes, comentarios,
    loadingAv, loadingCom,
    errorAv, errorCom,
    fetchAvaliacoes, submitAvaliacao, deleteAvaliacao,
    fetchComentarios, submitComentario, deleteComentario,
    reset,
  }
})
