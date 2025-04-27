<template>
  <div class="synthese-container">
    <h1>Synthèse Financière</h1>
    
    <div class="card">
      <h2>Filtres</h2>
      <div class="filters-form">
        <div class="form-group">
          <label for="periode">Période</label>
          <select id="periode" v-model="selectedPeriode">
            <option value="jour">Journalier</option>
            <option value="semaine">Hebdomadaire</option>
            <option value="mois">Mensuel</option>
            <option value="trimestre">Trimestriel</option>
            <option value="annee">Annuel</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="dateDebut">Date début</label>
          <input type="date" id="dateDebut" v-model="dateDebut">
        </div>
        
        <div class="form-group">
          <label for="dateFin">Date fin</label>
          <input type="date" id="dateFin" v-model="dateFin">
        </div>
        
        <div class="form-actions">
          <button @click="generateSynthese" class="btn btn-primary">
            Générer la synthèse
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="syntheseGenerated" class="synthese-results">
      <div class="card mt-4">
        <h2>Résumé des opérations financières</h2>
        <div class="summary-cards">
          <div class="summary-card income">
            <div class="card-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="card-content">
              <h3>Total Encaissements</h3>
              <p class="amount">{{ formatMontant(synthese.totalEncaissements) }} Ar</p>
            </div>
          </div>
          
          <div class="summary-card expense">
            <div class="card-icon">
              <i class="fas fa-file-invoice"></i>
            </div>
            <div class="card-content">
              <h3>Total Décaissements</h3>
              <p class="amount">{{ formatMontant(synthese.totalDecaissements) }} Ar</p>
            </div>
          </div>
          
          <div class="summary-card balance">
            <div class="card-icon">
              <i class="fas fa-balance-scale"></i>
            </div>
            <div class="card-content">
              <h3>Solde</h3>
              <p class="amount" :class="{ 'positive': synthese.solde >= 0, 'negative': synthese.solde < 0 }">
                {{ formatMontant(synthese.solde) }} Ar
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Taux de paiement par classe</h2>
        <div class="chart-container">
          <div class="chart-placeholder">
            <div class="bar-chart">
              <div class="bar-item">
                <div class="bar-label">6ème A</div>
                <div class="bar-value" style="width: 85%;">85%</div>
              </div>
              <div class="bar-item">
                <div class="bar-label">6ème B</div>
                <div class="bar-value" style="width: 78%;">78%</div>
              </div>
              <div class="bar-item">
                <div class="bar-label">5ème A</div>
                <div class="bar-value" style="width: 92%;">92%</div>
              </div>
              <div class="bar-item">
                <div class="bar-label">5ème B</div>
                <div class="bar-value" style="width: 65%;">65%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Répartition par mode de paiement</h2>
        <div class="chart-container">
          <div class="pie-chart-container">
            <div class="pie-chart">
              <div class="pie-slice" style="--percentage: 65; --color: #3f51b5;">
                <span class="pie-label">Cash</span>
              </div>
              <div class="pie-slice" style="--percentage: 35; --color: #f44336;">
                <span class="pie-label">ADRA</span>
              </div>
            </div>
            <div class="pie-legend">
              <div class="legend-item">
                <div class="legend-color" style="background-color: #3f51b5;"></div>
                <div class="legend-label">Cash: 65%</div>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #f44336;"></div>
                <div class="legend-label">ADRA: 35%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Répartition par motif</h2>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Motif</th>
                <th>Montant</th>
                <th>Pourcentage</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Frais de scolarité</td>
                <td>{{ formatMontant(750000) }} Ar</td>
                <td>75%</td>
              </tr>
              <tr>
                <td>Frais d'examen</td>
                <td>{{ formatMontant(150000) }} Ar</td>
                <td>15%</td>
              </tr>
              <tr>
                <td>Frais d'inscription</td>
                <td>{{ formatMontant(100000) }} Ar</td>
                <td>10%</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th>{{ formatMontant(1000000) }} Ar</th>
                <th>100%</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <div class="export-actions mt-4">
        <button class="btn btn-secondary">
          <i class="fas fa-file-excel"></i> Export Excel
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-file-pdf"></i> Export PDF
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FinanceSynthese',
  data() {
    return {
      selectedPeriode: 'mois',
      dateDebut: '',
      dateFin: '',
      syntheseGenerated: false,
      synthese: {
        totalEncaissements: 1000000,
        totalDecaissements: 470000,
        solde: 530000,
        repartitionModes: {
          Cash: 650000,
          ADRA: 350000
        },
        repartitionMotifs: {
          'Frais de scolarité': 750000,
          'Frais d\'examen': 150000,
          'Frais d\'inscription': 100000
        },
        periode: 'mois'
      }
    }
  },
  methods: {
    generateSynthese() {
      // Dans une application réelle, on récupérerait les données depuis l'API
      // Pour l'instant, on simule la génération
      this.syntheseGenerated = true;
      
      // Afficher un message de succès
      alert('Synthèse générée avec succès !');
    },
    
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-FR').format(montant);
    }
  }
}
</script>

