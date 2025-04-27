/* eslint-disable no-unused-vars */
// Simuler une API backend avec des délais pour imiter les appels réseau
const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// Service pour la gestion des élèves
export const ElevesService = {
  async getAll() {
    await delay(300);
    return [
      { id: 1, nom: 'Dupont', prenom: 'Jean', matricule: 'E001', classeId: 1 },
      { id: 2, nom: 'Martin', prenom: 'Sophie', matricule: 'E002', classeId: 1 },
      { id: 3, nom: 'Dubois', prenom: 'Pierre', matricule: 'E003', classeId: 1 },
      { id: 4, nom: 'Lefebvre', prenom: 'Marie', matricule: 'E004', classeId: 2 },
      { id: 5, nom: 'Moreau', prenom: 'Lucas', matricule: 'E005', classeId: 2 },
      { id: 6, nom: 'Petit', prenom: 'Emma', matricule: 'E006', classeId: 3 },
      { id: 7, nom: 'Robert', prenom: 'Thomas', matricule: 'E007', classeId: 3 }
    ];
  },
  
  async getByClasse(classeId) {
    const eleves = await this.getAll();
    return eleves.filter(eleve => eleve.classeId === classeId);
  },
  
  async getById(id) {
    const eleves = await this.getAll();
    return eleves.find(eleve => eleve.id === id);
  }
};

// Service pour la gestion des classes
export const ClassesService = {
  async getAll() {
    await delay(300);
    return [
      { id: 1, nom: '6ème A', effectif: 35 },
      { id: 2, nom: '6ème B', effectif: 32 },
      { id: 3, nom: '5ème A', effectif: 30 },
      { id: 4, nom: '5ème B', effectif: 28 }
    ];
  },
  
  async getById(id) {
    const classes = await this.getAll();
    return classes.find(classe => classe.id === id);
  }
};

// Service pour la gestion des matières
export const MatieresService = {
  async getAll() {
    await delay(300);
    return [
      { id: 1, nom: 'Mathématiques', abreviation: 'MATH', coefficient: 4 },
      { id: 2, nom: 'Physique', abreviation: 'PHY', coefficient: 3 },
      { id: 3, nom: 'Français', abreviation: 'FR', coefficient: 3 },
      { id: 4, nom: 'Histoire-Géographie', abreviation: 'HIST', coefficient: 2 },
      { id: 5, nom: 'Anglais', abreviation: 'ANG', coefficient: 2 },
      { id: 6, nom: 'SVT', abreviation: 'SVT', coefficient: 2 },
      { id: 7, nom: 'Éducation Physique', abreviation: 'EPS', coefficient: 1 },
      { id: 8, nom: 'Arts Plastiques', abreviation: 'ART', coefficient: 1 }
    ];
  },
  
  async create(matiere) {
    await delay(500);
    // Simuler la création avec un nouvel ID
    return { ...matiere, id: Date.now() };
  },
  
  async update(_, matiere) {
    await delay(500);
    // Simuler la mise à jour
    return { ...matiere };
  },
  
  async delete(id) {
    await delay(500);
    // Simuler la suppression
    return { success: true };
  }
};

// Service pour la gestion des examens
export const ExamensService = {
  async getAll() {
    await delay(300);
    return [
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
    ];
  },
  
  async create(examen) {
    await delay(500);
    // Simuler la création avec un nouvel ID
    return { ...examen, id: Date.now() };
  },
  
  async update(id, examen) {
    await delay(500);
    // Simuler la mise à jour
    return { ...examen, id };
  },
  
  async toggleVerrouillage(id) {
    await delay(300);
    const examens = await this.getAll();
    const examen = examens.find(e => e.id === id);
    if (examen) {
      examen.verrouille = !examen.verrouille;
      return examen;
    }
    throw new Error('Examen non trouvé');
  }
};

