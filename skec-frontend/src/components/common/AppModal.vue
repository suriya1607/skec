<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
        role="dialog"
        aria-modal="true"
      >
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          @click="$emit('update:modelValue', false)"
        />

        <!-- Panel -->
        <div
          class="relative bg-white w-full overflow-hidden flex flex-col"
          :class="[sizeClass, 'rounded-t-2xl sm:rounded-2xl shadow-2xl max-h-[92dvh] sm:max-h-[90vh]']"
          @click.stop
        >
          <!-- Header -->
          <div
            v-if="title"
            class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 flex-shrink-0"
          >
            <!-- Mobile drag indicator -->
            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-gray-300 rounded-full sm:hidden" />
            <h3 class="font-semibold text-gray-900 text-base sm:text-lg">{{ title }}</h3>
            <button
              @click="$emit('update:modelValue', false)"
              class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors ml-3 flex-shrink-0"
            >
              <XMarkIcon class="w-5 h-5 text-gray-500" />
            </button>
          </div>
          <!-- Close button when no title -->
          <div v-else class="absolute top-3 right-4 z-10 sm:hidden">
            <button
              @click="$emit('update:modelValue', false)"
              class="p-1.5 rounded-lg bg-gray-100"
            >
              <XMarkIcon class="w-4 h-4 text-gray-500" />
            </button>
          </div>

          <!-- Body -->
          <div class="px-5 sm:px-6 py-5 overflow-y-auto flex-1">
            <slot />
          </div>

          <!-- Footer -->
          <div
            v-if="$slots.footer"
            class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 flex-shrink-0"
          >
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  title:      { type: String,  default: '' },
  size:       { type: String,  default: 'md' },
})
defineEmits(['update:modelValue'])

// Lock body scroll when modal is open
watch(() => props.modelValue, (open) => {
  if (open) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

const sizeClass = computed(() => ({
  sm: 'sm:max-w-sm',
  md: 'sm:max-w-lg',
  lg: 'sm:max-w-2xl',
  xl: 'sm:max-w-4xl',
}[props.size]))
</script>

<style scoped>
/* Desktop: scale in from center */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Mobile: slide up from bottom */
@media (max-width: 639px) {
  .modal-enter-active .relative,
  .modal-leave-active .relative {
    transition: transform 0.25s cubic-bezier(0.32, 0.72, 0, 1);
  }
  .modal-enter-from .relative {
    transform: translateY(100%);
  }
  .modal-leave-to .relative {
    transform: translateY(100%);
  }
}

/* Desktop: scale from center */
@media (min-width: 640px) {
  .modal-enter-active .relative,
  .modal-leave-active .relative {
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
  .modal-enter-from .relative {
    transform: scale(0.95) translateY(-8px);
    opacity: 0;
  }
  .modal-leave-to .relative {
    transform: scale(0.95) translateY(-8px);
    opacity: 0;
  }
}
</style>