<template>
  <teleport to="body">
    <q-dialog class="responsive-modal" v-model="isModalOpened" no-backdrop-dismiss @hide="handleDialogHide">
      <q-card class="responsive-modal-card import-csv-modal">
        <q-form
          ref="importForm"
          @submit="handleSubmit"
          @reset="closeModal"
          class="responsive-modal-form"
        >
          <q-card-section>
            <div class="settings-toolbar-mobile">
              <div>
                <h4 class="settings-toolbar-mobile-title">{{ $t('modals.import_csv.header') }}</h4>
              </div>
            </div>

            <div class="responsive-modal-header">{{ $t('modals.import_csv.header') }}</div>

            <!-- File Upload -->
            <div class="import-csv-upload">
              <div v-if="!csvFile" class="responsive-modal-control">
                <q-file
                  v-model="csvFile"
                  :label="$t('modals.import_csv.file.label')"
                  outlined
                  accept=".csv"
                  max-file-size="10485760"
                  class="form-input full-width"
                  @update:model-value="handleFileSelect"
                  :rules="[
                    (val: File | null) => !!val || $t('elements.validation.required_field')
                  ]"
                >
                  <template v-slot:prepend>
                    <q-icon name="attach_file" />
                  </template>
                  <template v-slot:hint>
                    {{ $t('modals.import_csv.file.hint') }}
                  </template>
                </q-file>
              </div>

              <div v-else class="import-csv-file-selected">
                <div class="import-csv-file-info">
                  <q-icon name="description" size="20px" color="primary" />
                  <span class="import-csv-file-name">{{ csvFile.name }}</span>
                </div>
                <q-btn
                  flat
                  dense
                  size="sm"
                  color="primary"
                  :label="$t('elements.button.change.label')"
                  @click="csvFile = null; csvColumns = []"
                />
              </div>
            </div>

            <!-- Field Mapping -->
            <div v-if="csvFile && csvColumns.length > 0" class="import-csv-mapping">
              <div class="import-csv-mapping-description">
                {{ $t('modals.import_csv.mapping.description') }}
              </div>

              <!-- Account Field with Mode Toggle -->
              <div class="import-csv-account-row">
                <div class="responsive-modal-control import-csv-account-field">
                  <q-select
                    v-model="fieldMapping.account"
                    :options="accountSelectOptions"
                    :label="$t('modals.import_csv.fields.account') + ' *'"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                    :rules="[
                      (val: string) => !!val || $t('elements.validation.required_field')
                    ]"
                  >
                    <template v-slot:prepend>
                      <q-icon name="account_balance" />
                    </template>
                  </q-select>
                </div>
                <q-btn
                  flat
                  round
                  dense
                  :icon="accountMode === 'csv_column' ? 'swap_horiz' : 'description'"
                  color="primary"
                  @click="handleAccountModeChange(accountMode === 'csv_column' ? 'existing_account' : 'csv_column')"
                  class="import-csv-account-switch"
                >
                  <q-tooltip>{{ $t(accountMode === 'csv_column' ? 'modals.import_csv.switch_to_manual' : 'modals.import_csv.switch_to_csv') }}</q-tooltip>
                </q-btn>
              </div>

              <!-- Date Field with Mode Toggle -->
              <div class="import-csv-field-row">
                <div class="responsive-modal-control import-csv-field-control">
                  <q-select
                    v-if="dateMode === 'csv_column'"
                    v-model="fieldMapping.date"
                    :options="csvColumnOptions"
                    :label="$t('modals.import_csv.fields.date') + ' *'"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                    :rules="[
                      (val: string) => !!val || $t('elements.validation.required_field')
                    ]"
                  >
                    <template v-slot:prepend>
                      <q-icon name="event" />
                    </template>
                  </q-select>
                  <q-input
                    v-else
                    v-model="manualDate"
                    :label="$t('modals.import_csv.fields.date') + ' *'"
                    placeholder="YYYY-MM-DD"
                    outlined
                    mask="####-##-##"
                    class="form-input full-width"
                    :rules="[
                      (val: string) => !!val || $t('elements.validation.required_field'),
                      (val: string) => /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/.test(val) || $t('elements.validation.invalid_date')
                    ]"
                  >
                    <template v-slot:prepend>
                      <q-icon name="event" />
                    </template>
                  </q-input>
                </div>
                <q-btn
                  flat
                  round
                  dense
                  :icon="dateMode === 'csv_column' ? 'swap_horiz' : 'description'"
                  color="primary"
                  @click="handleDateModeChange(dateMode === 'csv_column' ? 'manual' : 'csv_column')"
                  class="import-csv-field-switch"
                >
                  <q-tooltip>{{ $t(dateMode === 'csv_column' ? 'modals.import_csv.switch_to_manual' : 'modals.import_csv.switch_to_csv') }}</q-tooltip>
                </q-btn>
              </div>

              <!-- Amount Field with Inline Toggle -->
              <div v-if="amountMode === 'single'" class="import-csv-amount-row">
                <div class="responsive-modal-control import-csv-amount-field">
                  <q-select
                    v-model="fieldMapping.amount"
                    :options="csvColumnOptionsWithNone"
                    :label="$t('modals.import_csv.fields.amount') + ' *'"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                    :rules="[
                      (val: string) => !!val || $t('elements.validation.required_field')
                    ]"
                  >
                    <template v-slot:prepend>
                      <q-icon name="payments" />
                    </template>
                  </q-select>
                </div>
                <q-btn
                  flat
                  round
                  dense
                  icon="call_split"
                  color="primary"
                  @click="amountMode = 'dual'; handleAmountModeChange('dual')"
                  class="import-csv-amount-switch"
                >
                  <q-tooltip>{{ $t('modals.import_csv.amount_mode.switch_to_dual') }}</q-tooltip>
                </q-btn>
              </div>

              <!-- Option 2: Separate Inflow/Outflow Fields -->
              <div v-if="amountMode === 'dual'" class="import-csv-dual-amounts">
                <div class="import-csv-amount-row">
                  <div class="responsive-modal-control import-csv-amount-field">
                    <q-select
                      v-model="fieldMapping.amountInflow"
                      :options="csvColumnOptionsWithNone"
                      :label="$t('modals.import_csv.fields.amount_inflow') + ' *'"
                      outlined
                      emit-value
                      map-options
                      class="form-select full-width"
                      :rules="[
                        (val: string) => !!val || $t('elements.validation.required_field')
                      ]"
                    >
                      <template v-slot:prepend>
                        <q-icon name="arrow_downward" color="positive" />
                      </template>
                    </q-select>
                  </div>
                  <q-btn
                    flat
                    round
                    dense
                    icon="call_merge"
                    color="primary"
                    @click="amountMode = 'single'; handleAmountModeChange('single')"
                    class="import-csv-amount-switch"
                  >
                    <q-tooltip>{{ $t('modals.import_csv.amount_mode.switch_to_single') }}</q-tooltip>
                  </q-btn>
                </div>

                <div class="responsive-modal-control import-csv-outflow-field">
                  <q-select
                    v-model="fieldMapping.amountOutflow"
                    :options="csvColumnOptionsWithNone"
                    :label="$t('modals.import_csv.fields.amount_outflow') + ' *'"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                    :rules="[
                      (val: string) => !!val || $t('elements.validation.required_field')
                    ]"
                  >
                    <template v-slot:prepend>
                      <q-icon name="arrow_upward" color="negative" />
                    </template>
                  </q-select>
                </div>
              </div>

              <div class="import-csv-section-break"></div>

              <!-- Category Field with Mode Toggle -->
              <div class="import-csv-field-row">
                <div class="responsive-modal-control import-csv-field-control">
                  <q-select
                    v-model="fieldMapping.category"
                    :options="categorySelectOptions"
                    :label="$t('modals.import_csv.fields.category')"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                  >
                    <template v-slot:prepend>
                      <q-icon name="category" />
                    </template>
                  </q-select>
                </div>
                <q-btn
                  flat
                  round
                  dense
                  :icon="categoryMode === 'csv_column' ? 'swap_horiz' : 'description'"
                  color="primary"
                  @click="handleCategoryModeChange(categoryMode === 'csv_column' ? 'existing_entity' : 'csv_column')"
                  class="import-csv-field-switch"
                >
                  <q-tooltip>{{ $t(categoryMode === 'csv_column' ? 'modals.import_csv.switch_to_manual' : 'modals.import_csv.switch_to_csv') }}</q-tooltip>
                </q-btn>
              </div>

              <!-- Description Field with Mode Toggle -->
              <div class="import-csv-field-row">
                <div class="responsive-modal-control import-csv-field-control">
                  <q-select
                    v-if="descriptionMode === 'csv_column'"
                    v-model="fieldMapping.description"
                    :options="csvColumnOptionsWithNone"
                    :label="$t('modals.import_csv.fields.description')"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                  >
                    <template v-slot:prepend>
                      <q-icon name="description" />
                    </template>
                  </q-select>
                  <q-input
                    v-else
                    v-model="manualDescription"
                    :label="$t('modals.import_csv.fields.description')"
                    :placeholder="$t('modals.import_csv.fields.description_placeholder')"
                    outlined
                    class="form-input full-width"
                  />
                </div>
                <q-btn
                  flat
                  round
                  dense
                  :icon="descriptionMode === 'csv_column' ? 'swap_horiz' : 'description'"
                  color="primary"
                  @click="handleDescriptionModeChange(descriptionMode === 'csv_column' ? 'manual' : 'csv_column')"
                  class="import-csv-field-switch"
                >
                  <q-tooltip>{{ $t(descriptionMode === 'csv_column' ? 'modals.import_csv.switch_to_manual' : 'modals.import_csv.switch_to_csv') }}</q-tooltip>
                </q-btn>
              </div>

              <!-- Payee Field with Mode Toggle -->
              <div class="import-csv-field-row">
                <div class="responsive-modal-control import-csv-field-control">
                  <q-select
                    v-model="fieldMapping.payee"
                    :options="payeeSelectOptions"
                    :label="$t('modals.import_csv.fields.payee')"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                  >
                    <template v-slot:prepend>
                      <q-icon name="person" />
                    </template>
                  </q-select>
                </div>
                <q-btn
                  flat
                  round
                  dense
                  :icon="payeeMode === 'csv_column' ? 'swap_horiz' : 'description'"
                  color="primary"
                  @click="handlePayeeModeChange(payeeMode === 'csv_column' ? 'existing_entity' : 'csv_column')"
                  class="import-csv-field-switch"
                >
                  <q-tooltip>{{ $t(payeeMode === 'csv_column' ? 'modals.import_csv.switch_to_manual' : 'modals.import_csv.switch_to_csv') }}</q-tooltip>
                </q-btn>
              </div>

              <!-- Tag Field with Mode Toggle -->
              <div class="import-csv-field-row">
                <div class="responsive-modal-control import-csv-field-control">
                  <q-select
                    v-model="fieldMapping.tag"
                    :options="tagSelectOptions"
                    :label="$t('modals.import_csv.fields.tag')"
                    outlined
                    emit-value
                    map-options
                    class="form-select full-width"
                  >
                    <template v-slot:prepend>
                      <q-icon name="label" />
                    </template>
                  </q-select>
                </div>
                <q-btn
                  flat
                  round
                  dense
                  :icon="tagMode === 'csv_column' ? 'swap_horiz' : 'description'"
                  color="primary"
                  @click="handleTagModeChange(tagMode === 'csv_column' ? 'existing_entity' : 'csv_column')"
                  class="import-csv-field-switch"
                >
                  <q-tooltip>{{ $t(tagMode === 'csv_column' ? 'modals.import_csv.switch_to_manual' : 'modals.import_csv.switch_to_csv') }}</q-tooltip>
                </q-btn>
              </div>
            </div>
          </q-card-section>

          <!-- Progress indicator for chunked uploads -->
          <q-card-section v-if="isImporting && isChunkedImport" class="import-csv-progress-section">
            <q-linear-progress
              :value="uploadProgress.percentage / 100"
              size="8px"
              color="primary"
              class="import-csv-progress-bar"
            />
            <div class="import-csv-progress-percentage">
              {{ Math.round(uploadProgress.percentage) }}%
            </div>
          </q-card-section>

          <q-card-actions class="responsive-modal-actions">
            <q-btn
              class="econumo-btn -medium -grey responsive-modal-actions-button"
              flat
              :label="$t('elements.button.cancel.label')"
              @click="closeModal"
            />
            <q-btn
              class="econumo-btn -medium -magenta responsive-modal-actions-button"
              flat
              :label="$t('elements.button.import.label')"
              type="submit"
              :disable="!csvFile || isImporting"
              :loading="isImporting"
            />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>

    <ImportResultModal
      v-if="showResultModal"
      :imported="importResult.imported"
      :failed="importResult.failed"
      :errors="importResult.errors"
      @close="handleResultModalClose"
    />
  </teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import transaction from '../modules/api/v1/transaction'
