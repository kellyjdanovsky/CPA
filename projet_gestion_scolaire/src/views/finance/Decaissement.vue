<template>
  <div class="decaissement-container">
    <h1>Gestion des Décaissements</h1>
    
    <div class="card">
      <h2>Création d'un ordre de paiement</h2>
      <form @submit.prevent="saveDecaissement" class="decaissement-form">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" v-model="newDecaissement.date" required>
        </div>
        
        <div class="form-group">
          <label for="beneficiaire">Bénéficiaire</label>
          <input type="text" id="beneficiaire" v-model="newDecaissement.beneficiaire" placeholder="Nom du bénéficiaire" required>
        </div>
        
        <div class="form-group">
          <label for="montant">Montant (Ar)</label>
          <input type="number" id="montant" v-model="newDecaissement.montant" min="0" required>
        </div>
        
        <div class="form-group">
          <label for="motif">Motif</label>
          <textarea id="motif" v-model="newDecaissement.motif" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
          <label for="justificatif">Justificatif (optionnel)</label>
          <input type="file" id="justificatif" @change="handleFileUpload">
          <small>Formats acceptés: PDF, JPG, PNG (max 5MB)</small>
        </div>
        
        <div class="form-actions">
          <button type="reset" class="btn btn-secondary">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
    
    <div class="card mt-4">
      <h2>Liste des décaissements</h2>
      <div class="search-form">
        <div class="form-group">
          <label for="searchBeneficiaire">Recherche par bénéficiaire</label>
          <input type="text" id="searchBeneficiaire" v-model="searchBeneficiaire" placeholder="Nom du bénéficiaire">
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
          <button @click="searchDecaissements" class="btn btn-primary">Rechercher</button>
        </div>
      </div>
      
      <div class="table-responsive mt-3">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Bénéficiaire</th>
              <th>Montant (Ar)</th>
              <th>Motif</th>
              <th>Justificatif</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(decaissement, index) in decaissements" :key="index">
              <td>{{ formatDate(decaissement.date) }}</td>
              <td>{{ decaissement.beneficiaire }}</td>
              <td>{{ formatMontant(decaissement.montant) }}</td>
              <td>{{ decaissement.motif }}</td>
              <td>
                <a v-if="decaissement.justificatif" href="#" @click.prevent="viewJustificatif(decaissement)">
                  <i class="fas fa-file-pdf"></i> Voir
                </a>
                <span v-else>-</span>
              </td>
              <td>
                <button class="btn-icon" title="Imprimer ordre">
                  <i class="fas fa-print"></i>
                </button>
                <button class="btn-icon" title="Voir détails">
                  <i class="fas fa-eye"></i>
                </button>
              </td>
            </tr>
            <tr v-if="decaissements.length === 0">
              <td colspan="6" class="text-center">Aucun décaissement trouvé</td>
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
  name: 'FinanceDecaissement',
  data() {
    return {
      newDecaissement: {
        date: new Date().toISOString().substr(0, 10),
        beneficiaire: '',
        montant: '',
        motif: '',
        justificatif: null
      },
      decaissements: [
        { 
          id: 1, 
          date: '2024-03-10', 
          beneficiaire: 'Fournisseur Papeterie', 
          montant: 120000, 
          motif: 'Achat fournitures', 
          justificatif: 'facture_001.pdf' 
        },
        { 
          id: 2, 
          date: '2024-03-18', 
          beneficiaire: 'JIRAMA', 
          montant: 350000, 
          motif: 'Facture électricité', 
          justificatif: 'facture_jirama.pdf' 
        }
      ],
      searchBeneficiaire: '',
      dateDebut: '',
      dateFin: ''
    }
  },
  methods: {
    saveDecaissement() {
      // Dans une application réelle, on enverrait les données au serveur
      // Pour l'instant, on simule l'ajout à la liste locale
      const decaissement = {
        id: Date.now(),
        ...this.newDecaissement
      };
      
      this.decaissements.unshift(decaissement);
      
      // Réinitialiser le formulaire
      this.newDecaissement = {
        date: new Date().toISOString().substr(0, 10),
        beneficiaire: '',
        montant: '',
        motif: '',
        justificatif: null
      };
      
      // Afficher un message de succès
      alert('Décaissement enregistré avec succès !');
    },
    
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Dans une application réelle, on enverrait le fichier au serveur
        // Pour l'instant, on stocke juste le nom du fichier
        this.newDecaissement.justificatif = file.name;
      }
    },
    
    searchDecaissements() {
      // Dans une application réelle, on filtrerait les données via une API
      // Pour l'instant, on simule une recherche
      alert('Recherche effectuée !');
    },
    
    viewJustificatif(decaissement) {
      // Dans une application réelle, on ouvrirait le fichier
      alert(`Affichage du justificatif: ${decaissement.justificatif}`);
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-FR').format(montant);
    }
  }
}
</script>

<style scoped>
.decaissement-container {
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

.decaissement-form, .search-form {
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

input, select, textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

textarea {
  resize: vertical;
}

small {
  display: block;
  margin-top: 0.25rem;
  color: #757575;
  font-size: 0.8rem;
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
