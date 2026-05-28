<template>
  <div class="profile-page fade-up">

    <!-- Header -->
    <div class="page-header">
      <button class="back-btn" @click="ui.goTo('inicio')">← Início</button>
      <h1 class="page-title">Meu perfil</h1>
    </div>

    <div class="profile-grid">
      <!-- Left: avatar + info rápida -->
      <aside class="profile-aside">
        <div class="avatar-block">
          <div class="avatar-ring">
            <div class="avatar">{{ auth.initials }}</div>
          </div>
          <div class="avatar-name">{{ auth.user?.name }}</div>
          <div class="avatar-handle" v-if="auth.user?.handle">@{{ auth.user?.handle }}</div>
          <div class="avatar-role">{{ auth.user?.roles?.[0]?.name ?? 'usuário' }}</div>
        </div>

        <div class="aside-info">
          <div class="info-row">
            <span class="info-key">Email</span>
            <span class="info-val">{{ auth.user?.email }}</span>
          </div>
          <div class="info-row" v-if="auth.user?.whatsapp">
            <span class="info-key">WhatsApp</span>
            <span class="info-val">{{ auth.user?.whatsapp }}</span>
          </div>
        </div>
      </aside>

      <!-- Right: edit form -->
      <div class="profile-main">
        <div class="form-card">
          <div class="card-header">
            <h2 class="card-title">Editar informações</h2>
            <p class="card-sub">Seus dados são exibidos na plataforma</p>
          </div>

          <form @submit.prevent="save" class="edit-form">
            <div class="field-grid">
              <div class="field">
                <label class="field-label">Nome completo</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="field-input"
                  placeholder="Seu nome"
                  maxlength="100"
                />
              </div>

              <div class="field">
                <label class="field-label">Arroba (handle)</label>
                <div class="input-prefix-wrap">
                  <span class="input-prefix">@</span>
                  <input
                    v-model="form.handle"
                    type="text"
                    class="field-input has-prefix"
                    placeholder="seu_handle"
                    maxlength="50"
                    pattern="[a-zA-Z0-9_.]+"
                  />
                </div>
                <span class="field-hint">Só letras, números, _ e .</span>
              </div>

              <div class="field">
                <label class="field-label">WhatsApp</label>
                <input
                  v-model="form.whatsapp"
                  type="tel"
                  class="field-input"
                  placeholder="55 11 99999-9999"
                  maxlength="20"
                />
                <span class="field-hint">Com código do país, ex: 55 21 99999-1234</span>
              </div>

              <div class="field">
                <label class="field-label">Email</label>
                <input
                  :value="auth.user?.email"
                  type="email"
                  class="field-input readonly"
                  readonly
                />
                <span class="field-hint">Email não pode ser alterado</span>
              </div>
            </div>

            <div class="field full-width">
              <label class="field-label">Bio / Apresentação</label>
              <textarea
                v-model="form.bio"
                class="field-input field-textarea"
                rows="4"
                placeholder="Conta quem você é, o que faz, do que gosta…"
                maxlength="500"
              ></textarea>
              <span class="field-hint char-count">{{ form.bio?.length ?? 0 }} / 500</span>
            </div>

            <!-- Feedback -->
            <Transition name="fade-msg">
              <div v-if="successMsg" class="msg-success">
                ✓ {{ successMsg }}
              </div>
            </Transition>
            <Transition name="fade-msg">
              <div v-if="errorMsg" class="msg-error">
                ✕ {{ errorMsg }}
              </div>
            </Transition>

            <div class="form-actions">
              <button type="button" class="btn-ghost" @click="reset">Desfazer</button>
              <button type="submit" class="btn-save" :disabled="auth.loading || !isDirty">
                <span v-if="auth.loading" class="spinner"></span>
                <span v-else>Salvar alterações</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Danger zone -->
        <div class="danger-card">
          <h3 class="danger-title">Conta</h3>
          <div class="danger-row">
            <div>
              <div class="danger-label">Sair da conta</div>
              <div class="danger-desc">Você precisará entrar novamente</div>
            </div>
            <button class="btn-danger" @click="doLogout">Sair →</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, computed, onMounted } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useAuthStore } from '../../stores/auth'

