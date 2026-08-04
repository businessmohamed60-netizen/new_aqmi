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
        ];

        $leadId = \App\Models\Lead::create($data);
        jsonResponse(['success' => true, 'lead_id' => $leadId]);
    }
}