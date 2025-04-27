<template>
  <div class="notes-container">
    <h1>Saisie des Notes</h1>
    
    <div class="card">
      <h2>Sélection de l'examen et de la classe</h2>
      <div class="selection-form">
        <div class="form-group">
          <label for="examen">Examen</label>
          <select id="examen" v-model="selectedExamenId" required>
            <option value="" disabled>Sélectionner un examen</option>
            <option v-for="examen in examens" :key="examen.id" :value="examen.id">
              {{ examen.type }} ({{ formatDate(examen.dateDebut) }} - {{ formatDate(examen.dateFin) }})
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="classe">Classe</label>
          <select id="classe" v-model="selectedClasseId" required>
            <option value="" disabled>Sélectionner une classe</option>
            <option v-for="classe in classesForExamen" :key="classe.id" :value="classe.id">
              {{ classe.nom }} ({{ classe.effectif }} élèves)
            </option>
          </select>
        </div>
        
        <div class="form-actions">
          <button @click="loadNotes" class="btn btn-primary" :disabled="!canLoadNotes">
            Charger les notes
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="notesLoaded" class="card mt-4">
      <div class="card-header-actions">
        <h2>Tableau de saisie des notes</h2>
        <div class="header-actions">
          <button class="btn btn-secondary">
            <i class="fas fa-file-excel"></i> Importer Excel
          </button>
          <button class="btn btn-secondary">
            <i class="fas fa-file-export"></i> Exporter
          </button>
        </div>
      </div>
      
      <div class="table-responsive">
        <table class="data-table notes-table">
          <thead>
            <tr>
              <th rowspan="2" class="sticky-col">Élève</th>
              <th v-for="matiere in matieres" :key="matiere.id" :colspan="3" class="matiere-header">
                {{ matiere.nom }} (Coef. {{ matiere.coefficient }})
              </th>
              <th rowspan="2">Moyenne</th>
              <th rowspan="2">Rang</th>
            </tr>
            <tr>
              <template v-for="matiere in matieres" :key="`sub-${matiere.id}`">
                <th>DS1</th>
                <th>DS2</th>
                <th>Examen</th>
              </template>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(eleve) in eleves" :key="eleve.id">
              <td class="sticky-col">{{ eleve.nom }} {{ eleve.prenom }}</td>
              
              <template v-for="matiere in matieres" :key="`note-${eleve.id}-${matiere.id}`">
                <td>
                  <input 
                    type="number" 
                    min="0" 
                    max="20" 
                    step="0.25"
                    v-model="notesData[eleve.id][matiere.id].ds1"
                    @change="calculateAverages()"
                    :disabled="examenVerrouille"
                  >
                </td>
                <td>
                  <input 
                    type="number" 
                    min="0" 
                    max="20" 
                    step="0.25"
                    v-model="notesData[eleve.id][matiere.id].ds2"
                    @change="calculateAverages()"
                    :disabled="examenVerrouille"
                  >
                </td>
                <td>
                  <input 
                    type="number" 
                    min="0" 
                    max="20" 
                    step="0.25"
                    v-model="notesData[eleve.id][matiere.id].examen"
                    @change="calculateAverages()"
                    :disabled="examenVerrouille"
                  >
                </td>
              </template>
              
              <td class="moyenne">{{ moyennes[eleve.id] ? moyennes[eleve.id].toFixed(2) : '-' }}</td>
              <td class="rang">{{ rangs[eleve.id] || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="form-actions mt-3">
        <button @click="saveNotes" class="btn btn-primary" :disabled="examenVerrouille">
          Enregistrer les notes
        </button>
        <button v-if="!examenVerrouille" @click="validateNotes" class="btn btn-success">
          Valider et verrouiller
        </button>
      </div>
    </div>
    
    <div v-if="examenVerrouille && notesLoaded" class="alert alert-warning mt-4">
      <i class="fas fa-lock"></i> Cet examen est verrouillé. Les notes ne peuvent plus être modifiées.
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotesGestion',
  data() {
    return {
      selectedExamenId: '',
      selectedClasseId: '',
      notesLoaded: false,
      examenVerrouille: false,
      
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
      ],
      
      matieres: [
        { id: 1, nom: 'Mathématiques', abreviation: 'MATH', coefficient: 4 },
        { id: 2, nom: 'Physique', abreviation: 'PHY', coefficient: 3 },
        { id: 3, nom: 'Français', abreviation: 'FR', coefficient: 3 }
      ],
      
      eleves: [
        { id: 1, nom: 'Dupont', prenom: 'Jean', matricule: 'E001', classeId: 1 },
        { id: 2, nom: 'Martin', prenom: 'Sophie', matricule: 'E002', classeId: 1 },
        { id: 3, nom: 'Dubois', prenom: 'Pierre', matricule: 'E003', classeId: 1 }
      ],
      
      notesData: {},
      moyennes: {},
      rangs: {}
    }
  },
  
  computed: {
    classesForExamen() {
      if (!this.selectedExamenId) return [];
      
      const examen = this.examens.find(e => e.id === this.selectedExamenId);
      if (!examen) return [];
      
      return this.classes.filter(c => examen.classesIds.includes(c.id));
    },
    
    canLoadNotes() {
      return this.selectedExamenId && this.selectedClasseId;
    }
  },
  
  methods: {
    loadNotes() {
      // Dans une application réelle, on chargerait les données depuis le serveur
      // Pour l'instant, on simule le chargement avec des données fictives
      
      // Vérifier si l'examen est verrouillé
      const examen = this.examens.find(e => e.id === this.selectedExamenId);
      this.examenVerrouille = examen ? examen.verrouille : false;
      
      // Filtrer les élèves de la classe sélectionnée
      this.eleves = [
        { id: 1, nom: 'Dupont', prenom: 'Jean', matricule: 'E001', classeId: this.selectedClasseId },
        { id: 2, nom: 'Martin', prenom: 'Sophie', matricule: 'E002', classeId: this.selectedClasseId },
        { id: 3, nom: 'Dubois', prenom: 'Pierre', matricule: 'E003', classeId: this.selectedClasseId }
      ];
      
      // Initialiser les données de notes
      this.notesData = {};
      this.eleves.forEach(eleve => {
        this.notesData[eleve.id] = {};
        this.matieres.forEach(matiere => {
          // Générer des notes aléatoires pour la démonstration
          this.notesData[eleve.id][matiere.id] = {
            ds1: Math.floor(Math.random() * 10) + 10, // Entre 10 et 19
            ds2: Math.floor(Math.random() * 10) + 10,
            examen: Math.floor(Math.random() * 10) + 10
          };
        });
      });
      
      this.calculateAverages();
      this.notesLoaded = true;
    },
    
    calculateAverages() {
      // Calculer les moyennes pour chaque élève
      this.moyennes = {};
      
      this.eleves.forEach(eleve => {
        let totalPoints = 0;
        let totalCoefficients = 0;
        
        this.matieres.forEach(matiere => {
          const notes = this.notesData[eleve.id][matiere.id];
          const moyenne = (parseFloat(notes.ds1) + parseFloat(notes.ds2) + parseFloat(notes.examen)) / 3;
          const points = moyenne * matiere.coefficient;
          
          totalPoints += points;
          totalCoefficients += matiere.coefficient;
        });
        
        this.moyennes[eleve.id] = totalPoints / totalCoefficients;
      });
      
      // Calculer les rangs
      const moyennesArray = Object.entries(this.moyennes)
        .map(([eleveId, moyenne]) => ({ eleveId: parseInt(eleveId), moyenne }))
        .sort((a, b) => b.moyenne - a.moyenne);
      
      this.rangs = {};
      moyennesArray.forEach((item, index) => {
        this.rangs[item.eleveId] = index + 1;
      });
    },
    
    saveNotes() {
      // Dans une application réelle, on enverrait les données au serveur
      alert('Notes enregistrées avec succès !');
    },
    
    validateNotes() {
      if (confirm('Êtes-vous sûr de vouloir valider et verrouiller ces notes ? Cette action est irréversible.')) {
        // Dans une application réelle, on enverrait la demande au serveur
        const examen = this.examens.find(e => e.id === this.selectedExamenId);
        if (examen) {
          examen.verrouille = true;
          this.examenVerrouille = true;
        }
        
        alert('Notes validées et verrouillées avec succès !');
      }
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    }
  }
}
</script>

