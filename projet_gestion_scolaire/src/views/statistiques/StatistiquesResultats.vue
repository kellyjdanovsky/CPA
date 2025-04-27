/* eslint-disable */
<template>
  <div class="statistiques-container">
    <h1>Statistiques des Résultats</h1>
    
    <div class="card">
      <h2>Filtres</h2>
      <div class="filters-form">
        <div class="form-group">
          <label for="examen">Examen</label>
          <select id="examen" v-model="selectedExamenId">
            <option value="" disabled>Sélectionner un examen</option>
            <option v-for="examen in examens" :key="examen.id" :value="examen.id">
              {{ examen.type }} ({{ formatDate(examen.dateDebut) }} - {{ formatDate(examen.dateFin) }})
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="classe">Classe</label>
          <select id="classe" v-model="selectedClasseId">
            <option value="" disabled>Sélectionner une classe</option>
            <option v-for="classe in classes" :key="classe.id" :value="classe.id">
              {{ classe.nom }}
            </option>
          </select>
        </div>
        
        <div class="form-actions">
          <button @click="generateStatistiques" class="btn btn-primary" :disabled="!canGenerateStats">
            Générer les statistiques
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="statsGenerated" class="stats-results">
      <div class="card mt-4">
        <h2>Moyennes par matière</h2>
        <div class="chart-container">
          <div class="bar-chart">
            <div v-for="(stat, id) in moyennesParMatiere" :key="id" class="bar-item">
              <div class="bar-label">{{ stat.matiere }}</div>
              <div class="bar-value" :style="{ width: `${stat.moyenne * 5}%` }">{{ stat.moyenne }}/20</div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Taux de réussite par matière</h2>
        <div class="chart-container">
          <div class="bar-chart">
            <div v-for="(stat, id) in moyennesParMatiere" :key="id" class="bar-item">
              <div class="bar-label">{{ stat.matiere }}</div>
              <div class="bar-value success" :style="{ width: `${stat.tauxReussite}%` }">{{ stat.tauxReussite }}%</div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Évolution des résultats</h2>
        <div class="chart-container">
          <div class="line-chart-placeholder">
            <div class="line-chart">
              <div class="chart-legend">
                <div class="legend-item">
                  <div class="legend-color" style="background-color: #3f51b5;"></div>
                  <div class="legend-label">Moyenne générale</div>
                </div>
                <div class="legend-item">
                  <div class="legend-color" style="background-color: #4caf50;"></div>
                  <div class="legend-label">Taux de réussite</div>
                </div>
              </div>
              <div class="chart-grid">
                <div class="chart-line">
                  <div class="point"></div>
                  <div class="point"></div>
                  <div class="point"></div>
                </div>
                <div class="chart-line">
                  <div class="point"></div>
                  <div class="point"></div>
                  <div class="point"></div>
                </div>
                <div class="x-axis">
                  <div class="tick">Trimestre 1</div>
                  <div class="tick">Trimestre 2</div>
                  <div class="tick">Trimestre 3</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Top 5 des élèves</h2>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Rang</th>
                <th>Élève</th>
                <th>Classe</th>
                <th>Moyenne</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(eleve, index) in topEleves" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ eleve.nom }}</td>
                <td>{{ getClasseNom(eleve.classe) }}</td>
                <td>{{ eleve.moyenne }}/20</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="card mt-4">
        <h2>Répartition des moyennes</h2>
        <div class="chart-container">
          <div class="distribution-chart">
            <div class="distribution-bar">
              <div class="segment" style="width: 5%; background-color: #f44336;" title="< 8/20: 5%"></div>
              <div class="segment" style="width: 15%; background-color: #ff9800;" title="8-10/20: 15%"></div>
              <div class="segment" style="width: 40%; background-color: #ffc107;" title="10-12/20: 40%"></div>
              <div class="segment" style="width: 25%; background-color: #8bc34a;" title="12-14/20: 25%"></div>
              <div class="segment" style="width: 10%; background-color: #4caf50;" title="14-16/20: 10%"></div>
              <div class="segment" style="width: 5%; background-color: #2196f3;" title="> 16/20: 5%"></div>
            </div>
            <div class="distribution-legend">
              <div class="legend-item">
                <div class="legend-color" style="background-color: #f44336;"></div>
                <div class="legend-label">Moins de 8/20: 5%</div>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #ff9800;"></div>
                <div class="legend-label">8-10/20: 15%</div>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #ffc107;"></div>
                <div class="legend-label">10-12/20: 40%</div>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #8bc34a;"></div>
                <div class="legend-label">12-14/20: 25%</div>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #4caf50;"></div>
                <div class="legend-label">14-16/20: 10%</div>
              </div>
              <div class="legend-item">
                <div class="legend-color" style="background-color: #2196f3;"></div>
                <div class="legend-label">Plus de 16/20: 5%</div>
              </div>
            </div>
          </div>
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
  name: 'StatistiquesResultats',
  data() {
    return {
      selectedExamenId: '',
      selectedClasseId: '',
      statsGenerated: false,
      
      examens: [
        { 
          id: 1, 
          type: 'Trimestre 1', 
          dateDebut: '2024-09-15', 
          dateFin: '2024-10-30', 
          classesIds: [1, 2], 
          verrouille: true 
        },
        { 
          id: 2, 
          type: 'Trimestre 1', 
          dateDebut: '2024-09-15', 
          dateFin: '2024-10-30', 
          classesIds: [3, 4], 
          verrouille: false 
        },
        { 
          id: 3, 
          type: 'Trimestre 2', 
          dateDebut: '2024-12-01', 
          dateFin: '2025-01-15', 
          classesIds: [1, 2, 3, 4], 
          verrouille: false 
        }
      ],
      
      classes: [
        { id: 1, nom: '6ème A', effectif: 35 },
        { id: 2, nom: '6ème B', effectif: 32 },
        { id: 3, nom: '5ème A', effectif: 30 },
        { id: 4, nom: '5ème B', effectif: 28 }
      ],
      
      moyennesParMatiere: {
        1: { matiere: 'Mathématiques', moyenne: 12.75, tauxReussite: 78 },
        2: { matiere: 'Physique', moyenne: 11.30, tauxReussite: 65 },
        3: { matiere: 'Français', moyenne: 13.45, tauxReussite: 82 },
        4: { matiere: 'Histoire-Géographie', moyenne: 12.10, tauxReussite: 70 },
        5: { matiere: 'Anglais', moyenne: 14.20, tauxReussite: 88 }
      },
      
      topEleves: [
        { id: 1, nom: 'Dupont Jean', classe: 1, moyenne: 17.85 },
        { id: 2, nom: 'Martin Sophie', classe: 1, moyenne: 16.92 },
        { id: 3, nom: 'Dubois Pierre', classe: 2, moyenne: 16.45 },
        { id: 4, nom: 'Lefebvre Marie', classe: 3, moyenne: 16.20 },
        { id: 5, nom: 'Moreau Lucas', classe: 2, moyenne: 15.78 }
      ]
    }
  },
  computed: {
    canGenerateStats() {
      return this.selectedExamenId && this.selectedClasseId;
    }
  },
  methods: {
    generateStatistiques() {
      // Dans une application réelle, on récupérerait les données depuis l'API
      // Pour l'instant, on simule la génération
      this.statsGenerated = true;
      
      // Afficher un message de succès
      alert('Statistiques générées avec succès !');
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    
    getClasseNom(classeId) {
      const classe = this.classes.find(c => c.id === classeId);
      return classe ? classe.nom : '';
    }
  }
}
</script>