import ImportResultModal from './ImportResultModal.vue'
import { useSyncStore } from '../stores/sync'
import { useAccountsStore } from '../stores/accounts'
import { useCategoriesStore } from '../stores/categories'
import { usePayeesStore } from '../stores/payees'
import { useTagsStore } from '../stores/tags'
import { useUsersStore } from '../stores/users'
import type { Id } from '../modules/types'

const { t } = useI18n()
const syncStore = useSyncStore()
const accountsStore = useAccountsStore()
const categoriesStore = useCategoriesStore()
const payeesStore = usePayeesStore()
const tagsStore = useTagsStore()
const usersStore = useUsersStore()

const emit = defineEmits<{
  cancel: []
  import: [config: ImportConfig]
}>()

interface ImportConfig {
  file: File
  mapping: {
    account: string
    date: string
    amount: string | null
    amountInflow: string | null
    amountOutflow: string | null
    category: string | null
    description: string | null
    payee: string | null
    tag: string | null
  }
}

const isModalOpened = ref(true)
const csvFile = ref<File | null>(null)
const csvColumns = ref<string[]>([])
const csvSampleValues = ref<Record<string, string[]>>({})
const importForm = ref()
const amountMode = ref<'single' | 'dual'>('single')
const accountMode = ref<'csv_column' | 'existing_account'>('csv_column')
const dateMode = ref<'csv_column' | 'manual'>('csv_column')
const categoryMode = ref<'csv_column' | 'existing_entity'>('csv_column')
const descriptionMode = ref<'csv_column' | 'manual'>('csv_column')
const payeeMode = ref<'csv_column' | 'existing_entity'>('csv_column')
const tagMode = ref<'csv_column' | 'existing_entity'>('csv_column')
const manualDate = ref<string>('')
const manualDescription = ref<string>('')

