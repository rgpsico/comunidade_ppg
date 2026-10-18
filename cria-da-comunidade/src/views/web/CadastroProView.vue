<template>
  <div class="cad">
    <header class="cad-top">
      <span class="cad-brand display">{{ data.configuracao.nome_plataforma || 'Comunidade PPG' }}</span>
      <button v-if="fase === 'form'" class="cad-sair" type="button" @click="sair">Sair</button>
    </header>

    <main class="cad-card" aria-live="polite">
      <!-- ───────── Boas-vindas ───────── -->
      <section v-if="fase === 'boasvindas'" class="cad-center">
        <div class="cad-emoji" aria-hidden="true">🎉</div>
        <h1 class="cad-title">Seja muito bem-vindo(a)!</h1>
        <div class="cad-sorte">
          <span aria-hidden="true">🍀</span>
          <p><strong>Você teve sorte!</strong> O dono do sistema viu que você é um <strong>ótimo profissional</strong>
            e quis te chamar para fazer parte da {{ data.configuracao.nome_plataforma || 'Comunidade PPG' }}.</p>
        </div>
        <p class="cad-sub">Responda algumas perguntinhas rápidas, leva uns <strong>2 minutinhos</strong>. Depois a gente chama você no WhatsApp.</p>
        <button class="btn-primary cad-big" type="button" @click="fase = 'form'">Começar meu cadastro →</button>
        <p class="cad-mini">Seus dados só são usados pela nossa equipe para falar com você.</p>
      </section>

      <!-- ───────── Sucesso ───────── -->
      <section v-else-if="fase === 'sucesso'" class="cad-center">
        <div class="cad-emoji" aria-hidden="true">✅</div>
        <h1 class="cad-title">Pronto, {{ primeiroNome }}! Cadastro enviado.</h1>
        <div class="cad-sorte">
          <span aria-hidden="true">🍀</span>
          <p>Lembra? Você teve sorte: o dono do sistema reconheceu o seu trabalho. <strong>Em breve a nossa equipe te chama no WhatsApp {{ f.whatsapp }}</strong>
            para os próximos passos.</p>
        </div>
        <p class="cad-sub">Salve o nosso número quando ele chegar, para não perder a mensagem. 😉</p>
        <button class="btn-primary cad-big" type="button" @click="conhecer">Conhecer a comunidade</button>
        <a class="cad-link" :href="compartilharUrl" target="_blank" rel="noopener">Indicar outro profissional pelo WhatsApp</a>
      </section>

      <!-- ───────── Formulário (5 passos) ───────── -->
      <form v-else novalidate @submit.prevent="avancar">
        <div class="cad-head">
          <button v-if="passo > 1" class="cad-back" type="button" @click="voltar">← Voltar</button>
          <span v-else></span>
          <span class="cad-step">Passo {{ passo }} de {{ TOTAL }}</span>
        </div>
        <div class="cad-progress" role="progressbar" :aria-valuenow="passo" aria-valuemin="1" :aria-valuemax="TOTAL">
          <div class="cad-progress-fill" :style="{ width: (passo / TOTAL) * 100 + '%' }"></div>
        </div>

        <!-- Isca anti-robô: invisível para pessoas -->
        <div class="cad-hp" aria-hidden="true"><label>Apelido<input v-model="f.apelido" type="text" name="apelido" tabindex="-1" autocomplete="off" /></label></div>

        <!-- 1. Quem é você -->
        <section v-if="passo === 1">
          <h2 class="cad-h">Vamos nos conhecer</h2>
          <p class="cad-sub">Só o básico para a gente poder falar com você.</p>

          <div class="cad-field">
            <label for="c-nome">Seu nome *</label>
            <input id="c-nome" v-model="f.nome" class="cad-input" :class="{ err: erros.nome }" placeholder="Como você é conhecido(a)?" autocomplete="name" maxlength="100" />
            <span v-if="erros.nome" class="cad-err">{{ erros.nome }}</span>
          </div>
          <div class="cad-field">
            <label for="c-zap">Seu WhatsApp *</label>
            <input id="c-zap" :value="f.whatsapp" class="cad-input" :class="{ err: erros.whatsapp }" inputmode="tel" placeholder="(21) 99999-8888" autocomplete="tel" maxlength="16" @input="onZap" />
            <span v-if="erros.whatsapp" class="cad-err">{{ erros.whatsapp }}</span>
          </div>
          <div class="cad-field">
            <label for="c-mail">E-mail <small>(opcional)</small></label>
            <input id="c-mail" v-model="f.email" class="cad-input" :class="{ err: erros.email }" type="email" inputmode="email" placeholder="seunome@email.com" autocomplete="email" maxlength="150" />
            <span v-if="erros.email" class="cad-err">{{ erros.email }}</span>
          </div>
        </section>

        <!-- 2. O que você faz -->
        <section v-else-if="passo === 2">
          <h2 class="cad-h">O que você faz?</h2>
          <p class="cad-sub">Escolha a área e depois a sua profissão.</p>

          <div class="cad-areas" role="group" aria-label="Área de atuação">
            <button v-for="a in AREAS" :key="a.nome" type="button" class="cad-area" :class="{ on: f.categoria === a.nome }" @click="escolherArea(a)">
              <span class="cad-area-emoji" aria-hidden="true">{{ a.emoji }}</span>{{ a.nome }}
            </button>
          </div>

          <template v-if="areaAtual">
            <h3 class="cad-h3">Qual é a sua profissão em {{ areaAtual.nome }}?</h3>
            <div class="cad-chips">
              <button v-for="p in areaAtual.profissoes" :key="p.label" type="button" class="cad-chip" :class="{ on: !outra && f.profissao === p.label }" @click="escolherProfissao(p)">{{ p.label }}</button>
              <button type="button" class="cad-chip" :class="{ on: outra }" @click="escolherOutra">✏️ Outra</button>
            </div>
            <div v-if="outra" class="cad-field">
              <label for="c-outra">Qual é a sua profissão? *</label>
              <input id="c-outra" v-model="f.profissao" class="cad-input" :class="{ err: erros.profissao }" placeholder="Ex.: Soldador" maxlength="80" />
            </div>
          </template>
          <span v-if="erros.categoria || erros.profissao" class="cad-err">{{ erros.categoria || erros.profissao }}</span>
        </section>

        <!-- 3. Serviço -->
        <section v-else-if="passo === 3">
          <h2 class="cad-h">Conte sobre o seu serviço</h2>
          <p class="cad-sub">Pode pular a descrição se preferir, ela é opcional.</p>

          <div class="cad-field">
            <label for="c-desc">Descrição do serviço <small>(opcional)</small></label>
            <textarea id="c-desc" v-model="f.descricao" class="cad-input" rows="4" maxlength="1000" placeholder="Ex.: Instalação e conserto elétrico residencial. Atendo a região toda."></textarea>
            <small class="cad-count">{{ f.descricao.length }}/1000</small>
          </div>

          <div v-if="perguntaDomicilio" class="cad-field">
            <label id="l-dom">Você atende em domicílio (na casa do cliente)? *</label>
            <div class="cad-yn" role="group" aria-labelledby="l-dom">
              <button type="button" class="cad-yn-btn" :class="{ on: f.atende_domicilio === true }" @click="f.atende_domicilio = true">🏠 Sim</button>
              <button type="button" class="cad-yn-btn" :class="{ on: f.atende_domicilio === false }" @click="f.atende_domicilio = false">Não</button>
            </div>
            <span v-if="erros.atende_domicilio" class="cad-err">{{ erros.atende_domicilio }}</span>
          </div>
        </section>

        <!-- 4. Digital -->
        <section v-else-if="passo === 4">
          <h2 class="cad-h">E no mundo digital?</h2>
          <p class="cad-sub">Sem certo ou errado: serve para a gente saber como ajudar você.</p>

          <div class="cad-field">
            <label id="l-site">Você tem site? *</label>
            <div class="cad-yn" role="group" aria-labelledby="l-site">
              <button type="button" class="cad-yn-btn" :class="{ on: f.tem_site === true }" @click="f.tem_site = true">Sim</button>
              <button type="button" class="cad-yn-btn" :class="{ on: f.tem_site === false }" @click="f.tem_site = false">Não</button>
            </div>
            <span v-if="erros.tem_site" class="cad-err">{{ erros.tem_site }}</span>
          </div>

          <div class="cad-field">
            <label id="l-google">Você tem o Google Meu Negócio? *</label>
            <small class="cad-hint">É a ficha da sua empresa que aparece no Google e no Maps quando alguém procura seu serviço.</small>
            <div class="cad-yn" role="group" aria-labelledby="l-google">
              <button type="button" class="cad-yn-btn" :class="{ on: f.tem_google_meu_negocio === true }" @click="f.tem_google_meu_negocio = true">Sim</button>
              <button type="button" class="cad-yn-btn" :class="{ on: f.tem_google_meu_negocio === false }" @click="f.tem_google_meu_negocio = false">Não</button>
            </div>
            <span v-if="erros.tem_google_meu_negocio" class="cad-err">{{ erros.tem_google_meu_negocio }}</span>
          </div>

          <div class="cad-field">
            <label id="l-nivel">Como você se considera no digital? *</label>
            <div class="cad-opts" role="group" aria-labelledby="l-nivel">
              <button type="button" class="cad-opt" :class="{ on: f.nivel_digital === 'basico' }" @click="f.nivel_digital = 'basico'">
                <strong>🙂 Só conheço o básico</strong><small>Uso WhatsApp e redes sociais, mas ainda não mexo com muito mais que isso.</small>
              </button>
              <button type="button" class="cad-opt" :class="{ on: f.nivel_digital === 'avancado' }" @click="f.nivel_digital = 'avancado'">
                <strong>🚀 Sou avançado no digital</strong><small>Me viro bem: divulgo online, uso anúncios, site ou loja virtual.</small>
              </button>
            </div>
            <span v-if="erros.nivel_digital" class="cad-err">{{ erros.nivel_digital }}</span>
          </div>
        </section>

        <!-- 5. Revisão -->
        <section v-else>
          <h2 class="cad-h">Confira os seus dados</h2>
          <p class="cad-sub">Está tudo certo? Se precisar corrigir, toque em “Editar”.</p>

          <dl class="cad-resumo">
            <div><dt>Nome</dt><dd>{{ f.nome }}</dd><button type="button" @click="irPara(1)">Editar</button></div>
            <div><dt>WhatsApp</dt><dd>{{ f.whatsapp }}</dd><button type="button" @click="irPara(1)">Editar</button></div>
            <div v-if="f.email"><dt>E-mail</dt><dd>{{ f.email }}</dd><button type="button" @click="irPara(1)">Editar</button></div>
            <div><dt>Profissão</dt><dd>{{ f.profissao }} <small>({{ f.categoria }})</small></dd><button type="button" @click="irPara(2)">Editar</button></div>
            <div v-if="f.descricao"><dt>Serviço</dt><dd>{{ f.descricao }}</dd><button type="button" @click="irPara(3)">Editar</button></div>
            <div v-if="perguntaDomicilio"><dt>Atende em domicílio</dt><dd>{{ f.atende_domicilio ? 'Sim' : 'Não' }}</dd><button type="button" @click="irPara(3)">Editar</button></div>
            <div><dt>Tem site</dt><dd>{{ f.tem_site ? 'Sim' : 'Não' }}</dd><button type="button" @click="irPara(4)">Editar</button></div>
            <div><dt>Google Meu Negócio</dt><dd>{{ f.tem_google_meu_negocio ? 'Sim' : 'Não' }}</dd><button type="button" @click="irPara(4)">Editar</button></div>
            <div><dt>Nível digital</dt><dd>{{ f.nivel_digital === 'avancado' ? 'Avançado' : 'Só o básico' }}</dd><button type="button" @click="irPara(4)">Editar</button></div>
          </dl>
          <p class="cad-mini">Ao enviar, você concorda que a equipe da {{ data.configuracao.nome_plataforma || 'Comunidade PPG' }} entre em contato pelo WhatsApp.</p>
          <p v-if="erroEnvio" class="cad-err cad-err-box" role="alert">{{ erroEnvio }}</p>
        </section>

        <button class="btn-primary cad-big" type="submit" :disabled="enviando">
          {{ enviando ? 'Enviando…' : passo === TOTAL ? 'Enviar meu cadastro ✓' : 'Continuar →' }}
        </button>
      </form>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import { api } from '../../services/api'

