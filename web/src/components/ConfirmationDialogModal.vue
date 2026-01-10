<template>
  <q-dialog :model-value="true" persistent>
    <q-card class="confirmation-dialog">
      <q-card-section class="confirmation-dialog-section">
        <q-avatar v-if="icon" :icon="icon" color="primary" text-color="white" />
        <span class="confirmation-dialog-title" v-if="questionTitle">{{ questionTitle }}</span>
        <span class="confirmation-dialog-label">{{ questionLabel }}</span>
      </q-card-section>

      <q-card-actions class="confirmation-dialog-actions">
        <div class="confirmation-dialog-actions-buttons">
          <q-btn
            flat
            :label="cancelLabel"
            class="econumo-btn -medium -grey full-width"
            v-close-popup
            @click="$emit('cancel')"
            v-if="cancelLabel" />
        </div>
        <div class="confirmation-dialog-actions-buttons">
          <q-btn
            flat
            :label="actionLabel"
            class="econumo-btn -medium -magenta full-width"
            @click="$emit('proceed')" />
        </div>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
  props: ['icon', 'questionTitle', 'questionLabel', 'actionLabel', 'cancelLabel'],
})
</script>
