<template>
  <div class="examens-container">
    <h1>Gestion des Examens</h1>
    
    <div class="card">
      <h2>Paramétrage d'un examen</h2>
      <form @submit.prevent="saveExamen" class="examen-form">
        <div class="form-group">
          <label for="type">Type d'examen</label>
          <select id="type" v-model="newExamen.type" required>
            <option value="" disabled>Sélectionner un type</option>
            <option value="Trimestre 1">Trimestre 1</option>
            <option value="Trimestre 2">Trimestre 2</option>
            <option value="Trimestre 3">Trimestre 3</option>
            <option value="Semestre 1">Semestre 1</option>
            <option value="Semestre 2">Semestre 2</option>
            <option value="Examen Final">Examen Final</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="dateDebut">Date de début</label>
          <input type="date" id="dateDebut" v-model="newExamen.dateDebut" required>
        </div>
        
        <div class="form-group">
          <label for="dateFin">Date de fin</label>
          <input type="date" id="dateFin" v-model="newExamen.dateFin" required>
        </div>
        
        <div class="form-group full-width">
          <label>Classes concernées</label>
          <div class="checkbox-group">
            <div v-for="classe in classes" :key="classe.id" class="checkbox-item">
              <input type="checkbox" :id="'classe-' + classe.id" :value="classe.id" v-model="newExamen.classesIds">
              <label :for="'classe-' + classe.id">{{ classe.nom }}</label>
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="verrouillage">Verrouillage des notes</label>
          <div class="toggle-switch">
            <input type="checkbox" id="verrouillage" v-model="newExamen.verrouille">
            <label for="verrouillage"></label>
            <span class="toggle-label">{{ newExamen.verrouille ? 'Verrouillé' : 'Déverrouillé' }}</span>
          </div>
        </div>
        
        <div class="form-actions">
          <button type="reset" class="btn btn-secondary">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
    
    <div class="card mt-4">
      <h2>Liste des examens</h2>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Période</th>
              <th>Classes</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(examen, index) in examens" :key="index">
              <td>{{ examen.type }}</td>
              <td>{{ formatDate(examen.dateDebut) }} - {{ formatDate(examen.dateFin) }}</td>
              <td>{{ getClassesNames(examen.classesIds) }}</td>
              <td>
                <span :class="['status-badge', examen.verrouille ? 'locked' : 'unlocked']">
                  {{ examen.verrouille ? 'Verrouillé' : 'Déverrouillé' }}
                </span>
              </td>
              <td>
                <button @click="toggleVerrouillage(examen)" class="btn-icon" :title="examen.verrouille ? 'Déverrouiller' : 'Verrouiller'">
                  <i :class="['fas', examen.verrouille ? 'fa-lock' : 'fa-lock-open']"></i>
                </button>
                <button @click="editExamen(examen)" class="btn-icon" title="Modifier">
                  <i class="fas fa-edit"></i>
                </button>
                <router-link :to="'/notes?examenId=' + examen.id" class="btn-icon" title="Saisir les notes">
                  <i class="fas fa-pen"></i>
                </router-link>
              </td>
            </tr>
            <tr v-if="examens.length === 0">
              <td colspan="5" class="text-center">Aucun examen trouvé</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExamensList',
  data() {
    return {
      newExamen: {
        type: '',
        dateDebut: '',
        dateFin: '',
        classesIds: [],
        verrouille: false
      },
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
        }
      ],
      classes: [
        { id: 1, nom: '6ème A', effectif: 35 },
        { id: 2, nom: '6ème B', effectif: 32 },
        { id: 3, nom: '5ème A', effectif: 30 },
        { id: 4, nom: '5ème B', effectif: 28 }
      ]
    }
  },
  methods: {
    saveExamen() {
      // Dans une application réelle, on enverrait les données au serveur
      // Pour l'instant, on simule l'ajout à la liste locale
      const examen = {
        id: Date.now(),
        ...this.newExamen
      };
      
      this.examens.push(examen);
      
      // Réinitialiser le formulaire
      this.newExamen = {
        type: '',
        dateDebut: '',
        dateFin: '',
        classesIds: [],
        verrouille: false
      };
      
      // Afficher un message de succès
      alert('Examen créé avec succès !');
    },
    
    editExamen() {
      // Dans une application réelle, on ouvrirait un modal d'édition
      alert('Fonctionnalité d\'édition à implémenter');
    },
    
    toggleVerrouillage(examen) {
      // Dans une application réelle, on enverrait la demande au serveur
      examen.verrouille = !examen.verrouille;
      
      // Afficher un message de succès
      alert(`Examen ${examen.verrouille ? 'verrouillé' : 'déverrouillé'} avec succès !`);
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    
    getClassesNames(classesIds) {
      return classesIds.map(id => {
        const classe = this.classes.find(c => c.id === id);
        return classe ? classe.nom : '';
      }).join(', ');
    }
  }
}
</script>

<style scoped>
.examens-container {
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

.examen-form {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.full-width {
  grid-column: 1 / -1;
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

.checkbox-group {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.5rem;
}

.checkbox-item {
  display: flex;
  align-items: center;
}

.checkbox-item input[type="checkbox"] {
  width: auto;
  margin-right: 0.5rem;
}

.toggle-switch {
  display: flex;
  align-items: center;
}

.toggle-switch input[type="checkbox"] {
  display: none;
}

.toggle-switch label {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
  background-color: #ccc;
  border-radius: 12px;
  margin: 0 10px 0 0;
  cursor: pointer;
  transition: background-color 0.3s;
}

.toggle-switch label:after {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: white;
  top: 2px;
  left: 2px;
  transition: left 0.3s;
}

.toggle-switch input[type="checkbox"]:checked + label {
  background-color: #3f51b5;
}

.toggle-switch input[type="checkbox"]:checked + label:after {
  left: 28px;
}

.toggle-label {
  font-size: 0.9rem;
  color: #555;
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

.text-center {
  text-align: center;
}

.btn-icon {
  background: none;
  border: none;
  color: #3f51b5;
  cursor: pointer;
  font-size: 1rem;
  padding: 0.25rem;
  margin-right: 0.5rem;
}

.btn-icon:hover {
  color: #303f9f;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
}

.locked {
  background-color: #f44336;
  color: white;
}

.unlocked {
  background-color: #4caf50;
  color: white;
}
</style>
