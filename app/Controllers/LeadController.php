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
            'consent_contact' => ($_POST['consent_contact'] ?? '') === 'yes' ? 1 : 0,
            'consent_share_industry' => ($_POST['consent_share_industry'] ?? '') === 'yes' ? 1 : 0,
        ];

        $leadId = \App\Models\Lead::create($data);
        jsonResponse(['success' => true, 'lead_id' => $leadId]);
    }
}