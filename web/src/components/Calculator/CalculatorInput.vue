<template>
  <div class="calculator-input">
    <q-input
      v-bind="$attrs"
      :model-value="valueToString(modelValue)"
      @update:model-value="handleInput"
      ref="input"
      @focus="isCalculatorVisible = true"
      @blur="onBlur"
      @keydown="onKeydown"
      inputmode="decimal"
      pattern="[0-9+\-\*\.=,]*"
    />
    <calculator-widget 
      v-show="isCalculatorVisible" 
      :value="valueToString(modelValue)" 
      @update="value => $emit('update:modelValue', value)"
      @focus="focusInput"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { QInput } from 'quasar'
import CalculatorWidget from './CalculatorWidget.vue'
import { sanitizeInput, evaluateFormula, validateFormula } from '../../modules/helpers/calculator'

interface Props {
  modelValue?: string | number
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: ''
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const valueToString = (val: string | number | undefined | null): string => {
  if (val === undefined || val === null) return ''
  return val.toString()
}

const input = ref<InstanceType<typeof QInput>>()
const isCalculatorVisible = ref(false)

const handleInput = (value: string | number | null) => {
  const stringValue = value?.toString() ?? ''
  
  // Calculate if "=" is entered
  if (stringValue.endsWith('=')) {
    const sanitized = sanitizeInput(stringValue.slice(0, -1)) // Remove the "=" for calculation
    if (validateFormula(sanitized)) {
      const result = evaluateFormula(sanitized + '=')
      emit('update:modelValue', result)
      return
    }
  }
  
  emit('update:modelValue', stringValue)
}

const onKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Enter') {
    const value = props.modelValue?.toString() || ''
    const sanitized = sanitizeInput(value)
    
    // Check if it's a formula (contains operators)
    if (/[+\-*/]/.test(sanitized)) {
      if (validateFormula(sanitized)) {
        e.preventDefault() // Prevent form submission for formulas
        const result = evaluateFormula(sanitized + '=')
        emit('update:modelValue', result)
      }
    }
    // If it's just a number, let the form submit naturally (don't call preventDefault)
  }
}

const onBlur = (e: Event) => {
  const target = e as FocusEvent
  // Don't hide calculator if clicking calculator buttons
  if (target.relatedTarget && (target.relatedTarget as HTMLElement).closest('.calculator-widget')) {
    input.value?.focus()
    return
  }
  isCalculatorVisible.value = false
}

const focusInput = () => {
  input.value?.focus()
}
</script>

<style lang="scss" scoped>
.calculator-input {
  position: relative;
  width: 100%;
}
</style> 