const ui = useUiStore()
const auth = useAuthStore()

const successMsg = ref('')
const errorMsg = ref('')

const form = reactive({
  name: '',
  handle: '',
  whatsapp: '',
  bio: '',
})

// snapshot para comparar dirty state
const snapshot = reactive({ name: '', handle: '', whatsapp: '', bio: '' })

function loadFromUser() {
  form.name     = auth.user?.name     ?? ''
  form.handle   = auth.user?.handle   ?? ''
  form.whatsapp = auth.user?.whatsapp ?? ''
  form.bio      = auth.user?.bio      ?? ''
  snapshot.name     = form.name
  snapshot.handle   = form.handle
  snapshot.whatsapp = form.whatsapp
  snapshot.bio      = form.bio
}

onMounted(loadFromUser)

const isDirty = computed(() =>
  form.name     !== snapshot.name     ||
  form.handle   !== snapshot.handle   ||
  form.whatsapp !== snapshot.whatsapp ||
  form.bio      !== snapshot.bio
)

function reset() {
  form.name     = snapshot.name
  form.handle   = snapshot.handle
  form.whatsapp = snapshot.whatsapp
  form.bio      = snapshot.bio
  successMsg.value = ''
  errorMsg.value   = ''
}

async function save() {
  successMsg.value = ''
  errorMsg.value   = ''
  try {
    await auth.updateProfile({
      name:     form.name     || undefined,
      handle:   form.handle   || undefined,
      whatsapp: form.whatsapp || undefined,
      bio:      form.bio      || undefined,
    })
    // atualiza snapshot
    snapshot.name     = form.name
    snapshot.handle   = form.handle
    snapshot.whatsapp = form.whatsapp
    snapshot.bio      = form.bio
    successMsg.value = 'Perfil atualizado com sucesso!'
    setTimeout(() => { successMsg.value = '' }, 4000)
  } catch (e: unknown) {
    errorMsg.value = e instanceof Error ? e.message : 'Erro ao salvar'
    setTimeout(() => { errorMsg.value = '' }, 5000)
  }
}

async function doLogout() {
  await auth.logout()
  ui.goTo('inicio')
}
</script>

<style scoped>
.profile-page {
  padding: 28px 32px;
  max-width: 1100px;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 28px;
}
.back-btn {
  font-size: 13px;
  color: var(--muted);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}
.back-btn:hover { color: var(--cream); }
.page-title {
  font-family: var(--display);
  font-size: 24px;
  font-weight: 800;
  letter-spacing: -0.03em;
}

/* Grid layout */
.profile-grid {
  display: grid;
  grid-template-columns: 240px 1fr;
  gap: 20px;
  align-items: start;
}

/* Aside */
.profile-aside {
  display: flex;
  flex-direction: column;
  gap: 16px;
  position: sticky;
  top: calc(var(--tb-h) + 20px);
}

.avatar-block {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  text-align: center;
}

.avatar-ring {
  width: 80px; height: 80px;
  border-radius: 50%;
  padding: 3px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  margin-bottom: 8px;
}
.avatar {
  width: 100%; height: 100%;
  border-radius: 50%;
  background: var(--bg-2);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display);
  font-weight: 800;
  font-size: 28px;
  color: var(--orange);
}

.avatar-name  { font-size: 14px; font-weight: 700; }
.avatar-handle { font-family: var(--mono); font-size: 11px; color: var(--muted); }
.avatar-role {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--orange);
  background: rgba(255,94,26,0.1);
  padding: 3px 10px;
  border-radius: 999px;
  margin-top: 4px;
}

