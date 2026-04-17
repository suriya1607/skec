import { createRouter, createWebHistory } from 'vue-router'
import { setupGuards } from './guards'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // ── Public / Auth ──────────────────────────────────────────
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/auth/LoginView.vue'),
      meta: { layout: 'AuthLayout', requiresAuth: false },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/auth/RegisterView.vue'),
      meta: { layout: 'AuthLayout', requiresAuth: false },
    },
    {
      path: '/invitation-expired',
      name: 'invitation-expired',
      component: () => import('../views/auth/InvitationExpiredView.vue'),
      meta: { layout: 'AuthLayout', requiresAuth: false },
    },

    // ── Admin ──────────────────────────────────────────────────
    {
      path: '/admin',
      redirect: '/admin/dashboard',
    },
    {
      path: '/admin/dashboard',
      name: 'admin.dashboard',
      component: () => import('../views/admin/DashboardView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/students',
      name: 'admin.students',
      component: () => import('../views/admin/StudentsView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/students/:id',
      name: 'admin.student',
      component: () => import('../views/admin/StudentDetailView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/notes',
      name: 'admin.notes',
      component: () => import('../views/admin/NotesView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/notes/upload',
      name: 'admin.note-upload',
      component: () => import('../views/admin/NoteUploadView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/categories',
      name: 'admin.categories',
      component: () => import('../views/admin/CategoriesView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/invitations',
      name: 'admin.invitations',
      component: () => import('../views/admin/InvitationsView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/sessions',
      name: 'admin.sessions',
      component: () => import('../views/admin/SessionsView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/settings',
      name: 'admin.settings',
      component: () => import('../views/admin/SettingsView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/admin/logs',
      name: 'admin.logs',
      component: () => import('../views/admin/ActivityLogsView.vue'),
      meta: { layout: 'AdminLayout', requiresAuth: true, role: 'admin' },
    },

    // ── Student ────────────────────────────────────────────────
    {
      path: '/',
      redirect: '/notes',
    },
    {
      path: '/notes',
      name: 'student.notes',
      component: () => import('../views/student/DashboardView.vue'),
      meta: { layout: 'StudentLayout', requiresAuth: true, role: 'student' },
    },
    {
      path: '/notes/:id/view',
      name: 'student.viewer',
      component: () => import('../views/student/NoteViewer.vue'),
      meta: { layout: 'BlankLayout', requiresAuth: true, role: 'student' },
    },
    {
      path: '/profile',
      name: 'student.profile',
      component: () => import('../views/student/ProfileView.vue'),
      meta: { layout: 'StudentLayout', requiresAuth: true, role: 'student' },
    },

    // ── Errors ─────────────────────────────────────────────────
    {
      path: '/unauthorized',
      name: 'unauthorized',
      component: () => import('../views/errors/UnauthorizedView.vue'),
      meta: { layout: 'BlankLayout' },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/errors/NotFoundView.vue'),
      meta: { layout: 'BlankLayout' },
    },
  ],
})

setupGuards(router)

export default router
