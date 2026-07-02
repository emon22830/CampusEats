<template>
  <main style="padding: 1rem; flex: 1; box-sizing: border-box;">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0; color: #111827;">UTM Food Courts</h2>
    <p style="font-size: 0.85rem; color: #6b7280; margin: 0 0 1.5rem 0;">Select a digital vendor counter to pre-order meals</p>

    <div v-for="v in vendors" :key="v.name" class="vendor-row-card" @click="$emit('select-vendor', v)">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #111827;">{{ v.name }}</h3>
          <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #6b7280;">📍 {{ v.location }}</p>
        </div>
        <span style="color: #d97706; font-size: 0.85rem; font-weight: 600;">⭐ {{ v.rating }}</span>
      </div>
      <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; font-size: 0.75rem; color: #4b5563;">
        <span>⏱️ Avg. Wait: <strong>{{ v.wait_time }}</strong></span>
        <span style="color: #059669; font-weight: 600;">View Menu →</span>
      </div>
    </div>
  </main>
</template>

<script setup>
defineProps({
  vendors: Array
});
defineEmits(['select-vendor']);
</script>

<style scoped>
.vendor-row-card { border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem; background: white; cursor: pointer; box-sizing: border-box; }
</style>