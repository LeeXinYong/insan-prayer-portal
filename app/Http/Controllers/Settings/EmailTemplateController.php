<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoggerController;
use App\DataTables\EmailTemplatesDataTable;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\EmailTemplate;
use App\Services\DateTimeFormatterService;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Jobs\SendEmail;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EmailTemplate::class, 'emailtemplate');
    }

    /**
     * Display a listing of the resource.
     *
     * @param EmailTemplatesDataTable $dataTable
     * @return mixed
     */
    public function index(EmailTemplatesDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.settings.emailtemplate.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param EmailTemplate $emailtemplate
     * @return View
     */
    public function edit(Request $request, EmailTemplate $emailtemplate): View
    {
        $emailtemplate->html_content = strip_scripts($emailtemplate->html_content);
        $updated_at_text = $emailtemplate->updated_at;
        if($emailtemplate->updated_at != __("general.message.not_applicable")) {
            $emailtemplate->updated_at_duration = DateTimeFormatterService::formatIntervals($emailtemplate->updated_at_epoch);
            $updated_at_text = $emailtemplate->updated_at_duration.", ".$emailtemplate->updated_at;
        }
        $hint_message = "
            <table>
                <tr>
                    <td>". __("settings.email_template.message.info.last_updated", []). "</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td>". $updated_at_text ."</td>
                </tr>
                <tr>
                    <td>". __("settings.email_template.message.info.target_user", []). "</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td>". $emailtemplate->target_user. "</td>
                </tr>
                <tr>
                    <td>". __("settings.email_template.message.info.description", []). "</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td>". $emailtemplate->description. "</td>
                </tr>
            </table>
        ";
        return view("pages.settings.emailtemplate.edit", compact("emailtemplate", "hint_message"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param EmailTemplate $emailtemplate
     * @return JsonResponse
     */
    public function update(Request $request, EmailTemplate $emailtemplate): JsonResponse
    {
        $validator = ValidationService::getValidator("settings.email_template", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $emailtemplate->subject = $request->subject;
            $emailtemplate->html_content = strip_scripts($request->html_content);
            $emailtemplate->updated_by = Auth::user()->id;
            $emailtemplate->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($emailtemplate);

            $emailtemplate->save();

            // Log Audit
            LoggerController::log("email_template", $emailtemplate, "audit_log.message.update_email_template", $emailtemplate->name, $changes);

            return response()->json([
                "success" => __("settings.email_template.message.success_update", ["template" => $emailtemplate->name]),
                "button" => __("settings.email_template.button.view_listing"),
                "redirect" => route("system.settings.emailtemplate.index")
            ]);
        }
    }

    /**
     * Get Email Template content.
     *
     * @param array $data - email data content
     * @param String $type - email type or code
     * @param null $extrasubject - extra subject
     * @return array
     */
    public function getEmailTemplate(array $data, string $type, $extrasubject = null): array
    {
        $template = EmailTemplate::query()->where('code', $type)->first();
        $subject = $data['subject'] ?? $template->subject;
        $html_content = $template->html_content;
        $subject = ($extrasubject != null) ? $extrasubject . $subject : $subject;

        $mustache = new \Mustache_Engine;
        $renderedHtml = $mustache->render($html_content, $data);

        $params = array(
            "subject" => $subject,
            "email_contents" => $renderedHtml,
        );

        if (isset($data['buttons'])) {
            $params['buttons'] =  $data['buttons'];
        }

        return $params;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param EmailTemplate $emailtemplate
     * @return JsonResponse
     */
    public function testemail(Request $request, EmailTemplate $emailtemplate): JsonResponse
    {
        $user = Auth::user();

        // send email to user
        $data = array(
            "user_name" => $user->name,
            "login_id" => $user->email,
            "login_password" => "XXXXX",
            "login_url" => config('app.url') . '/login',
        );

        if ($emailtemplate->code == 'new_user' || $emailtemplate->code == 'password_changed') {
            $data['buttons'] = [
                ["url" => config('app.url') . '/login', "text" => "Sign In Now"]
            ];
        }
        if ($emailtemplate->code == 'forgot_password') {
            $data['buttons'] = [
                ["url" => config('app.url') . "/password/reset/abc12312321313213", "text" => "Reset Password"]
            ];
        }

        SendEmail::dispatch($user, $data, $emailtemplate->code, '[Test Email] ');

        // Log Audit
        LoggerController::log("email_template", $emailtemplate, "audit_log.message.send_test_email", "Email: " . $emailtemplate->subject);

        return response()->json([
            "success" => __("settings.email_template.message.test_email_sent"),
            "button" => __("general.button.ok"),
            "redirect" => null
        ]);
    }
}