// Store last selected CSV columns for each field
const lastCsvColumnAccount = ref<string>('')
const lastCsvColumnDate = ref<string>('')
const lastCsvColumnCategory = ref<string | null>(null)
const lastCsvColumnDescription = ref<string | null>(null)
const lastCsvColumnPayee = ref<string | null>(null)
const lastCsvColumnTag = ref<string | null>(null)

const isImporting = ref(false)
const showResultModal = ref(false)
const importResult = ref({
  imported: 0,
  failed: 0,
  errors: [] as Array<{ message: string; rows: number[] }>
})
const pendingImportConfig = ref<ImportConfig | null>(null)

// Chunking and progress tracking
const CHUNK_SIZE = 500 // rows per chunk
const uploadProgress = ref({
  currentChunk: 0,
  totalChunks: 0,
  percentage: 0
})
const isChunkedImport = ref(false)

const fieldMapping = ref({
  account: '',
  date: '',
  amount: null as string | null,
  amountInflow: null as string | null,
  amountOutflow: null as string | null,
  category: null as string | null,
  description: null as string | null,
  payee: null as string | null,
  tag: null as string | null
})

/**
 * Check if a string looks like a date
 */
function isDateLike(value: string): boolean {
  // Check for common date patterns (ISO, US, EU formats)
  const datePatterns = [
    /^\d{4}-\d{2}-\d{2}/, // YYYY-MM-DD
    /^\d{2}\/\d{2}\/\d{4}/, // MM/DD/YYYY or DD/MM/YYYY
    /^\d{2}\.\d{2}\.\d{4}/, // DD.MM.YYYY
    /^\d{4}\/\d{2}\/\d{2}/, // YYYY/MM/DD
  ]
  return datePatterns.some(pattern => pattern.test(value))
}

