<template>
  <component :is="layout">
    <RouterView />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AuthLayout    from './layouts/AuthLayout.vue'
import AdminLayout   from './layouts/AdminLayout.vue'
import StudentLayout from './layouts/StudentLayout.vue'
import BlankLayout   from './layouts/BlankLayout.vue'
import PublicLayout  from './layouts/PublicLayout.vue'


const route = useRoute()

const layoutMap = {
  AuthLayout,
  AdminLayout,
  StudentLayout,
  BlankLayout,
  PublicLayout,
}

const layout = computed(() => {
  const name = route.meta.layout || 'BlankLayout'
  return layoutMap[name] || BlankLayout
})
</script>