.aside-info {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.info-row { display: flex; flex-direction: column; gap: 2px; }
.info-key  { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted-2); }
.info-val  { font-size: 12px; color: var(--cream); word-break: break-all; }

/* Main */
.profile-main {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-2xl);
  padding: 28px;
}

.card-header { margin-bottom: 24px; }
.card-title  { font-family: var(--display); font-size: 18px; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 4px; }
.card-sub    { font-size: 13px; color: var(--muted); }

.edit-form { display: flex; flex-direction: column; gap: 20px; }

.field-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.field { display: flex; flex-direction: column; gap: 6px; }
.full-width { grid-column: 1 / -1; }

.field-label {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted);
}

.field-input {
  padding: 11px 14px;
  background: var(--card-2);
  border: 1px solid var(--line);
  border-radius: 10px;
  color: var(--cream);
  font-size: 13px;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}
.field-input:focus { border-color: var(--orange); }
.field-input::placeholder { color: var(--muted-2); }
.field-input.readonly {
  color: var(--muted);
  cursor: not-allowed;
  opacity: 0.6;
}

.field-textarea { resize: vertical; }

.input-prefix-wrap { position: relative; }
.input-prefix {
  position: absolute;
  left: 14px; top: 50%; transform: translateY(-50%);
  color: var(--muted);
  font-size: 13px;
  pointer-events: none;
}
.field-input.has-prefix { padding-left: 28px; }

.field-hint {
  font-size: 11px;
  color: var(--muted-2);
}
.char-count { text-align: right; }

/* Messages */
.msg-success {
  padding: 12px 16px;
  background: rgba(43, 217, 107, 0.08);
  border: 1px solid rgba(43, 217, 107, 0.25);
  border-radius: 10px;
  font-size: 13px;
  color: var(--green);
}
.msg-error {
  padding: 12px 16px;
  background: rgba(255, 94, 26, 0.08);
  border: 1px solid rgba(255, 94, 26, 0.25);
  border-radius: 10px;
  font-size: 13px;
  color: var(--orange);
}

.fade-msg-enter-active, .fade-msg-leave-active { transition: opacity 0.3s, transform 0.3s; }
.fade-msg-enter-from, .fade-msg-leave-to { opacity: 0; transform: translateY(-6px); }

/* Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-ghost {
  padding: 11px 20px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: transparent;
  color: var(--muted);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-ghost:hover { border-color: var(--line-strong); color: var(--cream); }

.btn-save {
  padding: 11px 24px;
  border-radius: 10px;
  background: var(--orange);
  color: white;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 140px;
  box-shadow: var(--shadow-cta-orange);
}
.btn-save:hover:not(:disabled) { background: var(--orange-deep); transform: translateY(-1px); }
.btn-save:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Danger zone */
.danger-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  padding: 20px 24px;
}
.danger-title {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--muted-2);
  margin-bottom: 14px;
}
.danger-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.danger-label { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
.danger-desc  { font-size: 12px; color: var(--muted); }
.btn-danger {
  padding: 9px 18px;
  border-radius: 10px;
  border: 1px solid rgba(255, 107, 107, 0.3);
  background: rgba(255, 107, 107, 0.06);
  color: #ff6b6b;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  flex-shrink: 0;
}
.btn-danger:hover { background: rgba(255, 107, 107, 0.15); border-color: rgba(255,107,107,0.5); }

/* Mobile */
@media (max-width: 768px) {
  .profile-page { padding: 16px; }
  .page-header { margin-bottom: 20px; }
  .page-title { font-size: 20px; }

  .profile-grid {
    grid-template-columns: 1fr;
  }
  .profile-aside { position: static; }

  .avatar-ring { width: 68px; height: 68px; }
  .avatar { font-size: 24px; }

  .field-grid { grid-template-columns: 1fr; }
  .form-card { padding: 18px; }

  .form-actions { flex-direction: column-reverse; }
  .btn-save, .btn-ghost { width: 100%; justify-content: center; }
}
</style>
