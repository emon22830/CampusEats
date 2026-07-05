import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

function roleHome(role) {
  if (role === 'vendor') return '/vendor/dashboard'
  if (role === 'admin') return '/admin'
  return '/vendors'
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'welcome',
      component: () => import('../views/WelcomeView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { guestOnly: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue'),
      meta: { guestOnly: true },
    },
    {
      path: '/vendors',
      name: 'vendors',
      component: () => import('../views/VendorListView.vue'),
      meta: { requiresAuth: true, roles: ['customer'] },
    },
    {
      path: '/vendors/:id',
      name: 'vendor-menu',
      component: () => import('../views/VendorMenuView.vue'),
      meta: { requiresAuth: true, roles: ['customer'] },
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: () => import('../views/CheckoutView.vue'),
      meta: { requiresAuth: true, roles: ['customer'] },
    },
    {
      path: '/order-success/:id',
      name: 'order-success',
      component: () => import('../views/OrderSuccessView.vue'),
      meta: { requiresAuth: true, roles: ['customer'] },
    },
    {
      path: '/orders',
      name: 'orders',
      component: () => import('../views/OrderHistoryView.vue'),
      meta: { requiresAuth: true, roles: ['customer'] },
    },
    {
      path: '/vendor/dashboard',
      name: 'vendor-dashboard',
      component: () => import('../views/VendorManageView.vue'),
      meta: { requiresAuth: true, roles: ['vendor'] },
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/AdminPanelView.vue'),
      meta: { requiresAuth: true, roles: ['admin'] },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFoundView.vue'),
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return roleHome(auth.role)
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.roles && !to.meta.roles.includes(auth.role)) {
    return roleHome(auth.role)
  }

  return true
})

export default router
