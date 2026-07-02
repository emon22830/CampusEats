<template>
  <main style="padding: 1rem; flex: 1; display: flex; flex-direction: column; box-sizing: border-box;">
    <button class="btn-back" @click="$emit('go-back')">← Back to Menu</button>
    
    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0; color: #111827;">Your Cart Summary</h2>
    
    <div class="cart-box">
      <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
        <span style="font-size: 0.85rem;"><strong style="color:#059669;">{{ item.qty }}x</strong> {{ item.name }}</span>
        <span style="font-weight: 600; font-size: 0.85rem;">RM {{ (item.price * item.qty).toFixed(2) }}</span>
      </div>
      
      <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1rem; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #e5e7eb;">
        <span>Subtotal:</span>
        <span style="color: #059669;">RM {{ cartStore.cartTotal }}</span>
      </div>
    </div>

    <div style="margin-top: 1.5rem;">
      <label style="display: block; font-size: 0.75rem; color: #4b5563; font-weight: 600; text-transform: uppercase;">Select 15-Minute Pickup Window:</label>
      <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" class="select-input">
        <option value="12:30 PM - 12:45 PM">12:30 PM - 12:45 PM (Peak Period)</option>
        <option value="12:45 PM - 1:00 PM">12:45 PM - 1:00 PM</option>
        <option value="1:00 PM - 1:15 PM">1:00 PM - 1:15 PM</option>
        <option value="1:15 PM - 1:30 PM">1:15 PM - 1:30 PM</option>
      </select>
    </div>

    <button class="btn-confirm" @click="$emit('confirm-order')">
      CONFIRM PRE-ORDER (RM {{ cartStore.cartTotal }})
    </button>
  </main>
</template>

<script setup>
import { useCartStore } from '../stores/cart';

defineProps({
  modelValue: String
});
defineEmits(['go-back', 'confirm-order', 'update:modelValue']);

const cartStore = useCartStore();
</script>

<style scoped>
.btn-back { background: #f3f4f6; color: #4b5563; border: none; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.25rem; }
.btn-confirm { background: #059669; color: white; width: 100%; border: none; padding: 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: bold; cursor: pointer; margin-top: auto; text-align: center; box-sizing: border-box; }
.select-input { width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.75rem; background-color: white; font-size: 0.875rem; color: #374151; margin-top: 0.5rem; outline: none; box-sizing: border-box; }
.cart-box { background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 0.75rem; padding: 0.75rem; margin-top: 0.5rem; box-sizing: border-box; }
.cart-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; padding: 0.375rem 0; }
</style>