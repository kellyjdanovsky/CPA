<template>
  <div class="encaissement-container">
    <h1>Gestion des Encaissements</h1>
    
    <div class="card">
      <h2>Enregistrement d'un paiement</h2>
      <form @submit.prevent="saveEncaissement" class="encaissement-form">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" v-model="newEncaissement.date" required>
        </div>
        
        <div class="form-group">
          <label for="eleve">Élève</label>
          <select id="eleve" v-model="newEncaissement.eleveId" required>
            <option value="" disabled>Sélectionner un élève</option>
            <option v-for="eleve in eleves" :key="eleve.id" :value="eleve.id">
              {{ eleve.nom }} {{ eleve.prenom }} ({{ eleve.matricule }})
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="montant">Montant (Ar)</label>
          <input type="number" id="montant" v-model="newEncaissement.montant" min="0" required>
        </div>
        
        <div class="form-group">
          <label for="motif">Motif</label>
          <select id="motif" v-model="newEncaissement.motif" required>
            <option value="" disabled>Sélectionner un motif</option>
            <option value="Frais de scolarité">Frais de scolarité</option>
            <option value="Frais d'examen">Frais d'examen</option>
            <option value="Frais d'inscription">Frais d'inscription</option>
            <option value="Autre">Autre</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="modePaiement">Mode de paiement</label>
          <select id="modePaiement" v-model="newEncaissement.modePaiement" required>
            <option value="" disabled>Sélectionner un mode</option>
            <option value="Cash">Cash</option>
            <option value="ADRA">ADRA</option>
          </select>
        </div>
        
        <div class="form-actions">
          <button type="reset" class="btn btn-secondary">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
    
    <div class="card mt-4">
      <h2>Recherche avancée</h2>
      <div class="search-form">
        <div class="form-group">
          <label for="searchTerm">Recherche par matricule ou nom</label>
          <input type="text" id="searchTerm" v-model="searchTerm" placeholder="Entrez un matricule ou un nom">
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
          <button @click="searchEncaissements" class="btn btn-primary">Rechercher</button>
        </div>
      </div>
    </div>
    
    <div class="card mt-4">
      <h2>Liste des encaissements</h2>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Élève</th>
              <th>Matricule</th>
              <th>Montant (Ar)</th>
              <th>Motif</th>
              <th>Mode</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(encaissement, index) in encaissements" :key="index">
              <td>{{ formatDate(encaissement.date) }}</td>
              <td>{{ getEleveName(encaissement.eleveId) }}</td>
              <td>{{ getEleveMatricule(encaissement.eleveId) }}</td>
              <td>{{ formatMontant(encaissement.montant) }}</td>
              <td>{{ encaissement.motif }}</td>
              <td>{{ encaissement.modePaiement }}</td>
              <td>
                <button class="btn-icon" title="Imprimer reçu">
                  <i class="fas fa-print"></i>
                </button>
                <button class="btn-icon" title="Voir détails">
                  <i class="fas fa-eye"></i>
                </button>
              </td>
            </tr>
            <tr v-if="encaissements.length === 0">
              <td colspan="7" class="text-center">Aucun encaissement trouvé</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="export-actions mt-3">
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
  name: 'FinanceEncaissement',
  data() {
    return {
      newEncaissement: {
        date: new Date().toISOString().substr(0, 10),
        eleveId: '',
        montant: '',
        motif: '',
        modePaiement: ''
      },
      encaissements: [
        { 
          id: 1, 
          date: '2024-03-15', 
          eleveId: 1, 
          montant: 50000, 
          motif: 'Frais de scolarité', 
          modePaiement: 'Cash' 
        },
        { 
          id: 2, 
          date: '2024-03-20', 
          eleveId: 2, 
          montant: 25000, 
          motif: 'Frais d\'examen', 
          modePaiement: 'ADRA' 
        }
      ],
      eleves: [
        { id: 1, nom: 'Dupont', prenom: 'Jean', matricule: 'E001', classeId: 1 },
        { id: 2, nom: 'Martin', prenom: 'Sophie', matricule: 'E002', classeId: 1 }
      ],
      searchTerm: '',
      dateDebut: '',
      dateFin: ''
    }
  },
  methods: {
    saveEncaissement() {
      // Dans une application réelle, on enverrait les données au serveur
      // Pour l'instant, on simule l'ajout à la liste locale
      const encaissement = {
        id: Date.now(),
        ...this.newEncaissement
      };
      
      this.encaissements.unshift(encaissement);
      
      // Réinitialiser le formulaire
      this.newEncaissement = {
        date: new Date().toISOString().substr(0, 10),
        eleveId: '',
        montant: '',
        motif: '',
        modePaiement: ''
      };
      
      // Afficher un message de succès
      alert('Encaissement enregistré avec succès !');
    },
    
    searchEncaissements() {
      // Dans une application réelle, on filtrerait les données via une API
      // Pour l'instant, on simule une recherche
      alert('Recherche effectuée !');
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-FR').format(montant);
    },
    
    getEleveName(eleveId) {
      const eleve = this.eleves.find(e => e.id === eleveId);
      return eleve ? `${eleve.nom} ${eleve.prenom}` : 'Inconnu';
    },
    
    getEleveMatricule(eleveId) {
      const eleve = this.eleves.find(e => e.id === eleveId);
      return eleve ? eleve.matricule : 'Inconnu';
    }
  }
}
</script>

<style scoped>
.encaissement-container {
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

.encaissement-form, .search-form {
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

.mt-3 {
  margin-top: 1rem;
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

.export-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>
