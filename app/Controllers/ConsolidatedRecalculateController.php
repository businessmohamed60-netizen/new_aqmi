<?php
namespace App\Controllers\Admin; // adaptez le namespace à celui de vos autres contrôleurs admin

use App\Services\ConsolidationService;

/**
 * Recalcul d'un rapport consolidé (poids par évaluation + méthode de calcul).
 *
 * Route à ajouter DANS LE MÊME GROUPE (auth admin + CSRF) que /admin/consolidated/{id}/review :
 *   POST /admin/consolidated/{id}/recalculate  →  ConsolidatedRecalculateController::handle
 *
 * Champs POST attendus :
 *   mode               "preview" (JSON, rien n'est enregistré) ou "save" (enregistre puis redirige)
 *   calculation_method code d'une méthode de consolidation_methods
 *   weights[ID]        poids de l'évaluation ID (0 à 100)
 */
class ConsolidatedRecalculateController
{
    public function handle($id = null)
    {
        $id = (int)$id;
        if ($id <= 0 && preg_match('#/consolidated/(\d+)/recalculate#', (string)($_SERVER['REQUEST_URI'] ?? ''), $m)) {
            $id = (int)$m[1];
        }

        $isPreview = (($_POST['mode'] ?? 'save') === 'preview');
        $wantsJson = $isPreview
            || strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

        try {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                throw new \InvalidArgumentException('Méthode non autorisée.');
            }
            if ($id <= 0) {
                throw new \InvalidArgumentException('Rapport introuvable.');
            }

            $weights = is_array($_POST['weights'] ?? null) ? $_POST['weights'] : [];
            $service = new ConsolidationService();
            $result  = $service->recalculateReport(
                $id,
                (string)($_POST['calculation_method'] ?? ''),
                $weights,
                !$isPreview
            );

            if ($wantsJson) {
                return $this->json(['ok' => true] + $service->summarizeResult($result));
            }
            return $this->redirect('/admin/consolidated/' . $id . '?recalc=ok');
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
            $code = 422;
        } catch (\Throwable $e) {
            error_log('[consolidated/recalculate] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            $message = 'Erreur interne lors du recalcul (voir les journaux du serveur).';
            $code = 500;
        }

        if ($wantsJson) {
            return $this->json(['ok' => false, 'error' => $message], $code);
        }
        return $this->redirect('/admin/consolidated/' . max(0, $id) . '?recalc=error&msg=' . rawurlencode($message));
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
