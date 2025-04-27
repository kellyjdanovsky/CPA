import Vue from 'vue'
import Vuex from 'vuex'
import { 
  ElevesService, 
  ClassesService, 
  MatieresService, 
  ExamensService, 
  NotesService, 
  BulletinsService, 
  FinanceService,
  StatistiquesService 
} from '../services/api'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    // État global de l'application
    currentUser: { nom: 'Administrateur', role: 'admin' },
    isAuthenticated: true,
    isLoading: false,
    
    // Module Finance
    encaissements: [],
    decaissements: [],
    
    // Module Matières et Examens
    matieres: [],
    examens: [],
    
    // Module Notes
    notes: {},
    
    // Module Bulletins
    bulletins: [],
    
    // Données partagées
    eleves: [],
    classes: [],
    anneeScolaire: '2024-2025'
  },
  
  mutations: {
    SET_LOADING(state, isLoading) {
      state.isLoading = isLoading;
    },
    
    // Mutations pour le module Finance
    SET_ENCAISSEMENTS(state, encaissements) {
      state.encaissements = encaissements;
    },
    ADD_ENCAISSEMENT(state, encaissement) {
      state.encaissements.unshift(encaissement);
    },
    SET_DECAISSEMENTS(state, decaissements) {
      state.decaissements = decaissements;
    },
    ADD_DECAISSEMENT(state, decaissement) {
      state.decaissements.unshift(decaissement);
    },
    
    // Mutations pour le module Matières et Examens
    SET_MATIERES(state, matieres) {
      state.matieres = matieres;
    },
    ADD_MATIERE(state, matiere) {
      state.matieres.push(matiere);
    },
    UPDATE_MATIERE(state, { id, matiere }) {
      const index = state.matieres.findIndex(m => m.id === id);
      if (index !== -1) {
        state.matieres.splice(index, 1, matiere);
      }
    },
    DELETE_MATIERE(state, id) {
      state.matieres = state.matieres.filter(m => m.id !== id);
    },
    SET_EXAMENS(state, examens) {
      state.examens = examens;
    },
    ADD_EXAMEN(state, examen) {
      state.examens.push(examen);
    },
    UPDATE_EXAMEN(state, { id, examen }) {
      const index = state.examens.findIndex(e => e.id === id);
      if (index !== -1) {
        state.examens.splice(index, 1, examen);
      }
    },
    
    // Mutations pour le module Notes
    SET_NOTES(state, { classeId, examenId, notes }) {
      Vue.set(state.notes, `${classeId}_${examenId}`, notes);
    },
    UPDATE_NOTE(state, { classeId, examenId, eleveId, matiereId, note }) {
      const key = `${classeId}_${examenId}`;
      if (state.notes[key] && state.notes[key][eleveId] && state.notes[key][eleveId][matiereId]) {
        state.notes[key][eleveId][matiereId] = { ...state.notes[key][eleveId][matiereId], ...note };
      }
    },
    
    // Mutations pour le module Bulletins
    SET_BULLETINS(state, bulletins) {
      state.bulletins = bulletins;
    },
    ADD_BULLETIN(state, bulletin) {
      state.bulletins.push(bulletin);
    },
    
    // Mutations pour les données partagées
    SET_ELEVES(state, eleves) {
      state.eleves = eleves;
    },
    SET_CLASSES(state, classes) {
      state.classes = classes;
    },
    SET_ANNEE_SCOLAIRE(state, anneeScolaire) {
      state.anneeScolaire = anneeScolaire;
    }
  },
  
  actions: {
    // Actions pour le module Finance
    async fetchEncaissements({ commit }, filters = {}) {
      commit('SET_LOADING', true);
      try {
        const encaissements = await FinanceService.getEncaissements(filters);
        commit('SET_ENCAISSEMENTS', encaissements);
      } catch (error) {
        console.error('Erreur lors de la récupération des encaissements:', error);
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async saveEncaissement({ commit }, encaissement) {
      commit('SET_LOADING', true);
      try {
        const savedEncaissement = await FinanceService.saveEncaissement(encaissement);
        commit('ADD_ENCAISSEMENT', savedEncaissement);
        return savedEncaissement;
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de l\'encaissement:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    // Actions pour le module Matières
    async fetchMatieres({ commit }) {
      commit('SET_LOADING', true);
      try {
        const matieres = await MatieresService.getAll();
        commit('SET_MATIERES', matieres);
      } catch (error) {
        console.error('Erreur lors de la récupération des matières:', error);
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async createMatiere({ commit }, matiere) {
      commit('SET_LOADING', true);
      try {
        const savedMatiere = await MatieresService.create(matiere);
        commit('ADD_MATIERE', savedMatiere);
        return savedMatiere;
      } catch (error) {
        console.error('Erreur lors de la création de la matière:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async updateMatiere({ commit }, { id, matiere }) {
      commit('SET_LOADING', true);
      try {
        const updatedMatiere = await MatieresService.update(id, matiere);
        commit('UPDATE_MATIERE', { id, matiere: updatedMatiere });
        return updatedMatiere;
      } catch (error) {
        console.error('Erreur lors de la mise à jour de la matière:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async deleteMatiere({ commit }, id) {
      commit('SET_LOADING', true);
      try {
        await MatieresService.delete(id);
        commit('DELETE_MATIERE', id);
      } catch (error) {
        console.error('Erreur lors de la suppression de la matière:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    // Actions pour le module Examens
    async fetchExamens({ commit }) {
      commit('SET_LOADING', true);
      try {
        const examens = await ExamensService.getAll();
        commit('SET_EXAMENS', examens);
      } catch (error) {
        console.error('Erreur lors de la récupération des examens:', error);
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async createExamen({ commit }, examen) {
      commit('SET_LOADING', true);
      try {
        const savedExamen = await ExamensService.create(examen);
        commit('ADD_EXAMEN', savedExamen);
        return savedExamen;
      } catch (error) {
        console.error('Erreur lors de la création de l\'examen:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async toggleExamenVerrouillage({ commit }, id) {
      commit('SET_LOADING', true);
      try {
        const updatedExamen = await ExamensService.toggleVerrouillage(id);
        commit('UPDATE_EXAMEN', { id, examen: updatedExamen });
        return updatedExamen;
      } catch (error) {
        console.error('Erreur lors du verrouillage/déverrouillage de l\'examen:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    // Actions pour le module Notes
    async fetchNotes({ commit }, { classeId, examenId }) {
      commit('SET_LOADING', true);
      try {
        const notes = await NotesService.getByClasseAndExamen(classeId, examenId);
        commit('SET_NOTES', { classeId, examenId, notes });
        return notes;
      } catch (error) {
        console.error('Erreur lors de la récupération des notes:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async saveNotes({ commit }, { classeId, examenId, notes }) {
      commit('SET_LOADING', true);
      try {
        await NotesService.saveNotes(classeId, examenId, notes);
        commit('SET_NOTES', { classeId, examenId, notes });
        return { success: true };
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement des notes:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async calculateMoyennes({ commit }, { classeId, examenId }) {
      commit('SET_LOADING', true);
      try {
        const result = await NotesService.calculateMoyennes(classeId, examenId);
        return result;
      } catch (error) {
        console.error('Erreur lors du calcul des moyennes:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    // Actions pour le module Bulletins
    async generateBulletins({ commit }, { examenId, classeId, eleveId, options }) {
      commit('SET_LOADING', true);
      try {
        const bulletins = await BulletinsService.generer(examenId, classeId, eleveId, options);
        commit('SET_BULLETINS', bulletins);
        return bulletins;
      } catch (error) {
        console.error('Erreur lors de la génération des bulletins:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    // Actions pour les données partagées
    async fetchEleves({ commit }) {
      commit('SET_LOADING', true);
      try {
        const eleves = await ElevesService.getAll();
        commit('SET_ELEVES', eleves);
      } catch (error) {
        console.error('Erreur lors de la récupération des élèves:', error);
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async fetchClasses({ commit }) {
      commit('SET_LOADING', true);
      try {
        const classes = await ClassesService.getAll();
        commit('SET_CLASSES', classes);
      } catch (error) {
        console.error('Erreur lors de la récupération des classes:', error);
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    // Actions pour les statistiques
    async fetchStatistiquesParMatiere({ commit }, examenId) {
      commit('SET_LOADING', true);
      try {
        return await StatistiquesService.getResultatsParMatiere(examenId);
      } catch (error) {
        console.error('Erreur lors de la récupération des statistiques par matière:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async fetchEvolutionResultats({ commit }, classeId) {
      commit('SET_LOADING', true);
      try {
        return await StatistiquesService.getEvolutionResultats(classeId);
      } catch (error) {
        console.error('Erreur lors de la récupération de l\'évolution des résultats:', error);
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    }
  },
  
  getters: {
    // Getters pour le module Finance
    totalEncaissements: state => {
      return state.encaissements.reduce((total, e) => total + e.montant, 0);
    },
    encaissementsByPeriod: state => (debut, fin) => {
      return state.encaissements.filter(e => {
        const date = new Date(e.date);
        return date >= debut && date <= fin;
      });
    },
    
    // Getters pour le module Matières et Examens
    matieresSorted: state => {
      return [...state.matieres].sort((a, b) => a.nom.localeCompare(b.nom));
    },
    
    // Getters pour le module Notes
    notesByEleve: state => (classeId, examenId, eleveId) => {
      const key = `${classeId}_${examenId}`;
      if (state.notes[key]) {
        return state.notes[key][eleveId];
      }
      return null;
    },
    
    // Getters pour les données partagées
    elevesByClasse: state => classeId => {
      return state.eleves.filter(e => e.classeId === classeId);
    },
    getEleveById: state => id => {
      return state.eleves.find(e => e.id === id);
    },
    getClasseById: state => id => {
      return state.classes.find(c => c.id === id);
    },
    
    // Getter pour l'état de chargement
    isLoading: state => state.isLoading
  }
})
