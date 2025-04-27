import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

// Importation des vues
const Dashboard = () => import('../views/Dashboard.vue')
const FinanceEncaissement = () => import('../views/finance/Encaissement.vue')
const FinanceDecaissement = () => import('../views/finance/Decaissement.vue')
const FinanceSynthese = () => import('../views/finance/Synthese.vue')
const MatieresList = () => import('../views/matieres/MatieresList.vue')
const ExamensList = () => import('../views/examens/ExamensList.vue')
const NotesGestion = () => import('../views/notes/NotesGestion.vue')
const BulletinsGeneration = () => import('../views/bulletins/BulletinsGeneration.vue')
const ArchivesBulletins = () => import('../views/archives/ArchivesBulletins.vue')
const StatistiquesResultats = () => import('../views/statistiques/StatistiquesResultats.vue')

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard
  },
  // Module Finance
  {
    path: '/finance/encaissements',
    name: 'FinanceEncaissement',
    component: FinanceEncaissement
  },
  {
    path: '/finance/decaissements',
    name: 'FinanceDecaissement',
    component: FinanceDecaissement
  },
  {
    path: '/finance/synthese',
    name: 'FinanceSynthese',
    component: FinanceSynthese
  },
  // Module Matières et Examens
  {
    path: '/matieres',
    name: 'MatieresList',
    component: MatieresList
  },
  {
    path: '/examens',
    name: 'ExamensList',
    component: ExamensList
  },
  // Module Notes
  {
    path: '/notes',
    name: 'NotesGestion',
    component: NotesGestion
  },
  // Module Bulletins
  {
    path: '/bulletins',
    name: 'BulletinsGeneration',
    component: BulletinsGeneration
  },
  // Module Archives et Statistiques
  {
    path: '/archives',
    name: 'ArchivesBulletins',
    component: ArchivesBulletins
  },
  {
    path: '/statistiques',
    name: 'StatistiquesResultats',
    component: StatistiquesResultats
  },
  // Route par défaut en cas d'URL non trouvée
  {
    path: '*',
    redirect: '/'
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
