import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: []
  }),
  getters: {
    totalCount: (state) => state.items.reduce((sum, item) => sum + item.qty, 0),
    cartTotal: (state) => state.items.reduce((sum, item) => sum + (item.price * item.qty), 0).toFixed(2)
  },
  actions: {
    addToCart(item) {
      const existing = this.items.find(i => i.id === item.id);
      if (existing) {
        existing.qty++;
      } else {
        this.items.push({ ...item, qty: 1 });
      }
    },
    clearCart() {
      this.items = [];
    }
  }
});