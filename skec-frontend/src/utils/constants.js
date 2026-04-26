export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://dev.skecinstitute.in/api/v1'
export const APP_NAME = import.meta.env.VITE_APP_NAME || 'Sri Kumaran Education Centre'

export const ROLES = {
  ADMIN: 'admin',
  STUDENT: 'student',
}

export const NOTE_STATUS = {
  PUBLISHED: 'published',
  DRAFT: 'draft',
}

export const USER_STATUS = {
  ACTIVE: 'active',
  INACTIVE: 'inactive',
}

export const ROUTE_NAMES = {
  LOGIN: 'login',
  REGISTER: 'register',
  INVITATION_EXPIRED: 'invitation-expired',

  ADMIN_DASHBOARD: 'admin.dashboard',
  ADMIN_STUDENTS: 'admin.students',
  ADMIN_STUDENT: 'admin.student',
  ADMIN_NOTES: 'admin.notes',
  ADMIN_NOTE_UPLOAD: 'admin.note-upload',
  ADMIN_CATEGORIES: 'admin.categories',
  ADMIN_INVITATIONS: 'admin.invitations',
  ADMIN_SESSIONS: 'admin.sessions',
  ADMIN_SETTINGS: 'admin.settings',
  ADMIN_LOGS: 'admin.logs',

  STUDENT_NOTES: 'student.notes',
  STUDENT_VIEWER: 'student.viewer',
  STUDENT_PROFILE: 'student.profile',

  UNAUTHORIZED: 'unauthorized',
  NOT_FOUND: 'not-found',
}
