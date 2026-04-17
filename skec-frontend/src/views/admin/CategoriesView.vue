<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Categories</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Add form -->
      <div class="card h-fit">
        <h2 class="font-semibold text-gray-800 mb-4">{{ editing ? 'Edit Category' : 'New Category' }}</h2>
        <form @submit.prevent="save" class="space-y-4">
          <AppInput v-model="form.name"  label="Name"  placeholder="Mathematics" required @input="autoSlug" />
          <AppInput v-model="form.slug"  label="Slug"  placeholder="mathematics" required />
          <AppInput v-model="form.color" label="Color" type="color" />
          <AppInput v-model="form.icon"  label="Icon"  placeholder="book-open" />
          <AppButton type="submit" variant="primary" size="sm" :loading="saving" class="w-full">
            {{ editing ? 'Update' : 'Create' }}
          </AppButton>
          <AppButton v-if="editing" variant="ghost" size="sm" class="w-full" @click="reset">Cancel</AppButton>
        </form>
      </div>

      <!-- List -->
      <div class="lg:col-span-2 card">
        <AppLoader v-if="loading" />
        <div v-else class="space-y-3">
          <div
            v-for="cat in categories" :key="cat.id"
            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50"
          >
            <span class="w-4 h-4 rounded-full flex-shrink-0" :style="{ background: cat.color || '#999' }" />
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm text-gray-800">{{ cat.name }}</p>
              <p class="text-xs text-gray-400">{{ cat.notes_count }} notes</p>
            </div>
            <AppBadge :variant="cat.is_active ? 'active' : 'inactive'" :label="cat.is_active ? 'Active' : 'Inactive'" />
            <button @click="startEdit(cat)" class="text-xs text-primary-600 hover:underline">Edit</button>
            <button @click="deleteCategory(cat)" class="text-xs text-red-500 hover:underline">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { adminCategoriesApi } from '../../api/admin/categories'
import { slugify } from '../../utils/helpers'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppBadge  from '../../components/common/AppBadge.vue'
import AppLoader from '../../components/common/AppLoader.vue'

const loading    = ref(false)
const saving     = ref(false)
const editing    = ref(null)
const categories = ref([])
const form       = reactive({ name: '', slug: '', color: '#3498DB', icon: '', is_active: true })

function autoSlug() { form.slug = slugify(form.name) }

async function fetchCategories() {
  loading.value = true
  const res = await adminCategoriesApi.list()
  categories.value = res.data.data
  loading.value = false
}

async function save() {
  saving.value = true
  try {
    if (editing.value) await adminCategoriesApi.update(editing.value.id, form)
    else               await adminCategoriesApi.create(form)
    reset()
    fetchCategories()
  } finally { saving.value = false }
}

function startEdit(cat) {
  editing.value = cat
  Object.assign(form, { name: cat.name, slug: cat.slug, color: cat.color, icon: cat.icon, is_active: cat.is_active })
}

function reset() {
  editing.value = null
  Object.assign(form, { name: '', slug: '', color: '#3498DB', icon: '', is_active: true })
}

async function deleteCategory(cat) {
  if (!confirm(`Delete "${cat.name}"?`)) return
  await adminCategoriesApi.delete(cat.id)
  fetchCategories()
}

onMounted(fetchCategories)
</script>
