<template>
  <q-dialog :model-value="modelValue" minimized @hide="close">
    <div class="econumo-modal form-hint-modal">
      <div class="form-hint-modal-text">
        {{ $t('modules.app.modal.self_hosted.information') }}
      </div>
      <div class="form-hint-modal-button">
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
defineOptions({
  name: 'SelfHostedInfoModal'
})

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:model-value', value: boolean): void
  (e: 'cancel'): void
}>()

const close = () => {
  emit('update:model-value', false)
  emit('cancel')
}
</script>
