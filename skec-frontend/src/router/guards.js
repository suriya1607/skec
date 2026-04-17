import { useAuthStore } from '../stores/auth'

let intendedRoute = null

export function setupGuards(router) {
  router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    const requiresAuth = to.meta.requiresAuth
    const requiredRole = to.meta.role

    // If route requires auth and user is not logged in
    if (requiresAuth && !authStore.isLoggedIn) {
      intendedRoute = to.fullPath
      return next({ name: 'login' })
    }

    // If already logged in and trying to access login
    if (to.name === 'login' && authStore.isLoggedIn) {
      return next({ name: authStore.isAdmin ? 'admin.dashboard' : 'student.notes' })
    }

    // Role check
    if (requiresAuth && requiredRole && authStore.isLoggedIn) {
      if (authStore.user?.role !== requiredRole) {
        return next({ name: 'unauthorized' })
      }
    }

    // After successful login, redirect to intended route
    if (to.name !== 'login' && intendedRoute) {
      const redirect = intendedRoute
      intendedRoute = null
      return next(redirect)
    }

    next()
  })
}

export function getIntendedRoute() {
  return intendedRoute
}