/**
 * Format a date-like string to YYYY-MM-DD
 */
function formatDateSample(value: string): string {
  try {
    // Try to parse the date
    const date = new Date(value)
    if (!isNaN(date.getTime())) {
      // Format as YYYY-MM-DD
      const year = date.getFullYear()
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const day = String(date.getDate()).padStart(2, '0')
      return `${year}-${month}-${day}`
    }
  } catch (e) {
    // If parsing fails, return original
  }
  return value
}

const csvColumnOptions = computed(() => {
  return csvColumns.value
    .filter(col => col && col.trim()) // Filter out empty column names
    .map(col => {
      const samples = csvSampleValues.value[col] || []
      let label = col

      if (samples.length > 0 && samples[0]) {
        const sample = samples[0]
        // Format dates to YYYY-MM-DD if it looks like a date
        let formatted = sample
        if (isDateLike(sample)) {
          formatted = formatDateSample(sample)
        }
        // Truncate if too long
        if (formatted.length > 25) {
          formatted = formatted.substring(0, 25) + '...'
        }
        label = `${col} ("${formatted}")`
      }

      return {
        label,
        value: col
      }
    })
})

const csvColumnOptionsWithNone = computed(() => {
  return [
    { label: t('modals.import_csv.none'), value: null },
    ...csvColumnOptions.value
  ]
})

const existingAccountOptions = computed(() => {
  const currentUserId = usersStore.userId

  return accountsStore.accountsOrdered
    .filter(account => {
      // User is the owner - can add records
      if (account.owner.id === currentUserId) {
        return true
      }

      // Check shared access - exclude if user only has READ_ONLY ('guest') role
      const userAccess = account.sharedAccess.find(access => access.user.id === currentUserId)
      if (userAccess) {
        return userAccess.role !== 'guest' // Exclude READ_ONLY accounts
      }

      return false
    })
    .map(account => {
      const balance = account.balance.toFixed(2)
      const currencyCode = account.currency?.code || ''
      return {
        label: `${account.name} (${balance} ${currencyCode})`,
        value: account.id
      }
    })
})

// Determine which user's entities to show based on selected account
const targetUserId = computed(() => {
  // If an existing account is selected in existing_account mode, use its owner
  if (accountMode.value === 'existing_account' && fieldMapping.value.account) {
    const selectedAccount = accountsStore.accountsOrdered.find(
      acc => acc.id === fieldMapping.value.account
    )
    if (selectedAccount) {
      return selectedAccount.owner.id
    }
  }
  // Default to current user
  return usersStore.userId
})

const existingCategoryOptions = computed(() => {
  const ownerId = targetUserId.value
  return categoriesStore.categoriesOrdered
    .filter(category => category.ownerUserId === ownerId)
    .map(category => ({
      label: category.name,
      value: category.id
    }))
})

const existingPayeeOptions = computed(() => {
  const ownerId = targetUserId.value
  return payeesStore.payeesOrdered
    .filter(payee => payee.ownerUserId === ownerId)
    .map(payee => ({
      label: payee.name,
      value: payee.id
    }))
})

const existingTagOptions = computed(() => {
  const ownerId = targetUserId.value
  return tagsStore.tagsOrdered
    .filter(tag => tag.ownerUserId === ownerId)
    .map(tag => ({
      label: tag.name,
      value: tag.id
    }))
})

const accountSelectOptions = computed(() => {
  if (accountMode.value === 'csv_column') {
    return csvColumnOptions.value
  } else {
    return existingAccountOptions.value
  }
})

const categorySelectOptions = computed(() => {
  if (categoryMode.value === 'csv_column') {
    return csvColumnOptionsWithNone.value
  } else {
    return [
      { label: t('modals.import_csv.none'), value: null },
      ...existingCategoryOptions.value
    ]
  }
})

const payeeSelectOptions = computed(() => {
  if (payeeMode.value === 'csv_column') {
    return csvColumnOptionsWithNone.value
  } else {
    return [
      { label: t('modals.import_csv.none'), value: null },
      ...existingPayeeOptions.value
    ]
  }
})

const tagSelectOptions = computed(() => {
  if (tagMode.value === 'csv_column') {
    return csvColumnOptionsWithNone.value
  } else {
    return [
      { label: t('modals.import_csv.none'), value: null },
      ...existingTagOptions.value
    ]
  }
})

// Fetch all entities on mount if not already loaded
onMounted(() => {
  if (!accountsStore.isAccountsLoaded) {
    accountsStore.fetchAccounts()
  }
  if (!categoriesStore.isCategoriesLoaded) {
    categoriesStore.fetchCategories()
  }
  if (!payeesStore.isPayeesLoaded) {
    payeesStore.fetchPayees()
  }
  if (!tagsStore.isTagsLoaded) {
    tagsStore.fetchTags()
  }
})

// Clear category/payee/tag selections when target user changes
watch(targetUserId, () => {
  // Clear current selections if in existing_entity mode
  if (categoryMode.value === 'existing_entity') {
    fieldMapping.value.category = null
  }
  if (payeeMode.value === 'existing_entity') {
    fieldMapping.value.payee = null
  }
  if (tagMode.value === 'existing_entity') {
    fieldMapping.value.tag = null
  }

  // Also clear stored last CSV selections for these fields
  // because they might contain entity IDs from the previous account owner
  lastCsvColumnCategory.value = null
  lastCsvColumnPayee.value = null
  lastCsvColumnTag.value = null
})

