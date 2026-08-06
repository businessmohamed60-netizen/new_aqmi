# Procédure : Jauge de score dans le cercle gauche du questionnaire

## Objectif

Remplacer l'illustration décorative (flacon/particules) située à gauche du questionnaire par une **jauge circulaire de score** qui se remplit en temps réel, avec un **emoji au centre** exprimant le niveau de maturité.

---

## Étapes réalisées

### 1. HTML — Vue du questionnaire
**Fichier :** `resources/views/assessment/index.php`

- Remplacer le bloc `.aqmi-illustration-scene` par un nouveau conteneur `.aqmi-main-gauge`
- Inclure un `<svg>` avec deux cercles (fond + progression) et un dégradé linéaire
- Ajouter une zone centrale `.aqmi-main-gauge-center` contenant :
  - `.aqmi-main-gauge-emoji` — l'emoji qui change selon le score
  - `.aqmi-main-gauge-value` — le pourcentage animé
  - `.aqmi-main-gauge-label` — le label textuel (ex. « Avancé »)
- Ajouter `.aqmi-main-gauge-pulse` pour l'effet d'impulsion à chaque mise à jour

### 2. CSS — Styles de la jauge principale
**Fichier :** `public/css/aqmi-premium.css`

Ajouter après les styles `.aqmi-gauge-pulse` existants :

| Classe | Rôle |
|---|---|
| `.aqmi-main-gauge` | Conteneur positionné dans la zone d'illustration |
| `.aqmi-main-gauge svg` | SVG pivoté de -90° pour démarrer en haut |
| `.aqmi-main-gauge-bg` | Cercle de fond (gris clair) |
| `.aqmi-main-gauge-fg` | Cercle de progression (dégradé bleu→turquoise) |
| `.aqmi-main-gauge-center` | Zone centrée (flex column) pour emoji + valeur + label |
| `.aqmi-main-gauge-emoji` | Emoji 3.5rem avec animation `bounce` |
| `.aqmi-main-gauge-value` | Pourcentage 2.2rem, poids 800 |
| `.aqmi-main-gauge-label` | Label 0.7rem, majuscules, espacement des lettres |
| `.aqmi-main-gauge-pulse` | Anneau d'impulsion qui se déclenche à chaque mise à jour |

**Couleurs dynamiques selon le score :**
- ≥ 70% → turquoise `#2EC4B6`
- ≥ 40% → ambre `#C9A227`
- < 40% → rouge `#E5484D`

**Responsive :** masqué sur mobile (`max-width: 968px`)

### 3. JavaScript — Logique de mise à jour
**Fichier :** `public/js/aqmi-premium.js`

#### 3a. Constante
```js
var MAIN_GAUGE_CIRCUMFERENCE = 2 * Math.PI * 85; // r=85 pour le grand cercle
```

#### 3b. Références DOM
Ajouter dans l'objet `el` :
```js
mainGauge: $id('aqmiMainGauge'),
mainGaugeCircle: $id('aqmiMainGaugeCircle'),
mainGaugeValue: $id('aqmiMainGaugeValue'),
mainGaugeEmoji: $id('aqmiMainGaugeEmoji'),
mainGaugeLabel: $id('aqmiMainGaugeLabel'),
mainGaugePulse: $id('aqmiMainGaugePulse'),
```

#### 3c. Initialisation
Dans la fonction `init()`, après l'init de la petite jauge :
```js
el.mainGaugeCircle.style.strokeDasharray = String(MAIN_GAUGE_CIRCUMFERENCE);
el.mainGaugeCircle.style.strokeDashoffset = String(MAIN_GAUGE_CIRCUMFERENCE);
```

#### 3d. Fonction `updateGauge()`
Ajouter après la mise à jour de la petite jauge flottante :

1. **Anneau SVG** — animer `strokeDashoffset` avec GSAP (0.8s, `power2.out`)
2. **Couleur** — changer `stroke` selon le pourcentage
3. **Valeur** — animer le chiffre de 0→pct avec GSAP
4. **Emoji** — sélectionner selon les paliers :
   - ≥ 90% → 🏆 « Excellent »
   - ≥ 75% → 🌟 « Avancé »
   - ≥ 50% → 📈 « En progrès »
   - ≥ 25% → 🔧 « À améliorer »
   - < 25% → 🚀 « Décollage »
5. **Impulsion** — déclencher `.aqmi-main-gauge-pulse` à chaque mise à jour

#### 3e. Cas de réinitialisation
Quand `answeredCount === 0` :
- Remettre l'offset à `MAIN_GAUGE_CIRCUMFERENCE` (cercle vide)
- Afficher `0%`, emoji `🎯`, label « Démarrage »

---

## Résultat attendu

| État | Visuel |
|---|---|
| Avant de répondre | Cercle vide, 🎯, « Démarrage » |
| Score 0-24% | Anneau rouge, 🚀, « Décollage » |
| Score 25-49% | Anneau ambre, 🔧, « À améliorer » |
| Score 50-74% | Anneau ambre, 📈, « En progrès » |
| Score 75-89% | Anneau turquoise, 🌟, « Avancé » |
| Score 90-100% | Anneau turquoise, 🏆, « Excellent » |

La jauge flottante à droite reste active et synchronisée.

---

## Fichiers modifiés

1. `resources/views/assessment/index.php` — HTML de la jauge principale
2. `public/css/aqmi-premium.css` — styles `.aqmi-main-gauge-*`
3. `public/js/aqmi-premium.js` — logique de mise à jour (constante, DOM refs, init, `updateGauge`)
