<template>
  <div class="curriculo-page fade-up">
    <div class="page-head">
      <div>
        <div class="eyebrow">📄 banco de talentos</div>
        <h1 class="page-title display">Meu Currículo</h1>
        <p class="page-sub">Cadastre seu perfil e seja encontrado pelas vagas da comunidade</p>
      </div>
    </div>

    <!-- Status do currículo existente -->
    <div v-if="data.meuCurriculo" class="curriculo-status-card">
      <div class="status-icon">✅</div>
      <div class="status-body">
        <div class="status-title">Currículo cadastrado</div>
        <div class="status-sub">Você está no banco de talentos · atualizado {{ formatDate(data.meuCurriculo.created_at) }}</div>
      </div>
      <button class="ghost-btn small" @click="startEdit">Editar</button>
    </div>

    <!-- Tabs: formulário ou PDF -->
    <div v-if="!data.meuCurriculo || editando">
      <div class="mode-tabs">
        <button class="mode-tab" :class="{ active: modo === 'form' }" @click="modo = 'form'">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Preencher formulário
        </button>
        <button class="mode-tab" :class="{ active: modo === 'pdf' }" @click="modo = 'pdf'">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Enviar PDF
        </button>
      </div>

      <!-- Formulário -->
      <div v-if="modo === 'form'" class="form-card">
        <div class="form-section-label">Dados pessoais</div>
        <div class="form-grid">
          <div class="form-field">
            <label>Nome completo *</label>
            <input v-model="form.nome" type="text" placeholder="Seu nome completo" class="input" />
          </div>
          <div class="form-field">
            <label>WhatsApp *</label>
            <input v-model="form.telefone" type="text" placeholder="(21) 99999-0000" class="input" />
          </div>
          <div class="form-field">
            <label>E-mail *</label>
            <input v-model="form.email" type="email" placeholder="seu@email.com" class="input" />
          </div>
          <div class="form-field">
            <label>Cidade</label>
            <input v-model="form.cidade" type="text" placeholder="Ex: Rio de Janeiro - RJ" class="input" />
          </div>
        </div>

        <div class="form-section-label">Área e experiência</div>
        <div class="form-grid">
          <div class="form-field full">
            <label>Área de atuação *</label>
            <select v-model="form.area_atuacao" class="input">
              <option value="">Selecione sua área principal...</option>
              <option v-for="area in areas" :key="area" :value="area">{{ area }}</option>
            </select>
          </div>
          <div class="form-field full">
            <label>Disponibilidade</label>
            <div class="radio-group">
              <label v-for="d in disponibilidades" :key="d.value" class="radio-opt">
                <input type="radio" v-model="form.disponibilidade" :value="d.value" />
                <span>{{ d.label }}</span>
              </label>
            </div>
          </div>
          <div class="form-field full">
            <label>Habilidades</label>
            <div class="tags-input-area" @click="focarInput">
              <span
                v-for="(tag, i) in form.habilidades"
                :key="i"
                class="skill-tag"
              >
                {{ tag }}
                <button @click.prevent="removerHabilidade(i)" class="tag-remove">×</button>
              </span>
              <input
                ref="tagsInput"
                v-model="novaHabilidade"
                @keydown.enter.prevent="adicionarHabilidade"
                @keydown.comma.prevent="adicionarHabilidade"
                @keydown.backspace="backspaceHabilidade"
                type="text"
                placeholder="Ex: Costura — pressione Enter para adicionar"
                class="tags-input"
              />
            </div>
          </div>
          <div class="form-field full">
            <label>Experiência profissional</label>
            <textarea
              v-model="form.experiencia"
              class="input"
              rows="4"
              placeholder="Conte um pouco sobre sua experiência, onde trabalhou, o que fez..."
            ></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button v-if="editando" class="ghost-btn" @click="cancelarEdicao">Cancelar</button>
          <button class="primary-btn" :disabled="salvando || !formValido" @click="salvar">
            <span v-if="salvando">Salvando...</span>
            <span v-else>{{ data.meuCurriculo ? 'Salvar alterações' : 'Cadastrar no banco de talentos' }} →</span>
          </button>
        </div>
        <p v-if="erroForm" class="form-error">{{ erroForm }}</p>
      </div>

      <!-- Upload PDF -->
      <div v-if="modo === 'pdf'" class="form-card">
        <div v-if="!data.meuCurriculo" class="pdf-warning">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Primeiro preencha o formulário para cadastrar seus dados. O PDF é um complemento.
        </div>

        <div v-if="data.meuCurriculo && data.meuCurriculo.pdf_url" class="pdf-current">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span>PDF atual enviado</span>
          <a :href="data.meuCurriculo.pdf_url" target="_blank" class="pdf-link">Ver →</a>
        </div>

        <label class="upload-zone" :class="{ 'has-file': pdfFile }">
          <input type="file" accept=".pdf" @change="onPdfChange" style="display:none" />
          <div class="upload-icon">{{ pdfFile ? '📄' : '⬆️' }}</div>
          <div class="upload-text">
            <span v-if="pdfFile" class="file-name">{{ pdfFile.name }}</span>
            <span v-else>
              <span class="upload-link">Escolher arquivo PDF</span> ou arraste aqui<br>
              <small>Máximo 5 MB</small>
            </span>
          </div>
        </label>

        <div class="form-actions">
          <button
            class="primary-btn"
            :disabled="!pdfFile || !data.meuCurriculo || enviandoPdf"
            @click="enviarPdf"
          >
            <span v-if="enviandoPdf">Enviando...</span>
            <span v-else>Enviar PDF →</span>
          </button>
        </div>
        <p v-if="erroPdf" class="form-error">{{ erroPdf }}</p>
        <p v-if="sucessoPdf" class="form-success">{{ sucessoPdf }}</p>
      </div>
    </div>

    <!-- Perfil completo (somente visualização) -->
    <div v-if="data.meuCurriculo && !editando" class="perfil-card">
      <div class="perfil-header">
        <div class="perfil-av">{{ iniciais(data.meuCurriculo?.nome) }}</div>
        <div>
          <div class="perfil-nome">{{ data.meuCurriculo.nome }}</div>
          <div class="perfil-area">{{ data.meuCurriculo.area_atuacao }}</div>
        </div>
        <div class="perfil-disponivel" :class="data.meuCurriculo.disponibilidade === 'imediata' ? 'green' : 'gray'">
          {{ data.meuCurriculo.disponibilidade === 'imediata' ? '🟢 Disponível agora' : `⏳ Em ${data.meuCurriculo.disponibilidade}` }}
        </div>
      </div>

      <div class="perfil-infos">
        <div class="info-item">
          <span class="info-label">E-mail</span>
          <span>{{ data.meuCurriculo.email }}</span>
        </div>
        <div v-if="data.meuCurriculo.telefone" class="info-item">
          <span class="info-label">Telefone</span>
          <span>{{ data.meuCurriculo.telefone }}</span>
        </div>
        <div v-if="data.meuCurriculo.cidade" class="info-item">
          <span class="info-label">Cidade</span>
          <span>{{ data.meuCurriculo.cidade }}</span>
        </div>
      </div>

      <div v-if="data.meuCurriculo.habilidades?.length" class="perfil-tags">
        <span v-for="h in data.meuCurriculo.habilidades" :key="h" class="skill-badge">{{ h }}</span>
      </div>

      <div v-if="data.meuCurriculo.experiencia" class="perfil-exp">
        <div class="exp-label">Experiência</div>
        <p>{{ data.meuCurriculo.experiencia }}</p>
      </div>

      <div class="perfil-pdf-row">
        <a
          v-if="data.meuCurriculo.pdf_url"
          :href="data.meuCurriculo.pdf_url"
          target="_blank"
          class="pdf-btn"
        >
          📄 Ver PDF
        </a>
        <button class="upload-pdf-btn" @click="modo = 'pdf'; editando = true">
          {{ data.meuCurriculo.pdf_url ? '🔄 Atualizar PDF' : '📤 Enviar PDF' }}
        </button>
      </div>
    </div>

    <!-- Informativo sobre o banco -->
    <div class="info-box">
      <div class="info-box-icon">🎯</div>
      <div>
        <div class="info-box-title">Como funciona o match</div>
        <p class="info-box-text">
          Quando uma nova vaga é cadastrada na plataforma, o sistema compara automaticamente as áreas de atuação
          e habilidades dos candidatos com o perfil da vaga. Se houver compatibilidade, seu contato é enviado
          diretamente para o e-mail do recrutador.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useDataStore } from '../../stores/data'