function closeModal() {
  isModalOpened.value = false
  // Don't emit here - let @hide event handle it
}

function handleDialogHide() {
  // Called when dialog is hidden by any means (cancel button, escape key, etc.)
  emit('cancel')
}

async function handleFileSelect(file: File | null) {
  if (!file) {
    csvColumns.value = []
    csvSampleValues.value = {}
    return
  }

  try {
    const text = await file.text()
    const lines = text.split('\n').filter(line => line.trim())

    if (lines.length === 0) {
      return
    }

    // Parse header
    const headers = parseCSVLine(lines[0])
    csvColumns.value = headers

    // Analyze up to 500 rows to find first non-empty value for each column
    const samples: Record<string, string[]> = {}
    headers.forEach(header => {
      samples[header] = []
    })

    const maxRowsToAnalyze = Math.min(501, lines.length) // 500 data rows + 1 header
    for (let i = 1; i < maxRowsToAnalyze; i++) {
      const values = parseCSVLine(lines[i])
      headers.forEach((header, index) => {
        // Only collect first non-empty value for each column
        if (samples[header].length === 0) {
          const value = values[index] || ''
          if (value.trim()) {
            samples[header].push(value)
          }
        }
      })

      // Early exit if we've found samples for all columns
      if (headers.every(header => samples[header].length > 0)) {
        break
      }
    }

    csvSampleValues.value = samples

    // Auto-detect common field mappings
    autoDetectFields(headers)
  } catch (error) {
    console.error('Error parsing CSV:', error)
  }
}

function parseCSVLine(line: string): string[] {
  const result: string[] = []
  let current = ''
  let inQuotes = false

  for (let i = 0; i < line.length; i++) {
    const char = line[i]

    if (char === '"') {
      if (inQuotes && line[i + 1] === '"') {
        current += '"'
        i++
      } else {
        inQuotes = !inQuotes
      }
    } else if (char === ',' && !inQuotes) {
      result.push(current.trim())
      current = ''
    } else {
      current += char
    }
  }

  result.push(current.trim())
  return result
}

/**
 * Converts an array of parsed CSV values back into a CSV line string
 * Handles proper escaping of quotes and commas
 */
function serializeCSVLine(values: string[]): string {
  return values.map(value => {
    // If value contains comma, quote, or newline, wrap in quotes and escape quotes
    if (value.includes(',') || value.includes('"') || value.includes('\n')) {
      return `"${value.replace(/"/g, '""')}"`;
    }
    return value;
  }).join(',');
}

/**
 * Converts array of row arrays into full CSV text with header
 */
function buildCSVText(header: string[], rows: string[][]): string {
  const lines = [
    serializeCSVLine(header),
    ...rows.map(row => serializeCSVLine(row))
  ];
  return lines.join('\n');
}

function autoDetectFields(headers: string[]) {
  const lowerHeaders = headers.map(h => h.toLowerCase())

  // Helper to get translated field label
  const getFieldLabel = (fieldKey: string) => {
    return t(`modals.import_csv.fields.${fieldKey}`).toLowerCase()
  }

  // Auto-detect account
  const accountLabel = getFieldLabel('account')
  const accountIndex = lowerHeaders.findIndex(h => h.includes(accountLabel))
  if (accountIndex >= 0) fieldMapping.value.account = headers[accountIndex]

  // Auto-detect date
  const dateLabel = getFieldLabel('date')
  const dateIndex = lowerHeaders.findIndex(h => h.includes(dateLabel))
  if (dateIndex >= 0) fieldMapping.value.date = headers[dateIndex]

  // Auto-detect amount fields - first try to find inflow/outflow columns
  const amountInflowLabel = getFieldLabel('amount_inflow')
  const amountOutflowLabel = getFieldLabel('amount_outflow')

  const inflowIndex = lowerHeaders.findIndex(h => h.includes(amountInflowLabel))
  const outflowIndex = lowerHeaders.findIndex(h => h.includes(amountOutflowLabel))

  if (inflowIndex >= 0 && outflowIndex >= 0) {
    // Found both inflow and outflow columns - use dual mode
    amountMode.value = 'dual'
    fieldMapping.value.amountInflow = headers[inflowIndex]
    fieldMapping.value.amountOutflow = headers[outflowIndex]
    fieldMapping.value.amount = null
  } else {
    // Try to find a single amount column - use single mode
    amountMode.value = 'single'
    const amountLabel = getFieldLabel('amount')
    const amountIndex = lowerHeaders.findIndex(h => h.includes(amountLabel))
    if (amountIndex >= 0) fieldMapping.value.amount = headers[amountIndex]
    fieldMapping.value.amountInflow = null
    fieldMapping.value.amountOutflow = null
  }

  // Auto-detect category
  const categoryLabel = getFieldLabel('category')
  const categoryIndex = lowerHeaders.findIndex(h => h.includes(categoryLabel))
  if (categoryIndex >= 0) fieldMapping.value.category = headers[categoryIndex]

  // Auto-detect description
  const descriptionLabel = getFieldLabel('description')
  const descIndex = lowerHeaders.findIndex(h => h.includes(descriptionLabel))
  if (descIndex >= 0) fieldMapping.value.description = headers[descIndex]

  // Auto-detect payee
  const payeeLabel = getFieldLabel('payee')
  const payeeIndex = lowerHeaders.findIndex(h => h.includes(payeeLabel))
  if (payeeIndex >= 0) fieldMapping.value.payee = headers[payeeIndex]

  // Auto-detect tag
  const tagLabel = getFieldLabel('tag')
  const tagIndex = lowerHeaders.findIndex(h => h.includes(tagLabel))
  if (tagIndex >= 0) fieldMapping.value.tag = headers[tagIndex]
}

