<template>
  <Teleport to="body">
    <div class="lpm-overlay" @click.self="$emit('close')">
      <form class="lpm-box" @submit.prevent="salvar">
        <header class="lpm-head">
          <div>
            <h2>Políticas da loja</h2>
            <p>O assistente dos produtos usa só estas informações para responder os clientes. O que ficar vazio, ele diz que a loja não informou.</p>
          </div>
          <button type="button" class="lpm-close" @click="$emit('close')">✕</button>
        </header>

        <label class="lpm-field">
          <span>Garantia padrão</span>
          <input v-model="form.garantia_padrao" maxlength="300" placeholder="Ex: 90 dias contra defeito de fábrica" />
          <small>Vale para todos os produtos. Dá para colocar outra garantia em cada produto.</small>
        </label>

        <label class="lpm-field">
          <span>Prazo de entrega</span>
          <input v-model="form.prazo_entrega" maxlength="300" placeholder="Ex: no mesmo dia para pedidos até 16h" />
        </label>

        <label class="lpm-field">
          <span>Entrega, retirada e taxas</span>
          <textarea v-model="form.entrega_info" rows="3" maxlength="2000" placeholder="Ex: Entregamos na Tabajaras e Copacabana por R$ 10. Retirada grátis na loja." />
        </label>

        <label class="lpm-field">
          <span>Trocas e devoluções</span>
          <textarea v-model="form.trocas_info" rows="2" maxlength="2000" placeholder="Ex: Troca em até 7 dias com a caixa e a nota" />
        </label>

        <label class="lpm-field">
          <span>Formas de pagamento</span>
          <input v-model="form.pagamento_info" maxlength="300" placeholder="Ex: PIX, dinheiro e cartão em até 10x" />
        </label>

        <div v-if="erro" class="lpm-erro">{{ erro }}</div>

        <footer class="lpm-foot">
          <button type="button" class="lpm-cancel" @click="$emit('close')">Cancelar</button>
          <button type="submit" class="lpm-save" :disabled="salvando">{{ salvando ? 'Salvando…' : 'Salvar' }}</button>
        </footer>
      </form>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { api } from '../../services/api'
import type { Loja } from '../../types'

const props = defineProps<{ loja: Loja }>()
const emit = defineEmits<{ close: []; saved: [Partial<Loja>] }>()

const form = reactive({
  garantia_padrao: props.loja.garantiaPadrao,
  prazo_entrega:   props.loja.prazoEntrega,
  entrega_info:    props.loja.entregaInfo,
  trocas_info:     props.loja.trocasInfo,
  pagamento_info:  props.loja.pagamentoInfo,
})
const salvando = ref(false)
const erro = ref('')

async function salvar() {
  salvando.value = true
  erro.value = ''
  try {
    await api.put(`/lojas/${props.loja.id}`, form)
    emit('saved', {
      garantiaPadrao: form.garantia_padrao,
      prazoEntrega:   form.prazo_entrega,
      entregaInfo:    form.entrega_info,
      trocasInfo:     form.trocas_info,
      pagamentoInfo:  form.pagamento_info,
    })
  } catch (e: any) {
    erro.value = e?.message || 'Não foi possível salvar.'
  } finally {
    salvando.value = false
  }
}
</script>

<style scoped>
.lpm-overlay { position: fixed; inset: 0; z-index: 1000; background: rgba(0, 0, 0, .6); display: flex; align-items: flex-end; justify-content: center; }
@media (min-width: 640px) { .lpm-overlay { align-items: center; } }
.lpm-box {
  width: 100%; max-width: 520px; max-height: 92vh; overflow-y: auto;
  background: var(--card); border: 1px solid var(--line); border-radius: 18px 18px 0 0;
  padding: 20px; display: flex; flex-direction: column; gap: 14px;
}
@media (min-width: 640px) { .lpm-box { border-radius: 18px; } }
.lpm-head { display: flex; gap: 12px; align-items: flex-start; }
.lpm-head h2 { margin: 0; font-size: 18px; color: var(--cream); }
.lpm-head p { margin: 4px 0 0; font-size: 13px; color: var(--muted); line-height: 1.45; }
.lpm-close { margin-left: auto; background: none; border: none; color: var(--muted); font-size: 18px; cursor: pointer; }
.lpm-field { display: flex; flex-direction: column; gap: 6px; }
.lpm-field span { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
.lpm-field small { font-size: 12px; color: var(--muted); }
.lpm-field input, .lpm-field textarea {
  padding: 10px 12px; border-radius: 10px; font-size: 15px; font-family: inherit; resize: vertical;
  background: var(--bg); color: var(--cream); border: 1px solid var(--line); outline: none;
}
.lpm-field input:focus, .lpm-field textarea:focus { border-color: var(--orange); }
.lpm-erro { color: #F87171; font-size: 13px; }
.lpm-foot { display: flex; gap: 10px; justify-content: flex-end; }
.lpm-cancel, .lpm-save { padding: 10px 18px; border-radius: 10px; font-weight: 700; cursor: pointer; border: 1px solid var(--line); }
.lpm-cancel { background: transparent; color: var(--cream); }
.lpm-save { background: var(--orange); color: #fff; border-color: var(--orange); }
.lpm-save:disabled { opacity: .6; }
</style>