import { useAuthStore } from '../../stores/auth'
import { useUiStore } from '../../stores/ui'

const data = useDataStore()
const auth = useAuthStore()
const ui = useUiStore()

const modo = ref<'form' | 'pdf'>('form')
const editando = ref(false)
const salvando = ref(false)
const erroForm = ref('')
const enviandoPdf = ref(false)
const erroPdf = ref('')
const sucessoPdf = ref('')
const pdfFile = ref<File | null>(null)
const novaHabilidade = ref('')
const tagsInput = ref<HTMLInputElement | null>(null)

const areas = [
  'Beleza e Estética', 'Construção e Reforma', 'Costura e Moda',
  'Saúde e Bem-estar', 'Educação', 'Tecnologia', 'Gastronomia',
  'Transporte e Entregas', 'Eventos', 'Limpeza e Doméstica',
  'Administração', 'Comércio', 'Arte e Artesanato', 'Outro',
]

const disponibilidades = [
  { value: 'imediata', label: 'Imediata' },
  { value: '30 dias', label: 'Em 30 dias' },
  { value: '60 dias', label: 'Em 60 dias' },
]

const form = ref({
  nome: '',
  email: '',
  telefone: '',
  area_atuacao: '',
  habilidades: [] as string[],
  experiencia: '',
  cidade: '',
  disponibilidade: 'imediata',
})

