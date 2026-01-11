<template>
  <q-dialog :model-value="modelValue" @hide="$emit('close')" no-backdrop-dismiss>
    <div class="econumo-modal recovery-modal">
      <div class="recovery-modal-title">
        {{ $t('modules.user.modal.access_recovery.header') }}
      </div>
      
      <div class="recovery-modal-description">
        {{ $t('modules.user.modal.access_recovery.information') }}
      </div>
      
      <q-form
        ref="recoveryForm"
        @submit="handleSendRecoveryCode"
        class="recovery-form"
      >
        <div class="recovery-modal-input">
          <q-input 
            ref="emailInput"
            class="form-input -narrow full-width" 
            outlined 
            v-model="recovery"
            :disable="isCodeSent"
            :autofocus="!isCodeSent" 
            type="email" 
            :label="$t('modules.user.form.user.email.placeholder')"
            lazy-rules
            :rules="[
              val => isNotEmpty(val) || $t('modules.user.form.user.email.validation.required_field'),
              val => isValidEmail(val) || $t('modules.user.form.user.email.validation.invalid_email')
            ]"
          />
        </div>

        <q-btn
          v-if="!isCodeSent"
          class="econumo-btn -small -yellow full-width recovery-modal-button"
          :label="$t('modules.user.form.access_recovery.action.recover.label')"
          type="submit"
          flat
        />
      </q-form>

      <template v-if="isCodeSent">
        <div class="recovery-modal-description">
          {{ $t('modules.user.modal.access_recovery.instruction') }}
        </div>
        
        <div class="recovery-modal-input">
          <q-input 
            class="form-input -narrow full-width" 
            outlined 
            v-model="recoveryCode"
            type="text"
            :autofocus="true"
            :label="$t('modules.user.form.user.code.placeholder')"
            lazy-rules
            :rules="[
              val => isNotEmpty(val) || $t('modules.user.form.user.code.validation.required_field'),
              val => isValidRecoveryCode(val) || $t('modules.user.form.user.code.validation.invalid_code')
            ]"
          />
        </div>
        
        <div class="recovery-modal-input">
          <q-input 
            class="form-input -narrow full-width" 
            outlined 
            v-model="newPassword"
            type="password"
            :label="$t('modules.user.form.user.password.placeholder')"
            lazy-rules
            :rules="[
              val => isNotEmpty(val) || $t('modules.user.form.user.password.validation.required_field'),
              val => isValidPassword(val) || $t('modules.user.form.user.password.validation.invalid_password')
            ]"
          />
        </div>
      </template>

      <div>
        <q-btn
          v-if="isCodeSent"
          class="econumo-btn -small -yellow full-width recovery-modal-button"
          :label="$t('modules.user.form.access_recovery.action.change_password.label')"
          type="button"
          @click="handleResetPassword"
          flat
        />
      </div>
    </div>
  </q-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUsersStore } from 'stores/users'
import { useValidation } from '../../composables/useValidation'
import { useI18n } from 'vue-i18n'
import { QInput } from 'quasar'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'close'): void
}>()

const usersStore = useUsersStore()
const { isNotEmpty, isValidEmail, isValidRecoveryCode, isValidPassword } = useValidation()
const { t: $t } = useI18n()

const isCodeSent = ref(false)
const recovery = ref('')
const recoveryCode = ref('')
const newPassword = ref('')
const recoveryForm = ref(null)
const emailInput = ref<QInput | null>(null)

const handleSendRecoveryCode = async () => {
  const isValid = await emailInput.value?.validate()
  if (!isValid) return

  await usersStore.userRemindPassword(recovery.value).then(() => {
    isCodeSent.value = true
  }).catch((error) => {
    console.error(error)
  });
}

const handleResetPassword = async () => {
  const result = await usersStore.userResetPassword({
    username: recovery.value,
    code: recoveryCode.value,
    password: newPassword.value
  }).then(() => {
    emit('close')
  }).catch((error) => {
    console.error(error)
  });
}
</script> 