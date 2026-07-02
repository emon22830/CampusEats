<template>
  <main style="padding: 1rem; flex: 1; box-sizing: border-box;">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0; color: #111827;">UTM Food Courts</h2>
    <p style="font-size: 0.85rem; color: #6b7280; margin: 0 0 1rem 0;">Select a digital vendor counter to pre-order meals</p>

    <input
      v-model="search"
      type="text"
      placeholder="Search vendors..."
      class="search-input"
      @input="debouncedFetch"
    />

    <p v-if="errorMessage" class="error-banner" style="margin-top: 1rem;">{{ errorMessage }}</p>
    <p v-if="loading" class="empty-state">Loading vendors…</p>
    <p v-else-if="vendors.length === 0" class="empty-state">No vendors found.</p>

    <RouterLink
      v-for="v in vendors"
      :key="v.id"
      :to="`/vendors/${v.id}`"
      class="vendor-row-card"
    >
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #111827;">{{ v.name }}</h3>
          <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #6b7280;">📍 {{ v.location }}</p>
        </div>
        <span style="color: #d97706; font-size: 0.85rem; font-weight: 600;">
          ⭐ {{ v.rating ?? 'New' }}
        </span>
      </div>
      <div class="card-footer">
        <span>⏱️ Prep time: <strong>{{ v.prep_time_mins ? `${v.prep_time_mins} mins` : 'N/A' }}</strong></span>
        <span style="color: #059669; font-weight: 600;">View Menu →</span>
      </div>
    </RouterLink>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { api, ApiError } from '@/api/client'

const vendors = ref([])
const search = ref('')
const loading = ref(false)
const errorMessage = ref('')
let debounceTimer = null

async function fetchVendors() {
  loading.value = true
  errorMessage.value = ''

  try {
    const query = search.value ? `?search=${encodeURIComponent(search.value)}` : ''
    vendors.value = await api.get(`/api/vendors${query}`)
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load vendors.'
  } finally {
    loading.value = false
  }
}

function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchVendors, 300)
}

onMounted(fetchVendors)
</script>

<style scoped>
.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  box-sizing: border-box;
  font-size: 0.875rem;
  outline: none;
  margin-bottom: 1rem;
}
.search-input:focus {
  border-color: #059669;
}
.vendor-row-card {
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  padding: 1.25rem;
  margin-bottom: 1rem;
  background: white;
  cursor: pointer;
  box-sizing: border-box;
  display: block;
  text-decoration: none;
  color: inherit;
}
.card-footer {
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px solid #f3f4f6;
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: #4b5563;
}
</style>
