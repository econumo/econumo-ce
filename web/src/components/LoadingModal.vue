<template>
  <q-dialog persistent minimized>
    <div class="econumo-modal spinner-modal">
      <q-spinner class="econumo-spinner"/>
      <div class="spinner-modal-title">
        {{ headerLabel }}
      </div>
    </div>
  </q-dialog>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
  props: ['headerLabel'],
  methods: {
    close: function() {
      this.$emit('close')
    }
  }
})
</script>
