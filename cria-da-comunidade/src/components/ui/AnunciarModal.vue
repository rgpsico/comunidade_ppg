<template>
  <Teleport to="body">
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal">

        <!-- Cabeçalho -->
        <div class="modal-head">
          <div class="modal-title">
            {{ step === 'pick' ? 'O que você quer anunciar?' : labels[tipo!].title }}
          </div>
          <button class="close-btn" @click="$emit('close')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <!-- Passo 1: Escolha o tipo -->
        <div v-if="step === 'pick'" class="type-grid">
          <button
            v-for="opt in typeOptions"
            :key="opt.key"
            class="type-card"
            @click="pick(opt.key)"
          >
            <span class="type-icon">{{ opt.icon }}</span>
            <span class="type-label">{{ opt.label }}</span>
            <span class="type-desc">{{ opt.desc }}</span>
          </button>
        </div>

        <!-- Passo 2: Formulários -->
        <form v-else @submit.prevent="submit">

          <!-- CORRE (Profissional) -->
          <div v-if="tipo === 'corre'" class="fields">

            <!-- Foto de perfil -->
            <div class="field">
              <label>Foto de perfil *</label>
              <div
                class="photo-zone"
                :class="{ 'has-photo': photoPreview, 'photo-err': !photoPreview && submitAttempted }"
                @click="fileInput?.click()"
              >
                <img v-if="photoPreview" :src="photoPreview" class="photo-img" />
                <div v-else class="photo-empty">
                  <span class="photo-icon-big">📷</span>
                  <span class="photo-cta">Toque para adicionar sua foto</span>
                  <span class="photo-fmt">JPG ou PNG · máx 4 MB</span>
                </div>
                <div v-if="photoPreview" class="photo-change-overlay">Trocar foto</div>
              </div>
              <input ref="fileInput" type="file" accept="image/*" style="display:none" @change="onFileChange" />
              <span v-if="!photoPreview && submitAttempted" class="err-inline">Foto é obrigatória</span>
            </div>

            <div class="row-2">
              <div class="field">
                <label>Nome *</label>
                <input v-model="f.nome" placeholder="Seu nome completo" required />
              </div>
              <div class="field">
                <label>Especialidade / Cargo *</label>
                <input v-model="f.cargo" placeholder="Ex: Cabeleireira, Pedreiro" required />
              </div>
            </div>
            <div class="field">
              <label>Área *</label>
              <select v-model="f.categoria" required>
                <option value="">Selecione…</option>
                <option v-for="c in categorias" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
            <div class="field">
              <label>Sobre você (bio)</label>
              <textarea v-model="f.bio" rows="3" placeholder="Conta um pouco do seu corre…"></textarea>
            </div>
            <div class="row-2">
              <div class="field">
                <label>WhatsApp *</label>
                <input
                  v-model="f.whatsapp"
                  placeholder="(11) 99999-9999"
                  required
                  :class="{ 'input-err': !f.whatsapp && submitAttempted }"
                />
                <span v-if="!f.whatsapp && submitAttempted" class="err-inline">WhatsApp é obrigatório</span>
              </div>
              <div class="field">
                <label>Preço a partir de (R$)</label>
                <input v-model.number="f.preco_a_partir" type="number" min="0" placeholder="0" />
              </div>
            </div>
            <div class="row-2">
              <div class="field">
                <label>Cor 1</label>
                <div class="color-row">
                  <input type="color" v-model="f.cor1" />
                  <span class="color-val">{{ f.cor1 }}</span>
                </div>
              </div>
              <div class="field">
                <label>Cor 2</label>
                <div class="color-row">
                  <input type="color" v-model="f.cor2" />
                  <span class="color-val">{{ f.cor2 }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- EVENTO -->
          <div v-if="tipo === 'evento'" class="fields">
            <div class="field">
              <label>Título do evento *</label>
              <input v-model="f.titulo" placeholder="Ex: Baile do Bairro" required />
            </div>
            <div class="row-2">
              <div class="field">
                <label>Categoria *</label>
                <select v-model="f.categoria" required>
                  <option value="">Selecione…</option>
                  <option v-for="c in catEvento" :key="c.val" :value="c.val">{{ c.label }}</option>
                </select>
              </div>
              <div class="field">
                <label>Data e hora *</label>
                <input v-model="f.data_hora" type="datetime-local" required />
              </div>
            </div>
            <div class="field">
              <label>Local</label>
              <input v-model="f.local" placeholder="Ex: Quadra do bairro, Rua X, 123" />
            </div>
            <div class="field">
              <label>Descrição</label>
              <textarea v-model="f.descricao" rows="3" placeholder="O que vai rolar…"></textarea>
            </div>
            <div class="row-2">
              <div class="field toggle-field">
                <label>Evento gratuito?</label>
                <button
                  type="button"
                  class="toggle"
                  :class="{ on: f.gratuito }"
                  @click="f.gratuito = !f.gratuito"
                >
                  <span class="toggle-knob"></span>
                </button>
              </div>
              <div class="field" v-if="!f.gratuito">
                <label>Preço (R$)</label>
                <input v-model.number="f.preco" type="number" min="0" placeholder="0" />
              </div>
            </div>
            <div class="row-2">
              <div class="field">
                <label>Cor 1</label>
                <div class="color-row">
                  <input type="color" v-model="f.cor1" />
                  <span class="color-val">{{ f.cor1 }}</span>
                </div>
              </div>
              <div class="field">
                <label>Cor 2</label>
                <div class="color-row">
                  <input type="color" v-model="f.cor2" />
                  <span class="color-val">{{ f.cor2 }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- VAGA -->
          <div v-if="tipo === 'vaga'" class="fields">
            <div class="row-2">
              <div class="field">
                <label>Título da vaga *</label>
                <input v-model="f.titulo" placeholder="Ex: Designer UX" required />
              </div>
              <div class="field">
                <label>Empresa / Anunciante *</label>
                <input v-model="f.empresa" placeholder="Nome da empresa" required />
              </div>
            </div>
            <div class="row-2">
              <div class="field">
                <label>Tipo de contratação *</label>
                <select v-model="f.tipo" required>
                  <option value="">Selecione…</option>
                  <option v-for="t in tiposVaga" :key="t" :value="t">{{ t }}</option>
                </select>
              </div>
              <div class="field">
                <label>Local</label>
                <input v-model="f.local" placeholder="Ex: São Paulo - SP (Remoto)" />
              </div>
            </div>
            <div class="row-2">
              <div class="field">
                <label>Salário</label>
                <input v-model="f.salario" placeholder="Ex: R$ 3.000" />
              </div>
              <div class="field">
                <label>Período</label>
                <select v-model="f.salario_periodo">
                  <option value="">—</option>
                  <option value="hora">por hora</option>
                  <option value="dia">por dia</option>
                  <option value="semana">por semana</option>
                  <option value="mês">por mês</option>
                  <option value="ano">por ano</option>
                </select>
              </div>
            </div>
            <div class="field">
              <label>Descrição da vaga</label>
              <textarea v-model="f.descricao" rows="3" placeholder="O que a pessoa vai fazer…"></textarea>
            </div>
          </div>

          <!-- Erro -->
          <p v-if="erro" class="err">{{ erro }}</p>

          <!-- Rodapé -->
          <div class="modal-footer">
            <button type="button" class="btn-back" @click="step = 'pick'">← Voltar</button>
            <button type="submit" class="btn-submit" :disabled="loading">
              {{ loading ? 'Publicando…' : 'Publicar' }}
            </button>
          </div>
        </form>

      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { api } from '../../services/api'
import { useDataStore } from '../../stores/data'

const emit = defineEmits<{ close: [] }>()
const data = useDataStore()

type Tipo = 'corre' | 'evento' | 'vaga'
const step = ref<'pick' | 'form'>('pick')
const tipo = ref<Tipo | null>(null)
const loading = ref(false)
const erro = ref<string | null>(null)

// Foto
const fileInput = ref<HTMLInputElement | null>(null)
const photoPreview = ref<string | null>(null)
const submitAttempted = ref(false)

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
  f.foto = file
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value)
  photoPreview.value = URL.createObjectURL(file)
}

