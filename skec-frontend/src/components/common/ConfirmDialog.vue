<template>
  <AppModal v-model="show" :title="title" size="sm">
    <div class="flex items-start gap-4">
      <div :class="['w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0', iconBg]">
        <ExclamationTriangleIcon class="w-5 h-5 text-red-600" />
      </div>
      <div>
        <p class="text-sm text-gray-700">{{ message }}</p>
      </div>
    </div>
    <template #footer>
      <AppButton variant="secondary" size="sm" @click="cancel">Cancel</AppButton>
      <AppButton :variant="confirmVariant" size="sm" :loading="loading" @click="confirm">
        {{ confirmLabel }}
      </AppButton>
    </template>
  </AppModal>
</template>

<script setup>
import { ref } from 'vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import AppModal  from './AppModal.vue'
import AppButton from './AppButton.vue'

const props = defineProps({
  title:          { type: String, default: 'Are you sure?' },
  message:        { type: String, default: 'This action cannot be undone.' },
  confirmLabel:   { type: String, default: 'Confirm' },
  confirmVariant: { type: String, default: 'danger' },
})

const emit = defineEmits(['confirm', 'cancel'])
const show    = ref(true)
const loading = ref(false)
const iconBg  = 'bg-red-50'

function confirm() {
  loading.value = true
  emit('confirm')
}
function cancel() {
  show.value = false
  emit('cancel')
}
</script>
