<template>
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-logo">
        <div class="mark"></div>
        <div>
          <div class="brand">Cria da Comunidade</div>
          <div class="brand-tag">acesse sua conta</div>
        </div>
      </div>

      <div v-if="mode === 'login'">
        <h2 class="form-title">Entrar</h2>

        <div class="field">
          <label>E-mail</label>
          <input v-model="email" type="email" placeholder="seu@email.com" @keydown.enter="submit" />
        </div>
        <div class="field">
          <label>Senha</label>
          <input v-model="password" type="password" placeholder="••••••••" @keydown.enter="submit" />
        </div>

        <p v-if="auth.error" class="err">{{ auth.error }}</p>

        <button class="btn-primary" :disabled="auth.loading" @click="submit">
          {{ auth.loading ? 'Entrando…' : 'Entrar' }}
        </button>

        <p class="switch">
          Não tem conta?
          <span @click="switchMode('register')">Cadastrar</span>
        </p>
      </div>

      <div v-else>
        <h2 class="form-title">Criar conta</h2>

        <div class="field">
          <label>Nome completo</label>
          <input v-model="name" type="text" placeholder="Seu nome" />
        </div>
        <div class="field">
          <label>E-mail</label>
          <input v-model="email" type="email" placeholder="seu@email.com" />
        </div>
        <div class="field">
          <label>Senha</label>
          <input v-model="password" type="password" placeholder="Mínimo 8 caracteres" />
        </div>
        <div class="field">
          <label>Confirmar senha</label>
          <input v-model="passwordConf" type="password" placeholder="Repita a senha" @keydown.enter="submit" />
        </div>

        <p v-if="auth.error" class="err">{{ auth.error }}</p>

        <button class="btn-primary" :disabled="auth.loading" @click="submit">
          {{ auth.loading ? 'Cadastrando…' : 'Criar conta' }}
        </button>

        <p class="switch">
          Já tem conta?
          <span @click="switchMode('login')">Entrar</span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useUiStore } from '../../stores/ui'

const auth = useAuthStore()
const ui = useUiStore()

const mode = ref<'login' | 'register'>('login')
const name = ref('')
const email = ref('')
const password = ref('')
const passwordConf = ref('')

function switchMode(m: 'login' | 'register') {
  mode.value = m
  auth.error = null
}

async function submit() {
  try {
    if (mode.value === 'login') {
      await auth.login(email.value, password.value)
    } else {
      await auth.register(name.value, email.value, password.value, passwordConf.value)
    }
    ui.goTo('inicio')
  } catch {
    // error already set in store
  }
}
</script>

<style scoped>
.login-wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 16px;
  background: var(--bg);
}

.login-card {
  width: 100%;
  max-width: 400px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 20px;
  padding: 36px 32px;
}

.login-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 32px;
}
.mark {
  width: 36px; height: 36px;
  background: var(--orange);
  border-radius: 10px;
  transform: rotate(-4deg);
  box-shadow: 0 6px 16px -4px rgba(255,94,26,0.4);
  position: relative;
  flex-shrink: 0;
}
.mark::before {
  content: "";
  width: 14px; height: 14px;
  background: var(--yellow);
  border-radius: 3px;
  position: absolute;
  transform: rotate(45deg);
  top: 11px; left: 11px;
}
.brand {
  font-family: var(--display);
  font-weight: 800;
  font-size: 16px;
  letter-spacing: -0.02em;
}
.brand-tag {
  font-family: var(--mono);
  font-size: 10px;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.form-title {
  font-family: var(--display);
  font-size: 22px;
  font-weight: 800;
  letter-spacing: -0.02em;
  margin-bottom: 24px;
}

.field {
  margin-bottom: 16px;
}
.field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 6px;
}
.field input {
  width: 100%;
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 14px;
  color: var(--cream);
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.15s;
}
.field input:focus {
  border-color: var(--orange);
}
.field input::placeholder {
  color: var(--muted-2);
}

.err {
  font-size: 13px;
  color: #ff6b6b;
  margin-bottom: 12px;
  padding: 10px 12px;
  background: rgba(255,107,107,0.08);
  border-radius: 8px;
  border: 1px solid rgba(255,107,107,0.2);
}

.btn-primary {
  width: 100%;
  background: var(--orange);
  color: var(--black);
  font-family: var(--display);
  font-weight: 700;
  font-size: 14px;
  padding: 12px;
  border-radius: 12px;
  cursor: pointer;
  transition: opacity 0.15s, transform 0.1s;
  margin-top: 4px;
}
.btn-primary:hover:not(:disabled) {
  opacity: 0.9;
  transform: translateY(-1px);
}
.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.switch {
  font-size: 13px;
  color: var(--muted);
  text-align: center;
  margin-top: 20px;
}
.switch span {
  color: var(--orange);
  font-weight: 600;
  cursor: pointer;
}
.switch span:hover {
  color: var(--yellow);
}
</style>
