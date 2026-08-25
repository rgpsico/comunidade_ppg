export interface Pro {
  id: string
  name: string
  initials: string
  role: string
  category: 'Beleza' | 'Construção' | 'Casa' | 'Transporte' | 'Eventos' | 'Saúde'
  stars: number
  reviews: number
  dist: string
  price: number
  c1: string
  c2: string
  verified: boolean
  tags: string[]
  bio?: string
  services: { name: string; time: string; price: number }[]
  whatsapp: string
  yearsActive?: number
  attendances?: number
  responseTime?: string
  since?: string
  photoUrl?: string | null
  galleryUrls?: string[]
  isPremium?: boolean
  plano?: 'free' | 'premium'
}

export interface Event {
  id: string
  title: string
  day: string
  month: string
  date: string
  time: string
  place: string
  cat: 'baile' | 'pagode' | 'esporte' | 'cultura' | 'festa' | 'workshop'
  going: number
  c1: string
  c2: string
  featured?: boolean
  description: string
  free: boolean
  ageMin: number
  duration: string
  organizer: { name: string; eventsCount: number; followers: number }
  rsvp: { going: number; interested: number; userStatus: 'going' | 'interested' | null }
  comments: { author: string; text: string; time: string; likes: number }[]
  imagemCapaUrl?: string | null
  galeriaUrls?: string[]
}

export interface Project {
  id: string
  name: string
  desc: string
  icon: string
  tag: 'educação' | 'esporte' | 'cultura' | 'assistência' | 'saúde' | 'música'
  color: string
  impact: string
  impactLabel: string
  progress: number
  raised: string
  goal: string
  cta?: string
  yearsActive: number
  supporters: { name: string; initials: string; when: string; amount: string }[]
  updates: { time: string; title: string; text: string }[]
  activities?: { title: string; days: string; time: string; desc?: string; spots?: number }[]
  tutors?: { name: string; role: string; initials: string; color: string; bio?: string }[]
  gallery?: { color1: string; color2: string; caption?: string; imgUrl?: string }[]
  imagemCapaUrl?: string | null
  galeriaUrls?: string[]
  aceitaDoacoes?: boolean
}

export interface Vaga {
  id: string
  title: string
  company: string
  place: string
  pay: string
  per: string
  logoColor: string
  logoInitials: string
  desc: string
  tags: { label: string; kind: 'hot' | 'new' | '' }[]
  urgent: boolean
  isNew: boolean
  posted: string
  applicants: number
  type: 'CLT' | 'Freela' | 'Diária' | 'Divulgação'
  requirements: { text: string; level: 'obrigatório' | 'desejável' | 'opcional' }[]
  benefits: string[]
  companyInfo: { name: string; verified: boolean; rating: number; since: string }
  logoImagemUrl?: string | null
  whatsapp?: string | null
  emailContato?: string | null
}

export interface Conversation {
  id: string
  name: string
  initials: string
  color: string
  preview: string
  time: string
  unread: number
  online: boolean
}

export interface Produto {
  id: string
  lojaId: string
  nome: string
  descricao: string
  preco: number
  precoPromocional: number | null
  imagens: string[]
  imagemPrincipalUrl: string | null
  categoria: string
  disponivel: boolean
  destaque: boolean
  ordem: number
}

export interface Loja {
  id: string
  userId: number | null
  nome: string
  descricao: string
  categoria: string
  logoUrl: string
  capaUrl: string | null
  whatsapp: string
  endereco: string
  cor1: string
  cor2: string
  verificado: boolean
  ativo: boolean
  comunidade?: { id: number; nome: string } | null
  produtosCount: number
  produtos?: Produto[]
}

export interface AvaliacaoUser {
  id: number
  name: string
  avatar: string | null
}

export interface Avaliacao {
  id: string
  nota: number
  texto: string | null
  user: AvaliacaoUser
  createdAt: string
  isMine: boolean
}

export interface AvaliacoesState {
  data: Avaliacao[]
  media: number
  total: number
  minhaAvaliacao: Avaliacao | null
}

export interface Comentario {
  id: string
  corpo: string
  user: AvaliacaoUser
  respostas: Comentario[]
  createdAt: string
  isMine: boolean
}

export interface ComentariosState {
  data: Comentario[]
  total: number
}

export interface Informativo {
  id: string
  slug: string
  titulo: string
  fonte: string | null
  dataOcorrencia: string | null
  corpo: string
  urgente: boolean
  createdAt: string
}

export interface Artigo {
  id: string
  slug: string
  titulo: string
  resumo: string
  corpo: string
  imagemCapaUrl: string | null
  categoria: string
  autor: string | null
  publicadoEm: string | null
  createdAt: string
}

export interface Curriculo {
  id: string
  nome: string
  email: string
  telefone: string | null
  areaAtuacao: string
  habilidades: string[]
  experiencia: string | null
  cidade: string | null
  disponibilidade: 'imediata' | '30 dias' | '60 dias'
  pdfUrl: string | null
  publicado: boolean
  createdAt: string
}

export interface Patrocinador {
  id: string
  nome: string
  imagem_url: string | null
  texto: string | null
  link_url: string | null
  texto_botao: string
}

export interface Configuracao {
  nome_plataforma: string
  logo_url: string | null
  favicon_url: string | null
  cor_primaria: string
  cor_secundaria: string
  cor_destaque: string
  cor_fundo: string
  cor_card: string
  cor_texto: string
  cor_muted: string
  listagem_tipo: 'grade' | 'lista'
  itens_por_pagina: number
}

export interface Chamado {
  id: string
  tipo: 'problema' | 'servico'
  titulo: string
  descricao: string
  categoria: string
  fotos: string[]
  local: string | null
  estimativa_valor: string | null
  valor_acordado: string | null
  urgencia: 'normal' | 'urgente' | 'critico'
  status: 'aberto' | 'aceito' | 'em_andamento' | 'resolvido' | 'cancelado'
  user: { id: number; name: string } | null
  profissional: { id: number; nome: string; foto_url: string | null; whatsapp: string | null } | null
  doacoes: ChamadoDoacao[]
  total_doacoes: number
  aceito_em: string | null
  resolvido_em: string | null
  created_at: string
}

export interface ChamadoDoacao {
  id: string
  chamado_id: string
  user: { id: number; name: string } | null
  valor: number
  mensagem: string | null
  created_at: string
}

export interface RankingItem {
  user_id: number
  name: string
  pontos: number
  chamados_ajudados: number
  total_doado: number
}

export type WebView =
  | 'inicio' | 'profissionais' | 'eventos' | 'projetos' | 'vagas' | 'lojas' | 'artigos' | 'informativos'
  | 'chamados'
  | 'proDetail' | 'eventDetail' | 'projDetail' | 'vagaDetail' | 'lojaDetail' | 'artigoDetail' | 'informativoDetail'
  | 'produtoDetail' | 'chamadoDetail'
  | 'login' | 'perfil' | 'curriculos'

export type MobileScreen = 'home' | 'buscar' | 'postar' | 'msg' | 'perfil'
