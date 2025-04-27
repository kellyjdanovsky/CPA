<template>
  <div class="sidebar">
    <div class="sidebar-header">
      <h3>Menu Principal</h3>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li>
          <router-link to="/" exact>
            <i class="fas fa-home"></i> Tableau de bord
          </router-link>
        </li>
        <li class="menu-section">
          <span class="section-title">Finance</span>
          <ul>
            <li>
              <router-link to="/finance/encaissements">
                <i class="fas fa-money-bill-wave"></i> Encaissements
              </router-link>
            </li>
            <li>
              <router-link to="/finance/decaissements">
                <i class="fas fa-file-invoice"></i> Décaissements
              </router-link>
            </li>
            <li>
              <router-link to="/finance/synthese">
                <i class="fas fa-chart-pie"></i> Synthèse
              </router-link>
            </li>
          </ul>
        </li>
        <li class="menu-section">
          <span class="section-title">Académique</span>
          <ul>
            <li>
              <router-link to="/matieres">
                <i class="fas fa-book"></i> Matières
              </router-link>
            </li>
            <li>
              <router-link to="/examens">
                <i class="fas fa-tasks"></i> Examens
              </router-link>
            </li>
            <li>
              <router-link to="/notes">
                <i class="fas fa-pen"></i> Saisie des notes
              </router-link>
            </li>
            <li>
              <router-link to="/bulletins">
                <i class="fas fa-file-alt"></i> Bulletins
              </router-link>
            </li>
          </ul>
        </li>
        <li class="menu-section">
          <span class="section-title">Analyses</span>
          <ul>
            <li>
              <router-link to="/archives">
                <i class="fas fa-archive"></i> Archives
              </router-link>
            </li>
            <li>
              <router-link to="/statistiques">
                <i class="fas fa-chart-line"></i> Statistiques
              </router-link>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'AppSidebar'
}
</script>

<style scoped>
.sidebar {
  width: 250px;
  height: 100%;
  background-color: #f5f5f5;
  border-right: 1px solid #e0e0e0;
  overflow-y: auto;
}

.sidebar-header {
  padding: 1rem;
  border-bottom: 1px solid #e0e0e0;
}

.sidebar-header h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #333;
}

.sidebar-nav {
  padding: 1rem 0;
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-section {
  margin-bottom: 1rem;
}

.section-title {
  display: block;
  padding: 0.5rem 1rem;
  font-weight: bold;
  color: #757575;
  font-size: 0.9rem;
  text-transform: uppercase;
}

.sidebar-nav a {
  display: block;
  padding: 0.5rem 1rem 0.5rem 2rem;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s;
}

.sidebar-nav a:hover {
  background-color: #e0e0e0;
}

.sidebar-nav a.router-link-active {
  background-color: #3f51b5;
  color: white;
}

.sidebar-nav i {
  margin-right: 0.5rem;
  width: 20px;
  text-align: center;
}
</style>
