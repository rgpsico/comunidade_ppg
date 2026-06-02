import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'
import type { Paginated } from '../services/api'
import type { Pro, Event, Project, Vaga, Loja, Produto } from '../types'

// ── API response shapes ──────────────────────────────────────────────────────

export interface ApiComunidade {
  id: number
  nome: string
  cidade: string | null
  estado: string | null
}

interface ApiProfissional {
  id: number; nome: string; cargo: string; categoria: string
  estrelas: number; total_avaliacoes: number; total_atendimentos: number
  preco_a_partir: number | null; cor1: string; cor2: string
  verificado: boolean; tags: string[] | null; bio: string | null
  whatsapp: string | null; tempo_resposta: string | null
  foto_url: string | null; galeria_urls: string[] | null
  comunidade?: { id: number; nome: string }
}

interface ApiEvento {
  id: number; titulo: string; categoria: string; data_hora: string
  local: string | null; gratuito: boolean; preco: number | null
  confirmados: number; interessados: number; cor1: string; cor2: string
  destaque: boolean; descricao: string | null; duracao: string | null
  idade_minima: number; organizador?: { id: number; name: string }
  imagem_capa_url: string | null; galeria_urls: string[] | null
}

interface ApiProjeto {
  id: number; nome: string; descricao: string | null; icone: string | null
  causa: string; cor: string; impacto_valor: string | null
  impacto_label: string | null; progresso: number; arrecadado: number
  meta: number | null; cta_label: string | null; anos_atuando: number | null
  aceita_doacoes: boolean
  imagem_capa_url: string | null; galeria_urls: string[] | null
}

interface ApiVaga {
  id: number; titulo: string; empresa: string; local: string | null
  salario: string | null; salario_periodo: string | null
  logo_cor: string | null; logo_iniciais: string | null; logo_imagem_url: string | null
  descricao: string | null; urgente: boolean; candidatos: number; tipo: string
  created_at: string; whatsapp: string | null; email_contato: string | null
  requisitos?: { descricao: string; nivel: string }[]
  beneficios?: { descricao: string }[]
}

export interface ApiProduto {
  id: number; loja_id: number; nome: string; descricao: string | null
  preco: number; preco_promocional: number | null
  imagens: string[] | null; imagens_urls: string[]; imagem_principal_url: string | null
  categoria: string | null; disponivel: boolean; destaque: boolean; ordem: number
}

export interface ApiLoja {
  id: number; user_id: number | null; nome: string; descricao: string | null; categoria: string
  logo_url: string; capa_url: string | null; whatsapp: string | null
  endereco: string | null; cor1: string; cor2: string
  verificado: boolean; ativo: boolean; produtos_count?: number
  comunidade?: { id: number; nome: string } | null
  produtos?: ApiProduto[]
}

// ── Mappers ──────────────────────────────────────────────────────────────────