interface Profissao { label: string; domicilio?: boolean }
interface Area { nome: string; emoji: string; profissoes: Profissao[]; domicilioSempre?: boolean }

// "nome" é a categoria da plataforma (a mesma do cadastro de profissionais). "domicilio": a pergunta
// "atende em domicílio?" só aparece onde faz sentido (barbeiro, manicure...); obra e reparo já vão até o cliente.
const AREAS: Area[] = [
  { nome: 'Construção', emoji: '🔨', profissoes: [{ label: 'Eletricista' }, { label: 'Encanador' }, { label: 'Pedreiro' }, { label: 'Pintor' }, { label: 'Gesseiro' }, { label: 'Serralheiro' }, { label: 'Marceneiro' }, { label: 'Azulejista' }] },
  { nome: 'Beleza', emoji: '💇', profissoes: [{ label: 'Barbeiro', domicilio: true }, { label: 'Cabeleireiro(a)', domicilio: true }, { label: 'Manicure e pedicure', domicilio: true }, { label: 'Maquiador(a)', domicilio: true }, { label: 'Designer de sobrancelha', domicilio: true }, { label: 'Depilação', domicilio: true }, { label: 'Tatuador(a)' }] },
  { nome: 'Saúde', emoji: '💪', profissoes: [{ label: 'Massagista', domicilio: true }, { label: 'Personal trainer', domicilio: true }, { label: 'Cuidador(a) de idosos', domicilio: true }, { label: 'Fisioterapeuta', domicilio: true }] },
  { nome: 'Casa', emoji: '🏠', profissoes: [{ label: 'Diarista / faxineira' }, { label: 'Jardineiro' }, { label: 'Dedetizador' }, { label: 'Montador de móveis' }, { label: 'Chaveiro' }] },
  { nome: 'Serviços', emoji: '🧰', profissoes: [{ label: 'Costureira' }, { label: 'Mecânico' }, { label: 'Lavagem de carros', domicilio: true }, { label: 'Reparos em geral' }] },
  { nome: 'Alimentação', emoji: '🍰', profissoes: [{ label: 'Marmitas e refeições' }, { label: 'Confeiteira' }, { label: 'Salgadeira' }, { label: 'Churrasqueiro' }] },
  { nome: 'Eventos', emoji: '🎉', profissoes: [{ label: 'Fotógrafo(a)', domicilio: true }, { label: 'DJ / som' }, { label: 'Decoração de festas' }, { label: 'Buffet e garçom' }] },
  { nome: 'Educação', emoji: '📚', profissoes: [{ label: 'Professor(a) particular', domicilio: true }, { label: 'Reforço escolar', domicilio: true }, { label: 'Aulas de música', domicilio: true }] },
  { nome: 'Pet', emoji: '🐶', profissoes: [{ label: 'Banho e tosa', domicilio: true }, { label: 'Adestrador(a)', domicilio: true }, { label: 'Passeador(a) de cães' }, { label: 'Veterinário(a)', domicilio: true }] },
  { nome: 'Tecnologia', emoji: '💻', profissoes: [{ label: 'Técnico(a) de celular' }, { label: 'Técnico(a) de computador', domicilio: true }, { label: 'Social media / designer' }] },
  { nome: 'Transporte', emoji: '🚚', profissoes: [{ label: 'Frete e mudança' }, { label: 'Motorista particular' }, { label: 'Entregador(a) / mototáxi' }] },
]