function handleAmountModeChange(mode: 'single' | 'dual') {
  // Clear the fields that aren't being used
  if (mode === 'single') {
    fieldMapping.value.amountInflow = null
    fieldMapping.value.amountOutflow = null
  } else {
    fieldMapping.value.amount = null
  }
}

function autoDetectSingleField(field: 'account' | 'date' | 'category' | 'description' | 'payee' | 'tag') {
  const lowerHeaders = csvColumns.value.map(h => h.toLowerCase())

  // Get the translated label for the field and use it for matching
  const getFieldLabel = (fieldKey: string) => {
    return t(`modals.import_csv.fields.${fieldKey}`).toLowerCase()
  }

  if (field === 'account') {
    const label = getFieldLabel('account')
    const accountIndex = lowerHeaders.findIndex(h => h.includes(label))
    if (accountIndex >= 0) fieldMapping.value.account = csvColumns.value[accountIndex]
  } else if (field === 'date') {
    const label = getFieldLabel('date')
    const dateIndex = lowerHeaders.findIndex(h => h.includes(label))
    if (dateIndex >= 0) fieldMapping.value.date = csvColumns.value[dateIndex]
  } else if (field === 'category') {
    const label = getFieldLabel('category')
    const categoryIndex = lowerHeaders.findIndex(h => h.includes(label))
    if (categoryIndex >= 0) fieldMapping.value.category = csvColumns.value[categoryIndex]
  } else if (field === 'description') {
    const label = getFieldLabel('description')
    const descIndex = lowerHeaders.findIndex(h => h.includes(label))
    if (descIndex >= 0) fieldMapping.value.description = csvColumns.value[descIndex]
  } else if (field === 'payee') {
    const label = getFieldLabel('payee')
    const payeeIndex = lowerHeaders.findIndex(h => h.includes(label))
    if (payeeIndex >= 0) fieldMapping.value.payee = csvColumns.value[payeeIndex]
  } else if (field === 'tag') {
    const label = getFieldLabel('tag')
    const tagIndex = lowerHeaders.findIndex(h => h.includes(label))
    if (tagIndex >= 0) fieldMapping.value.tag = csvColumns.value[tagIndex]
  }
}

function handleAccountModeChange(mode: 'csv_column' | 'existing_account') {
  accountMode.value = mode

  if (mode === 'existing_account') {
    // Store current CSV selection before switching away
    lastCsvColumnAccount.value = fieldMapping.value.account
    fieldMapping.value.account = ''
  } else {
    // Restore last CSV selection or auto-detect
    if (lastCsvColumnAccount.value) {
      fieldMapping.value.account = lastCsvColumnAccount.value
    } else if (csvColumns.value.length > 0) {
      autoDetectSingleField('account')
    }
  }
}

function handleDateModeChange(mode: 'csv_column' | 'manual') {
  dateMode.value = mode

  if (mode === 'manual') {
    // Store current CSV selection before switching away
    lastCsvColumnDate.value = fieldMapping.value.date
    fieldMapping.value.date = ''
    manualDate.value = ''
  } else {
    // Restore last CSV selection or auto-detect
    manualDate.value = ''
    if (lastCsvColumnDate.value) {
      fieldMapping.value.date = lastCsvColumnDate.value
    } else if (csvColumns.value.length > 0) {
      autoDetectSingleField('date')
    }
  }
}

function handleCategoryModeChange(mode: 'csv_column' | 'existing_entity') {
  categoryMode.value = mode

  if (mode === 'existing_entity') {
    // Store current CSV selection before switching away
    lastCsvColumnCategory.value = fieldMapping.value.category
    fieldMapping.value.category = null
  } else {
    // Restore last CSV selection or auto-detect
    if (lastCsvColumnCategory.value) {
      fieldMapping.value.category = lastCsvColumnCategory.value
    } else if (csvColumns.value.length > 0) {
      autoDetectSingleField('category')
    }
  }
}

function handleDescriptionModeChange(mode: 'csv_column' | 'manual') {
  descriptionMode.value = mode

  if (mode === 'manual') {
    // Store current CSV selection before switching away
    lastCsvColumnDescription.value = fieldMapping.value.description
    fieldMapping.value.description = null
    manualDescription.value = ''
  } else {
    // Restore last CSV selection or auto-detect
    manualDescription.value = ''
    if (lastCsvColumnDescription.value) {
      fieldMapping.value.description = lastCsvColumnDescription.value
    } else if (csvColumns.value.length > 0) {
      autoDetectSingleField('description')
    }
  }
}

function handlePayeeModeChange(mode: 'csv_column' | 'existing_entity') {
  payeeMode.value = mode

  if (mode === 'existing_entity') {
    // Store current CSV selection before switching away
    lastCsvColumnPayee.value = fieldMapping.value.payee
    fieldMapping.value.payee = null
  } else {
    // Restore last CSV selection or auto-detect
    if (lastCsvColumnPayee.value) {
      fieldMapping.value.payee = lastCsvColumnPayee.value
    } else if (csvColumns.value.length > 0) {
      autoDetectSingleField('payee')
    }
  }
}

