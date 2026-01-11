<template>
  <q-card
    class="calculator-widget"
    :style="{
      position: 'fixed',
      left: `${position.x}px`,
      top: `${position.y}px`,
      zIndex: isDragging ? 9999 : 1000,
      cursor: isDragging ? 'grabbing' : 'default',
      transform: isDragging ? 'scale(1.02)' : 'scale(1)'
    }"
    ref="calculatorRef"
  >
    <div class="calculator-content">
      <!-- Calculator buttons -->
      <div class="calculator-grid">
        <q-btn 
          v-for="btn in buttons" 
          :key="btn"
          :label="btn"
          class="calculator-btn"
          flat
          dense
          tabindex="0"
          @click="update(btn)"
          @keyup.enter="update(btn)"
        />
      </div>
      
      <!-- Drag handle moved to the right -->
      <div 
        class="drag-handle"
        @mousedown="startDrag"
        @touchstart="startDrag"
        :style="{ cursor: isDragging ? 'grabbing' : 'grab' }"
      >
        <q-icon name="drag_indicator" size="20px" />
      </div>
    </div>
  </q-card>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useQuasar, QCard } from 'quasar'
import { sanitizeInput, evaluateFormula } from '../../modules/helpers/calculator'

const STORAGE_KEY_PREFIX = 'calculator-position'
const buttons = ['+', '−', '×', '÷', '=']

interface Position {
  x: number
  y: number
}

const props = defineProps<{
  value: string | number
}>()

const emit = defineEmits<{
  (e: 'update', value: string): void
  (e: 'focus'): void
}>()

const $q = useQuasar()
const calculatorRef = ref<QCard>()
const position = ref<Position>({ x: 0, y: 0 })
const isDragging = ref(false)
const dragOffset = ref({ x: 0, y: 0 })

// Get storage key based on current screen resolution
const getStorageKey = () => {
  return `${STORAGE_KEY_PREFIX}-${window.innerWidth}x${window.innerHeight}`
}

// Get current viewport height (handles iOS keyboard)
const getViewportHeight = (): number => {
  return window.visualViewport?.height || window.innerHeight
}

// Update position on resize or viewport changes
const updatePosition = () => {
  loadSavedPosition()
}

const calculateCenterPosition = (): Position => {
  if (!calculatorRef.value?.$el) return { x: 0, y: 0 }
  
  const el = calculatorRef.value.$el
  const width = el.offsetWidth || 360 // fallback to min width if not available
  const height = el.offsetHeight || 48 // fallback to content height if not available
  
  return {
    x: Math.max(0, Math.floor((window.innerWidth - width) / 2)),
    y: Math.max(0, Math.floor((getViewportHeight() - height) / 2))
  }
}

const loadSavedPosition = () => {
  if (!calculatorRef.value?.$el) return
  
  const savedPosition = localStorage.getItem(getStorageKey())
  if (savedPosition) {
    try {
      position.value = JSON.parse(savedPosition)
      validatePosition()
      localStorage.setItem(getStorageKey(), JSON.stringify(position.value))
    } catch {
      position.value = calculateCenterPosition()
      localStorage.setItem(getStorageKey(), JSON.stringify(position.value))
    }
  } else {
    position.value = calculateCenterPosition()
    localStorage.setItem(getStorageKey(), JSON.stringify(position.value))
  }
}

onMounted(() => {
  window.addEventListener('resize', updatePosition)
  window.visualViewport?.addEventListener('resize', updatePosition)
  // Force a layout reflow to get correct dimensions
  if (calculatorRef.value?.$el) {
    calculatorRef.value.$el.offsetHeight // force reflow
  }
  // Wait for next tick to ensure calculator is rendered with correct dimensions
  nextTick(() => {
    loadSavedPosition()
  })
})

