<?php
namespace App\Controllers;

class LeadController
{
    public function store(): void
    {
        $data = [
            'assessment_id' => $_POST['assessment_id'] ?? null,
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'company' => $_POST['company'] ?? '',
            'sector' => $_POST['sector'] ?? '',
            'activity_category' => $_POST['activity_category'] ?? '',
            'oem_tier' => $_POST['oem_tier'] ?? '',
            'product_category' => $_POST['product_category'] ?? '',
            'main_clients' => $_POST['main_clients'] ?? '',
            'annual_revenue' => $_POST['annual_revenue'] ?? '',
            'export_percentage' => $_POST['export_percentage'] ?? '',
            'production_sites' => $_POST['production_sites'] ?? '',
            'workforce_production' => $_POST['workforce_production'] ?? '',
            'workforce_engineering' => $_POST['workforce_engineering'] ?? '',
            'job_title' => $_POST['job_title'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'country' => $_POST['country'] ?? '',
            'company_size' => $_POST['company_size'] ?? '',
            'website' => $_POST['website'] ?? '',
            'certifications' => $_POST['certifications'] ?? '',
            'founded_year' => $_POST['founded_year'] ?? '',
            'production_type' => $_POST['production_type'] ?? '',
            'production_capacity' => $_POST['production_capacity'] ?? '',
            'machine_count' => $_POST['machine_count'] ?? null,
            'machine_types' => $_POST['machine_types'] ?? '',
            'main_materials' => $_POST['main_materials'] ?? '',
            'process_technologies' => $_POST['process_technologies'] ?? '',
            'ppm_target' => $_POST['ppm_target'] ?? '',
            'otd_rate' => $_POST['otd_rate'] ?? '',
            'fta_rate' => $_POST['fta_rate'] ?? '',
            'scrap_rate' => $_POST['scrap_rate'] ?? '',
            'traceability_system' => $_POST['traceability_system'] ?? '',
            'logistics_system' => $_POST['logistics_system'] ?? '',
            'rd_budget_percent' => $_POST['rd_budget_percent'] ?? '',
            'current_erp' => $_POST['current_erp'] ?? '',
            'consent_contact' => ($_POST['consent_contact'] ?? '') === 'yes' ? 1 : 0,
            'consent_share_industry' => ($_POST['consent_share_industry'] ?? '') === 'yes' ? 1 : 0,
        ];

        $leadId = \App\Models\Lead::create($data);
        jsonResponse(['success' => true, 'lead_id' => $leadId]);
    }
}
