<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
         $templates = [
            [
                'key' => 'password.reset',
                'name' => 'Password Reset Email',
                'subject' => 'Reset your password for {{ app_name }}',
                'body' => '
                    <p>Hi {{ user_name }},</p>
                    <h6>Password Reset</h6>
                    <p>Click the link below to reset your password:</p>
                    <p>{{ reset_link }}</p>
                    <p>If you did not request a password reset, please ignore this email.</p>
                    <p>Best regards,<br>{{ app_name }}</p>
                ',
                'variables' => ['app_name', 'reset_link', 'user_name'],
                'blade_template' => 'backend.emails.default',
                'status' => EmailTemplate::STATUS_ACTIVE,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate([
                'key' => $template['key'],
            ], $template);
        }
    }
}
