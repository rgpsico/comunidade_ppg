<template>
  <div class="vaga-detail fade-up" v-if="vaga">
    <div class="top-bar">
      <button class="back-btn" @click="ui.goTo('vagas')">← Vagas</button>
      <div class="share-wrap" ref="shareWrap">
        <button class="share-btn" @click="doShare">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          Compartilhar
        </button>
        <div class="share-dropdown" v-if="showShare">
          <div class="share-dropdown-title">Compartilhar vaga</div>
          <a :href="shareLinks.whatsapp" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            WhatsApp
          </a>
          <a :href="shareLinks.twitter" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            X (Twitter)
          </a>
          <a :href="shareLinks.linkedin" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#0077B5"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            LinkedIn
          </a>
          <a :href="shareLinks.facebook" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </a>
          <div class="share-divider"></div>
          <button @click="copyLink" class="share-item share-copy" :class="{ copied }">
            <svg v-if="!copied" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
            <svg v-else width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ copied ? 'Link copiado!' : 'Copiar link' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Hero -->
    <div class="vaga-hero">
      <div class="vh-logo" :style="{ background: vaga.logoColor }">{{ vaga.logoInitials }}</div>
      <div class="vh-main">
        <div class="vaga-flags">
          <span v-if="vaga.urgent" class="flag urgent-flag">urgente</span>
          <span v-if="vaga.isNew" class="flag new-flag">novo</span>
          <span class="flag type-flag">{{ vaga.type }}</span>
        </div>
        <h1 class="vh-title display">{{ vaga.title }}</h1>
        <div class="vh-company">{{ vaga.company }} · empresa verificada</div>
        <div class="vh-meta">
          <span>📍 {{ vaga.place }}</span>
          <span>🕒 postado {{ vaga.posted }}</span>
          <span>👥 {{ vaga.applicants }} candidatos</span>
        </div>
      </div>
      <div class="vh-right">
        <div class="vh-pay">{{ vaga.pay }}</div>
        <div class="vh-per">{{ vaga.per }}</div>
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-main">
        <div class="tabs">
          <button v-for="tab in tabs" :key="tab" class="tab" :class="{ active: activeTab === tab }" @click="activeTab = tab">{{ tab }}</button>
        </div>

        <div v-if="activeTab === 'Descrição'" class="tab-content">
          <h3 class="content-title">Sobre a vaga</h3>
          <div class="body-text rich-content" v-html="vaga.desc"></div>

          <h3 class="content-title" style="margin-top: 24px">Requisitos</h3>
          <div class="req-list">
            <div v-for="req in vaga.requirements" :key="req.text" class="req-row">
              <div class="req-check">✓</div>
              <div class="req-text">{{ req.text }}</div>
              <div class="req-level" :class="req.level">{{ req.level }}</div>
            </div>
          </div>

          <h3 class="content-title" style="margin-top: 24px">O que oferece</h3>
          <div class="ben-list">
            <div v-for="b in vaga.benefits" :key="b" class="ben-row">
              <div class="ben-star">⭐</div>
              <div>{{ b }}</div>
            </div>
          </div>

          <h3 class="content-title" style="margin-top: 24px">Sobre a empresa</h3>
          <div class="organizer-card">
            <div class="org-av">{{ vaga.companyInfo.name.slice(0,2).toUpperCase() }}</div>
            <div class="org-info">
              <div class="org-role">{{ vaga.companyInfo.verified ? '✓ verificada' : 'empresa' }} · desde {{ vaga.companyInfo.since }}</div>
              <div class="org-name">{{ vaga.companyInfo.name }}</div>
              <div class="org-meta">⭐ {{ vaga.companyInfo.rating }}</div>
            </div>
            <button class="ghost-btn">+ Seguir</button>
          </div>
        </div>
      </div>

      <aside class="detail-aside">
        <div class="aside-card contact-card">
          <h4 class="aside-title">Falar com a empresa</h4>
          <p class="aside-sub">Entre em contato direto com o anunciante</p>
          <div class="contact-actions">
            <a
              v-if="vaga.whatsapp"
              :href="`https://wa.me/55${vaga.whatsapp.replace(/\D/g, '')}?text=${encodeURIComponent('Oi! Vi a vaga de ' + vaga.title + ' e tenho interesse!')}`"
              target="_blank"
              class="btn-whatsapp"
              @click.stop
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
              Falar no WhatsApp
            </a>
            <a
              v-if="vaga.emailContato"
              :href="`mailto:${vaga.emailContato}?subject=${encodeURIComponent('Interesse na vaga: ' + vaga.title)}`"
              class="btn-email"
              @click.stop
            >
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
              Enviar e-mail
            </a>
            <p v-if="!vaga.whatsapp && !vaga.emailContato" class="no-contact-msg">
              Contato não informado pelo anunciante.
            </p>
          </div>
          <!-- Candidatura -->
          <div v-if="!auth.isAuthenticated" class="candidatura-login">
            <button class="btn-apply btn-login" @click.stop="ui.goTo('login')">
              Entrar para se candidatar
            </button>
            <p class="cand-hint">Faça login e candidate-se com 1 clique</p>
          </div>
          <template v-else>
            <button
              class="btn-apply"
              :class="{ applied, loading: sending, disabled: !vaga.emailContato }"
              :disabled="!vaga.emailContato || applied || sending"
              @click.stop="doCandidatar"
              :title="!vaga.emailContato ? 'Empresa não informou e-mail de contato' : ''"
            >
              <svg v-if="sending" class="spin" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
              <svg v-else-if="applied" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
              {{ applied ? 'Candidatura enviada!' : sending ? 'Enviando...' : !vaga.emailContato ? 'Sem e-mail de contato' : 'Me candidatar →' }}
            </button>
            <p v-if="applyError" class="cand-error">{{ applyError }}</p>
            <p v-if="!vaga.emailContato" class="cand-hint">A empresa não cadastrou e-mail de contato</p>
          </template>
        </div>

        <!-- Banco de Talentos -->
        <div class="aside-card talent-card">
          <div class="talent-header">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <h4 class="aside-title" style="margin:0">Banco de Talentos</h4>
          </div>

          <!-- Não autenticado -->
          <div v-if="!auth.isAuthenticated" class="talent-body">
            <p class="talent-text">Cadastre seu currículo e seja encontrado automaticamente pelas próximas vagas da comunidade!</p>
            <button class="btn-talent" @click="ui.goTo('login')">Entrar e cadastrar →</button>
          </div>

          <!-- Carregando -->
          <div v-else-if="loadingCurr" class="talent-body talent-loading">
            <div class="loading-dots"><span></span><span></span><span></span></div>
            <span>Verificando seu perfil...</span>
          </div>

          <!-- Já tem currículo -->
          <div v-else-if="data.meuCurriculo" class="talent-body talent-ok">
            <div class="talent-ok-badge">✅ Você está no banco de talentos</div>
            <p class="talent-text">Seu perfil será enviado automaticamente para recrutadores quando novas vagas compatíveis forem cadastradas.</p>
            <button class="btn-talent-ghost" @click="ui.goTo('curriculos')">Ver meu currículo →</button>
          </div>

          <!-- Sem currículo — mini form -->
          <div v-else class="talent-body">
            <p class="talent-text">Cadastre seus dados e seja encontrado automaticamente pelas próximas vagas!</p>
            <div class="tf-field">
              <label>Nome completo</label>
              <input v-model="currForm.nome" type="text" class="tf-input" placeholder="Seu nome" />
            </div>
            <div class="tf-field">
              <label>WhatsApp</label>
              <input v-model="currForm.telefone" type="tel" class="tf-input" placeholder="(21) 99999-0000" />
            </div>
            <div class="tf-field">
              <label>Área de atuação</label>
              <select v-model="currForm.area_atuacao" class="tf-input">
                <option value="">Selecione...</option>
                <option v-for="a in currAreas" :key="a" :value="a">{{ a }}</option>
              </select>
            </div>
            <div class="tf-field">
              <label>Disponibilidade</label>
              <div class="tf-radios">
                <label class="tf-radio"><input type="radio" v-model="currForm.disponibilidade" value="imediata" /> Imediata</label>
                <label class="tf-radio"><input type="radio" v-model="currForm.disponibilidade" value="30 dias" /> 30 dias</label>
                <label class="tf-radio"><input type="radio" v-model="currForm.disponibilidade" value="60 dias" /> 60 dias</label>
              </div>
            </div>
            <button class="btn-talent" :disabled="!currFormValido || salvandoCurr" @click="salvarCurriculoRapido">
              <svg v-if="salvandoCurr" class="spin" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
              {{ salvandoCurr ? 'Salvando...' : 'Entrar no banco de talentos →' }}
            </button>
            <p v-if="erroCurr" class="curr-error">{{ erroCurr }}</p>
          </div>
        </div>

        <div class="aside-card">
          <h4 class="aside-title">Detalhes</h4>
          <div class="detail-rows">
            <div class="drow"><span class="drow-k">Tipo</span><span>{{ vaga.type }}</span></div>
            <div class="drow"><span class="drow-k">Local</span><span>{{ vaga.place }}</span></div>
            <div class="drow"><span class="drow-k">Salário</span><span class="green-text">{{ vaga.pay }}</span></div>
            <div class="drow"><span class="drow-k">Período</span><span>{{ vaga.per }}</span></div>
            <div class="drow"><span class="drow-k">Candidatos</span><span>{{ vaga.applicants }}</span></div>
            <div class="drow"><span class="drow-k">Status</span><span class="green-text">aberta</span></div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useUiStore } from '../../../stores/ui'