const TOTAL = 5
const ui = useUiStore()
const data = useDataStore()

const fase = ref<'boasvindas' | 'form' | 'sucesso'>('boasvindas')
const passo = ref(1)
const enviando = ref(false)
const erroEnvio = ref('')
const outra = ref(false)
const erros = reactive<Record<string, string>>({})

const f = reactive({
  nome: '', whatsapp: '', email: '',
  categoria: '', profissao: '', descricao: '',
  atende_domicilio: null as boolean | null,
  tem_site: null as boolean | null,
  tem_google_meu_negocio: null as boolean | null,
  nivel_digital: '' as '' | 'basico' | 'avancado',
  apelido: '', // isca anti-robô
})

const areaAtual = computed(() => AREAS.find(a => a.nome === f.categoria) ?? null)
const primeiroNome = computed(() => f.nome.trim().split(/\s+/)[0] || 'profissional')

// A pergunta de domicílio aparece para as profissões marcadas e para "Outra" (a pessoa responde).
const perguntaDomicilio = computed(() => {
  if (!areaAtual.value) return false
  if (outra.value) return true
  return !!areaAtual.value.profissoes.find(p => p.label === f.profissao)?.domicilio
})

const compartilharUrl = computed(() => {
  const link = `${window.location.origin}${ui.slugPath('/cadastro')}`
  const texto = `Oi! Conheci a ${data.configuracao.nome_plataforma || 'Comunidade PPG'} e lembrei de você. Faz o cadastro rápido por aqui (2 minutinhos): ${link}`
  return `https://wa.me/?text=${encodeURIComponent(texto)}`
})

