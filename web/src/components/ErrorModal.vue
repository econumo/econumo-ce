<template>
  <q-dialog :model-value="true" minimized :no-backdrop-dismiss="$q.screen.gt.md">
    <div class="econumo-modal form-fail-modal">
      <div class="form-fail-modal-title">
        {{ header }}
      </div>
      <div class="form-fail-modal-description">
        {{ description }}
      </div>
      <div class="form-fail-modal-button">
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
import { useQuasar } from 'quasar';

const $q = useQuasar();

defineOptions({
  name: 'ErrorModal'
});

const props = defineProps<{
  header: string,
  description: string
}>();

const emit = defineEmits(['close']);

function close() {
  emit('close');
}
</script>

