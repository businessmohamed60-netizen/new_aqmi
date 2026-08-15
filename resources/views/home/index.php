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
    <a href="#account-request" class="nav-link">Contact</a>
  </div>

  <div class="nav-actions">
    <a href="/login" class="btn-access">
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
  <a href="#account-request" onclick="closeMobile()"><i class="fas fa-envelope"></i> Contact</a>
  <div class="nav-mobile-divider"></div>
  <a href="/login" class="btn-access" onclick="closeMobile()">
    <i class="fas fa-lock"></i> Connexion AQMI
  </a>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════════════════ -->
<section class="hero" id="top">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow-1"></div>
  <div class="hero-glow-2"></div>

  <div class="hero-content">
    <div class="hero-badge">
      <span class="hero-badge-dot"></span>
      <span>Industry 4.0 · Automotive Manufacturing Ecosystem</span>
    </div>
    <h1 class="hero-title">
      NOVAQYS<br>
      <span class="hero-gradient">Building the Future of Automotive Manufacturing Excellence</span>
    </h1>
    <p class="hero-subtitle">Développez un nouveau réseau de sous-traitance automobile performant grâce à un écosystème complet d'évaluation, de formation, de digitalisation et de mise en relation industrielle.</p>
    <div class="hero-slogans" id="heroSlogans">
      <span class="hero-slogan active">Créer un nouveau réseau de sous-traitance automobile</span>
      <span class="hero-slogan">Maîtriser les coûts sans compromis sur la qualité</span>
      <span class="hero-slogan">Accélérer votre transition vers l'industrie 4.0</span>
      <span class="hero-slogan">Construire la compétitivité et la notoriété des fabricants</span>
      <span class="hero-slogan">Évaluer · Former · Digitaliser · Connecter</span>
    </div>
    <div class="hero-ctas">
      <a href="#ecosystem">
        <button class="btn-primary">
          Découvrir l'écosystème
          <i class="fas fa-arrow-right"></i>
        </button>
      </a>
      <a href="#account-request">
        <button class="btn-outline">
          <i class="fas fa-play"></i>
          Demander une démonstration
        </button>
      </a>
      <a href="/login">
        <button class="btn-ghost">
          <i class="fas fa-lock"></i>
          Connexion AQMI
        </button>
      </a>
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
        <a href="/login" class="btn-primary">
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
        'btn' => 'Découvrir AQMI', 'url' => '/register',
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
        'btn' => 'Découvrir LMS', 'url' => '/lms.html',
        'extra' => ''
      ],
      [
        'num' => '04', 'id' => 'digitalisation-qms',
        'color' => '#ff9f43', 'icon' => 'fa-industry',
        'title' => 'Digitalisation QMS',
        'desc' => 'L\'entreprise déploie NOVAQYS QMS pour structurer et digitaliser ses processus qualité, production et supply chain.',
        'btn' => 'Découvrir NOVAQYS', 'url' => '#qms',
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
        'btn' => 'Découvrir NARA', 'url' => '#nara',
        'extra' => ''
      ],
      [
        'num' => '07', 'id' => 'asin',
        'color' => '#7367f0', 'icon' => 'fa-globe',
        'title' => 'Visibilité ASIN',
        'desc' => 'L\'entreprise rejoint ASIN et publie son passeport industriel international. Visibilité mondiale auprès des donneurs d\'ordre.',
        'btn' => 'Découvrir ASIN', 'url' => '/asin.html',
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
        'btn' => 'Commencer maintenant', 'url' => '#account-request',
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
      <a href="/login">
        <button class="btn-outline">
          <i class="fas fa-lock"></i> Connexion AQMI
        </button>
      </a>
      <a href="#account-request">
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
     ACCOUNT REQUEST FORM
     ═══════════════════════════════════════════════════════════════════ -->
<section class="account-request-section scroll-mt-24" id="account-request">
  <div class="account-request-grid-bg"></div>
  <div class="account-request-glow"></div>
  <div class="max-w-7xl px-6">
    <div class="account-request-wrapper">
      <div class="account-request-info fade-up">
        <span class="section-label"><i class="fas fa-user-plus"></i> Demande de compte</span>
        <h2 class="section-title">Rejoignez l'écosystème<br><span class="gradient-text">NOVAQYS</span></h2>
        <p class="section-desc">Demandez votre accès à l'écosystème NOVAQYS. Notre équipe vous recontactera sous 48h pour activer votre compte et vous accompagner.</p>
        <div class="account-request-features">
          <div class="account-request-feature"><i class="fas fa-check-circle" style="color:#28c76f"></i> Accès à AQMI — évaluation de maturité</div>
          <div class="account-request-feature"><i class="fas fa-check-circle" style="color:#28c76f"></i> Plateforme LMS — formations automobiles</div>
          <div class="account-request-feature"><i class="fas fa-check-circle" style="color:#28c76f"></i> Rapports détaillés & scoring par domaine</div>
          <div class="account-request-feature"><i class="fas fa-check-circle" style="color:#28c76f"></i> Mise en relation réseau de sous-traitance</div>
        </div>
      </div>

      <div class="account-request-form-wrap fade-up">
        <?php
        $reqError = \App\Helpers\Session::getFlash('account_request_error');
        $reqSuccess = \App\Helpers\Session::getFlash('account_request_success');
        $oldInput = \App\Helpers\Session::getFlash('old_input.account', []);
        ?>
        <?php if ($reqSuccess): ?>
          <div class="account-request-success">
            <i class="fas fa-check-circle"></i>
            <h3>Demande envoyée avec succès</h3>
            <p>Votre demande a bien été transmise à notre équipe. Nous vous contacterons sous 48h.</p>
          </div>
        <?php else: ?>
          <?php if ($reqError): ?>
            <div class="account-request-error-msg">
              <i class="fas fa-exclamation-circle"></i> <?= e($reqError) ?>
            </div>
          <?php endif; ?>
          <form action="/account-request" method="POST" class="account-request-form">
            <?= csrf_field() ?>
            <div class="arf-row">
              <div class="arf-field">
                <label for="arf-company">Entreprise <span class="arf-required">*</span></label>
                <input type="text" id="arf-company" name="company" required placeholder="Nom de votre entreprise" value="<?= e($oldInput['company'] ?? '') ?>">
              </div>
              <div class="arf-field">
                <label for="arf-fullname">Nom complet <span class="arf-required">*</span></label>
                <input type="text" id="arf-fullname" name="fullname" required placeholder="Prénom et nom" value="<?= e($oldInput['fullname'] ?? '') ?>">
              </div>
            </div>
            <div class="arf-row">
              <div class="arf-field">
                <label for="arf-jobtitle">Fonction <span class="arf-required">*</span></label>
                <input type="text" id="arf-jobtitle" name="job_title" required placeholder="Ex: Directeur Qualité" value="<?= e($oldInput['job_title'] ?? '') ?>">
              </div>
              <div class="arf-field">
                <label for="arf-country">Pays <span class="arf-required">*</span></label>
                <input type="text" id="arf-country" name="country" required placeholder="Ex: France" value="<?= e($oldInput['country'] ?? '') ?>">
              </div>
            </div>
            <div class="arf-row">
              <div class="arf-field">
                <label for="arf-email">Email professionnel <span class="arf-required">*</span></label>
                <input type="email" id="arf-email" name="email" required placeholder="vous@entreprise.com" value="<?= e($oldInput['email'] ?? '') ?>">
              </div>
              <div class="arf-field">
                <label for="arf-phone">Téléphone <span class="arf-required">*</span></label>
                <input type="tel" id="arf-phone" name="phone" required placeholder="+33 6 12 34 56 78" value="<?= e($oldInput['phone'] ?? '') ?>">
              </div>
            </div>
            <div class="arf-row">
              <div class="arf-field">
                <label for="arf-size">Taille entreprise <span class="arf-required">*</span></label>
                <select id="arf-size" name="company_size" required>
                  <option value="">Sélectionner...</option>
                  <option value="1-10" <?= ($oldInput['company_size'] ?? '') === '1-10' ? 'selected' : '' ?>>1-10 employés</option>
                  <option value="11-50" <?= ($oldInput['company_size'] ?? '') === '11-50' ? 'selected' : '' ?>>11-50 employés</option>
                  <option value="51-200" <?= ($oldInput['company_size'] ?? '') === '51-200' ? 'selected' : '' ?>>51-200 employés</option>
                  <option value="201-500" <?= ($oldInput['company_size'] ?? '') === '201-500' ? 'selected' : '' ?>>201-500 employés</option>
                  <option value="500+" <?= ($oldInput['company_size'] ?? '') === '500+' ? 'selected' : '' ?>>500+ employés</option>
                </select>
              </div>
              <div class="arf-field">
                <label for="arf-activity">Secteur d'activité <span class="arf-required">*</span></label>
                <input type="text" id="arf-activity" name="activity" required placeholder="Ex: Fabrication de pièces automobiles" value="<?= e($oldInput['activity'] ?? '') ?>">
              </div>
            </div>
            <div class="arf-field arf-full">
              <label>Plateformes qui vous intéressent</label>
              <div class="arf-checkboxes">
                <label class="arf-check"><input type="checkbox" name="platforms[]" value="AQMI Starter" <?= in_array('AQMI Starter', $oldInput['platforms'] ?? []) ? 'checked' : '' ?>><span>AQMI Starter</span></label>
                <label class="arf-check"><input type="checkbox" name="platforms[]" value="AQMI Professional" <?= in_array('AQMI Professional', $oldInput['platforms'] ?? []) ? 'checked' : '' ?>><span>AQMI Professional</span></label>
                <label class="arf-check"><input type="checkbox" name="platforms[]" value="NARA" <?= in_array('NARA', $oldInput['platforms'] ?? []) ? 'checked' : '' ?>><span>NARA</span></label>
                <label class="arf-check"><input type="checkbox" name="platforms[]" value="LMS" <?= in_array('LMS', $oldInput['platforms'] ?? []) ? 'checked' : '' ?>><span>NOVAQYS LMS</span></label>
                <label class="arf-check"><input type="checkbox" name="platforms[]" value="QMS" <?= in_array('QMS', $oldInput['platforms'] ?? []) ? 'checked' : '' ?>><span>NOVAQYS QMS</span></label>
                <label class="arf-check"><input type="checkbox" name="platforms[]" value="ASIN" <?= in_array('ASIN', $oldInput['platforms'] ?? []) ? 'checked' : '' ?>><span>ASIN</span></label>
              </div>
            </div>
            <div class="arf-field arf-full">
              <label for="arf-message">Message (optionnel)</label>
              <textarea id="arf-message" name="message" rows="3" placeholder="Précisez votre demande..."><?= e($oldInput['message'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn-primary arf-submit">
              <i class="fas fa-paper-plane"></i>
              Envoyer ma demande
            </button>
          </form>
        <?php endif; ?>
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
        <a href="/login">AQMI</a>
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
        <a href="#account-request">Contact</a>
        <a href="/admin/login">Admin</a>
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

// Rotating slogans in hero
(function(){
  var slogans = document.querySelectorAll('#heroSlogans .hero-slogan');
  if(slogans.length < 2) return;
  var idx = 0;
  setInterval(function(){
    slogans[idx].classList.remove('active');
    slogans[idx].classList.add('exit');
    var prev = idx;
    idx = (idx + 1) % slogans.length;
    setTimeout(function(){
      slogans[prev].classList.remove('exit');
      slogans[idx].classList.add('active');
    }, 600);
  }, 3500);
})();
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
?>
