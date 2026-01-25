<template>
  <q-dialog class="access-modal" :model-value="true" @hide="$emit('cancel')">
    <q-card class="access-modal-card">
      <q-card-section class="access-modal-section -toolbar">
        <div class="settings-toolbar-mobile access-modal-toolbar">
          <div>
            <q-btn
              class="settings-toolbar-mobile-button"
              flat
              icon="arrow_back"
              v-close-popup
            />
          </div>
          <div>
            <h4 class="settings-toolbar-mobile-title">{{ item.name }}</h4>
          </div>
          <div class="settings-toolbar-mobile-container"></div>
        </div>
      </q-card-section>

      <q-card-section class="access-modal-section">
        <div class="access-modal-list">
          <div class="access-modal-title">{{ item.name }}</div>
          <div class="access-modal-note" style="margin-bottom: 16px">
            {{ $t('modules.connections.modals.share_access.tap_to_share') }}
          </div>
          <div class="access-modal-note" v-if="!access.length">
            {{ $t('modules.connections.modals.share_access.list_empty') }}
          </div>
          <div
            v-else
            v-for="accessItem in access"
            v-bind:key="accessItem.user.id"
          >
            <div
              class="preview-modal-account-info-access-item"
              @click="
                $emit(
                  'open-access-level-dialog',
                  item.id,
                  accessItem.user.id,
                  accessItem.role
                )
              "
              v-if="accessItem.user.id !== userId"
              style="cursor: pointer; margin-bottom: 12px"
            >
              <q-avatar class="preview-modal-account-info-access-item-avatar">
                <img
                  :src="avatarUrl(accessItem.user.avatar, 100)"
                  class="preview-modal-account-info-access-item-avatar-img"
                  width="100"
                  height="100"
                  :alt="accessItem.user.name"
                  :title="accessItem.user.name"
                />
              </q-avatar>
              <div class="preview-modal-account-info-access-item-user">
                <div class="preview-modal-account-info-access-item-user-name">
                  {{ accessItem.user.name }}
                </div>
                <div
                  class="preview-modal-account-info-access-item-user-role"
                  v-if="!accessItem.role"
                >
                  {{ $t('modules.connections.accounts.roles.no_access') }}
                </div>
                <div
                  class="preview-modal-account-info-access-item-user-role"
                  v-else
                >
                  {{
                    $t('modules.connections.accounts.roles.' + accessItem.role)
                  }}
                </div>
              </div>
              <q-icon
                name="chevron_right"
                size="32px"
                color="grey-6"
                style="margin-left: auto; align-self: center"
              />
            </div>
          </div>
        </div>
      </q-card-section>
      <q-card-actions class="responsive-modal-actions">
        <q-space />
        <q-btn
          class="econumo-btn -medium -grey responsive-modal-actions-button"
          :label="$t('elements.button.ok.label')"
          flat
          v-close-popup
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { defineComponent } from 'vue';
import _ from 'lodash';
import { useAvatar } from '../composables/useAvatar';

export default defineComponent({
  props: ['userId', 'item', 'itemOwnerUserId', 'connections'],
  setup() {
    const { avatarUrl } = useAvatar();
    return { avatarUrl };
  },
  computed: {
    access: function () {
      let userCollection = {};
      _.forEach(this.connections, (connection) => {
        userCollection[connection.user.id] = {
          user: connection.user,
          role: connection.user.id === this.itemOwnerUserId ? 'owner' : null,
        };
      });

      _.forEach(this.item.sharedAccess, (item) => {
        if (item.user.id === this.userId) return;
        if (!userCollection[item.user.id]) {
          userCollection[item.user.id] = {
            user: item.user,
            role: null,
          };
        }
        userCollection[item.user.id].role =
          item.user.id === this.itemOwnerUserId ? 'owner' : item.role;
      });
      let result = [];
      _.forEach(userCollection, (item) => {
        result.push(item);
      });

      return result;
    },
  },
});
</script>