import { useDataStore } from '../../../stores/data'
import { useAuthStore } from '../../../stores/auth'
import { api } from '../../../services/api'

const ui = useUiStore()
const data = useDataStore()
const auth = useAuthStore()
const vaga = computed(() => ui.selectedVaga)
const activeTab = ref('Descrição')
const tabs = ['Descrição', 'Empresa', 'Similares (8)']
const applied = ref(false)
const sending = ref(false)
const applyError = ref('')
const showShare = ref(false)
const copied = ref(false)
const shareWrap = ref<HTMLElement | null>(null)

// Banco de Talentos
const loadingCurr = ref(true)
const salvandoCurr = ref(false)
const erroCurr = ref('')
const currForm = ref({
  nome: '',
  telefone: '',
  area_atuacao: '',
  disponibilidade: 'imediata',
})

const currAreas = [
  'Limpeza e Doméstica', 'Construção e Reforma', 'Beleza e Estética', 'Gastronomia',
  'Saúde e Bem-estar', 'Costura e Moda', 'Educação', 'Tecnologia',
  'Transporte e Entregas', 'Eventos', 'Comércio', 'Arte e Artesanato',
  'Administração', 'Hotelaria', 'Atendente', 'Trabalho na Praia', 'Outro',
]

const currFormValido = computed(() => currForm.value.nome.trim() && currForm.value.area_atuacao)

