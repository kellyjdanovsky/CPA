<template>
  <div class="bulletins-container">
    <h1>Génération des Bulletins</h1>
    
    <div class="card">
      <h2>Paramètres de génération</h2>
      <div class="generation-form">
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
          <select id="classe" v-model="selectedClasseId" :disabled="!selectedExamenId" required>
            <option value="" disabled>Sélectionner une classe</option>
            <option v-for="classe in classesForExamen" :key="classe.id" :value="classe.id">
              {{ classe.nom }} ({{ classe.effectif }} élèves)
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="eleve">Élève (optionnel)</label>
          <select id="eleve" v-model="selectedEleveId" :disabled="!selectedClasseId">
            <option value="">Tous les élèves</option>
            <option v-for="eleve in elevesForClasse" :key="eleve.id" :value="eleve.id">
              {{ eleve.nom }} {{ eleve.prenom }} ({{ eleve.matricule }})
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="format">Format</label>
          <select id="format" v-model="format" required>
            <option value="A5">A5 Portrait</option>
            <option value="A4">A4 Portrait</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Options</label>
          <div class="checkbox-item">
            <input type="checkbox" id="inclureSignature" v-model="options.inclureSignature">
            <label for="inclureSignature">Inclure signature du directeur</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="inclureGraphique" v-model="options.inclureGraphique">
            <label for="inclureGraphique">Inclure graphique de progression</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="inclureAppreciation" v-model="options.inclureAppreciation">
            <label for="inclureAppreciation">Inclure appréciation générale</label>
          </div>
        </div>
        
        <div class="form-actions">
          <button @click="previewBulletin" class="btn btn-secondary" :disabled="!canGenerateBulletin">
            <i class="fas fa-eye"></i> Aperçu
          </button>
          <button @click="generateBulletins" class="btn btn-primary" :disabled="!canGenerateBulletin">
            <i class="fas fa-file-pdf"></i> Générer PDF
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="bulletinsGenerated" class="card mt-4">
      <div class="card-header-actions">
        <h2>Bulletins générés</h2>
        <div class="header-actions">
          <button @click="downloadAllBulletins" class="btn btn-primary">
            <i class="fas fa-download"></i> Télécharger tout
          </button>
        </div>
      </div>
      
      <div class="bulletins-list">
        <div v-for="(bulletin, index) in generatedBulletins" :key="index" class="bulletin-item">
          <div class="bulletin-info">
            <span class="bulletin-name">{{ bulletin.eleveName }}</span>
            <span class="bulletin-details">{{ bulletin.classe }} - {{ bulletin.examen }}</span>
          </div>
          <div class="bulletin-actions">
            <button class="btn-icon" title="Aperçu">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn-icon" title="Télécharger">
              <i class="fas fa-download"></i>
            </button>
            <button class="btn-icon" title="Imprimer">
              <i class="fas fa-print"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="showPreview" class="bulletin-preview-overlay">
      <div class="bulletin-preview">
        <div class="preview-header">
          <h3>Aperçu du bulletin</h3>
          <button @click="closePreview" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="preview-content">
          <div class="bulletin-template" :class="format">
            <div class="bulletin-header">
              <div class="school-info">
                <h2>Collège Privé Adventiste Avaratetezana</h2>
                <p>Année scolaire: 2024-2025</p>
                <p>{{ currentExamen ? currentExamen.type : '' }}</p>
              </div>
              <div class="student-info">
                <p><strong>Nom et prénom:</strong> {{ currentEleve ? `${currentEleve.nom} ${currentEleve.prenom}` : 'Dupont Jean' }}</p>
                <p><strong>Matricule:</strong> {{ currentEleve ? currentEleve.matricule : 'E001' }}</p>
                <p><strong>Classe:</strong> {{ currentClasse ? currentClasse.nom : '6ème A' }}</p>
                <p><strong>Effectif:</strong> {{ currentClasse ? currentClasse.effectif : '35' }}</p>
              </div>
            </div>
            
            <div class="bulletin-body">
              <table class="grades-table">
                <thead>
                  <tr>
                    <th>Matière (Coeff)</th>
                    <th>DS1 (/20)</th>
                    <th>DS2 (/20)</th>
                    <th>Examen (/20)</th>
                    <th>Moyenne (/20)</th>
                    <th>Total Pondéré</th>
                    <th>Remarques</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(matiere, index) in matieres" :key="index">
                    <td>{{ matiere.nom }} ({{ matiere.coefficient }})</td>
                    <td>{{ getRandomNote() }}</td>
                    <td>{{ getRandomNote() }}</td>
                    <td>{{ getRandomNote() }}</td>
                    <td>{{ getRandomAverage() }}</td>
                    <td>{{ (getRandomAverage() * matiere.coefficient).toFixed(1) }}</td>
                    <td>{{ getRandomRemark() }}</td>
                  </tr>
                </tbody>
              </table>
              
              <div class="bulletin-summary">
                <div class="summary-item">
                  <span>Somme des Totaux Pondérés:</span>
                  <span>287.5</span>
                </div>
                <div class="summary-item">
                  <span>Somme des Coefficients:</span>
                  <span>18</span>
                </div>
                <div class="summary-item">
                  <span>Moyenne Générale Élève:</span>
                  <span>15.97/20</span>
                </div>
                <div class="summary-item">
                  <span>Moyenne de la Classe:</span>
                  <span>14.23/20</span>
                </div>
                <div class="summary-item">
                  <span>Rang:</span>
                  <span>5ème/35</span>
                </div>
              </div>
              
              <div v-if="options.inclureGraphique" class="bulletin-chart">
                <p class="chart-title">Progression trimestrielle</p>
                <div class="chart-placeholder">
                  [Graphique de progression]
                </div>
              </div>
              
              <div v-if="options.inclureAppreciation" class="bulletin-appreciation">
                <p class="appreciation-title">Appréciation générale</p>
                <p class="appreciation-content">Élève sérieux et appliqué. Bons résultats dans l'ensemble. Poursuivez vos efforts.</p>
              </div>
            </div>
            
            <div class="bulletin-footer">
              <div class="signature-block">
                <div class="signature-item">
                  <p>Professeur Principal</p>
                  <div class="signature-line"></div>
                </div>
                <div v-if="options.inclureSignature" class="signature-item">
                  <p>Directrice</p>
                  <div class="signature-line"></div>
                </div>
                <div v-if="options.inclureSignature" class="signature-item">
                  <p>Cachet de l'établissement</p>
                  <div class="signature-stamp"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="preview-actions">
          <button @click="closePreview" class="btn btn-secondary">Fermer</button>
          <button @click="generateBulletins" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Générer PDF
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BulletinsGeneration',
  data() {
    return {
      selectedExamenId: '',
      selectedClasseId: '',
      selectedEleveId: '',
      format: 'A5',
      options: {
        inclureSignature: true,
        inclureGraphique: false,
        inclureAppreciation: true
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
      ],
      
      eleves: [
        { id: 1, nom: 'Dupont', prenom: 'Jean', matricule: 'E001', classeId: 1 },
        { id: 2, nom: 'Martin', prenom: 'Sophie', matricule: 'E002', classeId: 1 },
        { id: 3, nom: 'Dubois', prenom: 'Pierre', matricule: 'E003', classeId: 1 },
        { id: 4, nom: 'Lefebvre', prenom: 'Marie', matricule: 'E004', classeId: 2 },
        { id: 5, nom: 'Moreau', prenom: 'Lucas', matricule: 'E005', classeId: 2 }
      ],
      
      matieres: [
        { id: 1, nom: 'Mathématiques', abreviation: 'MATH', coefficient: 4 },
        { id: 2, nom: 'Physique', abreviation: 'PHY', coefficient: 3 },
        { id: 3, nom: 'Français', abreviation: 'FR', coefficient: 3 },
        { id: 4, nom: 'Histoire-Géographie', abreviation: 'HIST', coefficient: 2 },
        { id: 5, nom: 'Anglais', abreviation: 'ANG', coefficient: 2 },
        { id: 6, nom: 'SVT', abreviation: 'SVT', coefficient: 2 },
        { id: 7, nom: 'Éducation Physique', abreviation: 'EPS', coefficient: 1 },
        { id: 8, nom: 'Arts Plastiques', abreviation: 'ART', coefficient: 1 }
      ],
      
      showPreview: false,
      bulletinsGenerated: false,
      generatedBulletins: []
    }
  },
  
  computed: {
    classesForExamen() {
      if (!this.selectedExamenId) return [];
      
      const examen = this.examens.find(e => e.id === this.selectedExamenId);
      if (!examen) return [];
      
      return this.classes.filter(c => examen.classesIds.includes(c.id));
    },
    
    elevesForClasse() {
      if (!this.selectedClasseId) return [];
      return this.eleves.filter(e => e.classeId === this.selectedClasseId);
    },
    
    canGenerateBulletin() {
      return this.selectedExamenId && this.selectedClasseId;
    },
    
    currentExamen() {
      return this.examens.find(e => e.id === this.selectedExamenId);
    },
    
    currentClasse() {
      return this.classes.find(c => c.id === this.selectedClasseId);
    },
    
    currentEleve() {
      if (!this.selectedEleveId) return null;
      return this.eleves.find(e => e.id === this.selectedEleveId);
    }
  },
  
  methods: {
    previewBulletin() {
      this.showPreview = true;
    },
    
    closePreview() {
      this.showPreview = false;
    },
    
    generateBulletins() {
      // Dans une application réelle, on générerait les PDF et les enverrait au serveur
      // Pour l'instant, on simule la génération
      
      this.generatedBulletins = [];
      
      if (this.selectedEleveId) {
        // Génération pour un seul élève
        const eleve = this.eleves.find(e => e.id === this.selectedEleveId);
        const classe = this.classes.find(c => c.id === this.selectedClasseId);
        const examen = this.examens.find(e => e.id === this.selectedExamenId);
        
        if (eleve && classe && examen) {
          this.generatedBulletins.push({
            id: Date.now(),
            eleveId: eleve.id,
            eleveName: `${eleve.nom} ${eleve.prenom}`,
            classe: classe.nom,
            examen: examen.type,
            format: this.format,
            options: { ...this.options }
          });
        }
      } else {
        // Génération pour toute la classe
        const classe = this.classes.find(c => c.id === this.selectedClasseId);
        const examen = this.examens.find(e => e.id === this.selectedExamenId);
        const classEleves = this.eleves.filter(e => e.classeId === this.selectedClasseId);
        
        if (classe && examen) {
          classEleves.forEach(eleve => {
            this.generatedBulletins.push({
              id: Date.now() + eleve.id,
              eleveId: eleve.id,
              eleveName: `${eleve.nom} ${eleve.prenom}`,
              classe: classe.nom,
              examen: examen.type,
              format: this.format,
              options: { ...this.options }
            });
          });
        }
      }
      
      this.bulletinsGenerated = true;
      this.showPreview = false;
      
      // Afficher un message de succès
      alert(`${this.generatedBulletins.length} bulletin(s) généré(s) avec succès !`);
    },
    
    downloadAllBulletins() {
      // Dans une application réelle, on téléchargerait les PDF
      alert('Téléchargement de tous les bulletins en cours...');
    },
    
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    
    getRandomNote() {
      return (Math.floor(Math.random() * 10) + 10).toString();
    },
    
    getRandomAverage() {
      return (Math.floor(Math.random() * 1000) / 100 + 10).toFixed(2);
    },
    
    getRandomRemark() {
      const remarks = [
        'Bon travail',
        'Peut mieux faire',
        'Excellents résultats',
        'En progrès',
        'Efforts à poursuivre'
      ];
      return remarks[Math.floor(Math.random() * remarks.length)];
    }
  }
}
</script>

<style scoped>
.bulletins-container {
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

.generation-form {
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

.checkbox-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.checkbox-item input[type="checkbox"] {
  width: auto;
  margin-right: 0.5rem;
}

.form-actions {
  grid-column: 1 / -1;
  disp
(Content truncated due to size limit. Use line ranges to read in chunks)