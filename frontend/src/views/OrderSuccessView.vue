<template>
  <main class="success-screen">
    <template v-if="order">
      <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
      <h2 style="color: #059669; font-weight: 800; font-size: 1.5rem; margin: 0 0 0.5rem 0;">Pre-Order Confirmed!</h2>
      <p style="color: #4b5563; font-size: 0.875rem; margin: 0 0 2rem 0; line-height: 1.4;">
        Your order has been sent to the vendor and is holding for pickup.
      </p>

      <div class="receipt-box">
        <div class="receipt-header">Digital Receipt Voucher</div>
        <div style="font-size: 0.9rem; margin-bottom: 0.5rem;">🔢 <strong>Order #:</strong> {{ order.id }}</div>
        <div style="font-size: 0.9rem; margin-bottom: 0.5rem;">
          ⏱️ <strong>Pickup Window:</strong>
          <span style="color: #059669; font-weight: bold;">{{ order.pickup_at }}</span>
        </div>
        <div style="font-size: 0.9rem; margin-bottom: 0.75rem;">
          📦 <strong>Items:</strong> {{ order.items.reduce((s, i) => s + i.qty, 0) }} items
        </div>
        <div class="receipt-total">
          <span>Total Charged:</span>
          <span style="color: #059669;">RM {{ order.total.toFixed(2) }}</span>
        </div>
      </div>

      <RouterLink to="/vendors" class="btn-confirm-dark">Back to Food Court Dashboard</RouterLink>
      <RouterLink to="/orders" class="link-orders">View Order History</RouterLink>
    </template>

    <p v-else-if="errorMessage" class="error-banner">{{ errorMessage }}</p>
    <p v-else class="empty-state">Loading receipt…</p>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { api, ApiError } from '@/api/client'

const route = useRoute()
const order = ref(null)
const errorMessage = ref('')

onMounted(async () => {
  try {
    order.value = await api.get(`/api/orders/${route.params.id}`, { auth: true })
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load order receipt.'
  }
})
</script>

<style scoped>
.success-screen {
  padding: 2.5rem 1.5rem;
  flex: 1;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
}
.receipt-box {
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  width: 100%;
  background: #f9fafb;
  padding: 1.25rem;
  box-sizing: border-box;
  text-align: left;
  margin-bottom: 2rem;
}
.receipt-header {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: bold;
  text-transform: uppercase;
  border-bottom: 1px dashed #d1d5db;
  padding-bottom: 0.5rem;
  margin-bottom: 0.75rem;
}
.receipt-total {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  border-top: 1px solid #e5e7eb;
  padding-top: 0.75rem;
  margin-top: 0.5rem;
  font-size: 1.05rem;
}
.btn-confirm-dark {
  background: #1f2937;
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
  text-decoration: none;
  display: block;
}
.link-orders {
  margin-top: 1rem;
  font-size: 0.8rem;
  color: #059669;
  font-weight: 600;
  text-decoration: none;
}
</style>
