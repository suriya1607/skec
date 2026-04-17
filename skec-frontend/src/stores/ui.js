import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const sidebarOpen  = ref(true)
  const isLoading    = ref(false)
  const modalState   = ref({ open: false, component: null, props: {} })

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  function setLoading(val) {
    isLoading.value = val
  }

  function openModal(component, props = {}) {
    modalState.value = { open: true, component, props }
  }

  function closeModal() {
    modalState.value = { open: false, component: null, props: {} }
  }

  return { sidebarOpen, isLoading, modalState, toggleSidebar, setLoading, openModal, closeModal }
})
