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
  gallery?: { color1: string; color2: string; caption?: string }[]
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

export type WebView =
  | 'inicio' | 'profissionais' | 'eventos' | 'projetos' | 'vagas' | 'lojas'
  | 'proDetail' | 'eventDetail' | 'projDetail' | 'vagaDetail' | 'lojaDetail'
  | 'login' | 'perfil'

export type MobileScreen = 'home' | 'buscar' | 'postar' | 'msg' | 'perfil'
