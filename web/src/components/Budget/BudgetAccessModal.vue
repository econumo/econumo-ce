<template>
  <q-dialog
    class="access-modal"
    :model-value="true"
    @hide="$emit('cancel')"
    role="dialog"
    aria-labelledby="budget-access-title"
    no-backdrop-dismiss
  >
    <q-card class="access-modal-card">
      <q-card-section class="access-modal-section -toolbar">
        <div class="settings-toolbar-mobile access-modal-toolbar">
          <div>
            <q-btn
              class="settings-toolbar-mobile-button"
              flat
              icon="arrow_back"
              v-close-popup
              :aria-label="$t('elements.button.back.label')"
            />
          </div>
          <div>
            <h4
              id="budget-access-title"
              class="settings-toolbar-mobile-title"
            >
              {{ budgetName }}
            </h4>
          </div>
          <div class="settings-toolbar-mobile-container" />
        </div>
      </q-card-section>

      <q-card-section class="access-modal-section">
        <div class="access-modal-list">
          <div class="access-modal-title">{{ budgetName }}</div>
          <div class="access-modal-note access-modal-hint">
            {{ $t('modules.connections.modals.share_access.tap_to_share') }}
          </div>
          <div
            v-if="!users.length"
            class="access-modal-note"
          >
            {{ $t('modules.connections.modals.share_access.list_empty') }}
          </div>
          <div v-else v-for="item in users" v-bind:key="item.userId">
            <div class="preview-modal-account-info-access-item access-item-clickable"
                 @click="handleUserSelect(item)">
              <q-avatar class="preview-modal-account-info-access-item-avatar">
                <img
                  :src="getUserAvatarUrl(item.userAvatar)"
                  class="preview-modal-account-info-access-item-avatar-img"
                  width="100"
                  height="100"
                  :alt="item.userName"
                  :title="item.userName"
                />
              </q-avatar>
              <div class="preview-modal-account-info-access-item-user">
                <div class="preview-modal-account-info-access-item-user-name">
                  {{ item.userName }}
                </div>
                <div class="preview-modal-account-info-access-item-user-role">
                  {{ getUserRoleText(item) }}
                </div>
              </div>
              <q-icon name="chevron_right" size="32px" color="grey-6" class="access-item-chevron"/>
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

<script setup lang="ts">
import { computed } from 'vue';
import { Id } from '@shared/types';
import { UserAccessDto, AcceptanceStatus } from '@shared/dto/access.dto';
import { ConnectionDto } from '@shared/dto/connection.dto';
import { useUsersStore } from 'stores/users';
import { useI18n } from 'vue-i18n';
import { useAvatar } from '../../composables/useAvatar';

defineOptions({
  name: 'BudgetAccessModal'
});

interface Props {
  budgetId: Id;
  budgetName: string;
  access: UserAccessDto[];
  connections: ConnectionDto[];
}

interface UserAccessItem {
  userId: string;
  userName: string;
  userAvatar: string;
  role: string | null;
  isAccepted: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  cancel: [];
  'user-selected': [budgetId: Id, userId: string, role: string | null];
}>();

const { t } = useI18n();
const currentUserId = computed(() => useUsersStore().userId);
const { avatarUrl } = useAvatar();

const users = computed((): UserAccessItem[] => {
  const result: UserAccessItem[] = [];
  const map: Record<string, number> = {};

  // First, add all connections
  props.connections.forEach((item: ConnectionDto, index: number) => {
    if (item.user.id === currentUserId.value) return;

    result.push({
      userId: item.user.id,
      userName: item.user.name,
      userAvatar: item.user.avatar,
      role: null,
      isAccepted: false,
    });
    map[item.user.id] = index;
  });

  // Then, update or add access information
  props.access.forEach((item: UserAccessDto) => {
    if (item.user.id === currentUserId.value) return;

    if (item.user.id in map) {
      const index = map[item.user.id];
      result[index].role = item.role;
      result[index].isAccepted = item.isAccepted === AcceptanceStatus.ACCEPTED;
    } else {
      result.push({
        userId: item.user.id,
        userName: item.user.name,
        userAvatar: item.user.avatar,
        role: item.role,
        isAccepted: item.isAccepted === AcceptanceStatus.ACCEPTED
      });
    }
  });

  return result;
});

const getUserAvatarUrl = (avatar: string): string => {
  return avatarUrl(avatar, 100);
};

const getUserRoleText = (user: UserAccessItem): string => {
  if (!user.role) {
    return t('modules.connections.budgets.roles.no_access');
  }

  const roleText = t(`modules.connections.budgets.roles.${user.role}`);
  return user.isAccepted
    ? roleText
    : `${roleText} – ${t('modules.connections.modals.share_access.not_accepted')}`;
};

const handleUserSelect = (user: UserAccessItem): void => {
  emit('user-selected', props.budgetId, user.userId, user.role);
};
</script>

<style lang="scss" scoped>
.access-modal-hint {
  margin-bottom: 16px;
}

.access-item-clickable {
  cursor: pointer;
  margin-bottom: 12px;
}

.access-item-chevron {
  margin-left: auto;
  align-self: center;
}
</style>