function resetPhoto() {
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value)
  photoPreview.value = null
  f.foto = null
  submitAttempted.value = false
}

const labels: Record<Tipo, { title: string }> = {
  corre:  { title: 'Anunciar meu serviço' },
  evento: { title: 'Criar evento' },
  vaga:   { title: 'Publicar vaga' },
}

const typeOptions = [
  { key: 'corre'  as Tipo, icon: '🛠', label: 'Corre / Serviço', desc: 'Divulga seu trabalho pra comunidade' },
  { key: 'evento' as Tipo, icon: '🎉', label: 'Evento',           desc: 'Cria e divulga um evento' },
  { key: 'vaga'   as Tipo, icon: '💼', label: 'Vaga',             desc: 'Publica uma oportunidade de emprego' },
]

const categorias = ['Beleza', 'Construção', 'Casa', 'Transporte', 'Eventos', 'Saúde']
const catEvento = [
  { val: 'baile',    label: 'Baile' },
  { val: 'pagode',   label: 'Pagode' },
  { val: 'esporte',  label: 'Esporte' },
  { val: 'cultura',  label: 'Cultura' },
  { val: 'festa',    label: 'Festa' },
  { val: 'workshop', label: 'Workshop' },
]
const tiposVaga = ['CLT', 'PJ', 'Freelance', 'Estágio', 'Voluntário']