function limparErros() { Object.keys(erros).forEach(k => delete erros[k]) }

function onZap(e: Event) {
  const d = (e.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 11)
  f.whatsapp = d.length <= 2 ? d : d.length <= 6 ? `(${d.slice(0, 2)}) ${d.slice(2)}` : d.length <= 10 ? `(${d.slice(0, 2)}) ${d.slice(2, 6)}-${d.slice(6)}` : `(${d.slice(0, 2)}) ${d.slice(2, 7)}-${d.slice(7)}`
  ;(e.target as HTMLInputElement).value = f.whatsapp
}

function escolherArea(a: Area) {
  if (f.categoria !== a.nome) { f.categoria = a.nome; f.profissao = ''; outra.value = false; f.atende_domicilio = null }
}
function escolherProfissao(p: Profissao) {
  outra.value = false
  f.profissao = p.label
  if (!p.domicilio) f.atende_domicilio = null
}
function escolherOutra() { outra.value = true; f.profissao = ''; f.atende_domicilio = null }

function validar(n: number): boolean {
  limparErros()
  if (n === 1) {
    if (f.nome.trim().length < 2) erros.nome = 'Diga o seu nome.'
    const d = f.whatsapp.replace(/\D/g, '')
    if (!/^[1-9][1-9]\d{8,9}$/.test(d)) erros.whatsapp = 'Confira o WhatsApp: use o DDD e o número (ex.: 21 99999-8888).'
    if (f.email.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email.trim())) erros.email = 'Esse e-mail parece incompleto. Pode deixar em branco se preferir.'
  } else if (n === 2) {
    if (!f.categoria) erros.categoria = 'Escolha a sua área de atuação.'
    else if (f.profissao.trim().length < 2) erros.profissao = outra.value ? 'Escreva qual é a sua profissão.' : 'Escolha a sua profissão (ou toque em “Outra”).'
  } else if (n === 3) {
    if (perguntaDomicilio.value && f.atende_domicilio === null) erros.atende_domicilio = 'Responda sim ou não.'
  } else if (n === 4) {
    if (f.tem_site === null) erros.tem_site = 'Responda sim ou não.'
    if (f.tem_google_meu_negocio === null) erros.tem_google_meu_negocio = 'Responda sim ou não.'
    if (!f.nivel_digital) erros.nivel_digital = 'Escolha uma das opções.'
  }
  return Object.keys(erros).length === 0
}

