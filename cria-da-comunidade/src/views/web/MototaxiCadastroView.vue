<template>
  <div class="cad">
    <header class="cad-top">
      <span class="cad-brand display">{{ data.configuracao.nome_plataforma || 'Comunidade PPG' }}</span>
    </header>

    <main class="cad-card" aria-live="polite">
      <!-- Sucesso: o link do painel é a chave do mototaxista -->
      <section v-if="fase === 'sucesso'" class="cad-center">
        <div class="cad-emoji" aria-hidden="true">🏍️</div>
        <h1 class="cad-title">Pronto, {{ primeiroNome }}! Você está cadastrado.</h1>
        <p class="cad-sub">Vamos conferir o seu cadastro e, depois de aprovado, você aparece na lista de mototáxi da comunidade.</p>
        <div class="cad-sorte">
          <span aria-hidden="true">📲</span>
          <p><strong>Guarde o seu link.</strong> É por ele que você avisa que está rodando. Salve na tela inicial do celular.</p>
        </div>
        <input class="cad-input cad-link-box" :value="linkPainel" readonly @focus="($event.target as HTMLInputElement).select()" />
        <button class="btn-primary cad-big" type="button" @click="abrirPainel">Abrir meu painel</button>
        <button class="cad-sec" type="button" @click="copiar">{{ copiado ? 'Link copiado ✓' : 'Copiar o link' }}</button>
      </section>

      <section v-else-if="fase === 'jacadastrado'" class="cad-center">
        <div class="cad-emoji" aria-hidden="true">✅</div>
        <h1 class="cad-title">{{ primeiroNome }}, esse telefone já está cadastrado.</h1>
        <p class="cad-sub">Para a sua segurança, o link do painel é entregue só uma vez. Se você perdeu, peça de novo a quem te mandou o convite.</p>
        <button class="btn-primary cad-big" type="button" @click="verLista">Ver mototáxis da comunidade</button>
      </section>

      <form v-else novalidate @submit.prevent="avancar">
        <div class="cad-head">
          <button v-if="passo > 1" class="cad-back" type="button" @click="passo = 1">← Voltar</button>
          <span v-else></span>
          <span class="cad-step">Passo {{ passo }} de 2</span>
        </div>
        <div class="cad-progress" role="progressbar" :aria-valuenow="passo" aria-valuemin="1" aria-valuemax="2">
          <div class="cad-progress-fill" :style="{ width: (passo / 2) * 100 + '%' }"></div>
        </div>

        <div class="cad-hp" aria-hidden="true"><label>Apelido<input v-model="f.apelido" type="text" name="apelido" tabindex="-1" autocomplete="off" /></label></div>

        <section v-if="passo === 1">
          <h1 class="cad-h">Cadastre o seu mototáxi 🏍️</h1>
          <p class="cad-sub">Seja encontrado por quem mora na comunidade. Leva menos de 1 minuto.</p>

          <div class="cad-field">
            <label for="m-nome">Seu nome *</label>
            <input id="m-nome" v-model="f.nome" class="cad-input" :class="{ err: erros.nome }" placeholder="Como te chamam?" autocomplete="name" maxlength="100" />
            <span v-if="erros.nome" class="cad-err">{{ erros.nome }}</span>
          </div>

          <div class="cad-field">
            <label for="m-tel">Seu WhatsApp *</label>
            <input id="m-tel" v-model="f.whatsapp" class="cad-input" :class="{ err: erros.whatsapp }" type="tel" inputmode="tel" placeholder="(21) 99999-8888" autocomplete="tel" maxlength="20" />
            <span v-if="erros.whatsapp" class="cad-err">{{ erros.whatsapp }}</span>
          </div>

          <button class="btn-primary cad-big" type="submit">Continuar →</button>
        </section>

        <section v-else>
          <h1 class="cad-h">Em que horário você roda?</h1>
          <p class="cad-sub">Opcional. Ajuda o pessoal a saber quando te chamar.</p>

          <div class="cad-chips cad-field">
            <button v-for="h in sugestoes" :key="h" type="button" class="cad-chip" :class="{ on: f.horario === h }" @click="f.horario = f.horario === h ? '' : h">{{ h }}</button>
          </div>
          <div class="cad-field">
            <label for="m-hor">Ou escreva <small>(opcional)</small></label>
            <input id="m-hor" v-model="f.horario" class="cad-input" placeholder="Ex.: seg a sáb, 6h às 14h" maxlength="80" />
          </div>

          <div v-if="erroGeral" class="cad-err-box"><span class="cad-err">{{ erroGeral }}</span></div>

          <button class="btn-primary cad-big" type="submit" :disabled="enviando">{{ enviando ? 'Enviando…' : 'Finalizar cadastro' }}</button>
        </section>
      </form>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { api } from '../../services/api'
import { useDataStore } from '../../stores/data'
import { useUiStore } from '../../stores/ui'

const data = useDataStore()
const ui = useUiStore()

const sugestoes = ['Manhã', 'Tarde', 'Noite', 'O dia todo']
const passo = ref(1)
const fase = ref<'form' | 'sucesso' | 'jacadastrado'>('form')
const enviando = ref(false)
const copiado = ref(false)
const erroGeral = ref('')
const token = ref('')
const erros = reactive({ nome: '', whatsapp: '' })
const f = reactive({ nome: '', whatsapp: '', horario: '', apelido: '' })

const primeiroNome = computed(() => f.nome.trim().split(/\s+/)[0] || 'tudo certo')
const linkPainel = computed(() => `${window.location.origin}/mototaxi/painel/${token.value}`)