const areaKeywords: [string, string[]][] = [
  ['Limpeza e Doméstica', ['limp', 'domésti', 'faxin', 'passadei', 'camarei', 'lavanderi']],
  ['Construção e Reforma', ['constru', 'reform', 'pintor', 'pedreir', 'eletric', 'encanad', 'carpint']],
  ['Beleza e Estética', ['beleza', 'estétic', 'cabelei', 'manicur', 'maquiag', 'depila', 'sobrancelh']],
  ['Gastronomia', ['cozinhei', 'gastr', 'garçom', 'confeit', 'salgad', 'marmit', 'barista']],
  ['Saúde e Bem-estar', ['saúde', 'enferma', 'cuidador', 'fisioter', 'personal']],
  ['Transporte e Entregas', ['motot', 'motorist', 'entregad', 'mudança', 'carreto']],
  ['Eventos', ['event', 'fotograf', 'decoraç', 'sonori', ' dj ']],
  ['Comércio', ['vendedor', 'caixa', 'estoquist', 'promotor', 'reposiç']],
  ['Hotelaria', ['hotel', 'pousad', 'hosped', 'recepcion']],
  ['Atendente', ['atendente', 'atendim', 'suporte']],
  ['Trabalho na Praia', ['praia', 'quiosq', 'surf', 'ambulante']],
]

