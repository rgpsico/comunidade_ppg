<template>
  <div class="star-rating" :class="[size, { readonly }]" role="group" aria-label="Avaliação por estrelas">
    <button
      v-for="i in 5"
      :key="i"
      class="star"
      :class="{ filled: i <= display, hover: !readonly && i <= hovered }"
      :disabled="readonly"
      @mouseenter="!readonly && (hovered = i)"
      @mouseleave="!readonly && (hovered = 0)"
      @click="!readonly && emit('update:nota', i)"
      :aria-label="`${i} estrela${i > 1 ? 's' : ''}`"
    >★</button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  nota: number
  readonly?: boolean
  size?: 'sm' | 'md' | 'lg'
}>()

const emit   = defineEmits<{ 'update:nota': [nota: number] }>()
const hovered = ref(0)
const display = computed(() => hovered.value || Math.round(props.nota))
</script>

<style scoped>
.star-rating { display: flex; gap: 2px; }

.star {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  font-size: 20px;
  color: var(--line-strong, #333);
  transition: color 0.1s, transform 0.1s;
  line-height: 1;
}
.star.filled, .star.hover { color: var(--yellow, #FFD23F); }
.star:not(.readonly):hover { transform: scale(1.15); }
.star-rating.readonly .star { cursor: default; }

.star-rating.sm .star { font-size: 14px; }
.star-rating.lg .star { font-size: 28px; }
</style>
