<template>
  <main style="padding: 1rem; flex: 1; box-sizing: border-box;">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0; color: #111827;">Admin Panel</h2>

    <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>

    <div class="pill-container">
      <button :class="['pill', tab === 'pending' ? 'active' : '']" @click="tab = 'pending'">
        Pending Vendors
      </button>
      <button :class="['pill', tab === 'analytics' ? 'active' : '']" @click="tab = 'analytics'">
        Analytics
      </button>
    </div>

    <template v-if="tab === 'pending'">
      <p v-if="pendingLoading" class="empty-state">Loading…</p>
      <p v-else-if="pendingVendors.length === 0" class="empty-state">No vendors awaiting approval.</p>

      <div v-for="v in pendingVendors" :key="v.id" class="pending-card">
        <div style="font-weight: 700; font-size: 0.95rem;">{{ v.name }}</div>
        <div style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0.75rem 0;">📍 {{ v.location }}</div>
        <div style="display: flex; gap: 0.5rem;">
          <button class="btn-approve" @click="decide(v.id, 'approved')">Approve</button>
          <button class="btn-reject" @click="decide(v.id, 'rejected')">Reject</button>
        </div>
      </div>
    </template>

    <template v-else>
      <p v-if="analyticsLoading" class="empty-state">Loading…</p>

      <div v-else-if="analytics" class="analytics-summary">
        <div class="stat-card">
          <div class="stat-value">{{ analytics.total_orders }}</div>
          <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">RM {{ analytics.total_revenue.toFixed(2) }}</div>
          <div class="stat-label">Total Revenue (collected)</div>
        </div>
      </div>

      <h3 style="font-size: 0.9rem; font-weight: 700; margin: 1.5rem 0 0.75rem 0; color: #374151;">By Vendor</h3>
      <div v-for="v in analytics?.by_vendor" :key="v.vendor_id" class="vendor-stat-row">
        <span>{{ v.vendor_name }}</span>
        <span style="color: #6b7280;">{{ v.order_count }} orders · RM {{ v.revenue.toFixed(2) }}</span>
      </div>
    </template>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api, ApiError } from '@/api/client'

const tab = ref('pending')
const errorMessage = ref('')

const pendingVendors = ref([])
const pendingLoading = ref(false)

const analytics = ref(null)
const analyticsLoading = ref(false)

async function fetchPending() {
  pendingLoading.value = true
  try {
    pendingVendors.value = await api.get('/api/admin/vendors/pending', { auth: true })
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load pending vendors.'
  } finally {
    pendingLoading.value = false
  }
}

async function decide(vendorId, status) {
  try {
    await api.patch(`/api/admin/vendors/${vendorId}/approve`, { status }, { auth: true })
    await fetchPending()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not update vendor.'
  }
}

async function fetchAnalytics() {
  analyticsLoading.value = true
  try {
    analytics.value = await api.get('/api/admin/analytics/summary', { auth: true })
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load analytics.'
  } finally {
    analyticsLoading.value = false
  }
}

onMounted(() => {
  fetchPending()
  fetchAnalytics()
})
</script>

<style scoped>
.pill-container {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.pill {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  padding: 0.375rem 1rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #4b5563;
  cursor: pointer;
}
.pill.active {
  background: #059669;
  color: white;
  border-color: #059669;
}
.pending-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 0.75rem;
  background: white;
}
.btn-approve {
  background: #059669;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.75rem;
  cursor: pointer;
  flex: 1;
}
.btn-reject {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.75rem;
  cursor: pointer;
  flex: 1;
}
.analytics-summary {
  display: flex;
  gap: 0.75rem;
}
.stat-card {
  flex: 1;
  background: #ecfdf5;
  border: 1px solid #d1fae5;
  border-radius: 0.75rem;
  padding: 1rem;
  text-align: center;
}
.stat-value {
  font-size: 1.35rem;
  font-weight: 800;
  color: #047857;
}
.stat-label {
  font-size: 0.7rem;
  color: #059669;
  font-weight: 600;
  text-transform: uppercase;
  margin-top: 0.25rem;
}
.vendor-stat-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  padding: 0.6rem 0;
  border-bottom: 1px solid #f3f4f6;
}
</style>