function rolarTopo() { window.scrollTo({ top: 0, behavior: 'smooth' }) }
function voltar() { limparErros(); passo.value--; rolarTopo() }
function irPara(n: number) { limparErros(); passo.value = n; rolarTopo() }

async function avancar() {
  if (!validar(passo.value)) return
  if (passo.value < TOTAL) { passo.value++; rolarTopo(); return }
  await enviar()
}

async function enviar() {
  erroEnvio.value = ''
  enviando.value = true
  try {
    const ref = new URLSearchParams(window.location.search).get('ref')
    await api.post('/cadastro-profissional', {
      nome: f.nome.trim(),
      whatsapp: f.whatsapp,
      email: f.email.trim() || null,
      categoria: f.categoria,
      profissao: f.profissao.trim(),
      descricao: f.descricao.trim() || null,
      atende_domicilio: perguntaDomicilio.value ? f.atende_domicilio : null,
      tem_site: f.tem_site,
      tem_google_meu_negocio: f.tem_google_meu_negocio,
      nivel_digital: f.nivel_digital,
      comunidade_id: data.activeComunidadeId ?? null,
      origem: ref,
      apelido: f.apelido,
    })
    fase.value = 'sucesso'
    rolarTopo()
  } catch (e) {
    erroEnvio.value = e instanceof Error && e.message && !e.message.startsWith('HTTP')
      ? e.message
      : 'Não conseguimos enviar agora. Confira a internet e tente de novo em instantes.'
  } finally {
    enviando.value = false
  }
}

