<template>
  <main class="login-screen">
    <div style="font-size: 3.5rem; margin-bottom: 1rem;">🔐</div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #059669; margin: 0 0 0.5rem 0;">Student Portal Login</h2>
    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 1.5rem;">Access CampusEats pre-orders using your account</p>

    <form class="login-form" @submit.prevent="handleLogin">
      <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>

      <input
        v-model="email"
        type="email"
        placeholder="Email"
        class="login-input"
        required
        autocomplete="email"
      />
      <input
        v-model="password"
        type="password"
        placeholder="Password"
        class="login-input"
        style="margin-top: 0.75rem"
        required
        autocomplete="current-password"
      />

      <button class="btn-login" type="submit" :disabled="loading">
        {{ loading ? 'Signing in…' : 'Secure Login →' }}
      </button>
    </form>

    <RouterLink to="/register" class="link-register">No account yet? Register</RouterLink>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ApiError } from '@/api/client'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

const auth = useAuthStore()
const router = useRouter()

async function handleLogin() {
  loading.value = true
  errorMessage.value = ''

  try {
    const user = await auth.login(email.value, password.value)
    if (user.role === 'vendor') router.push('/vendor/dashboard')
    else if (user.role === 'admin') router.push('/admin')
    else router.push('/vendors')
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-screen {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  text-align: center;
  box-sizing: border-box;
}
.login-form {
  width: 100%;
}
.login-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  box-sizing: border-box;
  font-size: 0.875rem;
  outline: none;
}
.btn-login {
  background: #059669;
  color: white;
  width: 100%;
  border: none;
  padding: 1rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  font-weight: bold;
  cursor: pointer;
  margin-top: 1.5rem;
  box-sizing: border-box;
}
.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.link-register {
  display: block;
  margin-top: 1.25rem;
  font-size: 0.8rem;
  color: #059669;
  font-weight: 600;
  text-decoration: none;
}
.error-banner {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
  border-radius: 0.75rem;
  padding: 0.65rem 1rem;
  font-size: 0.8rem;
  margin: 0 0 1rem 0;
  text-align: left;
}
</style>
