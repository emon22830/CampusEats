<template>
  <main class="welcome-screen">
    <div class="welcome-logo">🍔</div>
    <h1 class="welcome-heading">Welcome to CampusEats</h1>
    <p class="welcome-text">
      Skip the long food court lines at UTM. Pre-order your meals and pick them up instantly!
    </p>
    <RouterLink to="/login" class="btn-start">Explore Cafes &amp; Start Order →</RouterLink>
    <RouterLink to="/register" class="link-register">New here? Create an account</RouterLink>
    <div class="footer-tag">Group Apex • PR3 Full Stack Build</div>
  </main>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.welcome-screen {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  text-align: center;
  background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%);
  box-sizing: border-box;
  min-height: 100vh;
}
.welcome-logo {
  font-size: 4.5rem;
  margin-bottom: 1rem;
}
.welcome-heading {
  font-size: 2.25rem;
  font-weight: 900;
  color: #059669;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.03em;
}
.welcome-text {
  font-size: 0.95rem;
  color: #4b5563;
  line-height: 1.5;
  max-width: 280px;
  margin: 0;
}
.btn-start {
  background: #059669;
  color: white;
  width: 100%;
  border: none;
  padding: 1.15rem;
  border-radius: 1rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
  margin-top: 2rem;
  box-sizing: border-box;
  text-decoration: none;
  text-align: center;
  display: block;
}
.link-register {
  margin-top: 1rem;
  font-size: 0.8rem;
  color: #059669;
  font-weight: 600;
  text-decoration: none;
}
.footer-tag {
  margin-top: auto;
  padding-top: 2rem;
  font-size: 0.7rem;
  color: #9ca3af;
  font-weight: 500;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
</style>