const f = reactive<Record<string, any>>({
  // corre
  nome: '', cargo: '', categoria: '', bio: '', whatsapp: '', preco_a_partir: '',
  cor1: '#FF5E1A', cor2: '#FFD23F', foto: null as File | null,
  // evento
  titulo: '', data_hora: '', local: '', descricao: '', gratuito: true, preco: '',
  // vaga
  empresa: '', tipo: '', salario: '', salario_periodo: '',
})

function pick(t: Tipo) {
  tipo.value = t
  step.value = 'form'
  erro.value = null
  resetPhoto()
}

async function submit() {
  erro.value = null
  submitAttempted.value = true

  // Validação manual dos campos obrigatórios do corre
  if (tipo.value === 'corre') {
    if (!f.foto)      { erro.value = 'Adicione uma foto de perfil'; return }
    if (!f.whatsapp)  { erro.value = 'WhatsApp é obrigatório'; return }
  }

  loading.value = true
  try {
    if (tipo.value === 'corre') {
      const fd = new FormData()
      fd.append('nome',      f.nome)
      fd.append('cargo',     f.cargo)
      fd.append('categoria', f.categoria)
      if (f.bio)           fd.append('bio',            f.bio)
      fd.append('whatsapp', f.whatsapp)
      if (f.preco_a_partir) fd.append('preco_a_partir', String(f.preco_a_partir))
      fd.append('cor1',     f.cor1)
      fd.append('cor2',     f.cor2)
      fd.append('foto',     f.foto as File)
      await api.postForm('/profissionais', fd)
    } else if (tipo.value === 'evento') {
      await api.post('/eventos', {
        titulo:      f.titulo,
        categoria:   f.categoria,
        data_hora:   f.data_hora,
        local:       f.local || null,
        descricao:   f.descricao || null,
        gratuito:    f.gratuito,
        preco:       f.gratuito ? null : (f.preco || null),
        cor1:        f.cor1,
        cor2:        f.cor2,
        idade_minima: 0,
      })
    } else if (tipo.value === 'vaga') {
      await api.post('/vagas', {
        titulo:          f.titulo,
        empresa:         f.empresa,
        tipo:            f.tipo,
        local:           f.local || null,
        descricao:       f.descricao || null,
        salario:         f.salario || null,
        salario_periodo: f.salario_periodo || null,
      })
    }
    await data.fetchAll()
    emit('close')
  } catch (e: unknown) {
    erro.value = e instanceof Error ? e.message : 'Erro ao publicar'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal {
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 20px;
  width: 100%;
  max-width: 540px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 24px 0;
  margin-bottom: 20px;
}
.modal-title {
  font-family: var(--display);
  font-weight: 800;
  font-size: 18px;
  letter-spacing: -0.02em;
}
.close-btn {
  color: var(--muted);
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  transition: color 0.15s, background 0.15s;
}
.close-btn:hover { color: var(--cream); background: rgba(255,255,255,0.06); }

/* Type picker */
.type-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  padding: 0 24px 24px;
}
.type-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 20px 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: border-color 0.15s, background 0.15s, transform 0.1s;
  text-align: center;
}
.type-card:hover {
  border-color: var(--orange);
  background: rgba(255,94,26,0.06);
  transform: translateY(-2px);
}
.type-icon { font-size: 28px; }
.type-label { font-size: 13px; font-weight: 700; }
.type-desc { font-size: 11px; color: var(--muted); line-height: 1.4; }

