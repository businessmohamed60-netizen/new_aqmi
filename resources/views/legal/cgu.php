<?php
$title = 'Conditions Générales d\'Utilisation - NOVAQYS';
$showFooter = true;
ob_start();
?>
<div style="max-width:860px;margin:0 auto;padding:3rem 1.5rem 5rem;font-family:'Manrope','Inter',system-ui,sans-serif;color:#1a1a2e;line-height:1.7;">
  <h1 style="font-size:2rem;font-weight:800;margin-bottom:0.5rem;letter-spacing:-0.5px;">Conditions Générales d'Utilisation</h1>
  <p style="color:#7d8794;font-size:0.85rem;margin-bottom:2.5rem;">Dernière mise à jour : 11 septembre 2026</p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">1. Objet</h2>
  <p style="font-size:0.9rem;color:#475569;">
    Les présentes Conditions Générales d'Utilisation (ci-après « CGU ») régissent l'accès et l'utilisation de la plateforme NOVAQYS / AQMI (Automotive Quality Maturity Index) éditée par NOVAQYS. En utilisant la plateforme, vous acceptez sans réserve les présentes CGU.
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">2. Définitions</h2>
  <ul style="font-size:0.9rem;color:#475569;padding-left:1.5rem;">
    <li><strong>Plateforme</strong> : l'application web NOVAQYS accessible à l'adresse novaqys.com</li>
    <li><strong>Utilisateur</strong> : toute personne physique ou morale inscrite ou utilisant la plateforme</li>
    <li><strong>Évaluation</strong> : le questionnaire d'auto-évaluation de maturité qualité AQMI</li>
    <li><strong>Rapport</strong> : le document de synthèse généré à l'issue d'une évaluation</li>
  </ul>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">3. Accès à la plateforme</h2>
  <p style="font-size:0.9rem;color:#475569;">
    L'accès à la plateforme est réservé aux utilisateurs ayant créé un compte. L'inscription est gratuite. L'éditeur se réserve le droit de refuser ou suspendre un compte en cas de non-respect des présentes CGU. La plateforme est accessible 24h/24, sous réserve de maintenance et de force majeure.
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">4. Utilisation du service</h2>
  <p style="font-size:0.9rem;color:#475569;">
    L'utilisateur s'engage à utiliser la plateforme de manière licite et conforme. Sont notamment interdits :
  </p>
  <ul style="font-size:0.9rem;color:#475569;padding-left:1.5rem;">
    <li>La création de comptes sous une fausse identité</li>
    <li>La tentative d'accès non autorisé aux données d'autres utilisateurs</li>
    <li>L'extraction, la copie ou la redistribution des contenus sans autorisation</li>
    <li>L'utilisation de la plateforme à des fins commerciales sans accord préalable</li>
  </ul>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">5. Évaluations et rapports</h2>
  <p style="font-size:0.9rem;color:#475569;">
    Les évaluations AQMI sont des outils d'auto-évaluation. Les résultats reflètent les déclarations de l'utilisateur et ne constituent pas une certification officielle, sauf mention explicite de certification délivrée par NOVAQYS. Les rapports générés sont la propriété de l'utilisateur mais leur format et contenu restent la propriété intellectuelle de NOVAQYS.
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">6. Limitation de responsabilité</h2>
  <p style="font-size:0.9rem;color:#475569;">
    La plateforme est fournie « telle quelle ». NOVAQYS ne saurait être tenue responsable des décisions prises par l'utilisateur sur la base des résultats d'évaluation. L'éditeur ne garantit pas l'exactitude, l'exhaustivité ou la pertinence des recommandations générées.
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">7. Propriété intellectuelle</h2>
  <p style="font-size:0.9rem;color:#475569;">
    L'ensemble des éléments de la plateforme (design, logo, contenus, questionnaires, algorithmes de scoring) est protégé par le droit de la propriété intellectuelle. Toute reproduction sans autorisation est interdite.
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">8. Modification des CGU</h2>
  <p style="font-size:0.9rem;color:#475569;">
    Les présentes CGU peuvent être modifiées à tout moment. Les modifications entrent en vigueur dès leur publication sur la plateforme. Il appartient à l'utilisateur de consulter régulièrement cette page.
  </p>

  <h2 style="font-size:1.15rem;font-weight:700;margin:2rem 0 0.75rem;color:#1F6FEB;">9. Contact</h2>
  <p style="font-size:0.9rem;color:#475569;">
    Pour toute question relative aux CGU : <a href="mailto:contact@novaqys.com" style="color:#1F6FEB;">contact@novaqys.com</a>
  </p>

  <div style="margin-top:3rem;padding-top:1.5rem;border-top:1px solid #e7e1d7;">
    <a href="/" style="color:#1F6FEB;text-decoration:none;font-size:0.85rem;font-weight:600;">
      <i class="fas fa-arrow-left" style="margin-right:0.3rem;"></i>Retour à l'accueil
    </a>
  </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/landing.php';