const formValido = computed(() =>
  (form.value.nome ?? '').trim() &&
  (form.value.email ?? '').trim() &&
  form.value.area_atuacao
)

function iniciais(nome: string | null | undefined) {
  if (!nome) return '?'
  return nome.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}

function adicionarHabilidade() {
  const tag = novaHabilidade.value.trim().replace(/,$/, '')
  if (tag && !form.value.habilidades.includes(tag)) {
    form.value.habilidades.push(tag)
  }
  novaHabilidade.value = ''
}

function removerHabilidade(i: number) {
  form.value.habilidades.splice(i, 1)
}

function backspaceHabilidade() {
  if (!novaHabilidade.value && form.value.habilidades.length) {
    form.value.habilidades.pop()
  }
}

function focarInput() {
  tagsInput.value?.focus()
}

function cancelarEdicao() {
  editando.value = false
  modo.value = 'form'
  if (data.meuCurriculo) preencherForm(data.meuCurriculo)
}

function preencherForm(c: typeof data.meuCurriculo) {
  if (!c) return
  form.value.nome = c.nome ?? ''
  form.value.email = c.email ?? ''
  form.value.telefone = c.telefone ?? ''
  form.value.area_atuacao = c.area_atuacao ?? ''
  form.value.habilidades = [...(c.habilidades ?? [])]
  form.value.experiencia = c.experiencia ?? ''
  form.value.cidade = c.cidade ?? ''
  form.value.disponibilidade = c.disponibilidade ?? 'imediata'
}

function startEdit() {
  if (data.meuCurriculo) preencherForm(data.meuCurriculo)
  editando.value = true
  modo.value = 'form'
}

