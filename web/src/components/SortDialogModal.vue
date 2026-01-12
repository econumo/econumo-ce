<template>
  <q-dialog :model-value="true" @close="$emit('cancel')" class="sort-modal" no-backdrop-dismiss>
    <q-card class="econumo-modal sort-modal-card">
      <div class="sort-modal-card-title">{{ $t('modals.sort.header') }}</div>
      <ul class="sort-modal-card-list">
        <li class="sort-modal-card-list-item">
          <q-btn class="sort-modal-card-list-item-btn" flat :label="$t('modals.sort.mode.alphabet.asc')" @click="$emit('proceed', 'name', 'asc')"></q-btn>
        </li>
        <li class="sort-modal-card-list-item">
          <q-btn class="sort-modal-card-list-item-btn" flat :label="$t('modals.sort.mode.alphabet.desc')" @click="$emit('proceed', 'name', 'desc')"></q-btn>
        </li>
<!--        <li class="sort-modal-card-list-item">-->
<!--          <q-btn class="sort-modal-card-list-item-btn" flat :label="$t('modals.sort.mode.count.desc')" @click="$emit('proceed', 'count', 'desc')"></q-btn>-->
<!--        </li>-->
<!--        <li class="sort-modal-card-list-item">-->
<!--          <q-btn class="sort-modal-card-list-item-btn" flat :label="$t('modals.sort.mode.count.asc')" @click="$emit('proceed', 'count', 'asc')"></q-btn>-->
<!--        </li>-->
        <li class="sort-modal-card-list-item">
          <q-btn class="sort-modal-card-list-item-btn sort-modal-card-list-item-btn-cancel" flat :label="$t('elements.button.cancel.label')" @click="$emit('cancel')"></q-btn>
        </li>
      </ul>
    </q-card>
  </q-dialog>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
  props: [],
})
</script>

<style scoped>
.sort-modal-card-list-item-btn-cancel {
  color: #666;
}
</style>
