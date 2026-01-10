<template>
  <div class="form-container full-width column wrap justify-center items-center content-center">
    <q-form
      @submit.prevent="handleSubmit"
      class="full-width"
      aria-label="Login form"
    >
      <q-input
        outlined
        class="form-input full-width"
        :placeholder="$t('modules.user.form.user.email.placeholder')"
        type="email"
        v-model="formState.username"
        :label="$t('modules.user.form.user.email.label')"
        lazy-rules
        :rules="[
          val => isNotEmpty(val) || $t('modules.user.form.user.email.validation.required_field'),
          val => isValidEmail(val) || $t('modules.user.form.user.email.validation.invalid_email')
        ]"
        aria-required="true"
      />

      <q-input
        outlined
        class="form-input full-width"
        :placeholder="$t('modules.user.form.user.password.placeholder')"
        type="password"
        v-model="formState.password"
        :label="$t('modules.user.form.user.password.label')"
        lazy-rules
        :rules="[
          val => isNotEmpty(val) || $t('modules.user.form.user.password.validation.required_field')
        ]"
        aria-required="true"
      />

      <div v-if="isCustomApiAllowed">
        <div class="form-checkbox-group">
          <q-checkbox
            class="form-checkbox full-width"
            v-model="formState.selfHosted"
            :label="$t('modules.user.form.user.server_host.self_hosted')"
          />
          <div 
            @click="selfHostedModal.open" 
            class="form-checkbox-hint"
            role="button"
            tabindex="0"
          ></div>
        </div>

        <q-input
          v-show="formState.selfHosted"
          outlined
          class="form-input full-width"
          :placeholder="$t('modules.user.form.user.server_host.placeholder')"
          type="url"
          v-model="formState.host"
          :label="$t('modules.user.form.user.server_host.label')"
          lazy-rules
          :rules="[
            val => isNotEmpty(val) || $t('modules.user.form.user.server_host.validation.required_field'),
            val => isValidHttpUrl(val) || $t('modules.user.form.user.server_host.validation.invalid_url')
          ]"
          aria-required="true"
        />
      </div>

      <div class="form-btn-container">
        <q-btn
          class="econumo-btn -large -yellow full-width"
          :label="$t('modules.user.form.sign_in.action.sign_in')"
          type="submit"
          flat
        />
      </div>

      <div>
        <q-btn
          class="econumo-btn -large -grey full-width"
          :label="$t('modules.user.form.sign_in.action.forget_password')"
          type="button"
          @click="openRecoveryModal"
          flat
        />
      </div>
    </q-form>
  </div>

  <teleport to="body">
    <loading-modal 
      v-if="loadingModal.isOpen" 
      @close="loadingModal.close" 
      :header-label="$t('modules.user.modal.sign_in.label')"
    />
    
    <self-hosted-info-modal 
      :model-value="selfHostedModal.isOpen.value"
      @update:model-value="(val) => selfHostedModal.isOpen.value = val"
      @cancel="selfHostedModal.close"
    />

    <q-dialog v-model="modals.isFailLogin" @hide="closeFailModal">
      <div class="econumo-modal form-fail-modal">
        <div class="form-fail-modal-title">
          {{ $t('modules.user.modal.sign_in_failed.header') }}
        </div>
        <div class="form-fail-modal-description">
          {{ $t('modules.user.modal.sign_in_failed.information') }}
        </div>
        <div class="form-fail-modal-button">
          <q-btn
            class="econumo-btn -small -yellow full-width"
            :label="$t('elements.button.ok.label')"
            type="button"
            @click="closeFailModal"
            flat
          />
        </div>
      </div>
    </q-dialog>

    <recovery-modal
      v-model="modals.isRecovery"
      @close="closeRecoveryModal"
    />
  </teleport>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { defineOptions } from 'vue'
import { useRouter } from 'vue-router'
import { useUsersStore } from 'stores/users'
import { useI18n } from 'vue-i18n'
import config from '../modules/config'
import LoadingModal from '../components/LoadingModal.vue'
import SelfHostedInfoModal from '../components/Login/SelfHostedInfoModal.vue'
import RecoveryModal from '../components/Login/RecoveryModal.vue'
import { useLoadingModal } from '../composables/useLoadingModal'
import { useSelfHostedInfoModal } from '../composables/useSelfHostedInfoModal'
import { useValidation } from '../composables/useValidation'

defineOptions({
  name: 'LoginPage'
})

interface FormState {
  username: string
  password: string
  selfHosted: boolean
  host: string
}

// Form state
const formState = reactive<FormState>({
  username: '',
  password: '',
  selfHosted: Boolean(config.selfHosted() ?? false),
  host: String(config.backendHost() || '')
})

// Composables
const router = useRouter()
const usersStore = useUsersStore()
const loadingModal = useLoadingModal()
const selfHostedModal = useSelfHostedInfoModal()
const { isNotEmpty, isValidEmail, isValidHttpUrl } = useValidation()
const { t: $t } = useI18n()

// Modals state
const modals = reactive({
  isFailLogin: false,
  isRecovery: false
})

// Computed
const isCustomApiAllowed = computed(() => config.isCustomApiAllowed())

// Methods
const handleSubmit = async () => {
  loadingModal.open()
  try {
    const response = await usersStore.login(formState.username, formState.password)
    if (!response.token) {
      modals.isFailLogin = true
    } else {
      window.location.href = '/'
    }
  } catch {
    modals.isFailLogin = true
  } finally {
    loadingModal.close()
  }
}

const closeFailModal = () => {
  modals.isFailLogin = false
}

const openRecoveryModal = () => {
  modals.isRecovery = true
}

const closeRecoveryModal = () => {
  modals.isRecovery = false
}

// Watchers
watch(() => formState.host, (value) => {
  config.backendHost(value)
})

watch(() => formState.selfHosted, (value) => {
  config.selfHosted(value)
})

// Lifecycle
onMounted(() => {
  if (usersStore.isUserAuthenticated) {
    router.push({ name: 'home' })
  }
})
</script>