<style scoped>
.notes-container {
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

.selection-form {
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

.btn-success {
  background-color: #4caf50;
  color: white;
}

.btn-success:hover {
  background-color: #388e3c;
}

.btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.mt-3 {
  margin-top: 1rem;
}

.mt-4 {
  margin-top: 1.5rem;
}

.card-header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.card-header-actions h2 {
  margin: 0;
  border-bottom: none;
  padding-bottom: 0;
}

.header-actions {
  display: flex;
  gap: 0.5rem;
}

.table-responsive {
  overflow-x: auto;
  max-width: 100%;
}

.notes-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.notes-table th, .notes-table td {
  padding: 0.5rem;
  text-align: center;
  border: 1px solid #ddd;
}

.notes-table th {
  background-color: #f5f5f5;
  font-weight: 500;
}

.matiere-header {
  background-color: #e0e0e0;
}

.sticky-col {
  position: sticky;
  left: 0;
  background-color: #f5f5f5;
  z-index: 1;
  text-align: left;
  min-width: 150px;
}

.notes-table input {
  width: 60px;
  text-align: center;
  padding: 0.25rem;
}

.moyenne, .rang {
  font-weight: bold;
  background-color: #f5f5f5;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.alert-warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}

.alert i {
  margin-right: 0.5rem;
}
</style>