function sair() { ui.goTo('inicio') }
function conhecer() { ui.goTo('inicio') }
</script>

<style scoped>
.cad { min-height: 100vh; background: var(--bg); color: var(--cream); font-family: var(--body); display: flex; flex-direction: column; align-items: center; padding: 0 16px calc(32px + env(safe-area-inset-bottom, 0px)); }
.cad-top { width: 100%; max-width: 560px; display: flex; justify-content: space-between; align-items: center; padding: 18px 2px 10px; }
.cad-brand { font-size: 18px; font-weight: 800; color: var(--orange); }
.cad-sair { background: none; border: none; color: var(--muted); font-size: 13px; cursor: pointer; padding: 8px; min-height: 44px; }
.cad-card { width: 100%; max-width: 560px; background: var(--card); border: 1px solid var(--line); border-radius: 22px; padding: 22px 20px 24px; }
.cad-center { display: flex; flex-direction: column; gap: 16px; align-items: stretch; text-align: center; }
.cad-emoji { font-size: 54px; line-height: 1; }
.cad-title { font-family: var(--display); font-size: 26px; font-weight: 800; margin: 0; line-height: 1.15; }
.cad-sorte { display: flex; gap: 12px; align-items: flex-start; text-align: left; background: color-mix(in srgb, var(--green) 12%, var(--bg)); border: 1px solid color-mix(in srgb, var(--green) 40%, transparent); border-radius: 16px; padding: 14px 16px; }
.cad-sorte span { font-size: 26px; line-height: 1; }
.cad-sorte p { margin: 0; font-size: 15px; line-height: 1.5; }
.cad-sub { font-size: 14px; color: var(--muted); margin: 6px 0 0; line-height: 1.5; }
.cad-center .cad-sub { margin: 0; }
.cad-mini { font-size: 12px; color: var(--muted-2); margin: 4px 0 0; }
.cad-link { color: var(--orange); font-size: 14px; text-decoration: underline; padding: 10px; }

