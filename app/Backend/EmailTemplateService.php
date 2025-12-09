<?php

namespace App\Services\Backend;

use App\Models\EmailTemplate;

class EmailTemplateService
{

    public function getTemplate($key): EmailTemplate|null
    {
        return EmailTemplate::where('key', $key)->enabled()->first();
    }

    public function replaceVariables($content, $variables = [])
    {
        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
            $content = str_replace("{{ $key }}", $value, $content);
        }

        return $content;
    }

    public function compileTemplate($template, $variables = []): array|null
    {
        if (!$template) {
            return null;
        }

        $subject = $this->replaceVariables($template->subject, $variables);
        $body = $this->replaceVariables($template->body, $variables);

        return [
            'subject' => $subject,
            'body' => $body,
            'blade_template' => $template->blade_template,
            'variables' => $variables
        ];
    }

    public function getTemplateByKey($key): EmailTemplate|null
    {
        return $this->getTemplate($key);
    }
}
