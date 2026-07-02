<template>
  <main style="padding: 1rem; flex: 1; display: flex; flex-direction: column; box-sizing: border-box;">
    <RouterLink to="/vendors" class="btn-back">← Change Cafe Counter</RouterLink>

    <p v-if="errorMessage" class="error-banner">{{ errorMessage }}</p>

    <template v-if="vendor">
      <div class="vendor-card">
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 1.1rem;">
          <span>{{ vendor.name }}</span>
          <span style="color: #d97706; font-size: 0.95rem;">⭐ {{ vendor.rating ?? 'New' }}</span>
        </div>
        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.35rem;">
          📍 {{ vendor.location }} | ⏱️ Prep Time: {{ vendor.prep_time_mins ? `${vendor.prep_time_mins} mins` : 'N/A' }}
        </div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <input type="text" v-model="searchQuery" placeholder="Search dishes, drinks..." class="search-input" />
        <div class="pill-container">
          <button
            v-for="cat in categories"
            :key="cat"
            @click="activeCategory = cat"
            :class="['pill', activeCategory === cat ? 'active' : '']"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <div style="flex: 1; padding-bottom: 5rem;">
        <h3 style="margin: 0 0 0.75rem 0; font-size: 0.95rem; font-weight: 700; color: #374151;">Menu Items</h3>
        <p v-if="filteredMenuItems.length === 0" class="empty-state">No items match.</p>
        <div v-for="item in filteredMenuItems" :key="item.id" class="menu-card">
          <div>
            <div style="font-weight: 600; color: #111827; font-size: 0.9rem;">{{ item.name }}</div>
            <div style="font-size: 0.75rem; color: #6b7280; margin: 0.15rem 0 0.35rem 0;">{{ item.description }}</div>
            <div style="color: #059669; font-weight: 700; font-size: 0.9rem;">RM {{ item.price.toFixed(2) }}</div>
          </div>
          <button class="btn-add" :disabled="!item.in_stock" @click="handleAdd(item)">
            {{ item.in_stock ? '+ Add' : 'Sold out' }}
          </button>
        </div>
      </div>

      <div class="checkout-floating-bar" v-if="cartStore.totalCount > 0">
        <RouterLink to="/checkout" class="btn-confirm">
          Review Order Basket ({{ cartStore.totalCount }} items) →
        </RouterLink>
      </div>
    </template>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { api, ApiError } from '@/api/client'
import { useCartStore } from '@/stores/cart'

const route = useRoute()
const cartStore = useCartStore()

const vendor = ref(null)
const menuItems = ref([])
const errorMessage = ref('')
const searchQuery = ref('')
const activeCategory = ref('All')

const categories = computed(() => [
  'All',
  ...new Set(menuItems.value.map((item) => item.category)),
])

const filteredMenuItems = computed(() => {
  return menuItems.value.filter((item) => {
    const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = activeCategory.value === 'All' || item.category === activeCategory.value
    return matchesSearch && matchesCategory
  })
})

async function loadVendor(vendorId) {
  errorMessage.value = ''

  try {
    const [vendorData, items] = await Promise.all([
      api.get(`/api/vendors/${vendorId}`),
      api.get(`/api/vendors/${vendorId}/menu-items`),
    ])
    vendor.value = vendorData
    menuItems.value = items
    cartStore.setVendor(vendorData.id, vendorData.name)
  } catch (e) {
    errorMessage.value = e instanceof ApiError ? e.message : 'Could not load this vendor.'
  }
}

function handleAdd(item) {
  cartStore.addToCart(item)
}

onMounted(() => loadVendor(route.params.id))
watch(() => route.params.id, (id) => loadVendor(id))
</script>

<style scoped>
.vendor-card {
  background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  padding: 1rem;
  margin-bottom: 1.25rem;
  box-sizing: border-box;
}
.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  box-sizing: border-box;
  font-size: 0.875rem;
  outline: none;
}
.search-input:focus {
  border-color: #059669;
}
.pill-container {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding-bottom: 0.25rem;
  margin-top: 0.75rem;
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
.menu-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 0.75rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  box-sizing: border-box;
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
.btn-add:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}
.checkout-floating-bar {
  position: sticky;
  bottom: 0;
  left: 0;
  right: 0;
  background: #ffffff;
  border-top: 1px solid #e5e7eb;
  padding: 1rem;
  box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
  box-sizing: border-box;
}
.checkout-floating-bar .btn-confirm {
  text-decoration: none;
  display: block;
}
</style>
