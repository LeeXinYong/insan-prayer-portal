<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\EmailTemplate;

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')->truncate();

        $templates = self::getEmailTemplates();

        foreach ($templates as $template) {
            EmailTemplate::query()->create($template);
        }
    }

    public static function getEmailTemplates()
    {
        return [
            [
                "code" => "new_user",
                "name" => "Welcome Email",
                "target_user" => "New Users",
                "description" => "New Users for the portal",
                "subject" => "Welcome Email",
                "html_content" => Storage::disk('emailtemplates')->get("new_user.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Company' => 'company',
                    'Role' => 'role',
                    'Login URL' => 'login_url',
                    'Login ID' => 'login_id',
                    'Login Password' => 'login_password',
                )),
                "locale" => "en"
            ],
            [
                "code" => "reset_password",
                "name" => "Password Reset",
                "target_user" => "All users",
                "description" => "Password reset email",
                "subject" => "Password Reset",
                "html_content" => Storage::disk('emailtemplates')->get("reset_password.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Company' => 'company',
                    'Role' => 'role',
                    'Login Password' => 'login_password',
                )),
                "locale" => "en"
            ],
            [
                "code" => "password_changed",
                "name" => "Password Changed",
                "target_user" => "All users",
                "description" => "Password changed confirmation",
                "subject" => "Password Changed",
                "html_content" => Storage::disk('emailtemplates')->get("password_changed.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Company' => 'company',
                    'Role' => 'role',
                    'Login URL' => 'login_url',
                )),
                "locale" => "en"
            ],
            [
                "code" => "suspend_account",
                "name" => "Account Suspended",
                "target_user" => "All users",
                "description" => "Account suspended alert",
                "subject" => "Account Suspended",
                "html_content" => Storage::disk('emailtemplates')->get("suspend_account.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Company' => 'company',
                    'Role' => 'role',
                )),
                "locale" => "en"
            ],
            [
                "code" => "reactivate_account",
                "name" => "Account Reactivated",
                "target_user" => "All users",
                "description" => "Account reactivated alert",
                "subject" => "Account Reactivated",
                "html_content" => Storage::disk('emailtemplates')->get("reactivate_account.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Company' => 'company',
                    'Role' => 'role',
                )),
                "locale" => "en"
            ],
            [
                "code" => "forgot_password",
                "name" => "Forgot Password",
                "target_user" => "All users",
                "description" => "Forgot password email",
                "subject" => "Forgot Password",
                "html_content" => Storage::disk('emailtemplates')->get("forgot_password.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Reset Link' => 'reset_link',
                )),
                "locale" => "en"
            ],
            [
                "code" => "magic_link",
                "name" => "Sign In with Magic Link",
                "target_user" => "Portal Users",
                "description" => "Portal users who sign in using magic link",
                "subject" => "Sign In with Magic Link",
                "html_content" => Storage::disk('emailtemplates')->get("magic_link.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Magic Link' => 'magic_link',
                )),
                "locale" => "en"
            ],
            [
                "code" => "failed_job_alert",
                "name" => "Failed Job Alert",
                "target_user" => "Super Admin",
                "description" => "Alert super admin if any background job failed",
                "subject" => "Failed Background Job: {{job_name}}",
                "html_content" => Storage::disk('emailtemplates')->get("failed_job_alert.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                     'Job ID' => 'job_id',
                     'Job Name' => 'job_name',
                     'Failed At' => 'failed_at',
                     'Payload' => 'payload',
                     'Exception' => 'exception',
                     'Environment' => 'environment'
                )),
                "locale" => "en"
            ],
            [
                "code" => "new_device_login",
                "name" => "New Login Notification",
                "target_user" => "All Users",
                "description" => "Notified user if new login to a new or untrusted device",
                "subject" => "New Login Notification",
                "html_content" => Storage::disk('emailtemplates')->get("new_device_login.txt"),
                "fields" => json_encode(array(
                    'Name' => 'user_name',
                    'Device' => 'device',
                    'IP Address' => 'ip_address',
                    'Location' => 'location',
                    'Date' => 'date',
                )),
                "locale" => "en"
            ],
        ];
    }
}
