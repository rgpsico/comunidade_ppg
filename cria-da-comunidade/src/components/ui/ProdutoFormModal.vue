<template>
  <Teleport to="body">
    <div class="pfm-overlay" @click.self="$emit('close')">
      <div class="pfm-modal">
        <div class="pfm-header">
          <h3 class="pfm-title">{{ produto ? 'Editar produto' : 'Novo produto' }}</h3>
          <button class="pfm-close" @click="$emit('close')">✕</button>
        </div>

        <form class="pfm-form" @submit.prevent="submit">

          <!-- Imagens -->
          <div class="field-group">
            <label class="field-label">Fotos do produto</label>

            <!-- Preview imagens existentes -->
            <div v-if="imagensExistentes.length" class="img-preview-row">
              <div
                v-for="(img, i) in imagensExistentes"
                :key="img.path"
                class="img-thumb"
              >
                <img :src="img.url" :alt="`foto ${i+1}`" />
                <button type="button" class="img-remove" @click="removerExistente(i)">✕</button>
              </div>
            </div>

            <!-- Preview novas imagens -->
            <div v-if="novasPreview.length" class="img-preview-row">
              <div
                v-for="(src, i) in novasPreview"
                :key="i"
                class="img-thumb img-thumb-new"
              >
                <img :src="src" :alt="`nova ${i+1}`" />
                <button type="button" class="img-remove" @click="removerNova(i)">✕</button>
              </div>
            </div>

            <label class="img-upload-btn">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              Adicionar fotos
              <input type="file" accept="image/*" multiple @change="onImagens" style="display:none" />
            </label>
          </div>

          <!-- Nome -->
          <div class="field-group">
            <label class="field-label">Nome <span class="req">*</span></label>
            <input v-model="form.nome" class="field-input" type="text" placeholder="Ex: Camiseta do bairro" required />
          </div>

          <!-- Categoria + Preços em 2 colunas -->
          <div class="fields-row">
            <div class="field-group">
              <label class="field-label">Categoria</label>
              <input v-model="form.categoria" class="field-input" type="text" placeholder="Ex: Roupas" />
            </div>
            <div class="field-group">
              <label class="field-label">Preço <span class="req">*</span></label>
              <div class="input-prefix">
                <span>R$</span>
                <input v-model="form.preco" class="field-input" type="number" step="0.01" min="0" placeholder="0,00" required />
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">Preço promo</label>
              <div class="input-prefix">
                <span>R$</span>
                <input v-model="form.precoPromocional" class="field-input" type="number" step="0.01" min="0" placeholder="—" />
              </div>
            </div>
          </div>

          <!-- Descrição -->
          <div class="field-group">
            <label class="field-label">Descrição</label>
            <textarea v-model="form.descricao" class="field-input" rows="3" placeholder="Detalhes do produto..."></textarea>
          </div>

          <!-- Toggles -->
          <div class="toggles-row">
            <label class="toggle-item">
              <div class="toggle-switch" :class="{ on: form.disponivel }" @click="form.disponivel = !form.disponivel">
                <div class="toggle-knob"></div>
              </div>
              <span>Disponível para venda</span>
            </label>
            <label class="toggle-item">
              <div class="toggle-switch" :class="{ on: form.destaque }" @click="form.destaque = !form.destaque">
                <div class="toggle-knob"></div>
              </div>
              <span>⭐ Destaque</span>
            </label>
          </div>

          <!-- Erro -->
          <p v-if="erro" class="pfm-error">{{ erro }}</p>

          <!-- Actions -->
          <div class="pfm-actions">
            <button type="button" class="btn-cancel" @click="$emit('close')">Cancelar</button>
            <button type="submit" class="btn-save" :disabled="saving">
              <svg v-if="saving" class="spin" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
              {{ saving ? 'Salvando...' : produto ? 'Salvar alterações' : 'Adicionar produto' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { api } from '../../services/api'
import type { Produto } from '../../types'

const props = defineProps<{
  lojaId: string
  produto?: Produto | null
}>()

const emit = defineEmits<{
  close: []
  saved: [produto: Produto]
}>()

interface ImagemExistente { path: string; url: string }

const imagensExistentes = ref<ImagemExistente[]>([])
const novasImagens = ref<File[]>([])
const novasPreview = ref<string[]>([])
const saving = ref(false)
const erro = ref('')

const form = reactive({
  nome: '',
  categoria: '',
  preco: '' as string | number,
  precoPromocional: '' as string | number,
  descricao: '',
  disponivel: true,
  destaque: false,
})

onMounted(() => {
  if (props.produto) {
    form.nome             = props.produto.nome
    form.categoria        = props.produto.categoria ?? ''
    form.preco            = props.produto.preco
    form.precoPromocional = props.produto.precoPromocional ?? ''
    form.descricao        = props.produto.descricao ?? ''
    form.disponivel       = props.produto.disponivel
    form.destaque         = props.produto.destaque
    // Mapeia imagens existentes
    imagensExistentes.value = (props.produto.imagens ?? []).map((url) => ({
      path: url, // na edição, o frontend recebe URLs já prontas
      url,
    }))
  }
})

function onImagens(e: Event) {
  const files = Array.from((e.target as HTMLInputElement).files ?? [])
  files.forEach(f => {
    novasImagens.value.push(f)
    const reader = new FileReader()
    reader.onload = ev => novasPreview.value.push(ev.target!.result as string)
    reader.readAsDataURL(f)
  })
}

function removerExistente(i: number) {
  imagensExistentes.value.splice(i, 1)
}

function removerNova(i: number) {
  novasImagens.value.splice(i, 1)
  novasPreview.value.splice(i, 1)
}

async function submit() {
  erro.value = ''
  saving.value = true
  try {
    const fd = new FormData()
    fd.append('nome',       form.nome)
    fd.append('categoria',  form.categoria)
    fd.append('preco',      String(form.preco))
    fd.append('descricao',  form.descricao)
    fd.append('disponivel', form.disponivel ? '1' : '0')
    fd.append('destaque',   form.destaque   ? '1' : '0')
    if (form.precoPromocional !== '' && form.precoPromocional !== null) {
      fd.append('preco_promocional', String(form.precoPromocional))
    }

    // Novas imagens
    novasImagens.value.forEach(f => fd.append('imagens[]', f))

    let result: Produto

    if (props.produto) {
      // Edição — passa paths das imagens a manter
      imagensExistentes.value.forEach(img => fd.append('imagens_manter[]', img.path))
      result = await api.postForm<Produto>(`/produtos/${props.produto.id}`, fd)
    } else {
      result = await api.postForm<Produto>(`/lojas/${props.lojaId}/produtos`, fd)
    }

    emit('saved', result)
  } catch (e: unknown) {
    erro.value = (e as Error).message ?? 'Erro ao salvar. Tente novamente.'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.pfm-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.75);
  backdrop-filter: blur(4px);
  z-index: 300;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
}
.pfm-modal {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 20px;
  width: 100%; max-width: 520px;
  max-height: 92vh; overflow-y: auto;
}
.pfm-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px 0;
  position: sticky; top: 0;
  background: var(--card);
  z-index: 1;
}
.pfm-title { font-family: var(--display); font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
.pfm-close {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--card-2); border: 1px solid var(--line);
  color: var(--muted); cursor: pointer; font-size: 13px;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s;
}
.pfm-close:hover { color: var(--cream); border-color: var(--line-strong); }

.pfm-form { padding: 20px 24px 24px; display: flex; flex-direction: column; gap: 16px; }

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; }
.req { color: var(--orange); }
.field-input {
  width: 100%; padding: 10px 12px;
  background: var(--card-2); border: 1px solid var(--line);
  border-radius: 10px; color: var(--cream); font-size: 13px;
  outline: none; font-family: inherit; resize: vertical;
  transition: border-color 0.15s;
}
.field-input:focus { border-color: var(--orange); }
.field-input::placeholder { color: var(--muted); }

