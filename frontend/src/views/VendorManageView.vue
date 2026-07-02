<template>
  <main style="padding: 1rem; flex: 1; box-sizing: border-box;">
    <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>

    <!-- No vendor yet: onboarding form -->
    <template v-if="!loading && !myVendor">
      <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0; color: #111827;">
        Set Up Your Stall
      </h2>
      <p style="font-size: 0.85rem; color: #6b7280; margin: 0 0 1rem 0;">
        Create your vendor listing. An admin needs to approve it before it appears to customers.
      </p>

      <form @submit.prevent="createVendor">
        <input v-model="form.name" type="text" placeholder="Stall Name" class="form-input" required />
        <input v-model="form.location" type="text" placeholder="Location (e.g. Block C, Counter 3)" class="form-input" required />
        <input v-model="form.opening_hours" type="text" placeholder="Opening Hours (optional)" class="form-input" />
        <input
          v-model.number="form.prep_time_mins"
          type="number"
          min="1"
          placeholder="Avg. Prep Time (minutes, optional)"
          class="form-input"
        />
        <button class="btn-confirm" type="submit" :disabled="submitting">
          {{ submitting ? 'Creating…' : 'Create Stall' }}
        </button>
      </form>
    </template>

    <!-- Has a vendor -->
    <template v-else-if="myVendor">
      <div class="vendor-status-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <strong>{{ myVendor.name }}</strong>
          <span :class="['status-badge', myVendor.status]">{{ myVendor.status }}</span>
        </div>
        <p v-if="myVendor.status === 'pending'" style="font-size: 0.75rem; color: #92400e; margin: 0.5rem 0 0 0;">
          Waiting for admin approval — customers can't see this stall yet.
        </p>
        <p v-else-if="myVendor.status === 'rejected'" style="font-size: 0.75rem; color: #991b1b; margin: 0.5rem 0 0 0;">
          This stall was rejected by an admin.
        </p>
      </div>

      <div class="pill-container">
        <button :class="['pill', tab === 'orders' ? 'active' : '']" @click="tab = 'orders'">Orders</button>
        <button :class="['pill', tab === 'menu' ? 'active' : '']" @click="switchToMenu">My Menu</button>
      </div>

      <!-- Orders tab -->
      <template v-if="tab === 'orders'">
        <p v-if="ordersLoading" class="empty-state">Loading orders…</p>
        <p v-else-if="orders.length === 0" class="empty-state">No orders yet.</p>

        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-header">
            <span style="font-weight: 700;">Order #{{ order.id }}</span>
            <span :class="['status-badge', order.status]">{{ order.status }}</span>
          </div>
          <div style="font-size: 0.8rem; color: #6b7280; margin: 0.35rem 0;">
            Pickup: {{ order.pickup_at }} · RM {{ order.total.toFixed(2) }}
          </div>
          <div style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.5rem;">
            {{ order.items.map((i) => `${i.qty}x ${i.name}`).join(', ') }}
          </div>
          <button
            v-if="nextStatus(order.status)"
            class="btn-add"
            @click="advanceStatus(order)"
          >
            Mark as {{ nextStatus(order.status) }} →
          </button>
        </div>
      </template>

      <!-- Menu tab -->
      <template v-else>
        <form class="menu-form" @submit.prevent="createMenuItem">
          <input v-model="menuForm.name" type="text" placeholder="Item name" class="form-input" required />
          <input v-model="menuForm.category" type="text" placeholder="Category (e.g. Rice, Drinks)" class="form-input" required />
          <input
            v-model.number="menuForm.price"
            type="number"
            step="0.01"
            min="0.01"
            placeholder="Price (RM)"
            class="form-input"
            required
          />
          <input v-model="menuForm.description" type="text" placeholder="Description (optional)" class="form-input" />
          <button class="btn-add" type="submit" :disabled="menuSubmitting" style="width: 100%;">
            {{ menuSubmitting ? 'Adding…' : '+ Add Menu Item' }}
          </button>
        </form>

        <p v-if="menuLoading" class="empty-state">Loading menu…</p>
        <p v-else-if="menuItems.length === 0" class="empty-state">No menu items yet.</p>

        <div v-for="item in menuItems" :key="item.id" class="menu-card">
          <div>
            <div style="font-weight: 600; font-size: 0.9rem;">{{ item.name }}</div>
            <div style="font-size: 0.75rem; color: #6b7280;">{{ item.category }} · RM {{ item.price.toFixed(2) }}</div>
          </div>
          <div style="display: flex; gap: 0.5rem;">
            <button class="btn-toggle" @click="toggleStock(item)">
              {{ item.in_stock ? 'In stock' : 'Sold out' }}
            </button>
            <button class="btn-delete" @click="deleteMenuItem(item)">Delete</button>
          </div>
        </div>
      </template>
    </template>
  </main>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { api, ApiError } from '@/api/client'

