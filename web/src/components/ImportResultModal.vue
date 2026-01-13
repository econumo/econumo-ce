<template>
  <q-dialog :model-value="true" :no-backdrop-dismiss="$q.screen.gt.md">
    <div class="econumo-modal import-result-modal">
      <div class="import-result-modal-icon">
        <q-icon
          :name="resultIcon"
          :color="resultColor"
          size="48px"
        />
      </div>

      <div class="import-result-modal-title">
        {{ header }}
      </div>

      <div class="import-result-modal-summary">
        <div v-if="imported > 0" class="import-result-stat success">
          <q-icon name="check_circle" size="20px" />
          <span>{{ $t('modals.import_result.imported', { count: imported }) }}</span>
        </div>
        <div v-if="failed > 0" class="import-result-stat error">
          <q-icon name="error" size="20px" />
          <span>{{ $t('modals.import_result.failed', { count: failed }) }}</span>
        </div>
      </div>

      <div v-if="errors.length > 0" class="import-result-modal-errors">
        <div class="import-result-modal-errors-title">
          {{ $t('modals.import_result.errors_detail') }}:
        </div>
        <div class="import-result-modal-errors-list">
          <div
            v-for="(error, index) in displayedErrors"
            :key="index"
            class="import-result-error-item"
          >
            <span class="error-message">{{ error.message }}</span>
            <span class="error-rows">{{ formatRows(error.rows) }}</span>
          </div>
          <div v-if="errors.length > maxDisplayErrors" class="import-result-more-errors">
            {{ $t('modals.import_result.and_more', { count: errors.length - maxDisplayErrors }) }}
          </div>
        </div>
      </div>

      <div class="import-result-modal-button">
        <q-btn
          class="econumo-btn -small -yellow full-width"
          :label="$t('elements.button.ok.label')"
          type="button"
          @click="close"
          flat
        />
      </div>
    </div>
  </q-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useQuasar } from 'quasar'

const $q = useQuasar()

defineOptions({
  name: 'ImportResultModal'
})

const { t } = useI18n()

interface ImportError {
  message: string
  rows: number[]
}

const props = defineProps<{
  imported: number
  failed: number
  errors: ImportError[]
}>()

const emit = defineEmits(['close'])

const maxDisplayErrors = 5

const displayedErrors = computed(() => {
  return props.errors.slice(0, maxDisplayErrors)
})

function formatRows(rows: number[]): string {
  if (rows.length === 0) return ''
  if (rows.length === 1) return `${t('modals.import_result.row')} ${rows[0]}`

  const maxDisplay = 10
  if (rows.length <= maxDisplay) {
    return `${t('modals.import_result.rows')}: ${rows.join(', ')}`
  }

  const displayed = rows.slice(0, maxDisplay).join(', ')
  const remaining = rows.length - maxDisplay
  return `${t('modals.import_result.rows')}: ${displayed}, +${remaining} ${t('modals.import_result.more')}`
}

const resultIcon = computed(() => {
  if (props.failed === 0) {
    return 'check_circle'
  } else if (props.imported > 0) {
    return 'warning'
  } else {
    return 'error'
  }
})

const resultColor = computed(() => {
  if (props.failed === 0) {
    return 'positive'
  } else if (props.imported > 0) {
    return 'warning'
  } else {
    return 'negative'
  }
})

const header = computed(() => {
  if (props.failed === 0) {
    return t('modals.import_result.success_title')
  } else if (props.imported > 0) {
    return t('modals.import_result.partial_success_title')
  } else {
    return t('modals.import_result.error_title')
  }
})

function close() {
  emit('close')
}
</script>

<style scoped lang="scss">
.import-result-modal {
  padding: 32px 24px 24px;
  text-align: center;
}

.import-result-modal-icon {
  margin-bottom: 16px;
}

.import-result-modal-title {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin-bottom: 16px;
}

.import-result-modal-summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 16px;
}

.import-result-stat {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 14px;

  &.success {
    color: #21ba45;
  }

  &.error {
    color: #c10015;
  }
}

.import-result-modal-errors {
  margin-bottom: 24px;
  padding: 16px;
  background: #f5f5f5;
  border-radius: 8px;
  text-align: left;
}

.import-result-modal-errors-title {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 12px;
}

.import-result-modal-errors-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.import-result-error-item {
  font-size: 13px;
  color: #555;
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e0e0e0;

  &:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
}

.error-message {
  font-weight: 600;
  color: #c10015;
  word-break: break-word;
}

.error-rows {
  font-size: 12px;
  color: #999;
}

.import-result-more-errors {
  font-size: 13px;
  color: #999;
  font-style: italic;
  margin-top: 4px;
}

.import-result-modal-button {
  margin-top: 24px;
}
</style>