<style scoped>
.statistiques-container {
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

.btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.mt-4 {
  margin-top: 1.5rem;
}

.chart-container {
  margin-top: 1rem;
  height: 300px;
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
  width: 150px;
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

.bar-value.success {
  background-color: #4caf50;
}

.line-chart-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.line-chart {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.chart-legend {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-bottom: 1rem;
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

.chart-grid {
  position: relative;
  flex-grow: 1;
  border-left: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}

.chart-line {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 60%;
  border-top: 2px solid #3f51b5;
}

.chart-line:nth-child(2) {
  height: 80%;
  border-top-color: #4caf50;
}

.point {
  position: absolute;
  bottom: 100%;
  left: 0%;
  width: 8px;
  height: 8px;
  background-color: currentColor;
  border-radius: 50%;
  transform: translate(-50%, 50%);
}

.point:nth-child(2) {
  left: 50%;
}

.point:nth-child(3) {
  left: 100%;
}

.x-axis {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.tick {
  position: absolute;
  bottom: -30px;
  left: 0%;
  transform: translateX(-50%);
  text-align: center;
  font-size: 0.9rem;
  color: #757575;
}

.tick:nth-child(2) {
  left: 50%;
}

.tick:nth-child(3) {
  left: 100%;
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

.data-table tbody tr:hover {
  background-color: #f9f9f9;
}

.distribution-chart {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.distribution-bar {
  width: 100%;
  height: 40px;
  display: flex;
  border-radius: 4px;
  overflow: hidden;
}

.segment {
  height: 100%;
}

.distribution-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
}

.export-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>