async function salvar() {
  if (!auth.isAuthenticated) {
    ui.goTo('login')
    return
  }
  erroForm.value = ''
  salvando.value = true
  try {
    await data.salvarCurriculo({
      nome: form.value.nome,
      email: form.value.email,
      telefone: form.value.telefone || undefined,
      area_atuacao: form.value.area_atuacao,
      habilidades: form.value.habilidades,
      experiencia: form.value.experiencia || undefined,
      cidade: form.value.cidade || undefined,
      disponibilidade: form.value.disponibilidade,
    })
    editando.value = false
  } catch {
    erroForm.value = 'Erro ao salvar. Tente novamente.'
  } finally {
    salvando.value = false
  }
}

function onPdfChange(e: Event) {
  const input = e.target as HTMLInputElement
  pdfFile.value = input.files?.[0] ?? null
  erroPdf.value = ''
  sucessoPdf.value = ''
}

async function enviarPdf() {
  if (!pdfFile.value) return
  erroPdf.value = ''
  sucessoPdf.value = ''
  enviandoPdf.value = true
  try {
    await data.uploadCurriculoPdf(pdfFile.value)
    pdfFile.value = null
    sucessoPdf.value = 'PDF enviado com sucesso!'
    editando.value = false
  } catch {
    erroPdf.value = 'Erro ao enviar o PDF. Tente novamente.'
  } finally {
    enviandoPdf.value = false
  }
}

onMounted(async () => {
  if (auth.isAuthenticated) {
    await data.fetchMeuCurriculo()
    if (data.meuCurriculo) preencherForm(data.meuCurriculo)
  }
})
</script>

<style scoped>
.curriculo-page { padding: 32px 28px 80px; max-width: 720px; }

.page-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 28px;
  gap: 16px;
}

/* Status card */
.curriculo-status-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: rgba(43,217,107,0.07);
  border: 1px solid rgba(43,217,107,0.2);
  border-radius: 14px;
  padding: 16px 20px;
  margin-bottom: 24px;
}
.status-icon { font-size: 22px; }
.status-body { flex: 1; }
.status-title { font-size: 14px; font-weight: 700; color: var(--green); }
.status-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

/* Mode tabs */
.mode-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
}
.mode-tab {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 9px 16px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: var(--card);
  color: var(--muted);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.mode-tab.active {
  background: rgba(255,94,26,0.10);
  border-color: rgba(255,94,26,0.25);
  color: var(--orange);
}

/* Form card */
.form-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 16px;
  padding: 28px;
  margin-bottom: 24px;
}
.form-section-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 16px;
  margin-top: 8px;
}
.form-section-label:first-child { margin-top: 0; }

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 20px;
}
.form-field { display: flex; flex-direction: column; gap: 5px; }
.form-field.full { grid-column: span 2; }
.form-field label {
  font-size: 11px;
  font-weight: 600;
  color: var(--muted);
  letter-spacing: 0.04em;
}
.input {
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 9px;
  padding: 9px 12px;
  font-size: 13px;
  color: var(--cream);
  outline: none;
  transition: border-color 0.15s;
  font-family: inherit;
  width: 100%;
}
.input:focus { border-color: var(--orange); }
textarea.input { resize: vertical; }
select.input { cursor: pointer; }

/* Tags input */
.tags-input-area {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 9px;
  padding: 8px 10px;
  cursor: text;
  transition: border-color 0.15s;
  min-height: 42px;
  align-items: center;
}
.tags-input-area:focus-within { border-color: var(--orange); }
.tags-input {
  background: transparent;
  border: none;
  outline: none;
  font-size: 13px;
  color: var(--cream);
  flex: 1;
  min-width: 120px;
  font-family: inherit;
}
.tags-input::placeholder { color: var(--muted); }
.skill-tag {
  display: flex;
  align-items: center;
  gap: 5px;
  background: rgba(255,94,26,0.12);
  color: var(--orange);
  font-size: 12px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 20px;
}
.tag-remove {
  font-size: 14px;
  line-height: 1;
  color: var(--orange);
  opacity: 0.6;
  cursor: pointer;
  padding: 0;
}
.tag-remove:hover { opacity: 1; }

/* Radio group */
.radio-group { display: flex; gap: 20px; flex-wrap: wrap; }
.radio-opt {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 13px;
  color: var(--cream);
  cursor: pointer;
}
.radio-opt input { accent-color: var(--orange); cursor: pointer; }

