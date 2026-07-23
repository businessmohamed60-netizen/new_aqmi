<?php $title = 'NOVAQYS - Building the Future of Automotive Manufacturing Excellence'; ob_start(); ?>

<!-- ═══════════════════════════════════════════════════════════════════
     HEADER / NAVIGATION
     ═══════════════════════════════════════════════════════════════════ -->
<header class="nav-header" id="navHeader">
  <a href="/" class="nav-brand">
    <img src="<?= asset('img/logo-novaqys-header.png') ?>" alt="NOVAQYS" width="54" height="36" style="height:36px;width:auto">
    <div>
      <div class="nav-brand-text">NOVAQYS</div>
      <span class="nav-brand-sub">Automotive Ecosystem</span>
    </div>
  </a>

  <div class="nav-links">
    <a href="#ecosystem" class="nav-link">Écosystème</a>
    <a href="#solutions" class="nav-link">Solutions</a>
    <a href="#aqmi" class="nav-link">AQMI</a>
    <a href="#asin" class="nav-link">ASIN</a>
    <a href="/lms.html" class="nav-link">Learning</a>
    <a href="#contact" class="nav-link">Contact</a>
  </div>

  <div class="nav-actions">
    <a href="/aqmi/login" class="btn-access">
      <i class="fas fa-lock"></i>
      Connexion AQMI
    </a>
  </div>

  <button class="nav-toggle" id="navToggle" aria-label="Menu">
    <i class="fas fa-bars"></i>
  </button>
</header>

<!-- Mobile menu -->
<div class="nav-mobile" id="navMobile">
  <a href="#ecosystem" onclick="closeMobile()"><i class="fas fa-cube"></i> Écosystème</a>
  <a href="#solutions" onclick="closeMobile()"><i class="fas fa-layer-group"></i> Solutions</a>
  <a href="#aqmi" onclick="closeMobile()"><i class="fas fa-chart-line"></i> AQMI</a>
  <a href="#asin" onclick="closeMobile()"><i class="fas fa-globe"></i> ASIN</a>
  <a href="/lms.html" onclick="closeMobile()"><i class="fas fa-graduation-cap"></i> Automotive Learning</a>
  <a href="#contact" onclick="closeMobile()"><i class="fas fa-envelope"></i> Contact</a>
  <div class="nav-mobile-divider"></div>
  <a href="/aqmi/login" class="btn-access" onclick="closeMobile()">
    <i class="fas fa-lock"></i> Connexion AQMI
  </a>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     HERO SECTION — 360° Ecosystem
     ═══════════════════════════════════════════════════════════════════ -->
<section class="hero" id="top">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow-1"></div>
  <div class="hero-glow-2"></div>
  <div class="hero-scanline"></div>

  <div class="hero-content">
    <div class="hero-badge">
      <span class="hero-badge-dot"></span>
      <span>Industry 4.0 · Automotive Manufacturing Ecosystem</span>
    </div>

    <h1 class="hero-title">
      NOVAQYS<br>
      <span class="hero-gradient">Building the Future of Automotive<br>Manufacturing Excellence</span>
    </h1>

    <p class="hero-subtitle">
      Développez un nouveau réseau de sous-traitance automobile performant grâce à un écosystème complet d'évaluation, de formation, de digitalisation et de mise en relation industrielle.
    </p>

    <div class="hero-ctas">
      <a href="#ecosystem">
        <button class="btn-primary">
          Découvrir l'écosystème
          <i class="fas fa-arrow-right"></i>
        </button>
      </a>
      <a href="#contact">
        <button class="btn-outline">
          <i class="fas fa-play"></i>
          Demander une démonstration
        </button>
      </a>
      <a href="/aqmi/login">
        <button class="btn-ghost">
          <i class="fas fa-lock"></i>
          Connexion AQMI
        </button>
      </a>
    </div>
  </div>

  <!-- 360° Ecosystem Image -->
  <div class="hero-360-wrap">
    <div class="hero-360-container">
      <img src="/assets/img/novaqys-ecosystem-360.webp"
           alt="NOVAQYS Ecosystem 360°"
           class="hero-360-image"
           loading="eager">

      <!-- Clickable overlay links — positioned directly on image modules -->
      <a href="#nara" class="hero-360-link hero-360-link-nara"
         data-platform="NARA"
         data-desc="Outil professionnel d'audit fournisseur. Réalisez des évaluations directement chez vos fournisseurs avec collecte de preuves et scoring automatique."
         data-color="#00cfe8">
        <i class="fas fa-search"></i>
        <span>NARA</span>
      </a>
      <a href="/aqmi-starter.html" class="hero-360-link hero-360-link-aqmi"
         data-platform="AQMI"
         data-desc="Évaluez gratuitement le niveau de maturité de votre entreprise. Détectez vos points forts et identifiez vos axes de progrès en quelques minutes."
         data-color="#7367f0">
        <i class="fas fa-clipboard-check"></i>
        <span>AQMI</span>
      </a>
      <a href="/lms.html" class="hero-360-link hero-360-link-lms"
         data-platform="NOVAQYS LMS"
         data-desc="Plateforme de formation dédiée aux normes et méthodes automobiles. Formez vos équipes aux standards IATF, Core Tools et Lean Manufacturing."
         data-color="#28c76f">
        <i class="fas fa-graduation-cap"></i>
        <span>LMS</span>
      </a>
      <a href="#qms" class="hero-360-link hero-360-link-qms"
         data-platform="NOVAQYS QMS"
         data-desc="Plateforme centrale de gestion de la production, qualité, maintenance et supply chain. Solution complète avec IA intégrée et BI temps réel."
         data-color="#9b8cf7">
        <i class="fas fa-industry"></i>
        <span>QMS</span>
      </a>
      <a href="#asin" class="hero-360-link hero-360-link-asin"
         data-platform="ASIN"
         data-desc="Marketplace B2B mondiale dédiée à la chaîne d'approvisionnement automobile. Connectez-vous aux constructeurs et développez votre réseau."
         data-color="#ea5455">
        <i class="fas fa-globe"></i>
        <span>ASIN</span>
      </a>
      <a href="#ecosystem" class="hero-360-link hero-360-link-label"
         data-platform="Supplier Excellence Label"
         data-desc="Certification de excellence fournisseur reconnue par les grands constructeurs automobiles. Valorisez votre niveau de maturité et votre conformité."
         data-color="#ff9f43">
        <i class="fas fa-certificate"></i>
        <span>Label</span>
      </a>
      <!-- Bottom row: 6 data modules -->
      <a href="#qms" class="hero-360-link hero-360-link-d1"
         data-platform="Données Centralisées"
         data-desc="Plateforme unique de centralisation de toutes vos données de production, qualité et supply chain. Vue à 360° de votre entreprise."
         data-color="#7367f0">
        <i class="fas fa-database"></i>
        <span>Données</span>
      </a>
      <a href="#qms" class="hero-360-link hero-360-link-d2"
         data-platform="Analytique Avancée"
         data-desc="Tableaux de bord interactifs et reporting automatisé. Pilotez votre performance avec des indicateurs temps réel et des analyses prédictives."
         data-color="#7367f0">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
      </a>
      <a href="#qms" class="hero-360-link hero-360-link-d3"
         data-platform="IA & Insights"
         data-desc="Intelligence artificielle embarquée pour la détection d'anomalies, l'analyse prédictive et les recommandations intelligentes."
         data-color="#7367f0">
        <i class="fas fa-brain"></i>
        <span>IA</span>
      </a>
      <a href="#qms" class="hero-360-link hero-360-link-d4"
         data-platform="Sécurité & Conformité"
         data-desc="Respect des normes IATF 16949, ISO 27001 et RGPD. Contrôle d'accès, chiffrement des données et audit de conformité automatisé."
         data-color="#7367f0">
        <i class="fas fa-shield-halved"></i>
        <span>Sécurité</span>
      </a>
      <a href="#qms" class="hero-360-link hero-360-link-d5"
         data-platform="Traçabilité Complète"
         data-desc="Traçabilité batch de la matière première au produit fini. QR Code, RFID et blockchain pour une transparence totale de votre supply chain."
         data-color="#7367f0">
        <i class="fas fa-qrcode"></i>
        <span>Traçabilité</span>
      </a>
      <a href="#qms" class="hero-360-link hero-360-link-d6"
         data-platform="Amélioration Continue"
         data-desc="Cycle PDCA intégré. CAPA, 8D, QRQC et Kaizen pour une amélioration continue pilotée par la data et l'intelligence collective."
         data-color="#7367f0">
        <i class="fas fa-arrows-rotate"></i>
        <span>Progrès</span>
      </a>

      <!-- Info panel (shown on hover) -->
      <div class="hero-360-panel" id="hero360Panel">
        <div class="hero-360-panel-icon" id="hero360PanelIcon">
          <i class="fas fa-cube"></i>
        </div>
        <div class="hero-360-panel-body">
          <div class="hero-360-panel-title" id="hero360PanelTitle">Plateforme</div>
          <div class="hero-360-panel-desc" id="hero360PanelDesc">Description</div>
        </div>
        <div class="hero-360-panel-arrow">
          <i class="fas fa-arrow-right"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <span>Découvrir</span>
    <div class="scroll-arrow"></div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     MARQUEE
     ═══════════════════════════════════════════════════════════════════ -->