function handleTagModeChange(mode: 'csv_column' | 'existing_entity') {
  tagMode.value = mode

  if (mode === 'existing_entity') {
    // Store current CSV selection before switching away
    lastCsvColumnTag.value = fieldMapping.value.tag
    fieldMapping.value.tag = null
  } else {
    // Restore last CSV selection or auto-detect
    if (lastCsvColumnTag.value) {
      fieldMapping.value.tag = lastCsvColumnTag.value
    } else if (csvColumns.value.length > 0) {
      autoDetectSingleField('tag')
    }
  }
}

function handleResultModalClose() {
  showResultModal.value = false

  // Emit the import event now that the user has seen the results
  if (pendingImportConfig.value) {
    emit('import', pendingImportConfig.value)
    pendingImportConfig.value = null
  }

  // Close the modal completely
  emit('cancel')
}

/**
 * Parse entire CSV file and split into chunks of rows
 */
async function parseAndChunkCSV(file: File): Promise<{
  header: string[];
  chunks: string[][][]; // Array of chunks, each chunk is array of rows
  totalRows: number;
}> {
  const text = await file.text()
  const lines = text.split('\n').filter(line => line.trim())

  if (lines.length === 0) {
    throw new Error('Empty CSV file')
  }

  const header = parseCSVLine(lines[0])
  const allDataRows: string[][] = []

  // Parse all data rows (skip header)
  for (let i = 1; i < lines.length; i++) {
    const row = parseCSVLine(lines[i])
    // Skip empty rows
    if (row.some(cell => cell.trim())) {
      allDataRows.push(row)
    }
  }

  // Split into chunks
  const chunks: string[][][] = []
  for (let i = 0; i < allDataRows.length; i += CHUNK_SIZE) {
    chunks.push(allDataRows.slice(i, i + CHUNK_SIZE))
  }

  return {
    header,
    chunks,
    totalRows: allDataRows.length
  }
}

/**
 * Parse errors from backend grouped format
 * Format: { "Error message": [row1, row2, row3] }
 */
function parseBackendErrors(errors: Record<string, number[]> | null | undefined): Array<{ message: string; rows: number[] }> {
  if (!errors || typeof errors !== 'object') {
    return []
  }

  return Object.entries(errors).map(([message, rows]) => ({
    message,
    rows: Array.isArray(rows) ? rows : []
  }))
}

/**
 * Upload a single chunk to the API
 */
async function uploadChunk(
  chunkData: string[][],
  header: string[],
  mapping: Record<string, string | null>,
  chunkIndex: number,
  options?: {
    accountId?: Id;
    date?: string;
    categoryId?: Id;
    description?: string;
    payeeId?: Id;
    tagId?: Id;
  }
): Promise<{ imported: number; skipped: number; errors: Array<{ message: string; rows: number[] }> }> {
  // Build CSV text for this chunk
  const csvText = buildCSVText(header, chunkData)

  // Create blob and FormData (same format as current implementation)
  const blob = new Blob([csvText], { type: 'text/csv' })
  const file = new File([blob], `chunk_${chunkIndex}.csv`, { type: 'text/csv' })

  const formData = new FormData()
  formData.append('file', file)
  formData.append('mapping', JSON.stringify(mapping))

  // Use existing transaction API call wrapped in Promise
  return new Promise((resolve, reject) => {
    transaction.importTransactionList(
      file,
      mapping,
      (response: any) => {
        const result = response.data?.data || response.data || response
        // Parse errors from backend (handles both array and object formats)
        const parsedErrors = parseBackendErrors(result.errors)
        resolve({
          imported: result.imported || 0,
          skipped: result.skipped || 0,
          errors: parsedErrors
        })
      },
      (error: any) => {
        reject(error)
      },
      options
    )
  })
}