// Service pour la gestion des notes
export const NotesService = {
  async getByClasseAndExamen(classeId, _) {
    await delay(500);
    
    // Récupérer les élèves de la classe
    const eleves = await ElevesService.getByClasse(classeId);
    
    // Récupérer les matières
    const matieres = await MatieresService.getAll();
    
    // Générer des notes aléatoires pour chaque élève et chaque matière
    const notes = {};
    
    eleves.forEach(eleve => {
      notes[eleve.id] = {};
      matieres.forEach(matiere => {
        notes[eleve.id][matiere.id] = {
          ds1: Math.floor(Math.random() * 10) + 10, // Entre 10 et 19
          ds2: Math.floor(Math.random() * 10) + 10,
          examen: Math.floor(Math.random() * 10) + 10
        };
      });
    });
    
    return notes;
  },
  
  async saveNotes() {
    await delay(700);
    // Simuler la sauvegarde
    return { success: true, message: 'Notes enregistrées avec succès' };
  },
  
  async calculateMoyennes(classeId, examenId) {
    await delay(500);
    
    // Récupérer les élèves de la classe
    const eleves = await ElevesService.getByClasse(classeId);
    
    // Récupérer les matières avec leurs coefficients
    const matieres = await MatieresService.getAll();
    
    // Récupérer les notes
    const notes = await this.getByClasseAndExamen(classeId, examenId);
    
    // Calculer les moyennes pour chaque élève
    const moyennes = {};
    const totauxPonderes = {};
    const totalCoefficients = matieres.reduce((sum, matiere) => sum + matiere.coefficient, 0);
    
    eleves.forEach(eleve => {
      let totalPoints = 0;
      
      matieres.forEach(matiere => {
        const eleveNotes = notes[eleve.id][matiere.id];
        const moyenne = (parseFloat(eleveNotes.ds1) + parseFloat(eleveNotes.ds2) + parseFloat(eleveNotes.examen)) / 3;
        const points = moyenne * matiere.coefficient;
        
        totalPoints += points;
      });
      
      moyennes[eleve.id] = totalPoints / totalCoefficients;
      totauxPonderes[eleve.id] = totalPoints;
    });
    
    // Calculer les rangs
    const moyennesArray = Object.entries(moyennes)
      .map(([eleveId, moyenne]) => ({ eleveId: parseInt(eleveId), moyenne }))
      .sort((a, b) => b.moyenne - a.moyenne);
    
    const rangs = {};
    moyennesArray.forEach((item, index) => {
      rangs[item.eleveId] = index + 1;
    });
    
    // Calculer la moyenne de la classe
    const moyenneClasse = moyennesArray.reduce((sum, item) => sum + item.moyenne, 0) / moyennesArray.length;
    
    return {
      moyennes,
      totauxPonderes,
      rangs,
      moyenneClasse,
      totalCoefficients
    };
  }
};

// Service pour la gestion des bulletins
export const BulletinsService = {
  async generer(examenId, classeId, eleveId = null, options = {}) {
    await delay(1000);
    
    // Récupérer les informations nécessaires
    const examen = await ExamensService.getAll().then(examens => examens.find(e => e.id === examenId));
    const classe = await ClassesService.getById(classeId);
    
    // Récupérer les élèves concernés
    let eleves = [];
    if (eleveId) {
      const eleve = await ElevesService.getById(eleveId);
      if (eleve) eleves = [eleve];
    } else {
      eleves = await ElevesService.getByClasse(classeId);
    }
    
    // Récupérer les calculs de moyennes
    const { moyennes, totauxPonderes, rangs, moyenneClasse, totalCoefficients } = 
      await NotesService.calculateMoyennes(classeId, examenId);
    
    // Générer les bulletins
    const bulletins = eleves.map(eleve => ({
      id: `${examenId}_${classeId}_${eleve.id}`,
      examenId,
      classeId,
      eleveId: eleve.id,
      eleveName: `${eleve.nom} ${eleve.prenom}`,
      matricule: eleve.matricule,
      classe: classe.nom,
      examen: examen.type,
      moyenne: moyennes[eleve.id],
      totalPondere: totauxPonderes[eleve.id],
      rang: rangs[eleve.id],
      effectif: classe.effectif,
      moyenneClasse,
      totalCoefficients,
      dateGeneration: new Date().toISOString(),
      options
    }));
    
    return bulletins;
  },
  
  async getBulletinPDF(bulletinId) {
    await delay(700);
    // Simuler la génération d'un PDF
    return { url: `/bulletins/${bulletinId}.pdf` };
  }
};

