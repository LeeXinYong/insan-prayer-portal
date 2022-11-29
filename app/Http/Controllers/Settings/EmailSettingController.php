<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoggerController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\EmailServer;
use App\Mail\PortalEmail;
use Illuminate\Support\Facades\Config;

class EmailSettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EmailServer::class, 'emailserver');
    }

    public function index(Request $request): Factory|View|Application
    {
        // get email setting
        $emailserver = EmailServer::query()->first();
        $id = $emailserver->id ?? "";
        $transport = $emailserver->transport ?? "";
        $domain = $emailserver->mail_domain ?? "";
        $host = $emailserver->mail_host ?? "";
        $port = $emailserver->mail_port ?? "";
        $enc = $emailserver->mail_encryption ?? "";
        $username = $emailserver->mail_username ?? "";
        $name = $emailserver->mail_name ?? "";
        $address = $emailserver->mail_address ?? "";

        return view('pages.settings.emailserver.edit', ["id" => $id, "host" => $host, "port" => $port, "enc" => $enc, "username" => $username, "name" => $name, "address" => $address, "transport" => $transport, "domain" => $domain]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("settings.email_server", "edit", request: $request);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $emailserver = new EmailServer;
            $emailserver->transport = $request->transport;
            $emailserver->mail_domain = $request->domain;
            $emailserver->mail_secret = $request->secret;
            $emailserver->mail_host = $request->host;
            $emailserver->mail_port = $request->port;
            $emailserver->mail_encryption = $request->encryption;
            $emailserver->mail_username = $request->username;
            $emailserver->mail_password = $request->password;
            $emailserver->mail_name = $request->name;
            $emailserver->mail_address = $request->address;
            $emailserver->added_by = Auth::user()->id;
            $emailserver->added_ip = request()->ip();
            $emailserver->updated_by = Auth::user()->id;
            $emailserver->updated_ip = request()->ip();

            $emailserver->save();

            // Log Audit
            LoggerController::log("email_server", $emailserver, "audit_log.message.create_email_server", $emailserver->mail_name);

            return response()->json([
                "success" => __("settings.email_server.message.success_update"),
                "button" => __("general.button.ok"),
                "redirect" => route("system.settings.emailserver.index")
            ]);
        }
    }

    public function update(Request $request, EmailServer $emailserver): JsonResponse
    {
        $validator = ValidationService::getValidator("settings.email_server", "edit", request: $request);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $emailserver->transport = $request->transport;
            $emailserver->mail_domain = $request->domain;
            $emailserver->mail_secret = $request->secret;
            $emailserver->mail_host = $request->host;
            $emailserver->mail_port = $request->port;
            $emailserver->mail_encryption = $request->encryption;
            $emailserver->mail_username = $request->username;
            $emailserver->mail_password = $request->password;
            $emailserver->mail_name = $request->name;
            $emailserver->mail_address = $request->address;
            $emailserver->updated_by = Auth::user()->id;
            $emailserver->updated_ip = request()->ip();

            $emailserver->save();

            // Log Audit
            LoggerController::log("email_server", $emailserver, "audit_log.message.update_email_server", $emailserver->mail_name);

            return response()->json([
                "success" => __("settings.email_server.message.success_update"),
                "button" => __("general.button.ok"),
                "redirect" => route("system.settings.emailserver.index")
            ]);
        }
    }

    public function sendEmail($user, $data, $type, $extrasubject = null)
    {
        // get email setting
        $emailserver = EmailServer::query()->first();

        // if user configured the email setting, then use it
        if (
            $emailserver &&
            ($emailserver->transport === 'SMTP' ||
            $emailserver->transport === 'MAILGUN_API')
        ) {

            // email data for sending
            $email_sender = [
                "name" => $emailserver->mail_name,
                "address" => $emailserver->mail_address
            ];

            // Mailer Configuration
            if ($emailserver->transport === 'SMTP') { // SMTP

                // Setup your SMTP mailer
                Config::set('mail.mailers.smtp.host', $emailserver->mail_host);
                Config::set('mail.mailers.smtp.username', $emailserver->mail_username);
                Config::set('mail.mailers.smtp.password', $emailserver->mail_password);
                Config::set('mail.mailers.smtp.port', $emailserver->mail_port);
                Config::set('mail.mailers.smtp.encryption', $emailserver->mail_encryption);

                // mailable object
                $mail_data = (new EmailTemplateController)->getEmailTemplate($data, $type, $extrasubject);
                $mail_data['email'] = $email_sender;
                $mailable = new PortalEmail($mail_data);


                // send email
                Mail::to($user)
                    ->bcc($emailserver->mail_username)
                    ->send($mailable);
            } elseif ($emailserver->transport === 'MAILGUN_API') { //MAILGUN API

                // Setup your Mailgun API mailer
                //                $transport = new MailgunTransport(new Client(), $emailserver->mail_secret, $emailserver->mail_domain);

                config(['services.mailgun' => [
                    'domain' => $emailserver->mail_domain,
                    'secret' => $emailserver->mail_secret
                ]]);

                // mailable object
                $mail_data = (new EmailTemplateController)->getEmailTemplate($data, $type, $extrasubject);
                $mail_data['email'] = $email_sender;
                $mailable = new PortalEmail($mail_data);


                // send email
                Mail::mailer('mailgun')
                ->to($user)
                    //                    ->bcc($emailserver->mail_username)
                    ->send($mailable);
            }
        } else {
            // Fall back, if user not set their email

            // mailable object
            $email_sender = array(
                "address" => config('mail.from.address'),
                "name" => config('mail.from.name')
            );
            $mail_data = (new EmailTemplateController)->getEmailTemplate($data, $type, $extrasubject);
            $mail_data['email'] = $email_sender;
            $mailable = new PortalEmail($mail_data);

            // send email
            Mail::to($user)
                ->bcc(config('mail.from.address'))
                ->send($mailable);
        }
    }
}