function avancar() {
  if (passo.value === 1) {
    erros.nome = f.nome.trim().length < 2 ? 'Diga o seu nome.' : ''
    const d = f.whatsapp.replace(/\D/g, '')
    erros.whatsapp = d.length >= 10 && d.length <= 13 ? '' : 'Confira o WhatsApp: use o DDD e o número.'
    if (!erros.nome && !erros.whatsapp) passo.value = 2
    return
  }
  enviar()
}

async function enviar() {
  enviando.value = true
  erroGeral.value = ''
  try {
    const r = await api.post<{ data: { token?: string; ja_cadastrado?: boolean } }>('/mototaxi/cadastro', {
      nome: f.nome,
      whatsapp: f.whatsapp,
      horario: f.horario || null,
      apelido: f.apelido,
      comunidade_id: data.activeComunidadeId || undefined,
    })
    if (r.data.ja_cadastrado) {
      fase.value = 'jacadastrado'
      return
    }
    token.value = r.data.token ?? ''
    try { localStorage.setItem('mototaxi_token', token.value) } catch { /* sem armazenamento: o link continua na tela */ }
    fase.value = 'sucesso'
  } catch (e) {
    erroGeral.value = e instanceof Error && e.message ? e.message : 'Não foi possível enviar. Tente de novo.'
    if (/whatsapp|telefone/i.test(erroGeral.value)) passo.value = 1
  } finally {
    enviando.value = false
  }
}

async function copiar() {
  try {
    await navigator.clipboard.writeText(linkPainel.value)
    copiado.value = true
  } catch { /* o campo acima já vem selecionável */ }
}

function abrirPainel() {
  window.location.href = linkPainel.value
}

function verLista() {
  ui.goTo('mototaxis')
}
</script>

<style scoped>
.cad { min-height: 100vh; background: var(--bg); color: var(--cream); font-family: var(--body); display: flex; flex-direction: column; align-items: center; padding: 0 16px calc(32px + env(safe-area-inset-bottom, 0px)); }
.cad-top { width: 100%; max-width: 560px; display: flex; justify-content: space-between; align-items: center; padding: 18px 2px 10px; }
.cad-brand { font-size: 18px; font-weight: 800; color: var(--orange); }
.cad-card { width: 100%; max-width: 560px; background: var(--card); border: 1px solid var(--line); border-radius: 22px; padding: 22px 20px 24px; box-sizing: border-box; }
.cad-center { display: flex; flex-direction: column; gap: 16px; align-items: stretch; text-align: center; }
.cad-emoji { font-size: 54px; line-height: 1; }
.cad-title { font-family: var(--display); font-size: 24px; font-weight: 800; margin: 0; line-height: 1.15; }
.cad-sorte { display: flex; gap: 12px; align-items: flex-start; text-align: left; background: color-mix(in srgb, var(--green) 12%, var(--bg)); border: 1px solid color-mix(in srgb, var(--green) 40%, transparent); border-radius: 16px; padding: 14px 16px; }
.cad-sorte span { font-size: 26px; line-height: 1; }
.cad-sorte p { margin: 0; font-size: 15px; line-height: 1.5; }
.cad-sub { font-size: 14px; color: var(--muted); margin: 6px 0 0; line-height: 1.5; }
.cad-center .cad-sub { margin: 0; }
.cad-head { display: flex; justify-content: space-between; align-items: center; min-height: 32px; }
.cad-back { background: none; border: none; color: var(--muted); font-size: 14px; cursor: pointer; padding: 8px 0; min-height: 44px; }
.cad-step { font-family: var(--mono); font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
.cad-progress { height: 5px; background: var(--bg); border-radius: 3px; overflow: hidden; margin: 2px 0 18px; }
.cad-progress-fill { height: 100%; background: var(--orange); transition: width .25s ease; }
.cad-h { font-family: var(--display); font-size: 22px; font-weight: 800; margin: 0; }
.cad-field { display: flex; flex-direction: column; gap: 7px; margin-top: 18px; }
.cad-field label { font-size: 14px; font-weight: 600; }
.cad-field label small { font-weight: 400; color: var(--muted); font-size: 12px; }
.cad-input { background: var(--bg); border: 1px solid var(--line-strong); border-radius: 12px; color: var(--cream); font: inherit; font-size: 16px; padding: 13px 14px; min-height: 50px; width: 100%; box-sizing: border-box; }
.cad-input:focus { outline: 2px solid var(--orange); outline-offset: 1px; }
.cad-input.err { border-color: #ff6b6b; }
.cad-link-box { font-size: 13px; text-align: center; }
.cad-err { color: #ff8a8a; font-size: 13px; }
.cad-err-box { background: rgba(255, 107, 107, .1); border: 1px solid rgba(255, 107, 107, .4); border-radius: 12px; padding: 12px; margin-top: 12px; }
.cad-chips { flex-direction: row; flex-wrap: wrap; gap: 8px; }
.cad-chip { min-height: 44px; padding: 8px 16px; background: var(--bg); border: 1px solid var(--line-strong); border-radius: 999px; color: var(--cream); font: inherit; font-size: 14px; cursor: pointer; }
.cad-chip.on { background: var(--orange); border-color: var(--orange); color: #fff; font-weight: 700; }
.btn-primary { padding: 12px 22px; background: var(--orange); color: #fff; border: none; border-radius: var(--radius-full); font-weight: 700; font-size: 15px; cursor: pointer; transition: filter .15s; }
.btn-primary:hover { filter: brightness(1.1); }
.cad-big { width: 100%; min-height: 54px; margin-top: 22px; font-size: 16px; }
.cad-big:disabled { opacity: .6; cursor: wait; }
.cad-center .cad-big { margin-top: 4px; }
.cad-sec { background: none; border: 1px solid var(--line-strong); color: var(--cream); border-radius: var(--radius-full); min-height: 48px; font: inherit; font-size: 15px; cursor: pointer; }
.cad-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
</style>