.input-prefix { display: flex; align-items: center; background: var(--card-2); border: 1px solid var(--line); border-radius: 10px; overflow: hidden; transition: border-color 0.15s; }
.input-prefix:focus-within { border-color: var(--orange); }
.input-prefix span { padding: 0 10px; font-size: 12px; color: var(--muted); border-right: 1px solid var(--line); white-space: nowrap; }
.input-prefix .field-input { border: none; border-radius: 0; background: transparent; }

.fields-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }

/* Toggles */
.toggles-row { display: flex; gap: 20px; flex-wrap: wrap; }
.toggle-item { display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: var(--cream); user-select: none; }
.toggle-switch {
  width: 40px; height: 22px; border-radius: 999px;
  background: var(--card-2); border: 1px solid var(--line);
  position: relative; cursor: pointer; transition: background 0.2s, border-color 0.2s;
  flex-shrink: 0;
}
.toggle-switch.on { background: var(--orange); border-color: var(--orange); }
.toggle-knob {
  position: absolute; top: 2px; left: 2px;
  width: 16px; height: 16px; border-radius: 50%;
  background: white; transition: transform 0.2s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.3);
}
.toggle-switch.on .toggle-knob { transform: translateX(18px); }

/* Imagens */
.img-preview-row { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; }
.img-thumb {
  position: relative; width: 72px; height: 72px;
  border-radius: 10px; overflow: hidden;
  border: 1px solid var(--line);
}
.img-thumb img { width: 100%; height: 100%; object-fit: cover; }
.img-thumb-new { border-color: rgba(255,94,26,0.4); }
.img-remove {
  position: absolute; top: 3px; right: 3px;
  width: 20px; height: 20px; border-radius: 50%;
  background: rgba(0,0,0,0.7); color: white;
  font-size: 10px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.15s;
}
.img-remove:hover { background: #ff4444; }
.img-upload-btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 14px; border-radius: 10px;
  border: 1px dashed var(--line); background: var(--card-2);
  color: var(--muted); font-size: 12px; font-weight: 600;
  cursor: pointer; transition: all 0.15s;
}
.img-upload-btn:hover { border-color: var(--orange); color: var(--orange); }

/* Actions */
.pfm-actions { display: flex; gap: 10px; margin-top: 4px; }
.btn-cancel {
  flex: 1; padding: 12px; border-radius: 10px;
  border: 1px solid var(--line); background: transparent;
  color: var(--cream); font-size: 13px; font-weight: 600; cursor: pointer;
  transition: all 0.15s;
}
.btn-cancel:hover { border-color: var(--line-strong); background: var(--card-2); }
.btn-save {
  flex: 2; padding: 12px; border-radius: 10px;
  background: var(--orange); color: white;
  font-size: 13px; font-weight: 700;
  box-shadow: var(--shadow-cta-orange); cursor: pointer;
  transition: all 0.15s;
  display: flex; align-items: center; justify-content: center; gap: 7px;
}
.btn-save:hover:not(:disabled) { background: var(--orange-deep); transform: translateY(-1px); }
.btn-save:disabled { opacity: 0.65; cursor: wait; }

.pfm-error { font-size: 12px; color: #ff6b6b; padding: 8px 12px; background: rgba(255,107,107,0.08); border-radius: 8px; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.8s linear infinite; }

@media (max-width: 480px) {
  .fields-row { grid-template-columns: 1fr 1fr; }
}
</style>
