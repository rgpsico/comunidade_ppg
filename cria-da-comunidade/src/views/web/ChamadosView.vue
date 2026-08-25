<template>
  <div class="chamados fade-up">
    <div class="view-header">
      <div>
        <div class="eyebrow">🔧 comunidade em ação</div>
        <h1 class="view-title display">Chamados <span class="text-gradient-orange">Comunitários</span></h1>
        <p class="view-sub">Problemas e serviços que precisam de ajuda na comunidade.</p>
      </div>
      <button v-if="auth.user" class="btn-primary" @click="abrirModalNovo">
        + Abrir Chamado
      </button>
    </div>

    <!-- Filtros -->
    <div class="filtros">
      <div class="tabs-row">
        <button
          v-for="tab in statusTabs"
          :key="tab.value"
          class="tab-btn"
          :class="{ active: filtroStatus === tab.value }"
          @click="filtroStatus = tab.value"
        >{{ tab.label }}</button>
      </div>
      <div class="cats-row">
        <button
          v-for="cat in ['Todas', ...CATEGORIAS]"
          :key="cat"
          class="cat-chip"
          :class="{ active: filtroCategoria === cat }"
          @click="filtroCategoria = cat"
        >{{ cat }}</button>
      </div>
    </div>

    <!-- Lista -->
    <div v-if="data.loading" class="loading-msg">Carregando chamados...</div>
    <div v-else-if="chamadosFiltrados.length === 0" class="empty-state">
      <div class="empty-icon">🔧</div>
      <p>Nenhum chamado encontrado.</p>
      <button v-if="auth.user" class="btn-secondary" @click="abrirModalNovo">Abrir o primeiro chamado</button>
    </div>
    <div v-else class="chamados-grid">
      <ChamadoCard
        v-for="c in chamadosFiltrados"
        :key="c.id"
        :chamado="c"
        @click="ui.openChamado(c)"
      />
    </div>

    <!-- Modal novo chamado -->
    <Teleport to="body">
      <div v-if="modalAberto" class="modal-backdrop" @click.self="fecharModal">
        <div class="modal-sheet">
          <div class="modal-handle"></div>
          <h2 class="modal-titulo">Abrir Chamado</h2>

          <div class="form-group">
            <label>Tipo</label>
            <div class="radio-row">
              <label class="radio-opt" :class="{ active: form.tipo === 'problema' }">
                <input type="radio" v-model="form.tipo" value="problema" />
                ⚠️ Problema
              </label>
              <label class="radio-opt" :class="{ active: form.tipo === 'servico' }">
                <input type="radio" v-model="form.tipo" value="servico" />
                🔧 Serviço
              </label>
            </div>
          </div>

          <div class="form-group">
            <label>Título *</label>
            <input v-model="form.titulo" class="form-input" placeholder="Ex: Vazamento no encanamento da rua..." />
          </div>

          <div class="form-group">
            <label>Categoria *</label>
            <select v-model="form.categoria" class="form-input">
              <option value="" disabled>Selecione...</option>
              <option v-for="cat in CATEGORIAS" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Urgência</label>
            <div class="radio-row">
              <label v-for="u in urgencias" :key="u.value" class="radio-opt" :class="{ active: form.urgencia === u.value, [u.value]: true }">
                <input type="radio" v-model="form.urgencia" :value="u.value" />
                {{ u.label }}
              </label>
            </div>
          </div>

          <div class="form-group">
            <label>Descrição *</label>
            <textarea v-model="form.descricao" class="form-input" rows="3" placeholder="Descreva o problema ou serviço necessário..."></textarea>
          </div>

          <div class="form-group">
            <label>Local (opcional)</label>
            <input v-model="form.local" class="form-input" placeholder="Ex: Rua das Flores, nº 12..." />
          </div>

          <div class="form-group">
            <label>Estimativa de valor (opcional)</label>
            <input v-model="form.estimativa_valor" class="form-input" placeholder="Ex: R$ 150 a R$ 300..." />
          </div>

          <p v-if="formErro" class="form-erro">{{ formErro }}</p>

          <div class="modal-actions">
            <button class="btn-secondary" @click="fecharModal">Cancelar</button>
            <button class="btn-primary" :disabled="enviando" @click="submitChamado">
              {{ enviando ? 'Enviando...' : 'Abrir Chamado' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import { useAuthStore } from '../../stores/auth'
import ChamadoCard from '../../components/ui/ChamadoCard.vue'

const ui = useUiStore()
const data = useDataStore()
const auth = useAuthStore()

const CATEGORIAS = [
  'Beleza', 'Construção', 'Elétrica', 'Hidráulica',
  'Casa & Limpeza', 'Transporte', 'Saúde', 'Eventos', 'Segurança', 'Outros',
]

const urgencias = [
  { value: 'normal', label: '✅ Normal' },
  { value: 'urgente', label: '⚡ Urgente' },
  { value: 'critico', label: '🚨 Crítico' },
]

const statusTabs = [
  { value: '', label: 'Todos' },
  { value: 'aberto', label: 'Abertos' },
  { value: 'aceito', label: 'Aceitos' },
  { value: 'em_andamento', label: 'Em andamento' },
  { value: 'resolvido', label: 'Resolvidos' },
]

const filtroStatus = ref('')
const filtroCategoria = ref('Todas')

const chamadosFiltrados = computed(() => {
  let list = data.chamados
  if (filtroStatus.value) list = list.filter(c => c.status === filtroStatus.value)
  if (filtroCategoria.value !== 'Todas') list = list.filter(c => c.categoria === filtroCategoria.value)
  return list
})

// Modal
const modalAberto = ref(false)
const enviando = ref(false)
const formErro = ref('')

const form = reactive({
  tipo: 'problema' as 'problema' | 'servico',
  titulo: '',
  categoria: '',
  urgencia: 'normal',
  descricao: '',
  local: '',
  estimativa_valor: '',
})

function abrirModalNovo() {
  Object.assign(form, { tipo: 'problema', titulo: '', categoria: '', urgencia: 'normal', descricao: '', local: '', estimativa_valor: '' })
  formErro.value = ''
  modalAberto.value = true
  document.body.style.overflow = 'hidden'
}

function fecharModal() {
  modalAberto.value = false
  document.body.style.overflow = ''
}

async function submitChamado() {
  formErro.value = ''
  if (!form.titulo.trim()) { formErro.value = 'Título obrigatório.'; return }
  if (!form.categoria) { formErro.value = 'Selecione uma categoria.'; return }
  if (!form.descricao.trim()) { formErro.value = 'Descrição obrigatória.'; return }

  enviando.value = true
  try {
    const created = await data.criarChamado({
      tipo: form.tipo,
      titulo: form.titulo.trim(),
      descricao: form.descricao.trim(),
      categoria: form.categoria,
      urgencia: form.urgencia,
      local: form.local.trim() || undefined,
      estimativa_valor: form.estimativa_valor.trim() || undefined,
      comunidade_id: data.activeComunidadeId,
    })
    fecharModal()
    ui.openChamado(created)
  } catch (e: any) {
    formErro.value = e?.response?.data?.message ?? 'Erro ao criar chamado.'
  } finally {
    enviando.value = false
  }
}
</script>

<style scoped>
.chamados {
  padding: 40px 48px;
  max-width: 1200px;
  margin: 0 auto;
}

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 32px;
  flex-wrap: wrap;
}

.eyebrow {
  font-family: var(--mono);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--orange);
  margin-bottom: 8px;
}