// Service pour la gestion financière
export const FinanceService = {
  // Gestion des encaissements
  async getEncaissements(filters = {}) {
    await delay(400);
    
    const encaissements = [
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
      },
      { 
        id: 3, 
        date: '2024-03-22', 
        eleveId: 3, 
        montant: 50000, 
        motif: 'Frais de scolarité', 
        modePaiement: 'Cash' 
      },
      { 
        id: 4, 
        date: '2024-03-25', 
        eleveId: 4, 
        montant: 15000, 
        motif: 'Frais d\'inscription', 
        modePaiement: 'ADRA' 
      }
    ];
    
    // Appliquer les filtres si nécessaire
    let result = [...encaissements];
    
    if (filters.dateDebut) {
      result = result.filter(e => new Date(e.date) >= new Date(filters.dateDebut));
    }
    
    if (filters.dateFin) {
      result = result.filter(e => new Date(e.date) <= new Date(filters.dateFin));
    }
    
    if (filters.searchTerm) {
      const term = filters.searchTerm.toLowerCase();
      // Dans une vraie application, cette recherche serait faite côté serveur
      const eleves = await ElevesService.getAll();
      
      result = result.filter(e => {
        const eleve = eleves.find(el => el.id === e.eleveId);
        return eleve && (
          eleve.nom.toLowerCase().includes(term) || 
          eleve.prenom.toLowerCase().includes(term) || 
          eleve.matricule.toLowerCase().includes(term)
        );
      });
    }
    
    return result;
  },
  
  async saveEncaissement(encaissement) {
    await delay(500);
    // Simuler la sauvegarde
    return { ...encaissement, id: Date.now() };
  },
  
  async getEncaissementRecu(encaissementId) {
    await delay(300);
    // Simuler la génération d'un reçu
    return { url: `/recus/${encaissementId}.pdf` };
  },
  
  // Gestion des décaissements
  async getDecaissements(filters = {}) {
    await delay(400);
    
    const decaissements = [
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
    ];
    
    // Appliquer les filtres si nécessaire
    let result = [...decaissements];
    
    if (filters.dateDebut) {
      result = result.filter(d => new Date(d.date) >= new Date(filters.dateDebut));
    }
    
    if (filters.dateFin) {
      result = result.filter(d => new Date(d.date) <= new Date(filters.dateFin));
    }
    
    if (filters.beneficiaire) {
      const term = filters.beneficiaire.toLowerCase();
      result = result.filter(d => d.beneficiaire.toLowerCase().includes(term));
    }
    
    return result;
  },
  
  async saveDecaissement(decaissement) {
    await delay(500);
    // Simuler la sauvegarde
    return { ...decaissement, id: Date.now() };
  },
  
  // Synthèse financière
  async getSynthese(periode) {
    await delay(600);
    
    // Simuler des données de synthèse
    const encaissements = await this.getEncaissements();
    const decaissements = await this.getDecaissements();
    
    const totalEncaissements = encaissements.reduce((sum, e) => sum + e.montant, 0);
    const totalDecaissements = decaissements.reduce((sum, d) => sum + d.montant, 0);
    
    // Répartition par mode de paiement
    const repartitionModes = {
      Cash: encaissements.filter(e => e.modePaiement === 'Cash').reduce((sum, e) => sum + e.montant, 0),
      ADRA: encaissements.filter(e => e.modePaiement === 'ADRA').reduce((sum, e) => sum + e.montant, 0)
    };
    
    // Répartition par motif
    const motifs = [...new Set(encaissements.map(e => e.motif))];
    const repartitionMotifs = {};
    motifs.forEach(motif => {
      repartitionMotifs[motif] = encaissements
        .filter(e => e.motif === motif)
        .reduce((sum, e) => sum + e.montant, 0);
    });
    
    return {
      totalEncaissements,
      totalDecaissements,
      solde: totalEncaissements - totalDecaissements,
      repartitionModes,
      repartitionMotifs,
      periode
    };
  }
};

// Service pour les statistiques
export const StatistiquesService = {
  async getResultatsParMatiere() {
    await delay(700);
    
    // Récupérer les matières
    const matieres = await MatieresService.getAll();
    
    // Simuler des moyennes par matière
    const moyennesParMatiere = {};
    matieres.forEach(matiere => {
      moyennesParMatiere[matiere.id] = {
        matiere: matiere.nom,
        moyenne: (Math.random() * 6 + 10).toFixed(2), // Entre 10 et 16
        tauxReussite: Math.floor(Math.random() * 40 + 60) // Entre 60% et 100%
      };
    });
    
    return moyennesParMatiere;
  },
  
  async getEvolutionResultats(_) {
    await delay(700);
    
    // Simuler l'évolution des résultats sur 3 trimestres
    return {
      trimestres: ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'],
      moyennes: [12.45, 13.22, 14.05],
      tauxReussite: [68, 75, 82]
    };
  },
  
  async getTopEleves(examenId, limit = 5) {
    await delay(500);
    
    // Récupérer les élèves
    const eleves = await ElevesService.getAll();
    
    // Simuler le top des élèves
    const topEleves = eleves
      .slice(0, Math.min(limit, eleves.length))
      .map(eleve => ({
        id: eleve.id,
        nom: `${eleve.nom} ${eleve.prenom}`,
        moyenne: (Math.random() * 4 + 16).toFixed(2), // Entre 16 et 20
        classe: eleve.classeId
      }))
      .sort((a, b) => b.moyenne - a.moyenne);
    
    return topEleves;
  }
};
