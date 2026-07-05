<template>
  <main class="register-screen">
    <div style="font-size: 3.5rem; margin-bottom: 1rem;">📝</div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #059669; margin: 0 0 0.5rem 0;">Create Account</h2>
    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 1.5rem;">Join CampusEats to pre-order or run a stall</p>

    <form class="register-form" @submit.prevent="handleRegister">
      <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>

      <input v-model="name" type="text" placeholder="Full Name" class="reg-input" required minlength="2" />
      <input v-model="email" type="email" placeholder="Email" class="reg-input" required />
      <input
        v-model="password"
        type="password"
        placeholder="Password (min 8 characters)"
        class="reg-input"
        required
        minlength="8"
      />

      <div class="role-picker">
        <button
          type="button"
          :class="['role-btn', { active: role === 'customer' }]"
          @click="role = 'customer'"
        >
          🎓 Student
        </button>
        <button
          type="button"
          :class="['role-btn', { active: role === 'vendor' }]"
          @click="role = 'vendor'"
        >
          🏪 Vendor
        </button>
      </div>

      <button class="btn-register" type="submit" :disabled="loading">
        {{ loading ? 'Creating account…' : 'Create Account →' }}
      </button>
    </form>

    <RouterLink to="/login" class="link-login">Already have an account? Log in</RouterLink>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ApiError } from '@/api/client'

const name = ref('')
const email = ref('')
const password = ref('')
const role = ref('customer')
const loading = ref(false)
const errorMessage = ref('')

const auth = useAuthStore()
const router = useRouter()

async function handleRegister() {
  loading.value = true
  errorMessage.value = ''

  try {
    const user = await auth.register(name.value, email.value, password.value, role.value)
    router.push(user.role === 'vendor' ? '/vendor/dashboard' : '/vendors')
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.register-screen {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  text-align: center;
  box-sizing: border-box;
  min-height: 100vh;
}
.register-form {
  width: 100%;
}
.reg-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  box-sizing: border-box;
  font-size: 0.875rem;
  outline: none;
  margin-bottom: 0.75rem;
}
.role-picker {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}
.role-btn {
  flex: 1;
  padding: 0.65rem;
  border-radius: 0.75rem;
  border: 1px solid #d1d5db;
  background: white;
  font-size: 0.8rem;
  font-weight: 600;
  color: #4b5563;
  cursor: pointer;
}
.role-btn.active {
  border-color: #059669;
  background: #ecfdf5;
  color: #047857;
}
.btn-register {
  background: #059669;
  color: white;
  width: 100%;
  border: none;
  padding: 1rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  font-weight: bold;
  cursor: pointer;
  margin-top: 1rem;
  box-sizing: border-box;
}
.btn-register:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.link-login {
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
