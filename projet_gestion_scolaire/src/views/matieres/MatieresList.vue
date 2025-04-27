<template>
  <div class="matieres-container">
    <h1>Gestion des Matières</h1>
    
    <div class="card">
      <h2>Création d'une matière</h2>
      <form @submit.prevent="saveMatiere" class="matiere-form">
        <div class="form-group">
          <label for="nom">Nom complet</label>
          <input type="text" id="nom" v-model="newMatiere.nom" placeholder="Ex: Mathématiques" required>
        </div>
        
        <div class="form-group">
          <label for="abreviation">Abréviation</label>
          <input type="text" id="abreviation" v-model="newMatiere.abreviation" placeholder="Ex: MATH" required maxlength="5">
        </div>
        
        <div class="form-group">
          <label for="coefficient">Coefficient</label>
          <input type="number" id="coefficient" v-model="newMatiere.coefficient" min="1" max="10" required>
        </div>
        
        <div class="form-actions">
          <button type="reset" class="btn btn-secondary">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
    
    <div class="card mt-4">
      <h2>Liste des matières</h2>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Abréviation</th>
              <th>Coefficient</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(matiere, index) in matieresSorted" :key="index">
              <td>{{ matiere.nom }}</td>
              <td>{{ matiere.abreviation }}</td>
              <td>{{ matiere.coefficient }}</td>
              <td>
                <button @click="editMatiere(matiere)" class="btn-icon" title="Modifier">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="confirmDeleteMatiere(matiere.id)" class="btn-icon" title="Supprimer">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr v-if="matieres.length === 0">
              <td colspan="4" class="text-center">Aucune matière trouvée</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Modal d'édition (simulé) -->
    <div v-if="editMode" class="modal-overlay">
      <div class="modal-content">
        <h3>Modifier une matière</h3>
        <form @submit.prevent="updateMatiere" class="matiere-form">
          <div class="form-group">
            <label for="editNom">Nom complet</label>
            <input type="text" id="editNom" v-model="editedMatiere.nom" required>
          </div>
          
          <div class="form-group">
            <label for="editAbreviation">Abréviation</label>
            <input type="text" id="editAbreviation" v-model="editedMatiere.abreviation" required maxlength="5">
          </div>
          
          <div class="form-group">
            <label for="editCoefficient">Coefficient</label>
            <input type="number" id="editCoefficient" v-model="editedMatiere.coefficient" min="1" max="10" required>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="cancelEdit" class="btn btn-secondary">Annuler</button>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MatieresList',
  data() {
    return {
      newMatiere: {
        nom: '',
        abreviation: '',
        coefficient: 1
      },
      matieres: [
        { id: 1, nom: 'Mathématiques', abreviation: 'MATH', coefficient: 4 },
        { id: 2, nom: 'Physique', abreviation: 'PHY', coefficient: 3 },
        { id: 3, nom: 'Français', abreviation: 'FR', coefficient: 3 },
        { id: 4, nom: 'Histoire-Géographie', abreviation: 'HIST', coefficient: 2 }
      ],
      editMode: false,
      editedMatiere: null,
      editedIndex: -1
    }
  },
  computed: {
    matieresSorted() {
      return [...this.matieres].sort((a, b) => a.nom.localeCompare(b.nom));
    }
  },
  methods: {
    saveMatiere() {
      // Dans une application réelle, on enverrait les données au serveur
      // Pour l'instant, on simule l'ajout à la liste locale
      const matiere = {
        id: Date.now(),
        ...this.newMatiere
      };
      
      this.matieres.push(matiere);
      
      // Réinitialiser le formulaire
      this.newMatiere = {
        nom: '',
        abreviation: '',
        coefficient: 1
      };
      
      // Afficher un message de succès
      alert('Matière ajoutée avec succès !');
    },
    
    editMatiere(matiere) {
      this.editMode = true;
      this.editedIndex = this.matieres.findIndex(m => m.id === matiere.id);
      this.editedMatiere = { ...matiere };
    },
    
    updateMatiere() {
      // Dans une application réelle, on enverrait les données au serveur
      // Pour l'instant, on simule la mise à jour dans la liste locale
      if (this.editedIndex !== -1) {
        this.matieres.splice(this.editedIndex, 1, this.editedMatiere);
      }
      
      this.cancelEdit();
      
      // Afficher un message de succès
      alert('Matière mise à jour avec succès !');
    },
    
    cancelEdit() {
      this.editMode = false;
      this.editedMatiere = null;
      this.editedIndex = -1;
    },
    
    confirmDeleteMatiere(id) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')) {
        this.deleteMatiere(id);
      }
    },
    
    deleteMatiere(id) {
      // Dans une application réelle, on enverrait la demande au serveur
      // Pour l'instant, on simule la suppression dans la liste locale
      this.matieres = this.matieres.filter(m => m.id !== id);
      
      // Afficher un message de succès
      alert('Matière supprimée avec succès !');
    }
  }
}
</script>

<style scoped>
.matieres-container {
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

.matiere-form {
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

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.modal-content h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #333;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}
</style>
