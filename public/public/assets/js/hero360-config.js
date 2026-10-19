/**
 * NOVAQYS Hero 360° Configuration
 * ================================
 * Modifiez les URLs et les informations des hotspots ici.
 * Ce fichier est le point central de configuration.
 */

const HERO360_CONFIG = {
  // Image panorama 360° (chemin relatif depuis public/)
  panorama: 'assets/img/novaqys-ecosystem-360.png',

  // Durée des animations GSAP (en secondes)
  duration: {
    enter: 1.2,
    hover: 0.4,
    click: 0.8,
    scroll: 1.0,
  },

  // Sensibilité du mouvement 360° (en pixels)
  sensitivity: {
    x: 40,
    y: 20,
  },

  // Zoom au scroll (1200px de scroll)
  scrollZoom: {
    start: 1.0,
    end: 1.15,
    threshold: 1200,
  },

  // Plateformes / Hotspots
  // Les coordonnées sont en pourcentage (0-100) par rapport au conteneur
  // Positionnez-les pour qu'elles correspondent aux éléments de votre image
  platforms: [
    {
      id: 'aqmi',
      name: 'AQMI',
      fullName: 'Automotive Quality & Manufacturing Index',
      description: 'Évaluez la maturité industrielle de votre organisation.',
      url: '#aqmi-starter',
      x: 50,
      y: 15,
      icon: '📊',
    },
    {
      id: 'nara',
      name: 'NARA',
      fullName: 'Novaqys Automotive Risk Assessment',
      description: 'Analysez et maîtrisez les risques de votre chaîne d\'approvisionnement.',
      url: '#nara',
      x: 25,
      y: 30,
      icon: '🛡️',
    },
    {
      id: 'novaqys',
      name: 'NOVAQYS',
      fullName: 'NOVAQYS Quality Management System',
      description: 'Plateforme de gestion de la qualité automobile nouvelle génération.',
      url: '#qms',
      x: 50,
      y: 50,
      icon: '🌐',
      isCenter: true,
    },
    {
      id: 'lms',
      name: 'LMS',
      fullName: 'Learning Management System',
      description: 'Formation et certification en management de la qualité.',
      url: '#learning',
      x: 75,
      y: 20,
      icon: '🎓',
    },
    {
      id: 'market',
      name: 'MARKET',
      fullName: 'Market Intelligence',
      description: 'Veille stratégique et benchmarking industriel.',
      url: '#ecosystem',
      x: 18,
      y: 55,
      icon: '📈',
    },
    {
      id: 'asin',
      name: 'ASIN',
      fullName: 'Advanced Supplier Intelligence Network',
      description: 'Réseau intelligent de surveillance fournisseurs.',
      url: '#asin',
      x: 75,
      y: 50,
      icon: '🔗',
    },
  ],

  // Icônes flottantes supplémentaires (bas de page)
  floatingIcons: [
    { icon: '⚙️', x: 10, y: 85, delay: 0 },
    { icon: '🔬', x: 25, y: 88, delay: 0.5 },
    { icon: '📡', x: 40, y: 83, delay: 1.0 },
    { icon: '💡', x: 60, y: 86, delay: 0.3 },
    { icon: '🔋', x: 75, y: 89, delay: 0.7 },
    { icon: '🧠', x: 90, y: 84, delay: 1.2 },
  ],
};