function detectArea(title: string): string {
  const t = title.toLowerCase()
  for (const [area, kws] of areaKeywords) {
    if (kws.some(k => t.includes(k))) return area
  }
  return ''
}

async function salvarCurriculoRapido() {
  salvandoCurr.value = true
  erroCurr.value = ''
  try {
    await data.salvarCurriculo({
      nome: currForm.value.nome,
      email: auth.user?.email ?? '',
      telefone: currForm.value.telefone || undefined,
      area_atuacao: currForm.value.area_atuacao,
      habilidades: [],
      disponibilidade: currForm.value.disponibilidade,
    })
  } catch (e: unknown) {
    erroCurr.value = (e as { message?: string })?.message ?? 'Erro ao salvar. Tente novamente.'
  } finally {
    salvandoCurr.value = false
  }
}

async function doCandidatar() {
  if (!vaga.value || applied.value || sending.value || !vaga.value.emailContato) return
  sending.value = true
  applyError.value = ''
  try {
    await api.post(`/vagas/${vaga.value.id}/candidatar`, {})
    applied.value = true
    // atualiza contador local
    const v = data.vagas.find(v => v.id === vaga.value!.id)
    if (v) v.applicants++
  } catch (e: unknown) {
    const msg = (e as { message?: string })?.message
    applyError.value = msg ?? 'Erro ao enviar. Tente novamente.'
  } finally {
    sending.value = false
  }
}

const shareUrl = computed(() =>
  `${window.location.origin}/vagas/${vaga.value?.id ?? ''}`
)

const shareLinks = computed(() => {
  if (!vaga.value) return { whatsapp: '#', twitter: '#', linkedin: '#', facebook: '#' }
  const v = vaga.value
  const url = encodeURIComponent(shareUrl.value)
  const text = encodeURIComponent(
    `🚀 Vaga: ${v.title}\n🏢 ${v.company} · ${v.place}\n💰 ${v.pay} ${v.per}\n\nVeja mais em: ${shareUrl.value}`
  )
  const title = encodeURIComponent(`${v.title} — ${v.company}`)
  return {
    whatsapp: `https://wa.me/?text=${text}`,
    twitter: `https://x.com/intent/tweet?text=${title}&url=${url}`,
    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${url}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
  }
})

async function doShare() {
  if (!vaga.value) return
  if (navigator.share) {
    try {
      await navigator.share({
        title: `${vaga.value.title} — ${vaga.value.company}`,
        text: `${vaga.value.pay} ${vaga.value.per} · ${vaga.value.place} · ${vaga.value.type}`,
        url: shareUrl.value,
      })
    } catch { /* diálogo cancelado */ }
  } else {
    showShare.value = !showShare.value
  }
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(shareUrl.value)
  } catch {
    const el = document.createElement('textarea')
    el.value = shareUrl.value
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
  }
  copied.value = true
  setTimeout(() => {
    copied.value = false
    showShare.value = false
  }, 2000)
}

function onDocClick(e: MouseEvent) {
  if (shareWrap.value && !shareWrap.value.contains(e.target as Node)) {
    showShare.value = false
  }
}
onMounted(async () => {
  document.addEventListener('click', onDocClick)
  if (auth.isAuthenticated) {
    await data.fetchMeuCurriculo()
    currForm.value.nome = auth.user?.name ?? ''
    if (vaga.value) currForm.value.area_atuacao = detectArea(vaga.value.title)
  }
  loadingCurr.value = false
})
onUnmounted(() => document.removeEventListener('click', onDocClick))
</script>

