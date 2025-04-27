<template>
  <div class="archives-container">
    <h1>Archives des Bulletins</h1>
    
    <div class="card">
      <h2>Recherche d'archives</h2>
      <div class="search-form">
        <div class="form-group">
          <label for="anneeScolaire">Année scolaire</label>
          <select id="anneeScolaire" v-model="selectedAnneeScolaire">
            <option value="2024-2025">2024-2025</option>
            <option value="2023-2024">2023-2024</option>
            <option value="2022-2023">2022-2023</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="classe">Classe</label>
          <select id="classe" v-model="selectedClasseId">
            <option value="">Toutes les classes</option>
            <option v-for="classe in classes" :key="classe.id" :value="classe.id">
              {{ classe.nom }}
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="examen">Examen</label>
          <select id="examen" v-model="selectedExamenType">
            <option value="">Tous les examens</option>
            <option value="Trimestre 1">Trimestre 1</option>
            <option value="Trimestre 2">Trimestre 2</option>
            <option value="Trimestre 3">Trimestre 3</option>
            <option value="Examen Final">Examen Final</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="searchEleve">Recherche par élève</label>
          <input type="text" id="searchEleve" v-model="searchEleve" placeholder="Nom ou matricule de l'élève">
        </div>
        
        <div class="form-actions">
          <button @click="searchArchives" class="btn btn-primary">
            <i class="fas fa-search"></i> Rechercher
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="archives.length > 0" class="card mt-4">
      <h2>Résultats de la recherche</h2>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Élève</th>
              <th>Matricule</th>
              <th>Classe</th>
              <th>Examen</th>
              <th>Date de génération</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(archive, index) in archives" :key="index">
              <td>{{ archive.eleveName }}</td>
              <td>{{ archive.matricule }}</td>
              <td>{{ archive.classe }}</td>
              <td>{{ archive.examen }}</td>
              <td>{{ formatDate(archive.dateGeneration) }}</td>
              <td>
                <button @click="viewBulletin(archive)" class="btn-icon" title="Voir bulletin">
                  <i class="fas fa-eye"></i>
                </button>
                <button @click="downloadBulletin(archive)" class="btn-icon" title="Télécharger">
                  <i class="fas fa-download"></i>
                </button>
                <button @click="printBulletin(archive)" class="btn-icon" title="Imprimer">
                  <i class="fas fa-print"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="export-actions mt-3">
        <button @click="downloadAllBulletins" class="btn btn-secondary">
          <i class="fas fa-download"></i> Télécharger tous les bulletins
        </button>
      </div>
    </div>
    
    <div v-if="showCloudStorage" class="card mt-4">
      <h2>Stockage Cloud</h2>
      <div class="cloud-storage">
        <div class="storage-info">
          <div class="storage-icon">
            <i class="fas fa-cloud"></i>
          </div>
          <div class="storage-details">
            <h3>Espace de stockage</h3>
            <div class="storage-bar">
              <div class="storage-used" style="width: 35%;"></div>
            </div>
            <p>1.75 GB utilisés sur 5 GB</p>
          </div>
        </div>
        
        <div class="storage-actions">
          <button class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Synchroniser
          </button>
          <button class="btn btn-secondary">
            <i class="fas fa-cog"></i> Paramètres
          </button>
        </div>
      </div>
      
      <div class="folder-structure mt-3">
        <h3>Structure des dossiers</h3>
        <div class="folder-tree">
          <div class="folder-item">
            <i class="fas fa-folder"></i> Archives
            <div class="folder-children">
              <div class="folder-item">
                <i class="fas fa-folder"></i> 2024-2025
                <div class="folder-children">
                  <div class="folder-item">
                    <i class="fas fa-folder"></i> 6ème A
                  </div>
                  <div class="folder-item">
                    <i class="fas fa-folder"></i> 6ème B
                  </div>
                  <div class="folder-item">
                    <i class="fas fa-folder"></i> 5ème A
                  </div>
                  <div class="folder-item">
                    <i class="fas fa-folder"></i> 5ème B
                  </div>
                </div>
              </div>
              <div class="folder-item">
                <i class="fas fa-folder"></i> 2023-2024
              </div>
              <div class="folder-item">
                <i class="fas fa-folder"></i> 2022-2023
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ArchivesBulletins',
  data() {
    return {
      selectedAnneeScolaire: '2024-2025',
      selectedClasseId: '',
      selectedExamenType: '',
      searchEleve: '',
      showCloudStorage: true,
      
      classes: [
        { id: 1, nom: '6ème A', effectif: 35 },
        { id: 2, nom: '6ème B', effectif: 32 },
        { id: 3, nom: '5ème A', effectif: 30 },
        { id: 4, nom: '5ème B', effectif: 28 }
      ],
      
      archives: [
        {
          id: 1,
          eleveName: 'Dupont Jean',
          matricule: 'E001',
          classe: '6ème A',
          examen: 'Trimestre 1',
          dateGeneration: '2024-11-15T10:30:00',
          url: '/bulletins/E001_T1.pdf'
        },
        {
          id: 2,
          eleveName: 'Martin Sophie',
          matricule: 'E002',
          classe: '6ème A',
          examen: 'Trimestre 1',
          dateGeneration: '2024-11-15T10:35:00',
          url: '/bulletins/E002_T1.pdf'
        },
        {
          id: 3,
          eleveName: 'Dubois Pierre',
          matricule: 'E003',
          classe: '6ème A',
          examen: 'Trimestre 1',
          dateGeneration: '2024-11-15T10:40:00',
          url: '/bulletins/E003_T1.pdf'
        }
      ]
    }
  },
  methods: {
    searchArchives() {
      // Dans une application réelle, on filtrerait les données via une API
      // Pour l'instant, on simule une recherche
      alert('Recherche effectuée !');
    },
    
    viewBulletin(archive) {
      // Dans une application réelle, on ouvrirait le PDF
      alert(`Affichage du bulletin: ${archive.url}`);
    },
    
    downloadBulletin(archive) {
      // Dans une application réelle, on téléchargerait le PDF
      alert(`Téléchargement du bulletin: ${archive.url}`);
    },
    
    printBulletin(archive) {
      // Dans une application réelle, on imprimerait le PDF
      alert(`Impression du bulletin: ${archive.url}`);
    },
    
    downloadAllBulletins() {
      // Dans une application réelle, on téléchargerait tous les PDF
      alert(`Téléchargement de ${this.archives.length} bulletins`);
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    }
  }
}
</script>

<style scoped>
.archives-container {
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

h3 {
  margin-top: 0;
  margin-bottom: 0.5rem;
  font-size: 1rem;
  color: #333;
}

.search-form {
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

.cloud-storage {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.storage-info {
  display: flex;
  align-items: center;
}

.storage-icon {
  font-size: 2.5rem;
  color: #3f51b5;
  margin-right: 1.5rem;
}

.storage-bar {
  width: 200px;
  height: 8px;
  background-color: #e0e0e0;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  overflow: hidden;
}

.storage-used {
  height: 100%;
  background-color: #3f51b5;
  border-radius: 4px;
}

.storage-details p {
  margin: 0;
  color: #757575;
  font-size: 0.9rem;
}

.storage-actions {
  display: flex;
  gap: 1rem;
}

.folder-structure {
  padding: 1rem;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.folder-tree {
  margin-top: 1rem;
}

.folder-item {
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.folder-item i {
  color: #ffc107;
  margin-right: 0.5rem;
}

.folder-children {
  margin-left: 1.5rem;
  margin-top: 0.5rem;
}
</style>