.cad-head { display: flex; justify-content: space-between; align-items: center; min-height: 32px; }
.cad-back { background: none; border: none; color: var(--muted); font-size: 14px; cursor: pointer; padding: 8px 0; min-height: 44px; }
.cad-step { font-family: var(--mono); font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
.cad-progress { height: 5px; background: var(--bg); border-radius: 3px; overflow: hidden; margin: 2px 0 18px; }
.cad-progress-fill { height: 100%; background: var(--orange); transition: width .25s ease; }
.cad-h { font-family: var(--display); font-size: 22px; font-weight: 800; margin: 0; }
.cad-h3 { font-size: 15px; font-weight: 700; margin: 22px 0 10px; }

.cad-field { display: flex; flex-direction: column; gap: 7px; margin-top: 18px; }
.cad-field label { font-size: 14px; font-weight: 600; }
.cad-field label small, .cad-hint { font-weight: 400; color: var(--muted); font-size: 12px; }
.cad-input { background: var(--bg); border: 1px solid var(--line-strong); border-radius: 12px; color: var(--cream); font: inherit; font-size: 16px; padding: 13px 14px; min-height: 50px; width: 100%; box-sizing: border-box; }
.cad-input:focus { outline: 2px solid var(--orange); outline-offset: 1px; }
.cad-input.err { border-color: #ff6b6b; }
textarea.cad-input { resize: vertical; min-height: 110px; }
.cad-count { align-self: flex-end; color: var(--muted-2); font-size: 11px; }
.cad-err { color: #ff8a8a; font-size: 13px; }
.cad-err-box { background: rgba(255, 107, 107, .1); border: 1px solid rgba(255, 107, 107, .4); border-radius: 12px; padding: 12px; margin-top: 12px; }

.cad-areas { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-top: 16px; }
.cad-area { display: flex; align-items: center; gap: 8px; min-height: 52px; padding: 10px 12px; min-width: 0; overflow: hidden; box-sizing: border-box; background: var(--bg); border: 1px solid var(--line); border-radius: 14px; color: var(--cream); font: inherit; font-size: 14px; font-weight: 600; text-align: left; cursor: pointer; }
.cad-area-emoji { font-size: 22px; }
.cad-area.on, .cad-area:hover { border-color: var(--orange); }
.cad-area.on { background: color-mix(in srgb, var(--orange) 14%, var(--bg)); }
.cad-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.cad-chip { min-height: 44px; padding: 8px 16px; background: var(--bg); border: 1px solid var(--line-strong); border-radius: 999px; color: var(--cream); font: inherit; font-size: 14px; cursor: pointer; }
.cad-chip.on { background: var(--orange); border-color: var(--orange); color: #fff; font-weight: 700; }

.cad-yn { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.cad-yn-btn { min-height: 52px; background: var(--bg); border: 1px solid var(--line-strong); border-radius: 14px; color: var(--cream); font: inherit; font-size: 16px; font-weight: 600; cursor: pointer; }
.cad-yn-btn.on { background: var(--orange); border-color: var(--orange); color: #fff; }
.cad-opts { display: flex; flex-direction: column; gap: 10px; }
.cad-opt { display: flex; flex-direction: column; gap: 4px; text-align: left; padding: 14px 16px; background: var(--bg); border: 1px solid var(--line-strong); border-radius: 14px; color: var(--cream); font: inherit; cursor: pointer; }
.cad-opt strong { font-size: 15px; } .cad-opt small { color: var(--muted); font-size: 13px; line-height: 1.4; }
.cad-opt.on { border-color: var(--orange); background: color-mix(in srgb, var(--orange) 14%, var(--bg)); }

.cad-resumo { margin: 16px 0 0; display: flex; flex-direction: column; gap: 2px; }
.cad-resumo > div { display: grid; grid-template-columns: 110px 1fr auto; gap: 10px; align-items: start; padding: 10px 0; border-bottom: 1px solid var(--line); }
.cad-resumo dt { color: var(--muted); font-size: 13px; } .cad-resumo dd { margin: 0; font-size: 14px; word-break: break-word; }
.cad-resumo button { background: none; border: none; color: var(--orange); font-size: 13px; cursor: pointer; padding: 4px; }

.btn-primary { padding: 12px 22px; background: var(--orange); color: #fff; border: none; border-radius: var(--radius-full); font-weight: 700; font-size: 15px; cursor: pointer; transition: filter .15s; }
.btn-primary:hover { filter: brightness(1.1); }
.cad-big { width: 100%; min-height: 54px; margin-top: 22px; font-size: 16px; }
.cad-big:disabled { opacity: .6; cursor: wait; }
.cad-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
@media (max-width: 380px) { .cad-areas { grid-template-columns: 1fr; } .cad-resumo > div { grid-template-columns: 90px 1fr auto; } }
</style>
