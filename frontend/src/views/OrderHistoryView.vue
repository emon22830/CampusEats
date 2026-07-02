<template>
  <main style="padding: 1rem; flex: 1; box-sizing: border-box;">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0; color: #111827;">Your Orders</h2>

    <div class="pill-container">
      <button
        v-for="s in statusFilters"
        :key="s.value"
        :class="['pill', activeStatus === s.value ? 'active' : '']"
        @click="setStatus(s.value)"
      >
        {{ s.label }}
      </button>
    </div>

    <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>
    <p v-if="loading" class="empty-state">Loading orders…</p>
    <p v-else-if="orders.length === 0" class="empty-state">No orders yet.</p>

    <div v-for="order in orders" :key="order.id" class="order-card">
      <div class="order-header">
        <span style="font-weight: 700;">Order #{{ order.id }}</span>
        <span :class="['status-badge', order.status]">{{ order.status }}</span>
      </div>
      <div style="font-size: 0.8rem; color: #6b7280; margin: 0.35rem 0;">
        Pickup: {{ order.pickup_at }} · RM {{ order.total.toFixed(2) }}
      </div>
      <div style="font-size: 0.8rem; color: #4b5563;">
        {{ order.items.map((i) => `${i.qty}x ${i.name}`).join(', ') }}
      </div>
      <button v-if="order.status === 'placed'" class="btn-cancel" @click="cancelOrder(order.id)">
        Cancel Order
      </button>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api, ApiError } from '@/api/client'

const orders = ref([])
const loading = ref(false)
const errorMessage = ref('')
const activeStatus = ref('')

const statusFilters = [
  { label: 'All', value: '' },
  { label: 'Placed', value: 'placed' },
  { label: 'Preparing', value: 'preparing' },
  { label: 'Ready', value: 'ready' },
  { label: 'Collected', value: 'collected' },
]

async function fetchOrders() {
  loading.value = true
  errorMessage.value = ''

  try {
    const query = activeStatus.value ? `?status=${activeStatus.value}` : ''
    orders.value = await api.get(`/api/orders${query}`, { auth: true })
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load orders.'
  } finally {
    loading.value = false
  }
}

function setStatus(status) {
  activeStatus.value = status
  fetchOrders()
}

async function cancelOrder(id) {
  try {
    await api.patch(`/api/orders/${id}/cancel`, {}, { auth: true })
    await fetchOrders()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not cancel order.'
  }
}

onMounted(fetchOrders)
</script>

<style scoped>
.pill-container {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding-bottom: 0.25rem;
  margin-bottom: 1rem;
}
.pill {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  padding: 0.375rem 1rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  color: #4b5563;
  cursor: pointer;
  white-space: nowrap;
}
.pill.active {
  background: #059669;
  color: white;
  border-color: #059669;
}
.order-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 0.75rem;
  background: white;
}
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.status-badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.2rem 0.6rem;
  border-radius: 9999px;
  text-transform: uppercase;
  background: #f3f4f6;
  color: #4b5563;
}
.status-badge.placed {
  background: #fef3c7;
  color: #92400e;
}
.status-badge.preparing {
  background: #dbeafe;
  color: #1e40af;
}
.status-badge.ready {
  background: #d1fae5;
  color: #065f46;
}
.status-badge.collected {
  background: #e5e7eb;
  color: #374151;
}
.status-badge.cancelled {
  background: #fee2e2;
  color: #991b1b;
}
.btn-cancel {
  margin-top: 0.75rem;
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
  padding: 0.4rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
}
</style>
