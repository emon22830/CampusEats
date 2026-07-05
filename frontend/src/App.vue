<script setup>
import { RouterView, RouterLink, useRouter, useRoute } from 'vue-router'
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()
const route = useRoute()

const showHeader = computed(() => !['welcome', 'login', 'register'].includes(route.name))

function goHome() {
  if (auth.role === 'vendor') router.push('/vendor/dashboard')
  else if (auth.role === 'admin') router.push('/admin')
  else router.push('/vendors')
}

function logout() {
  auth.logout()
  cart.clearCart()
  router.push('/login')
}
</script>

<template>
  <div class="mobile-container">
    <header v-if="showHeader">
      <div class="brand" @click="goHome">CampusEats</div>
      <div class="header-actions">
        <RouterLink v-if="auth.role === 'customer'" to="/orders" class="icon-link">📋</RouterLink>
        <RouterLink v-if="auth.role === 'customer'" to="/checkout" class="cart-pill">
          🛒 <span class="badge">{{ cart.totalCount }}</span>
        </RouterLink>
        <button class="btn-logout" @click="logout">Logout</button>
      </div>
    </header>

    <RouterView />
  </div>
</template>

<style>
body {
  background-color: #f3f4f6;
  font-family: system-ui, sans-serif;
  margin: 0;
  padding: 0;
  color: #1f2937;
}
.mobile-container {
  max-width: 420px;
  margin: 0 auto;
  background: #ffffff;
  min-height: 100vh;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  position: relative;
}
header {
  background: #ffffff;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 50;
  border-bottom: 1px solid #e5e7eb;
  box-sizing: border-box;
  width: 100%;
}
.brand {
  font-weight: 800;
  font-size: 1.5rem;
  color: #059669;
  cursor: pointer;
  letter-spacing: -0.025em;
}
.header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.icon-link {
  font-size: 1.1rem;
  text-decoration: none;
}
.cart-pill {
  background: #ecfdf5;
  color: #047857;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  border: 1px solid #d1fae5;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  cursor: pointer;
  text-decoration: none;
}
.badge {
  background: #059669;
  color: white;
  border-radius: 9999px;
  padding: 0.125rem 0.5rem;
  font-size: 0.75rem;
  font-weight: bold;
}
.btn-logout {
  background: #f3f4f6;
  color: #6b7280;
  border: none;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
}
.btn-back {
  background: #f3f4f6;
  color: #4b5563;
  border: none;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}
.btn-confirm {
  background: #059669;
  color: white;
  width: 100%;
  border: none;
  padding: 1rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  font-weight: bold;
  cursor: pointer;
  text-align: center;
  box-sizing: border-box;
}
.btn-confirm:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.error-banner {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
  font-size: 0.8rem;
  margin-bottom: 1rem;
}
.empty-state {
  text-align: center;
  color: #9ca3af;
  font-size: 0.85rem;
  padding: 3rem 1rem;
}
</style>