<style scoped>
.vaga-detail { padding: 28px 32px; max-width: 1480px; }

.top-bar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 20px;
}
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
.back-btn:hover { color: var(--cream); }

/* ── Share button ──────────────────────────────── */
.share-wrap { position: relative; }
.share-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 10px;
  border: 1px solid var(--line); background: var(--card);
  color: var(--cream); font-size: 12px; font-weight: 600;
  cursor: pointer; transition: all 0.15s;
}
.share-btn:hover { border-color: var(--line-strong); background: var(--card-2); }

.share-dropdown {
  position: absolute; right: 0; top: calc(100% + 8px);
  width: 200px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 8px;
  box-shadow: 0 16px 40px -8px rgba(0,0,0,0.5);
  z-index: 200;
}
.share-dropdown-title {
  font-family: var(--mono); font-size: 9px; text-transform: uppercase;
  letter-spacing: 0.12em; color: var(--muted-2);
  padding: 4px 8px 8px;
}
.share-item {
  display: flex; align-items: center; gap: 10px;
  width: 100%; padding: 9px 10px;
  border-radius: 8px; font-size: 13px; font-weight: 500;
  color: var(--cream); cursor: pointer;
  transition: background 0.12s; text-decoration: none;
}
.share-item:hover { background: rgba(245,240,232,0.06); }
.share-divider {
  height: 1px; background: var(--line);
  margin: 4px 0;
}
.share-copy { color: var(--muted); }
.share-copy.copied { color: var(--green); }

.vaga-hero { display: grid; grid-template-columns: 80px 1fr auto; gap: 20px; align-items: start; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-2xl); padding: 28px 32px; margin-bottom: 20px; }
.vh-logo { width: 80px; height: 80px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 800; font-size: 28px; color: var(--black); flex-shrink: 0; }
.vaga-flags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
.flag { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; padding: 3px 8px; border-radius: 999px; font-weight: 500; }
.urgent-flag { background: rgba(255,94,26,0.15); color: var(--orange); border: 1px solid rgba(255,94,26,0.3); }
.new-flag { background: rgba(43,217,107,0.15); color: var(--green); border: 1px solid rgba(43,217,107,0.3); }
.type-flag { background: var(--card-2); color: var(--muted); border: 1px solid var(--line); }
.vh-title { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 4px; }
.vh-company { font-size: 14px; color: var(--cream); margin-bottom: 8px; }
.vh-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--muted); }
.vh-right { text-align: right; }
.vh-pay { font-family: var(--display); font-size: 28px; font-weight: 800; color: var(--green); letter-spacing: -0.03em; }
.vh-per { font-size: 12px; color: var(--muted); }

.detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
.tabs { display: flex; gap: 0; border-bottom: 1px solid var(--line); margin-bottom: 24px; }
.tab { padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; transition: color 0.15s; position: relative; }
.tab:hover { color: var(--cream); }
.tab.active { color: var(--orange); }
.tab.active::after { content: ""; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: var(--orange); }
.content-title { font-family: var(--display); font-size: 18px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.02em; }
.body-text { font-size: 14px; color: var(--muted); line-height: 1.65; }

.req-list { display: flex; flex-direction: column; gap: 8px; }
.req-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--card-2); border-radius: var(--radius-md); border: 1px solid var(--line); }
.req-check { width: 22px; height: 22px; background: rgba(43,217,107,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; color: var(--green); flex-shrink: 0; }
.req-text { flex: 1; font-size: 13px; }
.req-level { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; }
.req-level.obrigatório { color: var(--orange); }
.req-level.desejável { color: var(--yellow); }
.req-level.opcional { color: var(--muted); }

.ben-list { display: flex; flex-direction: column; gap: 8px; }
.ben-row { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.ben-star { font-size: 16px; }

.organizer-card { display: flex; align-items: center; gap: 12px; padding: 16px; background: var(--card-2); border-radius: var(--radius-lg); border: 1px solid var(--line); }
.org-av { width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, var(--orange), var(--yellow)); display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 800; font-size: 18px; color: var(--black); flex-shrink: 0; }
.org-info { flex: 1; }
.org-role { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); }
.org-name { font-size: 14px; font-weight: 700; margin: 2px 0; }
.org-meta { font-size: 11px; color: var(--muted); }
.ghost-btn { padding: 8px 16px; border-radius: 10px; border: 1px solid var(--line); background: transparent; color: var(--cream); font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card); }