const validatePosition = () => {
  if (!calculatorRef.value?.$el) return

  const maxX = window.innerWidth - calculatorRef.value.$el.offsetWidth
  const maxY = getViewportHeight() - calculatorRef.value.$el.offsetHeight

  position.value = {
    x: Math.max(0, Math.min(position.value.x, maxX)),
    y: Math.max(0, Math.min(position.value.y, maxY))
  }
}

const startDrag = (e: MouseEvent | TouchEvent) => {
  if (!calculatorRef.value?.$el) return
  
  e.preventDefault() // Prevent text selection and scrolling
  isDragging.value = true
  
  const rect = calculatorRef.value.$el.getBoundingClientRect()

  const clientX = e instanceof MouseEvent ? e.clientX : e.touches[0].clientX
  const clientY = e instanceof MouseEvent ? e.clientY : e.touches[0].clientY

  dragOffset.value = {
    x: clientX - rect.left,
    y: clientY - rect.top
  }

  // Add both mouse and touch event listeners
  document.addEventListener('mousemove', onDrag)
  document.addEventListener('touchmove', onDrag, { passive: false })
  document.addEventListener('mouseup', stopDrag)
  document.addEventListener('touchend', stopDrag)
}

const onDrag = (e: MouseEvent | TouchEvent) => {
  if (!isDragging.value || !calculatorRef.value?.$el) return

  e.preventDefault() // Prevent scrolling during drag

  const clientX = e instanceof MouseEvent ? e.clientX : e.touches[0].clientX
  const clientY = e instanceof MouseEvent ? e.clientY : e.touches[0].clientY

  // Calculate new position with bounds checking
  const newX = Math.max(0, Math.min(
    window.innerWidth - calculatorRef.value.$el.offsetWidth,
    clientX - dragOffset.value.x
  ))
  
  const newY = Math.max(0, Math.min(
    getViewportHeight() - calculatorRef.value.$el.offsetHeight,
    clientY - dragOffset.value.y
  ))

  position.value = { x: newX, y: newY }
  localStorage.setItem(getStorageKey(), JSON.stringify(position.value))
}

const stopDrag = () => {
  isDragging.value = false
  // Remove both mouse and touch event listeners
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('touchmove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchend', stopDrag)
}

const update = (operation: string) => {
  const operationMap: Record<string, string> = {
    '×': '*',
    '÷': '/',
    '−': '-'
  }

  const newValue = props.value.toString() + (operationMap[operation] || operation)
  emit('update', sanitizeInput(newValue))
  emit('focus')
}

// Watch for changes in value prop
watch(() => props.value, (newValue) => {
  if (newValue?.toString().endsWith('=')) {
    const sanitized = sanitizeInput(newValue.toString())
    const result = evaluateFormula(sanitized)
    emit('update', result)
  }
}, { immediate: true })

onUnmounted(() => {
  // Clean up all event listeners
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('touchmove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchend', stopDrag)
  window.removeEventListener('resize', updatePosition)
  window.visualViewport?.removeEventListener('resize', updatePosition)
})
</script>

<style lang="scss" scoped>
.calculator-widget {
  width: 320px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  user-select: none;
  touch-action: none;
  transition: transform 0.2s ease;
  
  // Hide on desktop
  @media (min-width: 1024px) {
    display: none;
  }
  
  &:active {
    transition: none;
  }

  .calculator-content {
    height: 48px;
    display: flex;
    align-items: center;
  }
  
  .drag-handle {
    padding: 0 4px;
    height: 100%;
    display: flex;
    align-items: center;
    border-left: 1px solid #eee;
    
    &:hover {
      background: #f0f2f5;
    }
  }

  .calculator-grid {
    display: flex;
    align-items: center;
    padding: 0 8px;
    flex: 1;
  }

  .calculator-btn {
    min-width: 48px;
    height: 40px;
    font-size: 1.2rem;
    border: 1px solid #eee;
    border-radius: 4px;
    margin: 0 4px;
    
    &:hover {
      background: #f0f2f5;
      transform: translateY(-1px);
    }

    &:active {
      transform: translateY(0);
    }
  }
}
</style> 