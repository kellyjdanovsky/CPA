<template>
  <div class="dashboard">
    <h1>Tableau de bord</h1>
    <div class="dashboard-grid">
      <div class="dashboard-card finance">
        <h2>Finance</h2>
        <div class="card-content">
          <div class="stat-item">
            <span class="stat-label">Encaissements du mois</span>
            <span class="stat-value">1 250 000 Ar</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Taux de paiement</span>
            <span class="stat-value">78%</span>
          </div>
        </div>
        <div class="card-actions">
          <router-link to="/finance/encaissements" class="btn">Voir détails</router-link>
        </div>
      </div>
      
      <div class="dashboard-card academic">
        <h2>Académique</h2>
        <div class="card-content">
          <div class="stat-item">
            <span class="stat-label">Examens en cours</span>
            <span class="stat-value">2</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Notes à saisir</span>
            <span class="stat-value">3 classes</span>
          </div>
        </div>
        <div class="card-actions">
          <router-link to="/notes" class="btn">Saisir notes</router-link>
        </div>
      </div>
      
      <div class="dashboard-card bulletins">
        <h2>Bulletins</h2>
        <div class="card-content">
          <div class="stat-item">
            <span class="stat-label">Bulletins générés</span>
            <span class="stat-value">125</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">En attente</span>
            <span class="stat-value">45</span>
          </div>
        </div>
        <div class="card-actions">
          <router-link to="/bulletins" class="btn">Générer bulletins</router-link>
        </div>
      </div>
      
      <div class="dashboard-card stats">
        <h2>Statistiques</h2>
        <div class="card-content">
          <div class="stat-item">
            <span class="stat-label">Moyenne générale</span>
            <span class="stat-value">12.8/20</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Taux de réussite</span>
            <span class="stat-value">65%</span>
          </div>
        </div>
        <div class="card-actions">
          <router-link to="/statistiques" class="btn">Voir analyses</router-link>
        </div>
      </div>
    </div>
    
    <div class="recent-activity">
      <h2>Activités récentes</h2>
      <ul class="activity-list">
        <li class="activity-item">
          <span class="activity-time">Aujourd'hui, 10:45</span>
          <span class="activity-desc">Paiement enregistré pour Rakoto Jean (6ème A)</span>
        </li>
        <li class="activity-item">
          <span class="activity-time">Hier, 15:30</span>
          <span class="activity-desc">Notes du Trimestre 1 validées pour la classe 5ème B</span>
        </li>
        <li class="activity-item">
          <span class="activity-time">Hier, 09:15</span>
          <span class="activity-desc">25 bulletins générés pour la classe 4ème A</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardView'
}
</script>

<style scoped>
.dashboard {
  padding: 1.5rem;
}

h1 {
  margin-bottom: 1.5rem;
  color: #333;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.dashboard-card h2 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #333;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.card-content {
  margin-bottom: 1rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: #757575;
}

.stat-value {
  font-weight: bold;
  color: #333;
}

.card-actions {
  text-align: right;
}

.btn {
  display: inline-block;
  background-color: #3f51b5;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.9rem;
  transition: background-color 0.3s;
}

.btn:hover {
  background-color: #303f9f;
}

.finance {
  border-top: 3px solid #4caf50;
}

.academic {
  border-top: 3px solid #2196f3;
}

.bulletins {
  border-top: 3px solid #ff9800;
}

.stats {
  border-top: 3px solid #9c27b0;
}

.recent-activity {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.recent-activity h2 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #333;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.activity-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.activity-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid #f5f5f5;
  display: flex;
  flex-direction: column;
}

.activity-time {
  font-size: 0.8rem;
  color: #757575;
  margin-bottom: 0.25rem;
}

.activity-desc {
  color: #333;
}
</style>
