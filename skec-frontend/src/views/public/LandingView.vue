<template>
  <div class="min-h-screen bg-white font-sans" style="font-family: 'Outfit', 'Inter', sans-serif;">

    <!-- ── Nav ───────────────────────────────────────────── -->
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100' : 'bg-transparent'">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-primary-900 rounded-xl flex items-center justify-center">
            <span class="text-white font-bold text-sm">SK</span>
          </div>
          <span class="font-bold text-primary-900 text-sm hidden sm:block">
            {{ settings.app_name || 'Sri Kumaran Education Centre' }}
          </span>
        </div>
        <div class="flex items-center gap-2">
          <a
            href="#about"
            :class="scrolled ? 'text-gray-600' : 'text-white/90'"
            class="hidden sm:block px-3 py-2 text-sm font-medium hover:opacity-80 transition"
          >About</a>
          <a
            href="#achievements"
            :class="scrolled ? 'text-gray-600' : 'text-white/90'"
            class="hidden sm:block px-3 py-2 text-sm font-medium hover:opacity-80 transition"
          >Achievements</a>
          <RouterLink
            to="/login"
            class="ml-2 px-4 py-2 rounded-xl text-sm font-semibold bg-primary-700 text-white hover:bg-primary-800 transition shadow-sm"
          >
            Student Login
          </RouterLink>
        </div>
      </div>
    </nav>

    <!-- ── Hero ──────────────────────────────────────────── -->
    <section class="relative min-h-screen flex items-center overflow-hidden bg-primary-600 from-primary-950 via-primary-800 to-primary-600">
      <!-- Background pattern -->
      <div class="absolute inset-0 opacity-10" aria-hidden="true">
        <svg width="100%" height="100%">
          <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
          <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
      </div>
      <!-- Glow blobs -->
      <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl" />
      <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-primary-300/20 rounded-full blur-3xl" />

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 text-white">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
            {{ settings.hero_badge || 'Admissions Open — Batch 2025' }}
          </div>
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 tracking-tight">
            {{ settings.hero_title || 'Shaping Futures Through' }}
            <span class="block text-primary-200">{{ settings.hero_subtitle || 'Excellence in Education' }}</span>
          </h1>
          <p class="text-lg sm:text-xl text-white/75 mb-10 max-w-xl leading-relaxed">
            {{ settings.hero_description || 'Sri Kumaran Education Centre — nurturing academic excellence, character, and lifelong learning for every student.' }}
          </p>
          <div class="flex flex-wrap gap-4">
            <RouterLink
              to="/login"
              class="px-6 py-3.5 rounded-xl bg-white text-primary-900 font-bold text-sm hover:bg-primary-50 transition shadow-lg"
            >
              Access Learning Portal →
            </RouterLink>
            <a
              href="#about"
              class="px-6 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white font-semibold text-sm hover:bg-white/20 transition"
            >
              Learn More
            </a>
          </div>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/40 animate-bounce" @click="scrollToNext">
        <ChevronDownIcon class="w-6 h-6" />
      </div>
    </section>

    <!-- ── Stats bar ──────────────────────────────────────── -->
    <section id="ranking" class="bg-white border-b border-gray-100 scroll-mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
          <div v-for="stat in statItems" :key="stat.label" class="text-center">
            <div class="text-3xl sm:text-4xl font-extrabold text-primary-900 mb-1">
              {{ stat.value }}
            </div>
            <div class="text-sm text-gray-500 font-medium">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── About ──────────────────────────────────────────── -->
    <section id="about" class="py-20 sm:py-28 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <div>
            <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">About Us</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">
              {{ settings.about_title || 'A Legacy of Academic Excellence' }}
            </h2>
            <p class="text-gray-600 leading-relaxed mb-6 text-base sm:text-lg">
              {{ settings.about_description || 'Sri Kumaran Education Centre has been a beacon of quality education, empowering students with knowledge, skills, and values needed to thrive in the modern world.' }}
            </p>
            <div class="space-y-3">
              <div v-for="point in aboutPoints" :key="point" class="flex items-start gap-3">
                <CheckCircleIcon class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" />
                <span class="text-gray-700 text-sm sm:text-base">{{ point }}</span>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="card in aboutCards" :key="card.title"
                 class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
              <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-4', card.bg]">
                <component :is="card.icon" :class="['w-5 h-5', card.color]" />
              </div>
              <h3 class="font-bold text-gray-900 text-sm mb-1">{{ card.title }}</h3>
              <p class="text-gray-500 text-xs leading-relaxed">{{ card.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Current Batch ─────────────────────────────────── -->
    <section class="py-20 sm:py-28 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">Enrollment</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
            {{ settings.batch_title || 'Current Batch 2025' }}
          </h2>
          <p class="text-gray-500 max-w-xl mx-auto text-base">
            {{ settings.batch_description || 'Join our growing community of learners. Limited seats available.' }}
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
          <div v-for="batch in batchItems" :key="batch.name"
               class="relative overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br p-6 hover:shadow-lg transition-all duration-200 group"
               :class="batch.featured ? 'from-primary-700 to-primary-900 text-white border-primary-700 shadow-md' : 'from-gray-50 to-white'"
          >
            <div v-if="batch.featured" class="absolute top-4 right-4">
              <span class="bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">Popular</span>
            </div>
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-4', batch.featured ? 'bg-white/20' : 'bg-primary-50']">
              <AcademicCapIcon :class="['w-5 h-5', batch.featured ? 'text-white' : 'text-primary-700']" />
            </div>
            <h3 :class="['font-bold text-lg mb-1', batch.featured ? 'text-white' : 'text-gray-900']">{{ batch.name }}</h3>
            <p :class="['text-sm mb-4 leading-relaxed', batch.featured ? 'text-white/75' : 'text-gray-500']">{{ batch.desc }}</p>
            <div :class="['text-xs font-semibold', batch.featured ? 'text-primary-200' : 'text-primary-700']">
              {{ batch.seats }} seats available
            </div>
          </div>
        </div>

        <!-- Openings -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 sm:p-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h3 class="font-bold text-amber-900 text-lg mb-1">
                {{ settings.openings_title || 'Current Openings & Enrollment' }}
              </h3>
              <p class="text-amber-700 text-sm">
                {{ settings.openings_description || 'Secure your seat for the upcoming academic year. Reach out to our team.' }}
              </p>
            </div>
            <a
              :href="'mailto:' + (settings.contact_email || 'admin@srikumaran.in')"
              class="flex-shrink-0 px-5 py-3 bg-amber-600 text-white rounded-xl text-sm font-bold hover:bg-amber-700 transition shadow-sm"
            >
              Enquire Now →
            </a>
          </div>
          <div class="mt-5 grid sm:grid-cols-3 gap-4">
            <div v-for="opening in openings" :key="opening.title" class="bg-white rounded-xl p-4 border border-amber-100">
              <div class="font-semibold text-gray-900 text-sm mb-0.5">{{ opening.title }}</div>
              <div class="text-xs text-gray-500">{{ opening.detail }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Achievements ───────────────────────────────────── -->
    <section id="achievements" class="py-20 sm:py-28 bg-primary-700 from-primary-950 to-primary-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-300 mb-3 block">Our Track Record</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">
            {{ settings.achievements_title || 'Milestones & Achievements' }}
          </h2>
          <p class="text-white/60 max-w-xl mx-auto text-base">
            {{ settings.achievements_description || 'Decades of excellence reflected in the success of our students.' }}
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(ach, i) in achievements" :key="i"
               class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-6 hover:bg-white/15 transition-colors">
            <div class="text-3xl sm:text-4xl font-extrabold text-primary-200 mb-2">{{ ach.metric }}</div>
            <div class="font-bold text-white text-base mb-2">{{ ach.title }}</div>
            <div class="text-white/60 text-sm leading-relaxed">{{ ach.description }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Testimonials ───────────────────────────────────── -->
    <section class="py-20 sm:py-28 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">What Students Say</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
            {{ settings.testimonials_title || 'Voices from Our Community' }}
          </h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(t, i) in testimonials" :key="i"
               class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex gap-1 mb-3">
              <StarIcon v-for="s in 5" :key="s" class="w-4 h-4 text-amber-400 fill-amber-400" />
            </div>
            <p class="text-gray-700 text-sm leading-relaxed mb-5 italic">"{{ t.quote }}"</p>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                <span class="text-primary-700 font-bold text-xs">{{ t.name.charAt(0) }}</span>
              </div>
              <div>
                <div class="font-semibold text-gray-900 text-sm">{{ t.name }}</div>
                <div class="text-gray-400 text-xs">{{ t.batch }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── CTA ───────────────────────────────────────────── -->
    <section class="py-16 sm:py-20 bg-white border-t border-gray-100">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
          {{ settings.cta_title || 'Ready to Begin Your Journey?' }}
        </h2>
        <p class="text-gray-500 text-base mb-8">
          {{ settings.cta_description || 'Join hundreds of students achieving their academic goals at SKEC.' }}
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
          <a
            :href="'mailto:' + (settings.contact_email || 'admin@srikumaran.in')"
            class="px-6 py-3.5 bg-primary-700 text-white rounded-xl font-bold text-sm hover:bg-primary-800 transition shadow-md"
          >
            Contact Us
          </a>
          <RouterLink
            to="/login"
            class="px-6 py-3.5 border-2 border-primary-700 text-primary-700 rounded-xl font-bold text-sm hover:bg-primary-50 transition"
          >
            Student Login
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- ── Footer ─────────────────────────────────────────── -->
    <footer class="bg-primary-700 text-white py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-xs">SK</span>
          </div>
          <span class="font-bold text-sm text-white/80">{{ settings.app_name || 'Sri Kumaran Education Centre' }}</span>
        </div>
        <p class="text-white/40 text-xs text-center">
          © {{ new Date().getFullYear() }} {{ settings.app_name || 'Sri Kumaran Education Centre' }}. All rights reserved.
        </p>
        <a :href="'mailto:' + (settings.contact_email || 'admin@srikumaran.in')"
           class="text-white/50 text-xs hover:text-white/80 transition">
          {{ settings.contact_email || 'admin@srikumaran.in' }}
        </a>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { settingsApi } from '../../api/settings'
import {
  ChevronDownIcon, CheckCircleIcon, AcademicCapIcon, StarIcon,
  BookOpenIcon, TrophyIcon, UsersIcon, ComputerDesktopIcon,
} from '@heroicons/vue/24/outline'

const scrolled  = ref(false)
const settings  = ref({})
const loading   = ref(true)

function onScroll() { scrolled.value = window.scrollY > 40 }

onMounted(async () => {
  window.addEventListener('scroll', onScroll)
  try {
    const res = await settingsApi.getPublic()
    settings.value = res.data.data || {}
  } catch {}
  loading.value = false
})
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ── Computed sections from settings ──────────────────────────
const statItems = computed(() => [
  { value: settings.value.stat_students   || '500+',  label: 'Students Enrolled' },
  { value: settings.value.stat_years      || '10+',   label: 'Years of Excellence' },
  { value: settings.value.stat_pass_rate  || '98%',   label: 'Pass Rate' },
  { value: settings.value.stat_rank_1     || '50+',   label: 'District Rank 1s' },
])

const aboutPoints = computed(() => {
  const raw = settings.value.about_points
  if (raw) return raw.split('|').map(s => s.trim()).filter(Boolean)
  return [
    'Expert faculty with years of teaching experience',
    'Personalised attention for every student',
    'Digital learning materials and resources',
    'Regular assessments and performance tracking',
  ]
})

const aboutCards = [
  { title: 'Expert Faculty', desc: 'Experienced educators committed to student success', icon: UsersIcon, bg: 'bg-blue-50', color: 'text-blue-600' },
  { title: 'Smart Learning', desc: 'Digital resources and PDF notes accessible anytime', icon: ComputerDesktopIcon, bg: 'bg-purple-50', color: 'text-purple-600' },
  { title: 'Top Results', desc: 'Consistently producing top rankers district-wide', icon: TrophyIcon, bg: 'bg-amber-50', color: 'text-amber-600' },
  { title: 'Study Materials', desc: 'Comprehensive notes for all subjects and grades', icon: BookOpenIcon, bg: 'bg-green-50', color: 'text-green-600' },
]

const batchItems = computed(() => {
  const raw = settings.value.batch_items
  if (raw) {
    try { return JSON.parse(raw) } catch {}
  }
  return [
    { name: 'Class 6–8',  desc: 'Foundation years — building core concepts and study habits', seats: '40', featured: false },
    { name: 'Class 9–10', desc: 'Board exam preparation with focus on scores and concept mastery', seats: '60', featured: true },
    { name: 'Class 11–12', desc: 'Higher secondary coaching for science and commerce streams', seats: '35', featured: false },
  ]
})

const openings = computed(() => {
  const raw = settings.value.openings_items
  if (raw) {
    try { return JSON.parse(raw) } catch {}
  }
  return [
    { title: 'New Admissions',     detail: 'June – July 2025 enrollment open' },
    { title: 'Scholarship Test',   detail: 'Merit-based discounts available' },
    { title: 'Trial Classes',      detail: 'Free demo session for new students' },
  ]
})

const achievements = computed(() => {
  const raw = settings.value.achievements_items
  if (raw) {
    try { return JSON.parse(raw) } catch {}
  }
  return [
    { metric: '98%',  title: 'Board Exam Pass Rate',    description: 'Students consistently achieving above 85% in board examinations.' },
    { metric: '50+',  title: 'District Rank Holders',   description: 'Proud to have produced over 50 district toppers across all grades.' },
    { metric: '500+', title: 'Students Mentored',       description: 'More than 500 students have graduated and gone on to pursue careers.' },
    { metric: '10+',  title: 'Years of Service',        description: 'A decade of trusted academic excellence in the community.' },
    { metric: '95%',  title: 'Parent Satisfaction',     description: 'Parents trust us with their children\'s academic future.' },
    { metric: '15+',  title: 'Subject Specialists',     description: 'Dedicated subject experts covering every topic in depth.' },
  ]
})

const testimonials = computed(() => {
  const raw = settings.value.testimonials_items
  if (raw) {
    try { return JSON.parse(raw) } catch {}
  }
  return [
    { quote: 'SKEC completely transformed how I approach studies. I went from average to district topper!', name: 'Priya S.', batch: 'Class 10, Batch 2024' },
    { quote: 'The teachers here genuinely care about every student. The digital notes are incredibly helpful.', name: 'Arjun M.', batch: 'Class 12, Batch 2023' },
    { quote: 'Best coaching centre in the district. My son scored 98% in boards thanks to SKEC.', name: 'Meena R.', batch: 'Parent' },
  ]
})

function scrollToNext() {
  const el = document.getElementById('ranking') // your next section id
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
</style>