const loading = ref(true)
const submitting = ref(false)
const errorMessage = ref('')
const myVendor = ref(null)
const tab = ref('orders')

const form = reactive({ name: '', location: '', opening_hours: '', prep_time_mins: null })

const orders = ref([])
const ordersLoading = ref(false)

const menuItems = ref([])
const menuLoading = ref(false)
const menuSubmitting = ref(false)
const menuForm = reactive({ name: '', category: '', price: null, description: '' })

const STATUS_SEQUENCE = ['placed', 'preparing', 'ready', 'collected']

function nextStatus(current) {
  const idx = STATUS_SEQUENCE.indexOf(current)
  return idx >= 0 && idx < STATUS_SEQUENCE.length - 1 ? STATUS_SEQUENCE[idx + 1] : null
}

async function loadMyVendor() {
  loading.value = true
  errorMessage.value = ''

  try {
    const vendors = await api.get('/api/vendors/mine', { auth: true })
    myVendor.value = vendors[0] ?? null
    if (myVendor.value) await fetchOrders()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load your stall.'
  } finally {
    loading.value = false
  }
}

async function createVendor() {
  submitting.value = true
  errorMessage.value = ''

  try {
    myVendor.value = await api.post('/api/vendors', { ...form }, { auth: true })
    await fetchOrders()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not create your stall.'
  } finally {
    submitting.value = false
  }
}

async function fetchOrders() {
  ordersLoading.value = true
  try {
    orders.value = await api.get('/api/orders', { auth: true })
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load orders.'
  } finally {
    ordersLoading.value = false
  }
}

async function advanceStatus(order) {
  const status = nextStatus(order.status)
  if (!status) return

  try {
    await api.patch(`/api/orders/${order.id}/status`, { status }, { auth: true })
    await fetchOrders()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not update order status.'
  }
}

function switchToMenu() {
  tab.value = 'menu'
  fetchMenuItems()
}

async function fetchMenuItems() {
  menuLoading.value = true
  try {
    menuItems.value = await api.get(`/api/vendors/${myVendor.value.id}/menu-items`)
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load menu items.'
  } finally {
    menuLoading.value = false
  }
}

async function createMenuItem() {
  menuSubmitting.value = true
  errorMessage.value = ''

  try {
    await api.post(`/api/vendors/${myVendor.value.id}/menu-items`, { ...menuForm }, { auth: true })
    menuForm.name = ''
    menuForm.category = ''
    menuForm.price = null
    menuForm.description = ''
    await fetchMenuItems()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not add menu item.'
  } finally {
    menuSubmitting.value = false
  }
}

async function toggleStock(item) {
  try {
    await api.put(`/api/menu-items/${item.id}`, { in_stock: !item.in_stock }, { auth: true })
    await fetchMenuItems()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not update item.'
  }
}

async function deleteMenuItem(item) {
  try {
    await api.delete(`/api/menu-items/${item.id}`, { auth: true })
    await fetchMenuItems()
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not delete item.'
  }
}

onMounted(loadMyVendor)
</script>

<style scoped>
.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  box-sizing: border-box;
  font-size: 0.875rem;
  outline: none;
  margin-bottom: 0.75rem;
}
.vendor-status-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 1rem;
  background: #f9fafb;
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
.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}
.status-badge.approved,
.status-badge.placed.collected {
  background: #d1fae5;
  color: #065f46;
}
.status-badge.rejected {
  background: #fee2e2;
  color: #991b1b;
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
.order-card,
.menu-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 0.75rem;
  background: white;
}
.menu-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn-add {
  background: #059669;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.75rem;
  cursor: pointer;
}
.btn-toggle {
  background: #f3f4f6;
  color: #4b5563;
  border: none;
  padding: 0.4rem 0.65rem;
  border-radius: 0.5rem;
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
}
.btn-delete {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
  padding: 0.4rem 0.65rem;
  border-radius: 0.5rem;
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
}
.menu-form {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 1rem;
  background: #f9fafb;
}
</style>
