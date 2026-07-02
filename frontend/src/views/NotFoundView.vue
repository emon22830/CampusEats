<template>
  <main class="not-found">
    <div style="font-size: 3rem;">🍽️</div>
    <h2 style="color: #111827; font-size: 1.25rem; margin: 1rem 0 0.5rem;">Page Not Found</h2>
    <RouterLink to="/" class="link-home">Go back home</RouterLink>
  </main>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.not-found {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 2rem;
  min-height: 100vh;
  box-sizing: border-box;
}
.link-home {
  color: #059669;
  font-weight: 600;
  font-size: 0.85rem;
  text-decoration: none;
}
</style>
