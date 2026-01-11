<template>
  <div class="responsive-modal-section">
    <div class="responsive-modal-icon-label" v-if="header !== null">{{ header }}</div>
    <div class="responsive-modal-icon">
      <q-carousel
        swipeable
        animated
        :navigation="$q.screen.gt.sm"
        infinite
        v-model="iconScreen"
        control-color="purple-5"
        class="responsive-modal-icon-carousel"
      >
        <q-carousel-slide :name="'icons-' + i" class="responsive-modal-icon" v-for="(iconsGroup, i) in iconGroups" v-bind:key="i">
          <q-btn
            class="responsive-modal-icon-btn"
            :class="(icon === item ? '-active' : '')"
            :icon="item"
            v-for="item in iconsGroup" v-bind:key="item"
            @click="icon = item" />
        </q-carousel-slide>
      </q-carousel>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { defaultEnvelopeIcon, iconsPagination } from '../../modules/icons';
import { Icon } from '@shared/types';
import { useQuasar } from 'quasar';

defineOptions({
  name: 'ResponsiveModalIcons',
});

const props = defineProps<{
  header: string | null,
  icon: Icon | null
}>();
const emit = defineEmits(['update-icon']);

const $q = useQuasar();
const iconScreen = ref('icons-0');
const iconGroups = computed(() => {
  let rows, cols;
  if ($q.screen.gt.sm) {
    rows = 6;
    cols = 8;
  } else {
    rows = 4;
    cols = 6;
  }
  return iconsPagination(rows, cols);
});
const icon = computed({
  get() {
    return props.icon || defaultEnvelopeIcon;
  },
  set(value: Icon) {
    emit('update-icon', value);
  }
});

</script>