.detail-aside { display: flex; flex-direction: column; gap: 12px; position: sticky; top: calc(var(--tb-h) + 20px); }
.aside-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-xl); padding: 18px; }
.contact-card { background: rgba(43,217,107,0.04); border-color: rgba(43,217,107,0.18); }
.aside-title { font-family: var(--display); font-size: 15px; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.02em; }
.aside-sub { font-size: 12px; color: var(--muted); margin-bottom: 14px; }

.contact-actions { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
.btn-whatsapp {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 12px; border-radius: 10px;
  background: #25D366; color: white;
  font-weight: 700; font-size: 13px;
  box-shadow: 0 4px 16px -4px rgba(37,211,102,0.45);
  transition: all 0.15s; text-decoration: none;
}
.btn-whatsapp:hover { background: #1db954; transform: translateY(-1px); }
.btn-email {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 11px; border-radius: 10px;
  border: 1px solid var(--line); background: var(--card-2);
  color: var(--cream); font-weight: 600; font-size: 13px;
  transition: all 0.15s; text-decoration: none;
}
.btn-email:hover { border-color: var(--line-strong); background: var(--card); }
.no-contact-msg { font-size: 12px; color: var(--muted); text-align: center; padding: 8px 0; }

.btn-apply {
  width: 100%; padding: 12px; border-radius: 10px;
  background: var(--orange); color: white;
  font-weight: 700; font-size: 13px;
  box-shadow: var(--shadow-cta-orange); transition: all 0.15s;
}
.btn-apply:hover { background: var(--orange-deep); transform: translateY(-1px); }
.btn-apply.applied { background: var(--green); box-shadow: 0 4px 16px -4px rgba(43,217,107,0.4); cursor: default; }
.btn-apply.applied:hover { transform: none; }
.btn-apply.disabled { background: var(--card-2); color: var(--muted); box-shadow: none; cursor: not-allowed; }
.btn-apply.disabled:hover { transform: none; background: var(--card-2); }
.btn-apply.loading { opacity: 0.75; cursor: wait; }
.btn-apply.loading:hover { transform: none; }
.btn-apply { display: flex; align-items: center; justify-content: center; gap: 7px; }
.btn-login { background: var(--card-2); border: 1px solid var(--line); color: var(--cream); box-shadow: none; }
.btn-login:hover { background: var(--card); border-color: var(--line-strong); transform: none; }
.cand-hint { font-size: 11px; color: var(--muted); text-align: center; margin-top: 6px; }
.cand-error { font-size: 12px; color: #ff6b6b; text-align: center; margin-top: 6px; }
.candidatura-login { display: flex; flex-direction: column; }
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.8s linear infinite; }
.detail-rows { display: flex; flex-direction: column; gap: 10px; }
.drow { display: flex; justify-content: space-between; font-size: 13px; }
.drow-k { color: var(--muted); }
.green-text { color: var(--green); font-weight: 600; }

/* ── Banco de Talentos card ── */
.talent-card {
  background: linear-gradient(135deg, rgba(255,94,26,0.06), rgba(255,210,63,0.04));
  border-color: rgba(255,94,26,0.22);
}
.talent-header {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 12px; color: var(--orange);
}
.talent-body { display: flex; flex-direction: column; gap: 10px; }
.talent-text { font-size: 12px; color: var(--muted); line-height: 1.5; }

.talent-ok-badge {
  font-size: 13px; font-weight: 700; color: var(--green);
  background: rgba(43,217,107,0.10);
  border: 1px solid rgba(43,217,107,0.2);
  border-radius: 8px; padding: 8px 12px;
}

.talent-loading { flex-direction: row; align-items: center; gap: 10px; font-size: 12px; color: var(--muted); }
.loading-dots { display: flex; gap: 4px; }
.loading-dots span {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--orange); opacity: 0.4;
  animation: dot-pulse 1.2s ease-in-out infinite;
}
.loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.loading-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes dot-pulse { 0%,80%,100% { opacity: 0.2 } 40% { opacity: 1 } }

/* Mini form */
.tf-field { display: flex; flex-direction: column; gap: 4px; }
.tf-field label { font-size: 10px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--muted); }
.tf-input {
  background: var(--bg-2); border: 1px solid var(--line); border-radius: 8px;
  padding: 8px 10px; font-size: 13px; color: var(--cream); outline: none;
  font-family: inherit; width: 100%; transition: border-color 0.15s;
}
.tf-input:focus { border-color: var(--orange); }
.tf-radios { display: flex; gap: 12px; flex-wrap: wrap; }
.tf-radio { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--cream); cursor: pointer; }
.tf-radio input { accent-color: var(--orange); }

