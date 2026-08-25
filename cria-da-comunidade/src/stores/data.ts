import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'
import type { Paginated } from '../services/api'
import type { Pro, Event, Project, Vaga, Loja, Produto, Artigo, Informativo, Patrocinador, Configuracao, Chamado, ChamadoDoacao, RankingItem } from '../types'

// ── API response shapes ──────────────────────────────────────────────────────

export interface ApiComunidade {
  id: number
  nome: string
  slug: string | null
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
  plano?: string; is_premium?: boolean
}

interface ApiEvento {
  id: number; titulo: string; categoria: string; data_hora: string
  local: string | null; gratuito: boolean; preco: number | null
  confirmados: number; interessados: number; cor1: string; cor2: string
  destaque: boolean; descricao: string | null; duracao: string | null
  idade_minima: number; organizador?: { id: number; name: string }
  imagem_capa_url: string | null; galeria_urls: string[] | null
}

interface ApiProjetoAtividade {
  id: number; titulo: string; dias: string; horario: string
  descricao: string | null; vagas: number | null; ordem: number
}

interface ApiProjetoMembro {
  id: number; nome: string; cargo: string; bio: string | null
  foto_url: string | null; cor: string; ordem: number
}

interface ApiProjeto {
  id: number; nome: string; descricao: string | null; icone: string | null
  causa: string; cor: string; impacto_valor: string | null
  impacto_label: string | null; progresso: number; arrecadado: number
  meta: number | null; cta_label: string | null; anos_atuando: number | null
  aceita_doacoes: boolean
  imagem_capa_url: string | null; galeria_urls: string[] | null
  atividades?: ApiProjetoAtividade[]
  membros?: ApiProjetoMembro[]
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

export interface ApiArtigo {
  id: number; slug: string; titulo: string; resumo: string | null
  corpo: string; imagem_capa_url: string | null; categoria: string
  autor: string | null; publicado_em: string | null; created_at: string
}

export interface ApiFeedPost {
  id: number; autor: string; legenda: string | null
  imagem_url: string | null; cor1: string; cor2: string
  tamanho: 'normal' | 'tall' | 'wide'; created_at: string
}

export interface ApiInformativo {
  id: number
  slug: string
  titulo: string
  fonte: string | null
  data_ocorrencia: string | null
  corpo: string
  urgente: boolean
  created_at: string
}

export interface ApiCurriculo {
  id: number; nome: string; email: string; telefone: string | null
  area_atuacao: string; habilidades: string[] | null; experiencia: string | null
  cidade: string | null; disponibilidade: string; pdf_url: string | null
  publicado: boolean; created_at: string
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
    isPremium: p.is_premium ?? false,
    plano: (p.plano ?? 'free') as 'free' | 'premium',
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
    // Mapeados apenas quando vêm do endpoint de detalhe (/projetos/{id})
    activities: p.atividades?.map(a => ({
      title: a.titulo,
      days: a.dias,
      time: a.horario,
      desc: a.descricao ?? undefined,
      spots: a.vagas ?? undefined,
    })),
    tutors: p.membros?.map(m => ({
      name: m.nome,
      role: m.cargo,
      initials: m.nome.split(' ').map((w: string) => w[0]).slice(0, 2).join('').toUpperCase(),
      color: m.cor,
      bio: m.bio ?? undefined,
    })),
    gallery: p.galeria_urls?.length
      ? p.galeria_urls.map(url => ({ color1: p.cor, color2: '#1a1a2e', caption: '', imgUrl: url }))
      : undefined,
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

function mapArtigo(a: ApiArtigo): Artigo {
  return {
    id: String(a.id),
    slug: a.slug,
    titulo: a.titulo,
    resumo: a.resumo ?? '',
    corpo: a.corpo,
    imagemCapaUrl: a.imagem_capa_url ?? null,
    categoria: a.categoria,
    autor: a.autor ?? null,
    publicadoEm: a.publicado_em ?? null,
    createdAt: a.created_at,
  }
}

function mapInformativo(i: ApiInformativo): Informativo {
  return {
    id: String(i.id),
    slug: i.slug ?? String(i.id),
    titulo: i.titulo,
    fonte: i.fonte ?? null,
    dataOcorrencia: i.data_ocorrencia ?? null,
    corpo: i.corpo,
    urgente: i.urgente,
    createdAt: i.created_at,
  }
}

function mapChamado(c: any): Chamado {
  return {
    id: String(c.id),
    tipo: c.tipo,
    titulo: c.titulo,
    descricao: c.descricao,
    categoria: c.categoria,
    fotos: Array.isArray(c.fotos) ? c.fotos : [],
    local: c.local ?? null,
    estimativa_valor: c.estimativa_valor ?? null,
    valor_acordado: c.valor_acordado ?? null,
    urgencia: c.urgencia,
    status: c.status,
    user: c.user ?? null,
    profissional: c.profissional ?? null,
    doacoes: Array.isArray(c.doacoes) ? c.doacoes.map((d: any): ChamadoDoacao => ({
      id: String(d.id),
      chamado_id: String(d.chamado_id),
      user: d.user ?? null,
      valor: Number(d.valor),
      mensagem: d.mensagem ?? null,
      created_at: d.created_at,
    })) : [],
    total_doacoes: Number(c.total_doacoes ?? 0),
    aceito_em: c.aceito_em ?? null,
    resolvido_em: c.resolvido_em ?? null,
    created_at: c.created_at,
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
  const artigos = ref<Artigo[]>([])
  const informativos = ref<Informativo[]>([])
  const feedPosts = ref<ApiFeedPost[]>([])
  const meuCurriculo = ref<ApiCurriculo | null>(null)
  const chamados = ref<Chamado[]>([])
  const ranking = ref<RankingItem[]>([])
  const patrocinador = ref<Patrocinador | null>(null)
  const configuracao = ref<Configuracao>({
    nome_plataforma: 'Cria da Comunidade',
    logo_url: null,
    favicon_url: null,
    cor_primaria: '#FF5E1A',
    cor_secundaria: '#FFD23F',
    cor_destaque: '#2BD96B',
    cor_fundo: '#0D0B09',
    cor_card: '#1C1916',
    cor_texto: '#F5F0E8',
    cor_muted: '#8B847B',
    listagem_tipo: 'grade',
    itens_por_pagina: 20,
  })
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
      const [pRes, eRes, prRes, vRes, lRes, aRes, iRes, fRes, spRes, cfRes, chRes, rkRes] = await Promise.all([
        api.get<Paginated<ApiProfissional>>(`/profissionais?per_page=50${cParam}`),
        api.get<Paginated<ApiEvento>>(`/eventos?per_page=50${cParam}`),
        api.get<Paginated<ApiProjeto>>(`/projetos?per_page=50${cParam}`),
        api.get<Paginated<ApiVaga>>(`/vagas?per_page=50${cParam}`),
        api.get<Paginated<ApiLoja>>(`/lojas?per_page=60${cParam}`),
        api.get<Paginated<ApiArtigo>>(`/artigos?per_page=50${cParam}`),
        api.get<Paginated<ApiInformativo>>(`/informativos?per_page=50${cParam}`),
        api.get<Paginated<ApiFeedPost>>(`/feed-posts?per_page=8${cParam}`),
        api.get<Patrocinador | null>(`/patrocinador-ativo${cid ? `?comunidade_id=${cid}` : ''}`).catch(() => null),
        api.get<Configuracao>(`/configuracoes${cid ? `?comunidade_id=${cid}` : ''}`).catch(() => null),
        api.get<{ data: any[] }>(`/chamados?per_page=50${cParam}`).catch(() => null),
        api.get<RankingItem[]>('/chamados-ranking').catch(() => null),
      ])
      pros.value = pRes.data.map(mapPro)
      events.value = eRes.data.map(mapEvent)
      projects.value = prRes.data.map(mapProject)
      vagas.value = vRes.data.map(mapVaga)
      lojas.value = lRes.data.map(mapLoja)
      artigos.value = aRes.data.map(mapArtigo)
      informativos.value = iRes.data.map(mapInformativo)
      feedPosts.value = fRes.data
      patrocinador.value = spRes ?? null
      if (cfRes) configuracao.value = { ...configuracao.value, ...cfRes }
      if (chRes) chamados.value = chRes.data.map(mapChamado)
      if (rkRes) ranking.value = rkRes
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

  async function fetchMeuCurriculo() {
    try {
      const res = await api.get<ApiCurriculo | null>('/curriculos/meu')
      meuCurriculo.value = res
    } catch {
      meuCurriculo.value = null
    }
  }

  async function salvarCurriculo(data: {
    nome: string; email: string; telefone?: string
    area_atuacao: string; habilidades?: string[]
    experiencia?: string; cidade?: string
    disponibilidade?: string; comunidade_id?: number | null
  }): Promise<ApiCurriculo> {
    const res = await api.post<ApiCurriculo>('/curriculos', data)
    meuCurriculo.value = res
    return res
  }

  async function uploadCurriculoPdf(file: File): Promise<string | null> {
    const form = new FormData()
    form.append('pdf', file)
    const res = await api.postForm<{ pdf_url: string }>('/curriculos/pdf', form)
    if (meuCurriculo.value) meuCurriculo.value.pdf_url = res.pdf_url
    return res.pdf_url
  }

  async function fetchLojaDetail(id: string): Promise<Loja | null> {
    try {
      const res = await api.get<ApiLoja>(`/lojas/${id}`)
      return mapLoja(res)
    } catch {
      return null
    }
  }

  async function fetchProjetoDetail(id: string): Promise<import('../types').Project | null> {
    try {
      const res = await api.get<ApiProjeto>(`/projetos/${id}`)
      return mapProject(res)
    } catch {
      return null
    }
  }

  async function fetchChamadoDetail(id: string): Promise<Chamado | null> {
    try {
      const res = await api.get<any>(`/chamados/${id}`)
      return mapChamado(res)
    } catch {
      return null
    }
  }

  async function aceitarChamado(id: string): Promise<Chamado> {
    const res = await api.post<any>(`/chamados/${id}/aceitar`, {})
    const updated = mapChamado(res)
    const idx = chamados.value.findIndex(c => c.id === id)
    if (idx !== -1) chamados.value[idx] = updated
    return updated
  }

  async function resolverChamado(id: string): Promise<Chamado> {
    const res = await api.post<any>(`/chamados/${id}/resolver`, {})
    const updated = mapChamado(res)
    const idx = chamados.value.findIndex(c => c.id === id)
    if (idx !== -1) chamados.value[idx] = updated
    return updated
  }

  async function registrarDoacao(chamadoId: string, valor: number, mensagem: string): Promise<void> {
    await api.post(`/chamados/${chamadoId}/doacoes`, { valor, mensagem })
  }

  async function criarChamado(payload: {
    tipo: string; titulo: string; descricao: string; categoria: string
    local?: string; estimativa_valor?: string; urgencia: string; comunidade_id?: number | null
  }): Promise<Chamado> {
    const res = await api.post<any>('/chamados', payload)
    const created = mapChamado(res)
    chamados.value.unshift(created)
    return created
  }

  return {
    pros, events, projects, vagas, lojas, artigos, informativos, feedPosts, meuCurriculo,
    chamados, ranking,
    patrocinador, configuracao,
    loading, error,
    communities, activeComunidadeId, activeComunidade,
    fetchComunidades, setComunidade, fetchAll,
    rsvp, apoiar, candidatar, fetchLojaDetail, fetchProjetoDetail,
    fetchMeuCurriculo, salvarCurriculo, uploadCurriculoPdf,
    fetchChamadoDetail, aceitarChamado, resolverChamado, registrarDoacao, criarChamado,
  }
})
