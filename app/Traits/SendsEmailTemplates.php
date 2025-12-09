<?php

namespace App\Traits;

use App\Services\Backend\EmailTemplateService;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplatedEmail;
use Illuminate\Support\Facades\Log;

trait SendsEmailTemplates
{
    protected function sendTemplatedEmail($templateKey, $recipientEmail, $variables = [])
    {
        $emailService = app(EmailTemplateService::class);
        $template = $emailService->getTemplateByKey($templateKey);
        if (!$template) {
            Log::error("Email template not found: {$templateKey}");
            return false;
        }

        $compiled = $emailService->compileTemplate($template, $variables);

        try {
            $mail = new TemplatedEmail(
                $compiled['subject'],
                $compiled['body'],
                $template->blade_template,
                $compiled['variables']
            );

            Mail::to($recipientEmail)->send($mail);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            return false;
        }
    }

    // New method to get template content for custom usage
    protected function getTemplatedEmailContent($templateKey, $variables = [])
    {
        $emailService = app(EmailTemplateService::class);
        $template = $emailService->getTemplateByKey($templateKey);

        if (!$template) {
            return null;
        }

        return $emailService->compileTemplate($template, $variables);
    }

    // Method to send email with custom blade template
    protected function sendCustomTemplatedEmail($templateKey, $recipientEmail, $bladeTemplate, $variables = [])
    {
        $emailService = app(EmailTemplateService::class);
        $template = $emailService->getTemplateByKey($templateKey);

        if (!$template) {
            Log::error("Email template not found: {$templateKey}");
            return false;
        }

        $compiled = $emailService->compileTemplate($template, $variables);

        try {
            $mail = new TemplatedEmail(
                $compiled['subject'],
                $compiled['body'],
                $bladeTemplate,
                $compiled['variables']
            );

            Mail::to($recipientEmail)->send($mail);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send custom email: " . $e->getMessage());
            return false;
        }
    }
}
