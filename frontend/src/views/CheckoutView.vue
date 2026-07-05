<template>
  <main style="padding: 1rem; flex: 1; display: flex; flex-direction: column; box-sizing: border-box;">
    <RouterLink to="/vendors" class="btn-back">← Back to Menu</RouterLink>

    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0; color: #111827;">Your Cart Summary</h2>

    <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>

    <div v-if="cartStore.items.length === 0" class="empty-state">
      Your cart is empty. <RouterLink to="/vendors" style="color: #059669;">Browse vendors</RouterLink>
    </div>

    <template v-else>
      <p style="font-size: 0.8rem; color: #6b7280; margin: 0 0 0.5rem 0;">
        Ordering from <strong>{{ cartStore.vendorName }}</strong>
      </p>

      <div class="cart-box">
        <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
          <span style="font-size: 0.85rem;"
            ><strong style="color: #059669;">{{ item.qty }}x</strong> {{ item.name }}</span
          >
          <span style="font-weight: 600; font-size: 0.85rem;">RM {{ (item.price * item.qty).toFixed(2) }}</span>
        </div>

        <div class="subtotal-row">
          <span>Subtotal:</span>
          <span style="color: #059669;">RM {{ cartStore.cartTotal }}</span>
        </div>
      </div>

      <div style="margin-top: 1.5rem;">
        <label style="display: block; font-size: 0.75rem; color: #4b5563; font-weight: 600; text-transform: uppercase;"
          >Select 15-Minute Pickup Window:</label
        >
        <select v-model="pickupSlot" class="select-input">
          <option value="12:30 PM - 12:45 PM">12:30 PM - 12:45 PM (Peak Period)</option>
          <option value="12:45 PM - 1:00 PM">12:45 PM - 1:00 PM</option>
          <option value="1:00 PM - 1:15 PM">1:00 PM - 1:15 PM</option>
          <option value="1:15 PM - 1:30 PM">1:15 PM - 1:30 PM</option>
        </select>
      </div>

      <button class="btn-confirm" style="margin-top: auto;" :disabled="submitting" @click="confirmOrder">
        {{ submitting ? 'Placing order…' : `CONFIRM PRE-ORDER (RM ${cartStore.cartTotal})` }}
      </button>
    </template>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { api, ApiError } from '@/api/client'
import { useCartStore } from '@/stores/cart'

const cartStore = useCartStore()
const router = useRouter()

const pickupSlot = ref('12:30 PM - 12:45 PM')
const submitting = ref(false)
const errorMessage = ref('')

async function confirmOrder() {
  submitting.value = true
  errorMessage.value = ''

  try {
    const order = await api.post(
      '/api/orders',
      {
        vendor_id: cartStore.vendorId,
        pickup_at: pickupSlot.value,
        items: cartStore.items.map((item) => ({ menu_item_id: item.id, qty: item.qty })),
      },
      { auth: true },
    )
    cartStore.clearCart()
    router.push(`/order-success/${order.id}`)
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not place your order.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.select-input {
  width: 100%;
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  background-color: white;
  font-size: 0.875rem;
  color: #374151;
  margin-top: 0.5rem;
  outline: none;
  box-sizing: border-box;
}
.cart-box {
  background: #f9fafb;
  border: 1px solid #f3f4f6;
  border-radius: 0.75rem;
  padding: 0.75rem;
  margin-top: 0.5rem;
  box-sizing: border-box;
}
.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  padding: 0.375rem 0;
}
.subtotal-row {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  font-size: 1rem;
  margin-top: 0.75rem;
  padding-top: 0.5rem;
  border-top: 1px solid #e5e7eb;
}
</style>