/* Form */
form { padding: 0 24px 24px; }
.fields { display: flex; flex-direction: column; gap: 14px; margin-bottom: 16px; }

.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.field { display: flex; flex-direction: column; gap: 6px; }
.field label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--muted);
}
.field input,
.field select,
.field textarea {
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 9px 12px;
  font-size: 13px;
  color: var(--cream);
  outline: none;
  transition: border-color 0.15s;
  font-family: inherit;
  resize: none;
}
.field input:focus,
.field select:focus,
.field textarea:focus { border-color: var(--orange); }
.field input::placeholder,
.field textarea::placeholder { color: var(--muted-2); }
.field select { cursor: pointer; }
.field select option { background: var(--bg-2); }

.color-row { display: flex; align-items: center; gap: 10px; }
.color-row input[type="color"] {
  width: 36px; height: 36px;
  border-radius: 8px;
  padding: 2px;
  cursor: pointer;
}
.color-val { font-family: var(--mono); font-size: 11px; color: var(--muted); }

/* Toggle */
.toggle-field { flex-direction: row; align-items: center; gap: 12px; }
.toggle-field label { margin: 0; }
.toggle {
  width: 40px; height: 22px;
  background: var(--line);
  border-radius: 99px;
  position: relative;
  cursor: pointer;
  transition: background 0.2s;
  flex-shrink: 0;
}
.toggle.on { background: var(--orange); }
.toggle-knob {
  position: absolute;
  width: 16px; height: 16px;
  background: white;
  border-radius: 50%;
  top: 3px; left: 3px;
  transition: transform 0.2s;
}
.toggle.on .toggle-knob { transform: translateX(18px); }

.err {
  font-size: 13px;
  color: #ff6b6b;
  margin-bottom: 12px;
  padding: 10px 12px;
  background: rgba(255,107,107,0.08);
  border-radius: 8px;
  border: 1px solid rgba(255,107,107,0.2);
}

.modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 4px;
}
.btn-back {
  font-size: 13px;
  color: var(--muted);
  cursor: pointer;
  padding: 8px 4px;
  transition: color 0.15s;
}
.btn-back:hover { color: var(--cream); }
.btn-submit {
  background: var(--orange);
  color: var(--black);
  font-family: var(--display);
  font-weight: 700;
  font-size: 14px;
  padding: 10px 28px;
  border-radius: 12px;
  cursor: pointer;
  transition: opacity 0.15s, transform 0.1s;
}
.btn-submit:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

/* Zona de foto */
.photo-zone {
  height: 130px;
  border: 2px dashed var(--line);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden;
  position: relative;
  transition: border-color 0.2s, background 0.2s;
  background: var(--bg);
}
.photo-zone:hover { border-color: var(--orange); background: rgba(255,94,26,0.04); }
.photo-zone.photo-err { border-color: #ff6b6b; }
.photo-zone.has-photo { border-style: solid; border-color: var(--orange); }

.photo-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  pointer-events: none;
}
.photo-icon-big { font-size: 28px; }
.photo-cta { font-size: 13px; font-weight: 600; color: var(--cream); }
.photo-fmt { font-size: 11px; color: var(--muted-2); }

.photo-img {
  width: 100%; height: 100%;
  object-fit: cover;
}
.photo-change-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.5);
  color: white;
  font-size: 13px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}
.photo-zone.has-photo:hover .photo-change-overlay { opacity: 1; }

.err-inline {
  font-size: 11px;
  color: #ff6b6b;
  margin-top: 2px;
}
.input-err { border-color: #ff6b6b !important; }
</style>
