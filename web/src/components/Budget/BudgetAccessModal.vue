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
          <div 
            v-if="!users.length"
            class="access-modal-note"
            role="status"
          >
            {{ $t('modules.connections.modals.share_access.list_empty') }}
          </div>
          <ul 
            v-else 
            class="access-modal-users-list"
            role="list"
          >
            <li
              v-for="item in users" 
              :key="item.userId"
              class="preview-modal-account-info-access-item"
              role="listitem"
            >
              <button
                class="preview-modal-account-info-access-button"
                @click="handleUserSelect(item)"
                :aria-label="$t('modules.connections.modals.share_access.select_user', { name: item.userName })"
              >
                <q-avatar class="preview-modal-account-info-access-item-avatar">
                  <img 
                    :src="getUserAvatarUrl(item.userAvatar)"
                    class="preview-modal-account-info-access-item-avatar-img" 
                    :alt="item.userName"
                    :aria-hidden="true"
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
              </button>
            </li>
          </ul>
        </div>
      </q-card-section>
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
    return t('modules.connections.elements.roles.no_access');
  }
  
  const roleText = t(`modules.connections.modals.share_access.level.${user.role}`);
  return user.isAccepted 
    ? roleText 
    : `${roleText} – ${t('modules.connections.modals.share_access.not_accepted')}`;
};

const handleUserSelect = (user: UserAccessItem): void => {
  emit('user-selected', props.budgetId, user.userId, user.role);
};
</script>
<style lang="scss" scoped>
.access-modal-users-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.preview-modal-account-info-access-button {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.5rem;
  border: none;
  background: none;
  cursor: pointer;
  text-align: left;

  &:hover {
    background-color: var(--q-primary-fade);
  }

  &:focus-visible {
    outline: 2px solid var(--q-primary);
    outline-offset: -2px;
  }
}
</style>

