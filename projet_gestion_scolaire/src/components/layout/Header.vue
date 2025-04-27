<template>
  <header class="app-header">
    <div class="logo-container">
      <img src="@/assets/logo.png" alt="Logo" class="logo" v-if="false">
      <h1>Collège Privé Adventiste Avaratetezana</h1>
    </div>
    <div class="user-menu">
      <span class="user-name">Administrateur</span>
      <button class="logout-btn">Déconnexion</button>
    </div>
  </header>
</template>

<script>
export default {
  name: 'AppHeader'
}
</script>

<style scoped>
.app-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 1rem;
  background-color: #3f51b5;
  color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo-container {
  display: flex;
  align-items: center;
}

.logo {
  height: 40px;
  margin-right: 1rem;
}

h1 {
  font-size: 1.2rem;
  margin: 0;
}

.user-menu {
  display: flex;
  align-items: center;
}

.user-name {
  margin-right: 1rem;
}

.logout-btn {
  background-color: transparent;
  border: 1px solid white;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.logout-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
}
</style>
