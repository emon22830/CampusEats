<template>
  <main style="padding: 1rem; flex: 1; display: flex; flex-direction: column; box-sizing: border-box;">
    <button class="btn-back" @click="$emit('go-back')">← Change Cafe Counter</button>

    <div style="background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1rem; margin-bottom: 1.25rem; box-sizing: border-box;">
      <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 1.1rem;">
        <span>{{ vendor?.name }}</span>
        <span style="color: #d97706; font-size: 0.95rem;">⭐ {{ vendor?.rating }}</span>
      </div>
      <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.35rem;">
        📍 {{ vendor?.location }} | ⏱️ Prep Time: {{ vendor?.wait_time }}
      </div>
    </div>

    <div style="margin-bottom: 1.25rem;">
      <input type="text" v-model="searchQuery" placeholder="Search dishes, drinks..." class="search-input">
      <div class="pill-container">
        <button v-for="cat in categories" :key="cat" @click="activeCategory = cat" :class="['pill', activeCategory === cat ? 'active' : '']">
          {{ cat }}
        </button>
      </div>
    </div>

    <div style="flex: 1; padding-bottom: 5rem;">
      <h3 style="margin: 0 0 0.75rem 0; font-size: 0.95rem; font-weight: 700; color: #374151;">Menu Items</h3>
      <div v-for="item in filteredMenuItems" :key="item.id" class="menu-card">
        <div>
          <div style="font-weight: 600; color: #111827; font-size: 0.9rem;">{{ item.name }}</div>
          <div style="font-size: 0.75rem; color: #6b7280; margin: 0.15rem 0 0.35rem 0;">{{ item.description }}</div>
          <div style="color: #059669; font-weight: 700; font-size: 0.9rem;">RM {{ item.price.toFixed(2) }}</div>
        </div>
        <button class="btn-add" @click="cartStore.addToCart(item)">+ Add</button>
      </div>
    </div>

    <div class="checkout-floating-bar" v-if="cartStore.totalCount > 0">
      <button class="btn-confirm" @click="$emit('go-checkout')" style="margin-top: 0;">
        Review Order Basket ({{ cartStore.totalCount }} items) →
      </button>
    </div>
  </main>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useCartStore } from '../stores/cart';

const props = defineProps({
  vendor: Object,
  menuItems: Array
});
defineEmits(['go-back', 'go-checkout']);

const cartStore = useCartStore();
const searchQuery = ref('');
const activeCategory = ref('All');
const categories = ref(['All', 'Rice', 'Noodles', 'Drinks', 'Snacks']);

const filteredMenuItems = computed(() => {
  return props.menuItems.filter(item => {
    const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = activeCategory.value === 'All' || item.category === activeCategory.value;
    return matchesSearch && matchesCategory;
  });
});
</script>

<style scoped>
.search-input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem; box-sizing: border-box; font-size: 0.875rem; outline: none; }
.search-input:focus { border-color: #059669; }
.pill-container { display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem; margin-top: 0.75rem; }
.pill { background: #f9fafb; border: 1px solid #e5e7eb; padding: 0.375rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; color: #4b5563; cursor: pointer; white-space: nowrap; }
.pill.active { background: #059669; color: white; border-color: #059669; }
.menu-card { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; background: white; box-sizing: border-box; }
.btn-add { background: #059669; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.75rem; cursor: pointer; }
.btn-back { background: #f3f4f6; color: #4b5563; border: none; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.25rem; }
.btn-confirm { background: #059669; color: white; width: 100%; border: none; padding: 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: bold; cursor: pointer; text-align: center; box-sizing: border-box; }
.checkout-floating-bar { position: sticky; bottom: 0; left: 0; right: 0; background: #ffffff; border-top: 1px solid #e5e7eb; padding: 1rem; box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.05); box-sizing: border-box; }
</style>