.btn-talent {
  width: 100%; padding: 11px; border-radius: 10px;
  background: var(--orange); color: white;
  font-weight: 700; font-size: 13px;
  box-shadow: var(--shadow-cta-orange);
  display: flex; align-items: center; justify-content: center; gap: 7px;
  transition: opacity 0.15s;
}
.btn-talent:hover:not(:disabled) { opacity: 0.9; }
.btn-talent:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-talent-ghost {
  font-size: 12px; font-weight: 600; color: var(--orange); cursor: pointer;
  text-align: left; padding: 0;
}
.btn-talent-ghost:hover { text-decoration: underline; }
.curr-error { font-size: 12px; color: #ff6b6b; }

@media (max-width: 768px) {
  .vaga-detail { padding: 16px; }

  .vaga-hero { flex-wrap: wrap; gap: 12px; padding: 16px; }
  .vh-logo { width: 56px; height: 56px; font-size: 20px; border-radius: 14px; }
  .vh-title { font-size: 20px; }
  .vh-right { width: 100%; display: flex; align-items: center; gap: 8px; }
  .vh-pay { font-size: 20px; }
  .vh-meta { font-size: 11px; gap: 8px; }

  .detail-grid { grid-template-columns: 1fr; }
  .detail-aside { position: static; }
}

/* ── Rich text content ─────────────────────────── */
.rich-content :deep(p) {
  margin-bottom: 12px;
  line-height: 1.7;
  color: var(--muted);
  font-size: 14px;
}
.rich-content :deep(h2) {
  font-family: var(--display);
  font-size: 20px;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: var(--cream);
  margin: 24px 0 10px;
}
.rich-content :deep(h3) {
  font-family: var(--display);
  font-size: 16px;
  font-weight: 700;
  color: var(--cream);
  margin: 18px 0 8px;
}
.rich-content :deep(ul),
.rich-content :deep(ol) {
  padding-left: 20px;
  margin-bottom: 12px;
  color: var(--muted);
  font-size: 14px;
}
.rich-content :deep(li) { margin-bottom: 5px; line-height: 1.65; }
.rich-content :deep(strong) { font-weight: 700; color: var(--cream); }
.rich-content :deep(em) { font-style: italic; }
.rich-content :deep(u) { text-decoration: underline; text-underline-offset: 3px; }
.rich-content :deep(s) { text-decoration: line-through; opacity: 0.6; }
.rich-content :deep(blockquote) {
  border-left: 3px solid var(--orange);
  padding: 8px 16px;
  margin: 12px 0;
  background: rgba(255,94,26,0.05);
  border-radius: 0 8px 8px 0;
  color: var(--cream);
  font-size: 14px;
  line-height: 1.6;
}
</style>
