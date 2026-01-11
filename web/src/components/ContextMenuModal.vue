<template>
  <q-dialog :model-value="true" @hide="$emit('cancel')" class="context-menu-modal">
    <q-card class="context-menu-modal-card">
      <div class="context-menu-modal-card-label">{{ headerLabel }}</div>
      <ul class="context-menu-modal-card-list">
        <li v-for="action in actions" v-bind:key="action.value" class="context-menu-modal-card-list-item"
            v-show="action.isHidden !== true">
          <q-btn
            :class="action.value === 'delete' ? 'context-menu-modal-card-list-item-btn -delete' : 'context-menu-modal-card-list-item-btn'"
            flat :label="action.label" @click="proceed(action.value, action.context)"></q-btn>
        </li>
      </ul>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">

interface ActionItem {
  value: string;
  label: string;
  isHidden: boolean;
  context: any;
}

defineOptions({
  name: 'ContextMenuModal'
});

const props = defineProps<{
  headerLabel: string,
  actions: ActionItem[]
}>();

const emit = defineEmits(['proceed', 'cancel']);

function proceed(value: string, context: any) {
  emit('proceed', value, context);
}
</script>