<div class="marquee-section">
  <div class="marquee-fade-left"></div>
  <div class="marquee-fade-right"></div>
  <div class="marquee-track">
    <?php
    $marqueeItems = ['IATF 16949:2016','ISO 9001:2015','TISAX Ready','APQP / PPAP','8D · Ishikawa','SPC & Capabilité','OEE Temps Réel','CAPA','Supply Chain','FMEA / AMDEC','Industry 4.0','Lean Manufacturing','QRQC','Control Plan','MSA'];
    for ($r = 0; $r < 2; $r++):
      foreach ($marqueeItems as $item): ?>
        <span class="marquee-item">
          <span class="marquee-dot"></span>
          <?= $item ?>
        </span>
      <?php endforeach;
    endfor; ?>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     SLOGAN SECTION
     ═══════════════════════════════════════════════════════════════════ -->
<section class="slogan-section">
  <div class="slogan-content fade-up">
    <div class="slogan-quote">
      Créer un nouveau réseau de sous-traitance automobile<br>
      avec une parfaite maîtrise des coûts,<br>
      tout en maintenant <span class="slogan-highlight">la qualité</span>,<br>
      <span class="slogan-highlight">la compétitivité</span> et <span class="slogan-highlight">la notoriété</span> des fabricants.
    </div>
    <div class="slogan-attribution">
      <div class="slogan-line"></div>
      <span>NOVAQYS Ecosystem Vision</span>
      <div class="slogan-line"></div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     WHY NOVAQYS — Challenges
     ═══════════════════════════════════════════════════════════════════ -->
