<template>
  <footer class="app-footer">
    <div class="footer-content">
      <p>&copy; {{ currentYear }} Collège Privé Adventiste Avaratetezana - Tous droits réservés</p>
      <p class="version">Version 1.0.0</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script>

<style scoped>
.app-footer {
  background-color: #f5f5f5;
  border-top: 1px solid #e0e0e0;
  padding: 1rem;
  text-align: center;
  font-size: 0.9rem;
  color: #757575;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
}

.version {
  margin-top: 0.5rem;
  font-size: 0.8rem;
}
</style>
