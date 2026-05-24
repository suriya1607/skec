<template>
  <div class="min-h-screen bg-white" style="font-family: 'Outfit', 'Inter', sans-serif;">

    <!-- ── Sticky Navbar (same as LandingView) ───────────────────────── -->
    <nav
      class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
      :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100' : 'bg-transparent'"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        <RouterLink to="/" class="flex items-center gap-3 flex-shrink-0">
          <img
            v-if="settings.app_logo"
            :src="settings.app_logo"
            :alt="settings.app_name || 'SKEC'"
            class="h-9 w-auto object-contain"
          />
          <div v-else class="w-9 h-9 bg-primary-900 rounded-xl flex items-center justify-center">
            <span class="text-white font-bold text-sm">SK</span>
          </div>
          <span
            class="font-bold text-sm hidden sm:block"
            :class="scrolled ? 'text-primary-900' : 'text-white'"
          >
            {{ settings.app_name || 'Sri Kumaran Education Centre' }}
          </span>
        </RouterLink>

        <div class="hidden sm:flex items-center gap-1">
          <RouterLink
            to="/"
            class="px-3 py-2 text-sm font-medium transition-colors rounded-lg"
            :class="scrolled ? 'text-gray-600 hover:text-primary-700' : 'text-white/80 hover:text-white'"
          >Home</RouterLink>
          <a
            href="/#about"
            class="px-3 py-2 text-sm font-medium transition-colors rounded-lg"
            :class="scrolled ? 'text-gray-600 hover:text-primary-700' : 'text-white/80 hover:text-white'"
          >About</a>
          <a
            href="/#batch"
            class="px-3 py-2 text-sm font-medium transition-colors rounded-lg"
            :class="scrolled ? 'text-gray-600 hover:text-primary-700' : 'text-white/80 hover:text-white'"
          >Batch</a>
        </div>

        <div class="flex items-center gap-2">
          <RouterLink
            to="/login"
            class="px-4 py-2 rounded-xl text-sm font-bold bg-primary-700 text-white hover:bg-primary-800 transition shadow-sm"
          >
            Student Login
          </RouterLink>
        </div>
      </div>
    </nav>

    <!-- ── Hero Section ──────────────────────────────────────────────── -->
    <section class="relative pt-32 pb-16 sm:pt-40 sm:pb-20 overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-primary-600 to-primary-900">
        <div class="absolute inset-0 opacity-10" aria-hidden="true">
          <svg width="100%" height="100%">
            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
            </pattern>
            <rect width="100%" height="100%" fill="url(#grid)" />
          </svg>
        </div>
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl" />
        <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-primary-300/20 rounded-full blur-3xl" />
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
        <div class="max-w-2xl">
          <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-4 tracking-tight">
            Get in Touch
          </h1>
          <p class="text-lg sm:text-xl text-white/80 mb-0">
            Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
          </p>
        </div>
      </div>
    </section>

    <!-- ── Main Content ─────────────────────────────────────────────── -->
    <section class="py-16 sm:py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-10 lg:gap-12">

          <!-- ── Contact Info Cards ─────────────────────────────── -->
          <div class="lg:col-span-1 space-y-6">
            
            <!-- Email -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
              <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <h3 class="font-bold text-gray-900 text-base mb-2">Email</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-3">
                Send us an email and we'll get back to you within 24 hours.
              </p>
              <a
                :href="'mailto:' + (settings.contact_email || 'admin@srikumaran.in')"
                class="text-primary-700 font-semibold text-sm hover:text-primary-800 transition"
              >
                {{ settings.contact_email || 'admin@srikumaran.in' }}
              </a>
            </div>

            <!-- Phone -->
            <div v-if="settings.contact_phone" class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
              <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <h3 class="font-bold text-gray-900 text-base mb-2">Phone</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-3">
                Call us during business hours. We're here to help!
              </p>
              <a
                :href="'tel:' + settings.contact_phone"
                class="text-primary-700 font-semibold text-sm hover:text-primary-800 transition"
              >
                {{ settings.contact_phone }}
              </a>
            </div>

            <!-- Address -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
              <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="font-bold text-gray-900 text-base mb-2">Location</h3>
              <p v-if="settings.contact_address" class="text-gray-600 text-sm leading-relaxed">
                {{ settings.contact_address }}
              </p>
              <p v-else class="text-gray-600 text-sm leading-relaxed">
                Sri Kumaran Education Centre<br />
                Education District, City<br />
                Pin Code
              </p>
            </div>

            <!-- Hours -->
            <div class="bg-primary-50 rounded-2xl p-6 border border-primary-100">
              <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="font-bold text-gray-900 text-base mb-2">Business Hours</h3>
              <div class="text-gray-600 text-sm space-y-1">
                <p>Mon - Fri: 9:00 AM - 6:00 PM</p>
                <p>Sat: 9:00 AM - 2:00 PM</p>
                <p>Sun: Closed</p>
              </div>
            </div>

          </div>

          <!-- ── Contact Form ───────────────────────────────────── -->
          <div class="lg:col-span-2">
            <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Send us a Message</h2>

              <form @submit.prevent="submitForm" class="space-y-5">

                <!-- Name -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    placeholder="Your full name"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition"
                    :class="errors.name ? 'border-red-500' : ''"
                    required
                  />
                  <p v-if="errors.name" class="text-red-600 text-xs mt-1">{{ errors.name }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="your@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition"
                    :class="errors.email ? 'border-red-500' : ''"
                    required
                  />
                  <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    placeholder="+91 XXXXX XXXXX"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition"
                  />
                  <p v-if="errors.phone" class="text-red-600 text-xs mt-1">{{ errors.phone }}</p>
                </div>

                <!-- Subject -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Subject *</label>
                  <select
                    v-model="form.subject"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition"
                    :class="errors.subject ? 'border-red-500' : ''"
                    required
                  >
                    <option value="">Select a subject</option>
                    <option value="admission">Admission Inquiry</option>
                    <option value="academics">Academic Concerns</option>
                    <option value="fees">Fee Related</option>
                    <option value="facilities">Facilities & Infrastructure</option>
                    <option value="general">General Inquiry</option>
                    <option value="other">Other</option>
                  </select>
                  <p v-if="errors.subject" class="text-red-600 text-xs mt-1">{{ errors.subject }}</p>
                </div>

                <!-- Message -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Message *</label>
                  <textarea
                    v-model="form.message"
                    placeholder="Tell us more about your inquiry..."
                    rows="6"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition resize-none"
                    :class="errors.message ? 'border-red-500' : ''"
                    required
                  />
                  <p v-if="errors.message" class="text-red-600 text-xs mt-1">{{ errors.message }}</p>
                </div>

                <!-- Success/Error Messages -->
                <div
                  v-if="submitMessage"
                  class="p-4 rounded-xl text-sm"
                  :class="submitMessage.type === 'success'
                    ? 'bg-green-50 border border-green-200 text-green-800'
                    : 'bg-red-50 border border-red-200 text-red-800'"
                >
                  {{ submitMessage.text }}
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="w-full px-6 py-3.5 bg-primary-700 text-white rounded-xl font-bold text-sm hover:bg-primary-800 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                  >
                    <span v-if="!isSubmitting">Send Message</span>
                    <span v-else>Sending...</span>
                    <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                  </button>
                </div>

              </form>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ── CTA ──────────────────────────────────────────────────── -->
    <section class="py-14 sm:py-20 bg-gray-50 border-t border-gray-100">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
          Ready to Enroll?
        </h2>
        <p class="text-gray-500 text-sm sm:text-base mb-8">
          Join hundreds of students achieving their academic goals at SKEC.
        </p>
        <RouterLink
          to="/login"
          class="inline-block px-6 py-3.5 bg-primary-700 text-white rounded-xl font-bold text-sm hover:bg-primary-800 transition shadow-md"
        >
          Access Student Portal →
        </RouterLink>
      </div>
    </section>

    <!-- ── Footer ────────────────────────────────────────────────── -->
    <footer class="bg-primary-700 text-white py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <img v-if="settings.app_logo" :src="settings.app_logo" class="h-7 w-auto opacity-80" alt="Logo" />
            <div v-else class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xs">SK</span>
            </div>
            <span class="font-bold text-sm text-white/80">
              {{ settings.app_name || 'Sri Kumaran Education Centre' }}
            </span>
          </div>
          <p class="text-white/40 text-xs text-center">
            © {{ new Date().getFullYear() }} {{ settings.app_name || 'Sri Kumaran Education Centre' }}. All rights reserved.
          </p>
          <a :href="'mailto:' + (settings.contact_email || 'admin@srikumaran.in')" class="text-white/50 text-xs hover:text-white/80 transition">
            {{ settings.contact_email || 'admin@srikumaran.in' }}
          </a>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { contactApi } from '../../api/contact'
import { settingsApi } from '../../api/settings'

const scrolled = ref(false)
const settings = ref({})

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
})