.view-title { font-size: 32px; font-weight: 900; color: var(--cream); margin: 0 0 8px; }
.view-sub { font-size: 14px; color: var(--muted); margin: 0; }

.filtros { margin-bottom: 24px; display: flex; flex-direction: column; gap: 12px; }

.tabs-row { display: flex; gap: 6px; flex-wrap: wrap; }
.tab-btn {
  padding: 7px 14px;
  border-radius: var(--radius-full);
  background: transparent;
  border: 1px solid var(--line);
  color: var(--muted);
  font-size: 13px;
  cursor: pointer;
  transition: all 0.15s;
}
.tab-btn.active, .tab-btn:hover {
  background: var(--orange);
  border-color: var(--orange);
  color: #fff;
}

.cats-row { display: flex; gap: 6px; flex-wrap: wrap; }
.cat-chip {
  padding: 5px 12px;
  border-radius: var(--radius-full);
  background: var(--card);
  border: 1px solid var(--line);
  color: var(--muted);
  font-size: 12px;
  cursor: pointer;
  transition: all 0.15s;
}
.cat-chip.active, .cat-chip:hover {
  background: var(--orange);
  border-color: var(--orange);
  color: #fff;
}

.chamados-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
}

.loading-msg, .empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--muted);
}
.empty-icon { font-size: 48px; margin-bottom: 12px; }

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  z-index: 1000;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}

.modal-sheet {
  background: var(--card);
  border-radius: 20px 20px 0 0;
  padding: 24px 24px 40px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.modal-handle {
  width: 40px; height: 4px;
  background: var(--line-strong);
  border-radius: 2px;
  margin: 0 auto 8px;
}

.modal-titulo {
  font-size: 20px;
  font-weight: 800;
  color: var(--cream);
  margin: 0;
}

.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; color: var(--muted); }

.form-input {
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  padding: 10px 12px;
  color: var(--cream);
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
  resize: vertical;
}
.form-input:focus { outline: none; border-color: var(--orange); }

.radio-row { display: flex; gap: 8px; flex-wrap: wrap; }
.radio-opt {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: var(--radius-full);
  border: 1px solid var(--line);
  cursor: pointer;
  font-size: 13px;
  color: var(--muted);
  transition: all 0.15s;
}
.radio-opt input { display: none; }
.radio-opt.active { border-color: var(--orange); color: var(--orange); background: #FF5E1A11; }
.radio-opt.urgente.active { border-color: var(--yellow); color: var(--yellow); background: #FFD23F11; }
.radio-opt.critico.active { border-color: #EF4444; color: #EF4444; background: #EF444411; }

.form-erro { color: #EF4444; font-size: 13px; margin: 0; }

.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 8px; }

.btn-primary {
  padding: 10px 22px;
  background: var(--orange);
  color: #fff;
  border: none;
  border-radius: var(--radius-full);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: filter 0.15s;
}
.btn-primary:hover { filter: brightness(1.1); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-secondary {
  padding: 10px 22px;
  background: transparent;
  color: var(--muted);
  border: 1px solid var(--line);
  border-radius: var(--radius-full);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}
.btn-secondary:hover { border-color: var(--muted); color: var(--cream); }

@media (max-width: 768px) {
  .chamados { padding: 20px 16px; }
  .chamados-grid { grid-template-columns: 1fr; }
}
</style>