async function handleSubmit() {
  // Validate form fields
  const isValid = await importForm.value.validate()

  if (!isValid || !csvFile.value) {
    return
  }

  isImporting.value = true

  const importConfig = {
    file: csvFile.value,
    mapping: {
      account: fieldMapping.value.account,
      date: fieldMapping.value.date,
      amount: fieldMapping.value.amount,
      amountInflow: fieldMapping.value.amountInflow,
      amountOutflow: fieldMapping.value.amountOutflow,
      category: fieldMapping.value.category,
      description: fieldMapping.value.description,
      payee: fieldMapping.value.payee,
      tag: fieldMapping.value.tag
    }
  }

  try {
    // Parse and chunk the CSV
    const { header, chunks, totalRows } = await parseAndChunkCSV(csvFile.value)

    // Determine if chunking is needed
    const needsChunking = chunks.length > 1
    isChunkedImport.value = needsChunking

    if (needsChunking) {
      uploadProgress.value.totalChunks = chunks.length
      uploadProgress.value.currentChunk = 0
      uploadProgress.value.percentage = 0
    }

    // Aggregate results across all chunks
    let totalImported = 0
    let totalSkipped = 0
    const errorMap = new Map<string, Set<number>>() // Map message to set of row numbers

    // Process chunks sequentially
    for (let i = 0; i < chunks.length; i++) {
      const chunk = chunks[i]
      const chunkStartRow = i * CHUNK_SIZE + 1 // +1 for header row

      // Update progress
      if (needsChunking) {
        uploadProgress.value.currentChunk = i + 1
        uploadProgress.value.percentage = ((i + 1) / chunks.length) * 100
      }

      try {
        // Upload chunk (continues even if previous chunks failed)
        // Build options object with entity IDs if in 'existing' mode
        const uploadOptions: {
          accountId?: Id;
          date?: string;
          categoryId?: Id;
          description?: string;
          payeeId?: Id;
          tagId?: Id;
        } = {}

        if (accountMode.value === 'existing_account' && fieldMapping.value.account) {
          uploadOptions.accountId = fieldMapping.value.account as Id
        }
        if (dateMode.value === 'manual' && manualDate.value.trim()) {
          uploadOptions.date = manualDate.value.trim()
        }
        if (categoryMode.value === 'existing_entity' && fieldMapping.value.category) {
          uploadOptions.categoryId = fieldMapping.value.category as Id
        }
        if (descriptionMode.value === 'manual' && manualDescription.value.trim()) {
          uploadOptions.description = manualDescription.value.trim()
        }
        if (payeeMode.value === 'existing_entity' && fieldMapping.value.payee) {
          uploadOptions.payeeId = fieldMapping.value.payee as Id
        }
        if (tagMode.value === 'existing_entity' && fieldMapping.value.tag) {
          uploadOptions.tagId = fieldMapping.value.tag as Id
        }

        const result = await uploadChunk(chunk, header, importConfig.mapping, i, uploadOptions)

        // Aggregate results
        totalImported += result.imported
        totalSkipped += result.skipped

        // Adjust error row numbers to match original file and merge by message
        result.errors.forEach(err => {
          const adjustedRows = err.rows.map(row => row + chunkStartRow - 1)

          if (!errorMap.has(err.message)) {
            errorMap.set(err.message, new Set())
          }
          const errorSet = errorMap.get(err.message)
          if (errorSet) {
            adjustedRows.forEach(row => errorSet.add(row))
          }
        })

      } catch (chunkError: any) {
        console.error(`Chunk ${i + 1} failed:`, chunkError)

        // Add chunk-level error
        const errorMessage = `Chunk ${i + 1} failed: ${chunkError?.data?.message || chunkError?.message || 'Unknown error'}`
        if (!errorMap.has(errorMessage)) {
          errorMap.set(errorMessage, new Set([chunkStartRow]))
        }

        // Count all rows in failed chunk as skipped
        totalSkipped += chunk.length
      }
    }

    // Convert error map to array of grouped errors
    const allErrors = Array.from(errorMap.entries()).map(([message, rowSet]) => ({
      message,
      rows: Array.from(rowSet).sort((a, b) => a - b)
    }))

    // Store aggregated results
    importResult.value = {
      imported: totalImported,
      failed: totalSkipped,
      errors: allErrors
    }

    // Store config for later emission
    pendingImportConfig.value = importConfig

    // Refresh data
    syncStore.fetchAll()

    // Close import modal and show results
    isModalOpened.value = false
    setTimeout(() => {
      showResultModal.value = true
    }, 100)

  } catch (error: any) {
    console.error('Import failed:', error)

    // Handle complete failure
    let errorMessage = t('modals.import_csv.error')
    if (error?.data?.message) {
      errorMessage = error.data.message
    } else if (error?.message) {
      errorMessage = error.message
    }

    importResult.value = {
      imported: 0,
      failed: 0,
      errors: [{ message: errorMessage, rows: [0] }]
    }

    isModalOpened.value = false
    setTimeout(() => {
      showResultModal.value = true
    }, 100)

  } finally {
    isImporting.value = false
    isChunkedImport.value = false
    uploadProgress.value = {
      currentChunk: 0,
      totalChunks: 0,
      percentage: 0
    }
  }
}
</script>

<style scoped lang="scss">
.import-csv-modal {
  max-width: 800px;
}

.import-csv-upload {
  margin-top: 16px;
}

.import-csv-file-selected {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #f5f5f5;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.import-csv-file-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  min-width: 0;
}

.import-csv-file-name {
  font-size: 14px;
  color: #333;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.import-csv-mapping {
  margin-top: 16px;
}

.import-csv-mapping-description {
  margin-bottom: 24px;
  padding: 12px 16px;
  background: #e3f2fd;
  border-left: 4px solid #2196f3;
  border-radius: 4px;
  font-size: 14px;
  color: #1976d2;
}

.import-csv-dual-amounts {
  margin-bottom: 16px;
}

.import-csv-amount-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 0;

  .import-csv-dual-amounts & {
    margin-bottom: 4px;
  }
}

.import-csv-amount-field {
  flex: 1;
  margin-bottom: 0 !important;
}

.import-csv-amount-switch {
  flex-shrink: 0;
  align-self: center;
}

.import-csv-outflow-field {
  margin-bottom: 0 !important;
}

.import-csv-account-row,
.import-csv-field-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.import-csv-account-field,
.import-csv-field-control {
  flex: 1;
  margin-bottom: 0 !important;
}

.import-csv-account-switch,
.import-csv-field-switch {
  flex-shrink: 0;
  align-self: center;
}

.import-csv-section-break {
  height: 16px;
}

.import-csv-progress-section {
  padding: 16px;
  background: #f5f5f5;
  border-radius: 8px;
  margin-top: 16px;
}

.import-csv-progress-bar {
  margin-bottom: 8px;
  border-radius: 4px;
}

.import-csv-progress-percentage {
  font-size: 12px;
  color: #666;
  text-align: center;
}
</style>