const errors = ref({})
const isSubmitting = ref(false)
const submitMessage = ref(null)

function onScroll() {
  scrolled.value = window.scrollY > 40
}

function validateForm() {
  errors.value = {}

  if (!form.value.name?.trim()) {
    errors.value.name = 'Name is required'
  }

  if (!form.value.email?.trim()) {
    errors.value.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Please enter a valid email'
  }

  if (form.value.phone && !/^[\d\s+()-]*$/.test(form.value.phone)) {
    errors.value.phone = 'Please enter a valid phone number'
  }

  if (!form.value.subject) {
    errors.value.subject = 'Please select a subject'
  }

  if (!form.value.message?.trim()) {
    errors.value.message = 'Message is required'
  }

  return Object.keys(errors.value).length === 0
}

async function submitForm() {
  submitMessage.value = null

  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    await contactApi.sendMessage(form.value)
    submitMessage.value = {
      type: 'success',
      text: 'Thank you! Your message has been sent successfully. We\'ll get back to you soon.',
    }
    form.value = {
      name: '',
      email: '',
      phone: '',
      subject: '',
      message: '',
    }
    setTimeout(() => {
      submitMessage.value = null
    }, 5000)
  } catch (error) {
    submitMessage.value = {
      type: 'error',
      text: error.response?.data?.message || 'Failed to send message. Please try again.',
    }
  } finally {
    isSubmitting.value = false
  }
}

onMounted(async () => {
  window.addEventListener('scroll', onScroll)
  try {
    const res = await settingsApi.getPublic()
    settings.value = res.data.data || {}
  } catch {}
  return () => {
    window.removeEventListener('scroll', onScroll)
  }
})
</script>

<style scoped>
</style>
