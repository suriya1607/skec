<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Subjects</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Add / Edit form -->
      <div class="card h-fit">
        <h2 class="font-semibold text-gray-800 mb-4">{{ editing ? 'Edit Subject' : 'New Subject' }}</h2>
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
            v-for="sub in subjects" :key="sub.id"
            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50"
          >
            <span class="w-4 h-4 rounded-full flex-shrink-0" :style="{ background: sub.color || '#999' }" />
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm text-gray-800">{{ sub.name }}</p>
              <p class="text-xs text-gray-400">{{ sub.notes_count }} notes</p>
            </div>
            <AppBadge :variant="sub.is_active ? 'active' : 'inactive'" :label="sub.is_active ? 'Active' : 'Inactive'" />
            <button @click="startEdit(sub)" class="text-xs text-primary-600 hover:underline">Edit</button>
            <button @click="deleteSubject(sub)" class="text-xs text-red-500 hover:underline">Delete</button>
          </div>
          <p v-if="!subjects.length" class="text-sm text-gray-400 text-center py-6">No subjects yet.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { adminSubjectsApi } from '../../api/admin/subjects'
import { slugify } from '../../utils/helpers'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppBadge  from '../../components/common/AppBadge.vue'
import AppLoader from '../../components/common/AppLoader.vue'

const loading  = ref(false)
const saving   = ref(false)
const editing  = ref(null)
const subjects = ref([])
const form     = reactive({ name: '', slug: '', color: '#3498DB', icon: '', is_active: true })

function autoSlug() { form.slug = slugify(form.name) }

async function fetchSubjects() {
  loading.value = true
  const res = await adminSubjectsApi.list()
  subjects.value = res.data.data
  loading.value = false
}

async function save() {
  saving.value = true
  try {
    if (editing.value) await adminSubjectsApi.update(editing.value.id, form)
    else               await adminSubjectsApi.create(form)
    reset()
    fetchSubjects()
  } finally { saving.value = false }
}

function startEdit(sub) {
  editing.value = sub
  Object.assign(form, { name: sub.name, slug: sub.slug, color: sub.color, icon: sub.icon, is_active: sub.is_active })
}

function reset() {
  editing.value = null
  Object.assign(form, { name: '', slug: '', color: '#3498DB', icon: '', is_active: true })
}

async function deleteSubject(sub) {
  if (!confirm(`Delete "${sub.name}"?`)) return
  await adminSubjectsApi.delete(sub.id)
  fetchSubjects()
}

onMounted(fetchSubjects)
</script>