function initials(name: string): string {
  return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

function formatMoney(val: number): string {
  return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

function mapPro(p: ApiProfissional): Pro {
  return {
    id: String(p.id),
    name: p.nome,
    initials: initials(p.nome),
    role: p.cargo,
    category: p.categoria as Pro['category'],
    stars: p.estrelas,
    reviews: p.total_avaliacoes,
    dist: '',
    price: p.preco_a_partir ?? 0,
    c1: p.cor1,
    c2: p.cor2,
    verified: p.verificado,
    tags: Array.isArray(p.tags) ? p.tags : [],
    bio: p.bio ?? '',
    whatsapp: p.whatsapp ?? '',
    attendances: p.total_atendimentos,
    responseTime: p.tempo_resposta ?? undefined,
    services: [],
    photoUrl: p.foto_url ?? null,
    galleryUrls: p.galeria_urls ?? [],
  }
}

function mapEvent(e: ApiEvento): Event {
  const dt = new Date(e.data_hora)
  const months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
  return {
    id: String(e.id),
    title: e.titulo,
    day: String(dt.getDate()).padStart(2, '0'),
    month: months[dt.getMonth()],
    date: dt.toLocaleDateString('pt-BR'),
    time: dt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
    place: e.local ?? '',
    cat: e.categoria as Event['cat'],
    going: e.confirmados,
    c1: e.cor1,
    c2: e.cor2,
    featured: e.destaque,
    description: e.descricao ?? '',
    free: e.gratuito,
    ageMin: e.idade_minima,
    duration: e.duracao ?? '',
    organizer: { name: e.organizador?.name ?? 'Organizador', eventsCount: 0, followers: 0 },
    rsvp: { going: e.confirmados, interested: e.interessados, userStatus: null },
    comments: [],
    imagemCapaUrl: e.imagem_capa_url ?? null,
    galeriaUrls: e.galeria_urls ?? [],
  }
}

function mapProject(p: ApiProjeto): Project {
  return {
    id: String(p.id),
    name: p.nome,
    desc: p.descricao ?? '',
    icon: p.icone ?? '❤',
    tag: p.causa.toLowerCase() as Project['tag'],
    color: p.cor,
    impact: p.impacto_valor ?? '0',
    impactLabel: p.impacto_label ?? 'impactados',
    progress: p.progresso,
    raised: formatMoney(p.arrecadado),
    goal: formatMoney(p.meta ?? 0),
    cta: p.cta_label ?? 'Apoiar',
    yearsActive: p.anos_atuando ?? 1,
    supporters: [],
    updates: [],
    imagemCapaUrl: p.imagem_capa_url ?? null,
    galeriaUrls: p.galeria_urls ?? [],
    aceitaDoacoes: p.aceita_doacoes ?? false,
  }
}

function mapVaga(v: ApiVaga): Vaga {
  const daysAgo = Math.floor((Date.now() - new Date(v.created_at).getTime()) / 86400000)
  const tags: Vaga['tags'] = []
  if (v.urgente) tags.push({ label: 'Urgente', kind: 'hot' })
  if (daysAgo <= 3) tags.push({ label: 'Nova', kind: 'new' })
  tags.push({ label: v.tipo, kind: '' })

  return {
    id: String(v.id),
    title: v.titulo,
    company: v.empresa,
    place: v.local ?? '',
    pay: v.salario ?? 'A combinar',
    per: v.salario_periodo ?? '',
    logoColor: v.logo_cor ?? '#FF5E1A',
    logoInitials: v.logo_iniciais ?? v.empresa.slice(0, 2).toUpperCase(),
    desc: v.descricao ?? '',
    tags,
    urgent: v.urgente,
    isNew: daysAgo <= 3,
    posted: daysAgo === 0 ? 'hoje' : `${daysAgo}d atrás`,
    applicants: v.candidatos,
    type: v.tipo as Vaga['type'],
    requirements: (v.requisitos ?? []).map(r => ({ text: r.descricao, level: r.nivel as Vaga['requirements'][0]['level'] })),
    benefits: (v.beneficios ?? []).map(b => b.descricao),
    companyInfo: { name: v.empresa, verified: false, rating: 5.0, since: '2020' },
    logoImagemUrl: v.logo_imagem_url ?? null,
    whatsapp: v.whatsapp ?? null,
    emailContato: v.email_contato ?? null,
  }
}

function mapProduto(p: ApiProduto): Produto {
  return {
    id: String(p.id),
    lojaId: String(p.loja_id),
    nome: p.nome,
    descricao: p.descricao ?? '',
    preco: p.preco,
    precoPromocional: p.preco_promocional ?? null,
    imagens: p.imagens_urls ?? [],
    imagemPrincipalUrl: p.imagem_principal_url ?? null,
    categoria: p.categoria ?? '',
    disponivel: p.disponivel,
    destaque: p.destaque,
    ordem: p.ordem,
  }
}

function mapLoja(l: ApiLoja): Loja {
  return {
    id: String(l.id),
    userId: l.user_id ?? null,
    nome: l.nome,
    descricao: l.descricao ?? '',
    categoria: l.categoria,
    logoUrl: l.logo_url,
    capaUrl: l.capa_url ?? null,
    whatsapp: l.whatsapp ?? '',
    endereco: l.endereco ?? '',
    cor1: l.cor1 ?? '#FF5E1A',
    cor2: l.cor2 ?? '#FFD23F',
    verificado: l.verificado,
    ativo: l.ativo,
    comunidade: l.comunidade ?? null,
    produtosCount: l.produtos_count ?? (l.produtos?.length ?? 0),
    produtos: l.produtos ? l.produtos.map(mapProduto) : undefined,
  }
}

// ── Store ─────────────────────────────────────────────────────────────────────

export const useDataStore = defineStore('data', () => {
  const pros = ref<Pro[]>([])
  const events = ref<Event[]>([])
  const projects = ref<Project[]>([])
  const vagas = ref<Vaga[]>([])
  const lojas = ref<Loja[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // ── Comunidades ────────────────────────────────────────────────────────────
  const communities = ref<ApiComunidade[]>([])
  const activeComunidadeId = ref<number | null>(null)
  const activeComunidade = computed(() =>
    communities.value.find(c => c.id === activeComunidadeId.value) ?? null
  )

  async function fetchComunidades() {
    try {
      communities.value = await api.get<ApiComunidade[]>('/comunidades')
    } catch (e) {
      console.error('Erro ao carregar comunidades:', e)
    }
  }

  async function setComunidade(id: number | null) {
    activeComunidadeId.value = id
    await fetchAll()
  }

  // ── Dados ──────────────────────────────────────────────────────────────────
  async function fetchAll() {
    loading.value = true
    error.value = null
    try {
      const cid = activeComunidadeId.value
      const cParam = cid ? `&comunidade_id=${cid}` : ''
      const [pRes, eRes, prRes, vRes, lRes] = await Promise.all([
        api.get<Paginated<ApiProfissional>>(`/profissionais?per_page=50${cParam}`),
        api.get<Paginated<ApiEvento>>(`/eventos?per_page=50${cParam}`),
        api.get<Paginated<ApiProjeto>>(`/projetos?per_page=50${cParam}`),
        api.get<Paginated<ApiVaga>>(`/vagas?per_page=50${cParam}`),
        api.get<Paginated<ApiLoja>>(`/lojas?per_page=60${cParam}`),
      ])
      pros.value = pRes.data.map(mapPro)
      events.value = eRes.data.map(mapEvent)
      projects.value = prRes.data.map(mapProject)
      vagas.value = vRes.data.map(mapVaga)
      lojas.value = lRes.data.map(mapLoja)
    } catch (e: unknown) {
      error.value = 'Não foi possível carregar os dados. Verifique a conexão com a API.'
      console.error('API error:', e)
    } finally {
      loading.value = false
    }
  }

  async function rsvp(eventoId: string, status: 'going' | 'interested' | 'not_going') {
    await api.post(`/eventos/${eventoId}/rsvp`, { status })
    const ev = events.value.find(e => e.id === eventoId)
    if (ev) ev.rsvp.userStatus = status === 'not_going' ? null : status
  }

  async function apoiar(projetoId: string, valor: number, forma_pagamento: string) {
    await api.post(`/projetos/${projetoId}/apoiar`, { valor, forma_pagamento, status: 'confirmado' })
    await fetchAll()
  }

  async function candidatar(vagaId: string) {
    await api.post(`/vagas/${vagaId}/candidatar`, {})
    const v = vagas.value.find(v => v.id === vagaId)
    if (v) v.applicants++
  }

  async function fetchLojaDetail(id: string): Promise<Loja | null> {
    try {
      const res = await api.get<ApiLoja>(`/lojas/${id}`)
      return mapLoja(res)
    } catch {
      return null
    }
  }

  return {
    pros, events, projects, vagas, lojas, loading, error,
    communities, activeComunidadeId, activeComunidade,
    fetchComunidades, setComunidade, fetchAll,
    rsvp, apoiar, candidatar, fetchLojaDetail,
  }
})