<style scoped>
.synthese-container {
  padding: 1.5rem;
}

h1 {
  margin-bottom: 1.5rem;
  color: #333;
}

.card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

h2 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #333;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.filters-form {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #555;
}

input, select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.form-actions {
  grid-column: 1 / -1;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s;
  display: flex;
  align-items: center;
}

.btn i {
  margin-right: 0.5rem;
}

.btn-primary {
  background-color: #3f51b5;
  color: white;
}

.btn-primary:hover {
  background-color: #303f9f;
}

.btn-secondary {
  background-color: #f5f5f5;
  color: #333;
}

.btn-secondary:hover {
  background-color: #e0e0e0;
}

.mt-4 {
  margin-top: 1.5rem;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.summary-card {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.summary-card.income {
  background-color: #e8f5e9;
  border-left: 4px solid #4caf50;
}

.summary-card.expense {
  background-color: #fff3e0;
  border-left: 4px solid #ff9800;
}

.summary-card.balance {
  background-color: #e3f2fd;
  border-left: 4px solid #2196f3;
}

.card-icon {
  font-size: 2rem;
  margin-right: 1.5rem;
  color: #757575;
}

.card-content h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  color: #757575;
}

.amount {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0;
  color: #333;
}

.amount.positive {
  color: #4caf50;
}

.amount.negative {
  color: #f44336;
}

.chart-container {
  margin-top: 1rem;
  height: 300px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.chart-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.bar-chart {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.bar-item {
  display: flex;
  align-items: center;
}

.bar-label {
  width: 100px;
  text-align: right;
  padding-right: 1rem;
  font-weight: 500;
}

.bar-value {
  height: 30px;
  background-color: #3f51b5;
  color: white;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 0 1rem;
  border-radius: 4px;
  font-weight: 500;
}

.pie-chart-container {
  display: flex;
  justify-content: space-around;
  align-items: center;
  width: 100%;
}

.pie-chart {
  position: relative;
  width: 200px;
  height: 200px;
  border-radius: 50%;
  background-color: #f5f5f5;
  overflow: hidden;
}

.pie-slice {
  position: absolute;
  width: 100%;
  height: 100%;
  clip-path: polygon(50% 50%, 50% 0%, 100% 0%, 100% 100%, 50% 100%);
  transform: rotate(calc(var(--percentage) * 3.6deg));
  transform-origin: center;
  background-color: var(--color);
}

.pie-label {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-weight: bold;
}

.pie-legend {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.legend-color {
  width: 20px;
  height: 20px;
  border-radius: 4px;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th, .data-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.data-table th {
  background-color: #f5f5f5;
  font-weight: 500;
}

.data-table tfoot th {
  background-color: #e0e0e0;
}

.export-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>