<section class="challenges-section" id="pourquoi">
  <div class="max-w-7xl px-6">
    <div class="section-header fade-up">
      <span class="section-label"><i class="fas fa-question-circle"></i> Pourquoi NOVAQYS ?</span>
      <h2 class="section-title">Les défis de l'industrie<br><span class="gradient-text">automobile aujourd'hui</span></h2>
      <p class="section-desc">L'industrie automobile fait face à des exigences croissantes. NOVAQYS vous accompagne pour transformer chaque défi en opportunité.</p>
    </div>

    <div class="challenges-grid stagger-children observe-once">
      <?php
      $challenges = [
        ['icon'=>'fa-globe','bg'=>'#7367f0','color'=>'#fff','title'=>'Concurrence mondiale','desc'=>'Face à une compétition internationale intense, les fabricants doivent constamment améliorer leur compétitivité tout en maîtrisant leurs coûts de production.'],
        ['icon'=>'fa-coins','bg'=>'#28c76f','color'=>'#fff','title'=>'Réduction des coûts','desc'=>'La pression sur les marges impose une optimisation continue des processus et une réduction des gaspillages sans compromis sur la qualité.'],
        ['icon'=>'fa-check-double','bg'=>'#9b8cf7','color'=>'#fff','title'=>'Qualité & Traçabilité','desc'=>'Les constructeurs exigent une traçabilité parfaite et une qualité irréprochable. Chaque défaut peut entraîner des pénalités et la perte de contrats.'],
        ['icon'=>'fa-laptop-code','bg'=>'#00cfe8','color'=>'#fff','title'=>'Digitalisation','desc'=>'La transformation digitale est un impératif. Les fabricants doivent adopter les outils numériques pour rester compétitifs et répondre aux attentes des OEM.'],
        ['icon'=>'fa-file-contract','bg'=>'#ff9f43','color'=>'#fff','title'=>'Nouvelles exigences','desc'=>'Les cahiers des charges des constructeurs évoluent constamment. Les fournisseurs doivent s\'adapter rapidement à des exigences toujours plus strictes.'],
        ['icon'=>'fa-shield-halved','bg'=>'#ea5455','color'=>'#fff','title'=>'Transition IATF 16949','desc'=>'La transition vers la norme IATF 16949 est complexe et coûteuse. Les PME ont besoin d\'un accompagnement structuré pour réussir cette certification.'],
        ['icon'=>'fa-robot','bg'=>'#7367f0','color'=>'#fff','title'=>'Industrie 4.0','desc'=>'L\'usine connectée, l\'IoT et l\'intelligence artificielle transforment les processus de fabrication. Les fabricants doivent intégrer ces technologies.'],
        ['icon'=>'fa-certificate','bg'=>'#9b8cf7','color'=>'#fff','title'=>'Conformité normative','desc'=>'Multiplicité des normes (IATF, ISO, TISAX, RGPD) à respecter simultanément, nécessitant un système de management intégré et performant.'],
      ];
      foreach ($challenges as $c): ?>
        <div class="challenge-card">
          <div class="challenge-icon" style="background:<?= $c['bg'] ?>20">
            <i class="fas <?= $c['icon'] ?>" style="color:<?= $c['bg'] ?>"></i>
          </div>
          <h3><?= $c['title'] ?></h3>
          <p><?= $c['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     ÉCOSYSTÈME NOVAQYS — Interactive Infographic
     ═══════════════════════════════════════════════════════════════════ -->
<section class="ecosystem-section" id="ecosystem">
  <div class="max-w-7xl px-6">
    <div class="section-header fade-up">
      <span class="section-label"><i class="fas fa-cubes"></i> L'Écosystème</span>
      <h2 class="section-title">L'écosystème complet<br><span class="gradient-text">NOVAQYS</span></h2>
      <p class="section-desc">Six plateformes interconnectées qui couvrent l'intégralité du cycle de développement d'un fabricant de pièces automobiles.</p>
    </div>

    <div class="ecosystem-grid stagger-children observe-once">
      <?php
      $ecosystems = [
        ['num'=>'01','icon'=>'fa-clipboard-check','bg'=>'#7367f0','title'=>'AQMI Starter','subtitle'=>'Automotive Quality & Manufacturing Index','desc'=>'Évaluez gratuitement le niveau de maturité de votre entreprise. Détectez vos points forts et identifiez vos axes de progrès en quelques minutes.','tags'=>['Auto-évaluation','Rapport','Feuille de route','Recommandations'],'color'=>'#7367f0','id'=>'aqmi-starter'],
        ['num'=>'02','icon'=>'fa-chart-simple','bg'=>'#9b8cf7','title'=>'AQMI Professional','subtitle'=>'Évaluation Approfondie','desc'=>'Évaluation détaillée avec analyse approfondie de votre système qualité. Plan de progrès personnalisé intégré directement dans l\'écosystème NOVAQYS.','tags'=>['Analyse détaillée','Plan de progrès','Scoring','Benchmark'],'color'=>'#9b8cf7','id'=>'aqmi-pro'],
        ['num'=>'03','icon'=>'fa-search','bg'=>'#00cfe8','title'=>'NARA','subtitle'=>'NOVAQYS Automotive Readiness Assessment','desc'=>'Outil professionnel d\'audit pour constructeurs et équipementiers. Réalisez des évaluations directement chez vos fournisseurs avec collecte de preuves.','tags'=>['Audit','Preuves','Scores','Tableaux de bord'],'color'=>'#00cfe8','id'=>'nara'],
        ['num'=>'04','icon'=>'fa-graduation-cap','bg'=>'#28c76f','title'=>'NOVAQYS LMS','subtitle'=>'Automotive Learning Platform','desc'=>'Plateforme de formation dédiée aux normes et méthodes automobiles. Formez vos équipes aux standards IATF, Core Tools et Lean Manufacturing.','tags'=>['IATF 16949','Core Tools','APQP/PPAP','FMEA','8D','QRQC'],'color'=>'#28c76f','id'=>'learning'],
        ['num'=>'05','icon'=>'fa-industry','bg'=>'#ff9f43','title'=>'NOVAQYS QMS','subtitle'=>'Automotive Manufacturing Management Platform','desc'=>'Plateforme centrale de gestion de la production, qualité, maintenance et supply chain. Solution complète avec IA intégrée et BI temps réel.','tags'=>['Production','Qualité','ERP/MES','OEE','BI','IA'],'color'=>'#ff9f43','id'=>'qms'],
        ['num'=>'06','icon'=>'fa-globe','bg'=>'#ea5455','title'=>'ASIN','subtitle'=>'Automotive Supplier Intelligence Network','desc'=>'Marketplace B2B mondiale dédiée à la chaîne d\'approvisionnement automobile. Le LinkedIn + Alibaba + ThomasNet de l\'industrie auto.','tags'=>['Marketplace','RFQ','Passeport Industriel','Recherche IA'],'color'=>'#ea5455','id'=>'asin'],
      ];
      foreach ($ecosystems as $e): ?>
        <div class="ecosystem-card">
          <span class="ecosystem-number"><?= $e['num'] ?></span>
          <div class="ecosystem-icon" style="background:<?= $e['color'] ?>20;color:<?= $e['color'] ?>">
            <i class="fas <?= $e['icon'] ?>"></i>
          </div>
          <h3><?= $e['title'] ?></h3>
          <div class="ecosystem-subtitle"><?= $e['subtitle'] ?></div>
          <p class="ecosystem-desc"><?= $e['desc'] ?></p>
          <div class="ecosystem-tags">
            <?php foreach ($e['tags'] as $tag): ?>
              <span class="ecosystem-tag"><?= $tag ?></span>
            <?php endforeach; ?>
          </div>
          <a href="<?= $e['id'] === 'learning' ? '/lms.html' : ($e['id'] === 'aqmi-starter' ? '/aqmi-starter.html' : '#'.$e['id']) ?>" class="ecosystem-btn">
            En savoir plus <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     AQMI STARTER
     ═══════════════════════════════════════════════════════════════════ -->
<section class="platform-section" id="aqmi-starter" style="background:var(--nova-bg-secondary)">
  <div class="max-w-7xl px-6">
    <div class="platform-layout">
      <div class="platform-content fade-up">
        <span class="platform-badge" style="background:rgba(115,103,240,0.1);border:1px solid rgba(115,103,240,0.15);color:#7367f0">
          <i class="fas fa-clipboard-check"></i> AQMI Starter
        </span>
        <h3 class="platform-title">Automotive Quality &<br><span style="color:#7367f0">Manufacturing Index</span></h3>
        <p class="platform-subtitle">Évaluez gratuitement votre niveau de maturité</p>
        <p class="platform-desc">AQMI Starter vous permet de détecter les bonnes pratiques déjà appliquées dans votre entreprise, même lorsqu'elles ne sont pas documentées. Les PME peuvent identifier leurs points forts et leurs axes de progrès en toute autonomie.</p>
        <div class="platform-list">
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#7367f0"><i class="fas fa-check"></i></div>Auto-évaluation gratuite de votre maturité qualité</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#7367f0"><i class="fas fa-check"></i></div>Rapport détaillé avec scoring par domaine</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#7367f0"><i class="fas fa-check"></i></div>Feuille de route personnalisée vers la conformité</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#7367f0"><i class="fas fa-check"></i></div>Recommandations actionnables immédiatement</div>
        </div>
        <a href="/aqmi/login" class="btn-primary">
          <i class="fas fa-lock"></i> Connexion AQMI
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="platform-visual fade-up">
        <div class="platform-visual-inner">
          <div class="platform-visual-grid">
            <div class="platform-visual-item"><i class="fas fa-file-alt" style="color:#7367f0"></i><span>Auto-évaluation</span></div>
            <div class="platform-visual-item"><i class="fas fa-chart-bar" style="color:#9b8cf7"></i><span>Rapport</span></div>
            <div class="platform-visual-item"><i class="fas fa-road" style="color:#28c76f"></i><span>Feuille de route</span></div>
            <div class="platform-visual-item"><i class="fas fa-tachometer-alt" style="color:#00cfe8"></i><span>Maturité</span></div>
            <div class="platform-visual-item"><i class="fas fa-lightbulb" style="color:#ff9f43"></i><span>Recommandations</span></div>
            <div class="platform-visual-item"><i class="fas fa-star" style="color:#ea5455"></i><span>Scoring</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     AQMI PRO
     ═══════════════════════════════════════════════════════════════════ -->
<section class="platform-section" id="aqmi-pro" style="background:var(--nova-bg)">
  <div class="max-w-7xl px-6">
    <div class="platform-layout" style="direction:rtl">
      <div class="platform-content fade-up" style="direction:ltr">
        <span class="platform-badge" style="background:rgba(115,103,240,0.1);border:1px solid rgba(115,103,240,0.15);color:#9b8cf7">
          <i class="fas fa-chart-simple"></i> AQMI Professional
        </span>
        <h3 class="platform-title">L'évolution naturelle<br><span style="color:#9b8cf7">de votre évaluation</span></h3>
        <p class="platform-subtitle">Évaluation approfondie · Analyse détaillée</p>
        <p class="platform-desc">AQMI Professional est l'évolution naturelle d'AQMI Starter. Après l'auto-évaluation, bénéficiez d'une analyse approfondie avec un plan de progrès détaillé, intégré directement dans l'écosystème NOVAQYS.</p>
        <div class="platform-list">
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#9b8cf7"><i class="fas fa-check"></i></div>Évaluation détaillée multicritères</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#9b8cf7"><i class="fas fa-check"></i></div>Analyse des écarts avec benchmark sectoriel</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#9b8cf7"><i class="fas fa-check"></i></div>Plan de progrès personnalisé et piloté</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(115,103,240,0.15);color:#9b8cf7"><i class="fas fa-check"></i></div>Intégration directe dans NOVAQYS QMS</div>
        </div>
      </div>

      <div class="platform-visual fade-up" style="direction:ltr">
        <div class="platform-visual-inner">
          <div class="platform-visual-grid">
            <div class="platform-visual-item"><i class="fas fa-search-plus" style="color:#9b8cf7"></i><span>Analyse</span></div>
            <div class="platform-visual-item"><i class="fas fa-balance-scale" style="color:#7367f0"></i><span>Écarts</span></div>
            <div class="platform-visual-item"><i class="fas fa-tasks" style="color:#28c76f"></i><span>Plan</span></div>
            <div class="platform-visual-item"><i class="fas fa-chart-line" style="color:#00cfe8"></i><span>Progrès</span></div>
            <div class="platform-visual-item"><i class="fas fa-database" style="color:#ff9f43"></i><span>Intégration</span></div>
            <div class="platform-visual-item"><i class="fas fa-crown" style="color:#ea5455"></i><span>Premium</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     NARA
     ═══════════════════════════════════════════════════════════════════ -->
<section class="platform-section" id="nara" style="background:var(--nova-bg-secondary)">
  <div class="max-w-7xl px-6">
    <div class="platform-layout">
      <div class="platform-content fade-up">
        <span class="platform-badge" style="background:rgba(0,207,232,0.1);border:1px solid rgba(0,207,232,0.15);color:#00cfe8">
          <i class="fas fa-search"></i> NARA
        </span>
        <h3 class="platform-title">NOVAQYS Automotive<br><span style="color:#00cfe8">Readiness Assessment</span></h3>
        <p class="platform-subtitle">Outil professionnel d'audit fournisseur</p>
        <p class="platform-desc">NARA permet aux constructeurs, équipementiers et auditeurs de réaliser des évaluations directement chez les fournisseurs. Collectez des preuves, générez des scores et pilotez la performance de votre supply chain.</p>
        <div class="platform-list">
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(0,207,232,0.15);color:#00cfe8"><i class="fas fa-check"></i></div>Réalisation d'audits chez les fournisseurs</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(0,207,232,0.15);color:#00cfe8"><i class="fas fa-check"></i></div>Collecte et gestion des preuves d'audit</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(0,207,232,0.15);color:#00cfe8"><i class="fas fa-check"></i></div>Rapports détaillés avec scoring automatique</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(0,207,232,0.15);color:#00cfe8"><i class="fas fa-check"></i></div>Tableaux de bord consolidés multi-fournisseurs</div>
        </div>
      </div>

      <div class="platform-visual fade-up">
        <div class="platform-visual-inner">
          <div class="platform-visual-grid">
            <div class="platform-visual-item"><i class="fas fa-clipboard" style="color:#00cfe8"></i><span>Audit</span></div>
            <div class="platform-visual-item"><i class="fas fa-camera" style="color:#7367f0"></i><span>Preuves</span></div>
            <div class="platform-visual-item"><i class="fas fa-file-pdf" style="color:#9b8cf7"></i><span>Rapports</span></div>
            <div class="platform-visual-item"><i class="fas fa-tachometer-alt" style="color:#28c76f"></i><span>Scores</span></div>
            <div class="platform-visual-item"><i class="fas fa-columns" style="color:#ff9f43"></i><span>Dashboard</span></div>
            <div class="platform-visual-item"><i class="fas fa-sync" style="color:#ea5455"></i><span>Suivi</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     NOVAQYS LMS
     ═══════════════════════════════════════════════════════════════════ -->
<section class="platform-section" id="learning" style="background:var(--nova-bg)">
  <div class="max-w-7xl px-6">
    <div class="platform-layout" style="direction:rtl">
      <div class="platform-content fade-up" style="direction:ltr">
        <span class="platform-badge" style="background:rgba(40,199,111,0.1);border:1px solid rgba(40,199,111,0.15);color:#28c76f">
          <i class="fas fa-graduation-cap"></i> NOVAQYS LMS
        </span>
        <h3 class="platform-title">Automotive Learning<br><span style="color:#28c76f">Platform</span></h3>
        <p class="platform-subtitle">Formation aux normes et méthodes automobiles</p>
        <p class="platform-desc">Formez vos équipes aux standards exigeants de l'industrie automobile. Vidéos, quiz et certifications couvrant l'intégralité des référentiels IATF 16949 et des Core Tools.</p>
        <div class="platform-list">
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(40,199,111,0.15);color:#28c76f"><i class="fas fa-check"></i></div>Normes : IATF 16949, ISO 9001, VDA</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(40,199,111,0.15);color:#28c76f"><i class="fas fa-check"></i></div>Core Tools : APQP, PPAP, FMEA, SPC, MSA</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(40,199,111,0.15);color:#28c76f"><i class="fas fa-check"></i></div>Méthodes : 8D, QRQC, Lean Manufacturing</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(40,199,111,0.15);color:#28c76f"><i class="fas fa-check"></i></div>Vidéos interactives, quiz et certification</div>
        </div>
        <a href="/lms.html" class="platform-btn" style="display:inline-flex;align-items:center;gap:10px;margin-top:20px;padding:14px 28px;background:linear-gradient(135deg,#28c76f,#1f9d57);color:#fff;border-radius:12px;font-weight:600;text-decoration:none;transition:all 0.3s ease;font-size:0.95rem">
          Découvrir la plateforme LMS <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="platform-visual fade-up" style="direction:ltr">
        <div class="platform-visual-inner">
          <div class="platform-visual-grid">
            <div class="platform-visual-item"><i class="fas fa-video" style="color:#28c76f"></i><span>Vidéos</span></div>
            <div class="platform-visual-item"><i class="fas fa-question-circle" style="color:#7367f0"></i><span>Quiz</span></div>
            <div class="platform-visual-item"><i class="fas fa-certificate" style="color:#9b8cf7"></i><span>Certification</span></div>
            <div class="platform-visual-item"><i class="fas fa-book" style="color:#00cfe8"></i><span>Modules</span></div>
            <div class="platform-visual-item"><i class="fas fa-chart-pie" style="color:#ff9f43"></i><span>Progression</span></div>
            <div class="platform-visual-item"><i class="fas fa-users" style="color:#ea5455"></i><span>Groupes</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     NOVAQYS QMS
     ═══════════════════════════════════════════════════════════════════ -->
<section class="platform-section" id="qms" style="background:var(--nova-bg-secondary)">
  <div class="max-w-7xl px-6">
    <div class="section-header fade-up">
      <span class="section-label"><i class="fas fa-industry"></i> Plateforme Centrale</span>
      <h2 class="section-title">NOVAQYS Automotive<br><span class="gradient-text">Manufacturing Management Platform</span></h2>
      <p class="section-desc">La plateforme centrale de gestion de votre entreprise. Modules interconnectés couvrant tous les aspects de votre production.</p>
    </div>

    <div class="ecosystem-grid stagger-children observe-once">
      <?php
      $qmsModules = [
        ['icon'=>'fa-industry','color'=>'#7367f0','title'=>'Production & MES','desc'=>'Gestion des ordres de fabrication, suivi shop floor et OEE temps réel.'],
        ['icon'=>'fa-shield-halved','color'=>'#28c76f','title'=>'Qualité','desc'=>'CAPA, FMEA, SPC, plan de surveillance. Contrôle qualité intégral.'],
        ['icon'=>'fa-wrench','color'=>'#ff9f43','title'=>'Maintenance','desc'=>'GMAO préventive et curative. Historique des interventions et alertes.'],
        ['icon'=>'fa-truck','color'=>'#9b8cf7','title'=>'Supply Chain','desc'=>'Gestion des stocks, achats et logistique. Approvisionnements optimisés.'],
        ['icon'=>'fa-laptop','color'=>'#00cfe8','title'=>'ERP Intégré','desc'=>'Gestion complète des ressources : RH, ventes, finances et planification.'],
        ['icon'=>'fa-microchip','color'=>'#ea5455','title'=>'MES & Traçabilité','desc'=>'Manufacturing Execution System avec traçabilité complète batch.'],
        ['icon'=>'fa-chart-line','color'=>'#7367f0','title'=>'OEE & Performance','desc'=>'Taux de rendement synthétique, indicateurs de performance en temps réel.'],
        ['icon'=>'fa-chart-bar','color'=>'#28c76f','title'=>'SPC & FMEA','desc'=>'Maîtrise statistique des processus et analyse des modes de défaillance.'],
        ['icon'=>'fa-code-branch','color'=>'#9b8cf7','title'=>'APQP & PPAP','desc'=>'Gestion de projets, phases APQP et livrables PPAP.'],
        ['icon'=>'fa-file-alt','color'=>'#00cfe8','title'=>'GED','desc'=>'Gestion documentaire. Procédures, processus et manuel qualité.'],
        ['icon'=>'fa-bullseye','color'=>'#ff9f43','title'=>'CAPA & Audits','desc'=>'Actions correctives, audit interne et revue de direction.'],
        ['icon'=>'fa-calendar','color'=>'#ea5455','title'=>'Planification','desc'=>'Planification de production, ordonnancement et gestion des capacités.'],
        ['icon'=>'fa-boxes','color'=>'#7367f0','title'=>'Stocks & Achats','desc'=>'Gestion des inventaires, approvisionnements et réapprovisionnement.'],
        ['icon'=>'fa-shopping-cart','color'=>'#28c76f','title'=>'Ventes & CRM','desc'=>'Gestion des clients, devis, commandes et facturation.'],
        ['icon'=>'fa-users','color'=>'#9b8cf7','title'=>'RH & Compétences','desc'=>'Matrice de compétences, plan de formation et habilitations.'],
        ['icon'=>'fa-chart-pie','color'=>'#00cfe8','title'=>'BI & Reporting','desc'=>'Business Intelligence, tableaux de bord et reporting automatisé.'],
        ['icon'=>'fa-brain','color'=>'#ff9f43','title'=>'Intelligence Artificielle','desc'=>'Analyse prédictive, détection d\'anomalies et recommandations IA.'],
        ['icon'=>'fa-database','color'=>'#ea5455','title'=>'Digitalisation','desc'=>'Transformation numérique complète de vos processus industriels.'],
      ];
      foreach ($qmsModules as $m): ?>
        <div class="ecosystem-card">
          <div class="ecosystem-icon" style="background:<?= $m['color'] ?>15;color:<?= $m['color'] ?>">
            <i class="fas <?= $m['icon'] ?>"></i>
          </div>
          <h3><?= $m['title'] ?></h3>
          <p class="ecosystem-desc"><?= $m['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     ASIN — Automotive Supplier Intelligence Network
     ═══════════════════════════════════════════════════════════════════ -->
<section class="platform-section" id="asin" style="background:var(--nova-bg)">
  <div class="max-w-7xl px-6">
    <div class="platform-layout">
      <div class="platform-content fade-up">
        <span class="platform-badge" style="background:rgba(234,84,85,0.1);border:1px solid rgba(234,84,85,0.15);color:#ea5455">
          <i class="fas fa-globe"></i> ASIN
        </span>
        <h3 class="platform-title">Automotive Supplier<br><span style="color:#ea5455">Intelligence Network</span></h3>
        <p class="platform-subtitle">LinkedIn + Alibaba + ThomasNet de l'industrie automobile</p>
        <p class="platform-desc">ASIN est la marketplace B2B mondiale dédiée à la chaîne d'approvisionnement automobile. Connectez-vous aux constructeurs, publiez votre offre et développez votre réseau de sous-traitance international.</p>
        <div class="platform-list">
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(234,84,85,0.15);color:#ea5455"><i class="fas fa-check"></i></div>Passeport Industriel intelligent avec scoring AQMI</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(234,84,85,0.15);color:#ea5455"><i class="fas fa-check"></i></div>Marketplace produits, machines et matières premières</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(234,84,85,0.15);color:#ea5455"><i class="fas fa-check"></i></div>Système de RFQ et messagerie intégrée</div>
          <div class="platform-list-item"><div class="pl-icon" style="background:rgba(234,84,85,0.15);color:#ea5455"><i class="fas fa-check"></i></div>Recherche intelligente par procédés et capacités</div>
        </div>
      </div>

      <div class="platform-visual fade-up">
        <div class="platform-visual-inner">
          <div class="platform-visual-grid">
            <div class="platform-visual-item"><i class="fas fa-address-card" style="color:#ea5455"></i><span>Passeport</span></div>
            <div class="platform-visual-item"><i class="fas fa-store" style="color:#7367f0"></i><span>Marketplace</span></div>
            <div class="platform-visual-item"><i class="fas fa-file-invoice" style="color:#9b8cf7"></i><span>RFQ</span></div>
            <div class="platform-visual-item"><i class="fas fa-comments" style="color:#28c76f"></i><span>Messagerie</span></div>
            <div class="platform-visual-item"><i class="fas fa-search" style="color:#00cfe8"></i><span>Recherche IA</span></div>
            <div class="platform-visual-item"><i class="fas fa-handshake" style="color:#ff9f43"></i><span>Mise en relation</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     JOURNEY — Parcours interactif d'une entreprise
     Premium scroll-driven experience
     ═══════════════════════════════════════════════════════════════════ -->
<section class="journey-section" id="solutions">
  <div class="journey-header">
    <span class="section-label"><i class="fas fa-road"></i> Le Parcours</span>
    <h2>Le parcours d'une entreprise<br><span class="gradient-text">dans l'écosystème NOVAQYS</span></h2>
    <p>Du diagnostic initial à l'intégration dans la supply chain mondiale, chaque étape est structurée pour garantir votre succès.</p>
  </div>

  <div class="journey-container">
    <!-- Glowing Path -->
    <div class="journey-path">
      <div class="journey-path-track"></div>
      <div class="journey-path-fill" style="height:0%"></div>
      <div class="journey-path-glow"></div>
    </div>

    <?php
    $steps = [
      [
        'num' => '01', 'id' => 'decouverte',
        'color' => '#7367f0', 'icon' => 'fa-compass',
        'title' => 'Découverte',
        'desc' => 'L\'entreprise découvre l\'écosystème NOVAQYS et évalue sa maturité avec AQMI Starter. Une première vision claire de son potentiel.',
        'btn' => 'Découvrir AQMI', 'url' => '/aqmi/register',
        'extra' => ''
      ],
      [
        'num' => '02', 'id' => 'aqmi-starter',
        'color' => '#9b8cf7', 'icon' => 'fa-clipboard-check',
        'title' => 'AQMI Starter',
        'desc' => 'Auto-évaluation gratuite. L\'entreprise identifie ses forces et ses axes de progrès avec un premier score de maturité.',
        'btn' => 'Évaluer gratuitement', 'url' => '/assessment/start',
        'extra' => '<div class="journey-step-anim"><div class="journey-gauge"><div class="journey-gauge-fill" style="background:linear-gradient(90deg,#9b8cf7,#7367f0)" data-value="52%"></div></div><div class="journey-gauge-label"><span>Score de maturité</span><span style="color:#9b8cf7;font-weight:700">52%</span></div></div>'
      ],
      [
        'num' => '03', 'id' => 'formation-lms',
        'color' => '#28c76f', 'icon' => 'fa-graduation-cap',
        'title' => 'Formation LMS',
        'desc' => 'Les équipes sont formées aux normes IATF 16949, aux méthodes Lean et aux standards qualité automobile via notre plateforme LMS.',
        'btn' => 'Découvrir LMS', 'url' => '/lms',
        'extra' => ''
      ],
      [
        'num' => '04', 'id' => 'digitalisation-qms',
        'color' => '#ff9f43', 'icon' => 'fa-industry',
        'title' => 'Digitalisation QMS',
        'desc' => 'L\'entreprise déploie NOVAQYS QMS pour structurer et digitaliser ses processus qualité, production et supply chain.',
        'btn' => 'Découvrir NOVAQYS', 'url' => '/novaqys-qms',
        'extra' => ''
      ],
      [
        'num' => '05', 'id' => 'aqmi-professional',
        'color' => '#00cfe8', 'icon' => 'fa-chart-simple',
        'title' => 'AQMI Professional',
        'desc' => 'Évaluation approfondie avec analyse détaillée, plan de progrès personnalisé et suivi de la progression.',
        'btn' => 'Passer à l\'étape supérieure', 'url' => '/assessment/start',
        'extra' => '<div class="journey-step-anim"><div class="journey-gauge"><div class="journey-gauge-fill" style="background:linear-gradient(90deg,#00cfe8,#7367f0)" data-value="95%"></div></div><div class="journey-gauge-label"><span>Progression du score</span><span style="color:#00cfe8;font-weight:700">95%</span></div><div class="journey-score-row"><span class="journey-score-badge" style="background:rgba(0,207,232,0.12);color:#00cfe8;border:1px solid rgba(0,207,232,0.2)">52%</span><span style="color:var(--nova-text-tertiary);font-size:0.75rem">&rarr;</span><span class="journey-score-badge" style="background:rgba(115,103,240,0.12);color:#7367f0;border:1px solid rgba(115,103,240,0.2)">78%</span><span style="color:var(--nova-text-tertiary);font-size:0.75rem">&rarr;</span><span class="journey-score-badge" style="background:rgba(40,199,111,0.12);color:#28c76f;border:1px solid rgba(40,199,111,0.2)">95%</span></div></div>'
      ],
      [
        'num' => '06', 'id' => 'nara',
        'color' => '#ea5455', 'icon' => 'fa-search',
        'title' => 'Évaluation NARA',
        'desc' => 'Audit professionnel par un constructeur ou un équipementier via NARA. Validation officielle et rapport détaillé.',
        'btn' => 'Découvrir NARA', 'url' => '/nara',
        'extra' => ''
      ],
      [
        'num' => '07', 'id' => 'asin',
        'color' => '#7367f0', 'icon' => 'fa-globe',
        'title' => 'Visibilité ASIN',
        'desc' => 'L\'entreprise rejoint ASIN et publie son passeport industriel international. Visibilité mondiale auprès des donneurs d\'ordre.',
        'btn' => 'Découvrir ASIN', 'url' => '/asin',
        'extra' => ''
      ],
      [
        'num' => '08', 'id' => 'acces-constructeurs',
        'color' => '#9b8cf7', 'icon' => 'fa-handshake',
        'title' => 'Accès constructeurs',
        'desc' => 'Mise en relation directe avec les acheteurs des plus grands constructeurs automobiles mondiaux.',
        'btn' => 'Voir les opportunités', 'url' => '/asin',
        'extra' => '<div class="journey-step-anim"><div class="journey-logos"><span class="journey-logo-item">Stellantis</span><span class="journey-logo-item">Renault</span><span class="journey-logo-item">Volkswagen</span><span class="journey-logo-item">Toyota</span><span class="journey-logo-item">BMW</span><span class="journey-logo-item">Hyundai</span></div></div>'
      ],
      [
        'num' => '09', 'id' => 'developpement',
        'color' => '#28c76f', 'icon' => 'fa-rocket',
        'title' => 'Développement',
        'desc' => 'Nouveaux contrats, croissance internationale, développement commercial accéléré. L\'entreprise devient un fournisseur de rang mondial.',
        'btn' => 'Commencer maintenant', 'url' => '/contact',
        'extra' => '<div class="journey-step-anim"><div class="journey-final-scene"><svg viewBox="0 0 400 120" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="140" y="50" width="120" height="60" rx="4" fill="rgba(40,199,111,0.08)" stroke="rgba(40,199,111,0.2)" stroke-width="0.5"/><rect x="155" y="60" width="20" height="15" rx="2" fill="rgba(40,199,111,0.15)"/><rect x="180" y="60" width="20" height="15" rx="2" fill="rgba(40,199,111,0.15)"/><rect x="205" y="60" width="20" height="15" rx="2" fill="rgba(40,199,111,0.15)"/><rect x="155" y="80" width="20" height="15" rx="2" fill="rgba(40,199,111,0.15)"/><rect x="180" y="80" width="20" height="15" rx="2" fill="rgba(40,199,111,0.15)"/><rect x="205" y="80" width="20" height="15" rx="2" fill="rgba(40,199,111,0.15)"/><circle cx="80" cy="80" r="18" fill="rgba(115,103,240,0.08)" stroke="rgba(115,103,240,0.2)" stroke-width="0.5"/><circle cx="80" cy="80" r="8" fill="rgba(115,103,240,0.15)"/><circle cx="320" cy="80" r="18" fill="rgba(0,207,232,0.08)" stroke="rgba(0,207,232,0.2)" stroke-width="0.5"/><circle cx="320" cy="80" r="8" fill="rgba(0,207,232,0.15)"/><line x1="98" y1="80" x2="140" y2="80" stroke="rgba(115,103,240,0.2)" stroke-width="0.5" stroke-dasharray="3 3"/><line x1="260" y1="80" x2="302" y2="80" stroke="rgba(0,207,232,0.2)" stroke-width="0.5" stroke-dasharray="3 3"/><rect x="130" y="20" width="140" height="25" rx="4" fill="rgba(255,159,67,0.06)" stroke="rgba(255,159,67,0.15)" stroke-width="0.5"/><rect x="140" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><rect x="156" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><rect x="172" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><rect x="192" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><rect x="208" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><rect x="224" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><rect x="240" y="27" width="12" height="4" rx="1" fill="rgba(255,159,67,0.2)"/><text x="200" y="16" text-anchor="middle" fill="rgba(255,255,255,0.3)" font-size="6">Usine moderne</text><line x1="200" y1="45" x2="200" y2="50" stroke="rgba(255,159,67,0.15)" stroke-width="0.5"/></svg></div></div>'
      ],
    ];
    foreach ($steps as $s): ?>
      <div class="journey-step" data-step="<?= $s['num'] ?>" data-color="<?= $s['color'] ?>" data-id="<?= $s['id'] ?>">
        <div class="journey-step-marker">
          <div class="journey-step-dot" style="border-color:<?= $s['color'] ?>;color:<?= $s['color'] ?>">
            <div class="journey-step-dot-inner" style="background:<?= $s['color'] ?>"></div>
          </div>
          <div class="journey-step-dot-glow" style="background:radial-gradient(circle,<?= $s['color'] ?>66,transparent 70%)"></div>
        </div>
        <div class="journey-step-card">
          <div class="journey-step-number" style="color:<?= $s['color'] ?>">Étape <?= $s['num'] ?></div>
          <div class="journey-step-icon-wrap" style="color:<?= $s['color'] ?>;background:<?= $s['color'] ?>1a">
            <i class="fas <?= $s['icon'] ?>"></i>
          </div>
          <h3><?= $s['title'] ?></h3>
          <p><?= $s['desc'] ?></p>
          <?= $s['extra'] ?>
          <a href="<?= $s['url'] ?>" class="journey-step-btn"<?= $s['extra'] ? ' style="margin-top:1rem"' : '' ?>>
            <?= $s['btn'] ?> <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     BENEFITS
     ═══════════════════════════════════════════════════════════════════ -->
<section class="benefits-section">
  <div class="max-w-7xl px-6">
    <div class="section-header fade-up">
      <span class="section-label"><i class="fas fa-star"></i> Bénéfices</span>
      <h2 class="section-title">Les bénéfices de<br><span class="gradient-text">l'écosystème NOVAQYS</span></h2>
      <p class="section-desc">Une transformation complète pour faire de votre entreprise un acteur incontournable de la supply chain automobile mondiale.</p>
    </div>

    <div class="benefits-grid stagger-children observe-once">
      <?php
      $benefits = [
        ['icon'=>'fa-coins','bg'=>'#28c76f','title'=>'Réduction des coûts','desc'=>'Optimisez vos processus et réduisez vos coûts de production jusqu\'à 30% grâce à la digitalisation et l\'élimination des gaspillages.'],
        ['icon'=>'fa-check-circle','bg'=>'#7367f0','title'=>'Amélioration qualité','desc'=>'Atteignez un niveau de qualité conforme aux exigences des plus grands constructeurs avec un système QMS performant.'],
        ['icon'=>'fa-shield-halved','bg'=>'#9b8cf7','title'=>'Conformité IATF','desc'=>'Maîtrisez les exigences IATF 16949 et obtenez votre certification avec un accompagnement structuré et progressif.'],
        ['icon'=>'fa-graduation-cap','bg'=>'#00cfe8','title'=>'Montée en compétences','desc'=>'Formez vos équipes aux standards automobiles internationaux grâce à notre plateforme LMS spécialisée.'],
        ['icon'=>'fa-laptop-code','bg'=>'#ff9f43','title'=>'Transformation digitale','desc'=>'Digitalisez l\'intégralité de vos processus qualité, production et supply chain avec une plateforme unifiée.'],
        ['icon'=>'fa-globe-americas','bg'=>'#ea5455','title'=>'Visibilité internationale','desc'=>'Accédez au marché mondial de la sous-traitance automobile via ASIN, la marketplace B2B dédiée.'],
        ['icon'=>'fa-project-diagram','bg'=>'#7367f0','title'=>'Nouveau réseau','desc'=>'Intégrez un nouveau réseau de sous-traitance performant et connectez-vous directement aux donneurs d\'ordre.'],
        ['icon'=>'fa-trophy','bg'=>'#28c76f','title'=>'Compétitivité','desc'=>'Renforcez votre position concurrentielle grâce à une meilleure efficacité opérationnelle et une visibilité accrue.'],
      ];
      foreach ($benefits as $b): ?>
        <div class="benefit-card">
          <div class="benefit-icon" style="background:<?= $b['bg'] ?>15;color:<?= $b['bg'] ?>">
            <i class="fas <?= $b['icon'] ?>"></i>
          </div>
          <h3><?= $b['title'] ?></h3>
          <p><?= $b['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     RAPPORT AQMI EXEMPLE — Modernisé
     ═══════════════════════════════════════════════════════════════════ -->
<section class="report-example-section">
  <div class="report-example-orb report-example-orb-1"></div>
  <div class="report-example-orb report-example-orb-2"></div>
  <div class="report-example-dots"></div>
  <div class="max-w-7xl px-6">
    <div class="section-header">
      <span class="section-label"><i class="fas fa-file-pdf"></i> Rapport AQMI</span>
      <h2 class="section-title">Exemple de rapport<br><span class="gradient-text">d'évaluation AQMI</span></h2>
      <p class="section-desc">Découvrez un aperçu du rapport détaillé que vous recevrez après votre évaluation. Score global, analyse par domaine, forces, faiblesses et recommandations personnalisées.</p>
    </div>
    <div class="report-example-layout">
      <div class="report-example-preview">
        <div class="report-example-preview-glow"></div>
        <div class="report-example-preview-frame">
          <a href="<?= asset('pdf/rapport-aqmi-exemple.png') ?>" target="_blank" class="report-example-image-link">
            <img src="<?= asset('pdf/rapport-aqmi-exemple.png') ?>" alt="Aperçu du rapport AQMI" class="report-example-image" loading="lazy">
            <div class="report-example-overlay">
              <i class="fas fa-search-plus"></i>
              <span>Cliquez pour agrandir</span>
            </div>
          </a>
          <div class="report-example-frame-badge">
            <i class="fas fa-file-pdf"></i> PDF
          </div>
        </div>
      </div>
      <div class="report-example-info">
        <div class="report-example-badge">
          <i class="fas fa-star"></i> Rapport officiel
        </div>
        <h3 class="report-example-title">Ce que contient le rapport</h3>
        <ul class="report-example-list">
          <li><span class="report-example-list-icon" style="background:rgba(40,199,111,0.15);color:#28c76f"><i class="fas fa-chart-line"></i></span> Score global de maturité</li>
          <li><span class="report-example-list-icon" style="background:rgba(115,103,240,0.15);color:#9b8cf7"><i class="fas fa-layer-group"></i></span> Analyse détaillée par domaine</li>
          <li><span class="report-example-list-icon" style="background:rgba(0,207,232,0.15);color:#00cfe8"><i class="fas fa-bolt"></i></span> Forces et axes d'amélioration</li>
          <li><span class="report-example-list-icon" style="background:rgba(255,159,67,0.15);color:#ff9f43"><i class="fas fa-chart-bar"></i></span> Benchmark sectoriel</li>
          <li><span class="report-example-list-icon" style="background:rgba(40,199,111,0.15);color:#28c76f"><i class="fas fa-lightbulb"></i></span> Recommandations personnalisées</li>
          <li><span class="report-example-list-icon" style="background:rgba(234,84,85,0.15);color:#ea5455"><i class="fas fa-flag"></i></span> Plan d'action prioritaire</li>
        </ul>
        <div class="report-example-actions">
          <a href="<?= asset('pdf/rapport-aqmi-exemple.png') ?>" download class="report-example-btn">
            <i class="fas fa-download"></i> Télécharger l'exemple
          </a>
          <a href="/assessment/start" class="report-example-btn-outline">
            <i class="fas fa-clipboard-check"></i> Faire mon évaluation
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     SUCCESS STORY AQMI
     ═══════════════════════════════════════════════════════════════════ -->
<section class="success-section">
  <div class="success-orb success-orb-1"></div>
  <div class="success-orb success-orb-2"></div>
  <div class="max-w-7xl px-6">
    <div class="section-header">
      <span class="section-label"><i class="fas fa-trophy"></i> Success Story</span>
      <h2 class="section-title">Ils ont réussi leur<br><span class="gradient-text">transformation AQMI</span></h2>
      <p class="section-desc">Découvrez comment les entreprises du secteur automobile atteignent l'excellence opérationnelle grâce à l'écosystème NOVAQYS.</p>
    </div>
    <div class="success-layout">
      <div class="success-card">
        <div class="success-card-glow"></div>
        <div class="success-card-inner">
          <div class="success-image-wrap">
            <img src="<?= asset('img/success-aqmi.png') ?>" alt="Success Story AQMI" class="success-image" loading="lazy">
            <div class="success-score-badge">
              <span class="success-score-value">87%</span>
              <span class="success-score-label">Score AQMI</span>
            </div>
          </div>
          <div class="success-content">
            <div class="success-badge">
              <i class="fas fa-medal"></i> Excellence
            </div>
            <h3 class="success-title">Leader Industriel Certifié</h3>
            <p class="success-desc">Cette entreprise a atteint un score de maturité AQMI de 87%, démontrant une maîtrise exceptionnelle des processus qualité et une capacité d'innovation remarquable dans le secteur automobile.</p>
            <div class="success-stats">
              <div class="success-stat">
                <span class="success-stat-value">87%</span>
                <span class="success-stat-label">Score Global</span>
              </div>
              <div class="success-stat-divider"></div>
              <div class="success-stat">
                <span class="success-stat-value">6/6</span>
                <span class="success-stat-label">Domaines maîtrisés</span>
              </div>
              <div class="success-stat-divider"></div>
              <div class="success-stat">
                <span class="success-stat-value">Top 5%</span>
                <span class="success-stat-label">Benchmark sectoriel</span>
              </div>
            </div>
            <a href="/assessment/start" class="success-btn">
              <i class="fas fa-arrow-right"></i> Obtenez votre score AQMI
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     CTA
     ═══════════════════════════════════════════════════════════════════ -->
<section class="cta-section">
  <div class="cta-rings">
    <div class="cta-ring"></div>
    <div class="cta-ring"></div>
  </div>

  <div class="cta-content fade-up">
    <h2 class="cta-title">
      Prêt à transformer<br>votre <span class="gradient-text">entreprise</span> ?
    </h2>
    <p class="cta-desc">Rejoignez l'écosystème NOVAQYS et intégrez le nouveau réseau de sous-traitance automobile mondial.</p>
    <div class="cta-buttons">
      <a href="#ecosystem">
        <button class="btn-primary">
          Découvrir NOVAQYS
          <i class="fas fa-arrow-right"></i>
        </button>
      </a>
      <a href="/aqmi/login">
        <button class="btn-outline">
          <i class="fas fa-lock"></i> Connexion AQMI
        </button>
      </a>
      <a href="#contact">
        <button class="btn-ghost">
          <i class="fas fa-play"></i> Demander une démonstration
        </button>
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     CONTACT
     ═══════════════════════════════════════════════════════════════════ -->
<section class="contact-section" id="contact">
  <div class="px-6">
    <div class="section-header fade-up">
      <span class="section-label"><i class="fas fa-envelope"></i> Contact</span>
      <h2 class="section-title">Parlons de votre<br><span class="gradient-text">projet</span></h2>
    </div>

    <div class="contact-card fade-up">
      <div class="contact-avatar">MB</div>
      <h3 class="contact-name">BENSAFI Mohammed El Bachir</h3>
      <p class="contact-role">Consultant NOVAQYS Ecosystem</p>
      <div class="contact-links">
        <a href="tel:+213552609955" class="contact-link">
          <i class="fas fa-phone" style="color:#7367f0"></i>
          +213 552 60 99 55
        </a>
        <a href="mailto:contact@novaqys.com" class="contact-link">
          <i class="fas fa-envelope" style="color:#9b8cf7"></i>
          contact@novaqys.com
        </a>
        <a href="https://novaqys.com" target="_blank" class="contact-link">
          <i class="fas fa-globe" style="color:#28c76f"></i>
          NOVAQYS Ecosystem
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand">
        <img src="<?= asset('img/logo-novaqys-header.png') ?>" alt="NOVAQYS" width="48" height="32" style="height:32px;width:auto">
        <span class="footer-brand-text">NOVAQYS</span>
      </div>
      <p class="footer-desc">
        Écosystème complet de développement des fabricants de pièces de rechange automobiles. De l'évaluation à l'intégration dans la supply chain mondiale.
      </p>
      <div class="footer-social">
        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
        <a href="mailto:contact@novaqys.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
      </div>
    </div>

    <div>
      <p class="footer-col-title">Écosystème</p>
      <div class="footer-links">
        <a href="/">NOVAQYS</a>
        <a href="/aqmi/login">AQMI</a>
        <a href="#nara">NARA</a>
        <a href="/lms.html">Automotive Learning</a>
        <a href="#asin">ASIN</a>
      </div>
    </div>

    <div>
      <p class="footer-col-title">Solutions</p>
      <div class="footer-links">
        <a href="#qms">QMS</a>
        <a href="/aqmi-starter.html">AQMI Starter</a>
        <a href="#aqmi-pro">AQMI Pro</a>
        <a href="#pourquoi">Pourquoi NOVAQYS</a>
        <a href="#solutions">Parcours</a>
      </div>
    </div>

    <div>
      <p class="footer-col-title">Liens</p>
      <div class="footer-links">
        <a href="#contact">Contact</a>
        <a href="/login">Admin</a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p class="footer-copy">&copy; <?= date('Y') ?> NOVAQYS. Tous droits réservés.</p>
    <div class="footer-legal">
      <a href="#">Mentions légales</a>
      <a href="#">Politique de confidentialité</a>
    </div>
  </div>
</footer>

<script>
function closeMobile(){
  document.getElementById('navMobile')?.classList.remove('open');
  const icon = document.querySelector('#navToggle i');
  if(icon) icon.className = 'fas fa-bars';
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>