/* Form actions */
.form-actions {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-top: 24px;
  flex-wrap: wrap;
}
.form-error { color: #ff6b6b; font-size: 13px; margin-top: 10px; }
.form-success { color: var(--green); font-size: 13px; margin-top: 10px; }

/* PDF upload */
.pdf-warning {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,210,63,0.08);
  border: 1px solid rgba(255,210,63,0.2);
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 13px;
  color: var(--yellow);
  margin-bottom: 20px;
}
.pdf-current {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(43,217,107,0.07);
  border: 1px solid rgba(43,217,107,0.15);
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 13px;
  color: var(--green);
  margin-bottom: 16px;
}
.pdf-link { color: var(--orange); font-weight: 600; margin-left: auto; }

.upload-zone {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  border: 2px dashed var(--line);
  border-radius: 14px;
  padding: 32px 20px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s;
  margin-bottom: 20px;
}
.upload-zone:hover, .upload-zone.has-file { border-color: var(--orange); }
.upload-icon { font-size: 32px; }
.upload-text { font-size: 13px; color: var(--muted); line-height: 1.6; }
.upload-link { color: var(--orange); font-weight: 700; }
.file-name { color: var(--cream); font-weight: 600; }

/* Perfil card */
.perfil-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 16px;
  padding: 24px 28px;
  margin-bottom: 24px;
}
.perfil-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.perfil-av {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  font-size: 18px;
  color: #111;
  flex-shrink: 0;
}
.perfil-nome { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
.perfil-area { font-size: 13px; color: var(--muted); margin-top: 2px; }
.perfil-disponivel {
  margin-left: auto;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 20px;
}
.perfil-disponivel.green { background: rgba(43,217,107,0.1); color: var(--green); }
.perfil-disponivel.gray { background: var(--bg-2); color: var(--muted); }

.perfil-infos {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 10px;
  margin-bottom: 16px;
}
.info-item { display: flex; flex-direction: column; gap: 3px; }
.info-label { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted); }

.perfil-tags { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 16px; }
.skill-badge {
  background: var(--bg-2);
  border: 1px solid var(--line);
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
  color: var(--muted);
}

.perfil-exp { margin-bottom: 20px; }
.exp-label { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
.perfil-exp p { font-size: 13px; color: var(--cream); line-height: 1.6; }

.perfil-pdf-row { display: flex; gap: 10px; flex-wrap: wrap; }
.pdf-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--orange);
  color: white;
  font-size: 13px;
  font-weight: 700;
  padding: 9px 16px;
  border-radius: 9px;
  text-decoration: none;
}
.upload-pdf-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--bg-2);
  border: 1px solid var(--line);
  color: var(--cream);
  font-size: 13px;
  font-weight: 600;
  padding: 9px 16px;
  border-radius: 9px;
  cursor: pointer;
}

/* Info box */
.info-box {
  display: flex;
  gap: 16px;
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 20px;
}
.info-box-icon { font-size: 24px; flex-shrink: 0; }
.info-box-title { font-size: 14px; font-weight: 700; margin-bottom: 6px; }
.info-box-text { font-size: 13px; color: var(--muted); line-height: 1.6; }

/* Buttons */
.primary-btn {
  background: var(--orange);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 11px 22px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.15s;
}
.primary-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.ghost-btn {
  background: transparent;
  border: 1px solid var(--line);
  color: var(--muted);
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s;
}
.ghost-btn:hover { border-color: var(--orange); color: var(--orange); }
.ghost-btn.small { padding: 7px 14px; font-size: 12px; }

@media (max-width: 600px) {
  .curriculo-page { padding: 20px 16px 80px; }
  .form-grid { grid-template-columns: 1fr; }
  .form-field.full { grid-column: span 1; }
  .perfil-header { flex-direction: column; align-items: flex-start; }
  .perfil-disponivel { margin-left: 0; }
}
</style>
