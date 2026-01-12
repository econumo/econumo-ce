<template>
  <q-dialog class="settings-classification-replace-modal" v-model="isReplaceModalOpened" @hide="closeReplaceModal" :position="$q.screen.gt.md ? 'standard' : 'bottom'" no-backdrop-dismiss>
    <q-card class="settings-classification-replace-modal-card">
      <q-card-section>
        <div class="settings-classification-replace-modal-card-header">{{ $t('modules.classifications.categories.modals.replace.header') }}</div>
        <div>
          <q-list class="settings-classification-replace-modal-card-list">
            <q-item v-for="newCategory in categoriesExcept(categories, replaceCategory.categoryId)" v-bind:key="newCategory.id"
                    class="settings-classification-replace-modal-card-list-item"
                    clickable @click="replaceCategory.selectedCategoryId = newCategory.id"
                    :active="replaceCategory.selectedCategoryId === newCategory.id" active-class="active">
              <q-item-section avatar class="settings-classification-replace-modal-card-list-item-avatar">
                <q-icon class="settings-classification-replace-modal-card-list-item-avatar-icon" :name="newCategory.icon"/>
              </q-item-section>
              <q-item-section class="settings-classification-replace-modal-card-list-item-name">{{ newCategory.name }}</q-item-section>
            </q-item>
          </q-list>
        </div>
      </q-card-section>

      <div class="settings-classification-replace-modal-note">
        <div class="settings-classification-replace-modal-note-icon">
          <img src="~assets/note-icon.svg">
        </div>
        {{ $t('modules.classifications.categories.modals.replace.note') }}
      </div>

      <q-card-actions class="settings-classification-modal-actions">
        <q-btn class="econumo-btn -medium -grey settings-classification-modal-actions-button" flat :label="$t('elements.button.cancel.label')" v-close-popup/>
        <q-btn class="econumo-btn -medium -magenta settings-classification-modal-actions-button" flat :label="$t('elements.button.replace.label')"
               :disable="!replaceCategory.selectedCategoryId"
               @click="submitReplaceCategory().finally(() => { $emit('update:categoriesCopy', null); })"/>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useQuasar } from 'quasar';
import { useReplaceCategory } from '../../composables/categories/useReplaceCategory';

interface Category {
  id: string;
  name: string;
  type: string;
  icon: string;
}

export default defineComponent({
  name: 'CategoriesReplaceModal',
  props: {
    categories: {
      type: Array as () => Category[],
      required: true
    }
  },
  emits: ['update:categoriesCopy'],
  setup() {
    const $q = useQuasar();
    const { isReplaceModalOpened, replaceCategory, closeReplaceModal, submitReplaceCategory, categoriesExcept } = useReplaceCategory();

    return {
      $q,
      isReplaceModalOpened,
      replaceCategory,
      closeReplaceModal,
      submitReplaceCategory,
      categoriesExcept
    };
  }
});
</script> 