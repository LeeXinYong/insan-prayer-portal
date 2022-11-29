<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!--<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!--[if (gte mso 9)|(IE)]>
    <![endif]-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,200;0,400;0,700;1,600&display=swap"
          rel="stylesheet">
    <style>
        .signature-text {
            font-family: 'Source Sans Pro', sans-serif;
            font-style: normal;
            font-weight: normal;
            font-size: 14px;
            line-height: 18px;
            text-align: justify;
            color: #333333;
        }

        @media only screen and (min-device-width: 750px) {
            .table750 {
                width: 750px !important;
            }
        }

        @media only screen and (max-device-width: 750px), only screen and (max-width: 750px) {
            table[class="table750"] {
                width: 100% !important;
            }

            .mob_b {
                width: 93% !important;
                max-width: 93% !important;
                min-width: 93% !important;
            }

            .mob_b1 {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
            }

            .mob_left {
                text-align: left !important;
            }

            .mob_soc {
                width: 50% !important;
                max-width: 50% !important;
                min-width: 50% !important;
            }

            .mob_menu {
                width: 50% !important;
                max-width: 50% !important;
                min-width: 50% !important;
                box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2);
            }
        }

        @media only screen and (max-device-width: 700px), only screen and (max-width: 700px) {
            .mob_div {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
            }

            .mob_tab {
                width: 88% !important;
                max-width: 88% !important;
                min-width: 88% !important;
            }
        }

        @media only screen and (max-device-width: 550px), only screen and (max-width: 550px) {
            .mod_div {
                display: block !important;
            }
        }

        .table750 {
            width: 750px;
        }
    </style>
</head>

<body style="margin: 0 !important;padding: 0;background-color:#f3f3f3;">
<table width="100%" class="table-body" cellspacing="0" cellpadding="0" border="0"
       style="background: #f3f3f3; min-width: 350px; line-height: normal; /*margin-top: 50px;*/">
    <tbody>
    <tr style="background: linear-gradient(to bottom, {{$header_color}} 150px, transparent 100px);">
        <td width="100%" align="center" valign="top">
            <table width="750" class="table750" cellspacing="0" cellpadding="0" border="0"
                   style="width: 100%; max-width: 750px; min-width: 350px; background: #f3f3f3;">
                <tbody>
                <tr>
                    <td align="center" valign="top" style="background: {{$header_color}};">
                        <table class="table-header" bgcolor="{{$header_color}}" cellpadding="4" cellspacing="4" border="0"
                               width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%;">
                            <tr>
                                <td align="right" valign="top">
                                    <div style="height: 22px; line-height: 22px; font-size: 30px;">&nbsp;</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="background: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td align="left" valign="top">
                                    <div style="height: 39px; line-height: 39px; font-size: 37px;">&nbsp;</div>
                                    <div style="display: block; max-width: 128px;">
                                        <img alt="Image" border="0"
                                             src="{{ asset(theme()->getMediaUrlPath() . 'logos/logo-demo3-default.svg') }}"
                                             title="Image" width="128" style="width: 128px;">
                                    </div>
                                    <div style="height: 29px; line-height: 29px; font-size: 20px;">&nbsp;</div>
                                    <hr style="display: block; border-width: 1px; margin-bottom: 20px; background-color: #BDBDBD">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="background: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tbody>
                            <tr>
                                <td align="left" valign="top"
                                    style="font-family: 'Source Sans Pro', sans-serif; font-size: 30px; font-weight: 600; line-height: 50px; color: #121A26;">
                                    {!! $subject !!}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="background: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td align="left" valign="top"
                                    style="font-family: 'Source Sans Pro', sans-serif; font-size: 16px; line-height: 20px; color: #333333">
                                    {!! $email_contents !!}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="background: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td align="center" valign="top">
                                    <table class="table-header" cellpadding="4" cellspacing="4" border="0"
                                           width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%;">
                                        <tr>
                                            <td align="right" valign="top">
                                                <div style="height: 22px; line-height: 22px; font-size: 30px;">&nbsp;</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if(isset($buttons) && is_array($buttons))
                <tr>
                    <td align="center" style="padding-top:10px; background-color: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td align="center" valign="middle" height="68">
                                    <table style="border-collapse:separate!important" border="0" cellspacing="0"
                                           cellpadding="0">
                                        <tr>
                                            <td style="background-color:#333333; font-family: 'Source Sans Pro', sans-serif; font-size:16px; border-radius: 4px;"
                                                align="center" valign="middle">
                                                @foreach ($buttons as $button)
                                                <a href="{{ $button['url'] }}" class="button"
                                                   style="font-weight: 600; letter-spacing:0px; padding:12px; line-height:100%; color: #ffffff; text-align:center; text-decoration:none; display:block;">{{ $button['text'] }}
                                                    <img
                                                        src="{{ asset(theme()->getMediaUrlPath() . 'misc/arrow.png') }}"
                                                        alt="img" width="10" style="width: 10px;"/></a>
                                                @endforeach
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
                <tr>
                    <td align="center" valign="top" style="background-color: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td>
                                    <p class="signature-text"
                                       style="font-family:'Source Sans Pro', sans-serif;font-style:normal;font-weight:normal;font-size:14px;line-height:18px;text-align:justify;color:#333333;">
                                        Regards,</p>
                                    <p class="signature-text"
                                       style="font-family:'Source Sans Pro', sans-serif;font-style:normal;font-weight:normal;font-size:14px;line-height:18px;text-align:justify;color:#333333;">
                                        Administrator</p>
                                    <p class="signature-text"
                                       style="font-family:'Source Sans Pro', sans-serif;font-style:normal;font-weight:normal;font-size:14px;line-height:18px;text-align:justify;color:#333333;">
                                        **This is an automatically generated email, please do not reply**</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="background: #ffffff;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td align="center" valign="top">
                                    <table class="table-header" cellpadding="4" cellspacing="4" border="0"
                                           width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%;">
                                        <tr>
                                            <td align="right" valign="top">
                                                <div style="height: 22px; line-height: 22px; font-size: 30px;">&nbsp;</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if(!empty(config('app.download.googleplay')) && !empty(config('app.download.appstore')))
                <tr>
                    <td align="center" style="padding-top: 16px;background-color: #F5F5F5;">
                        <table cellpadding="0" cellspacing="0" border="0" width="88%"
                               style="width: 88% !important; min-width: 88%; max-width: 88%;">
                            <tr>
                                <td align="center" valign="middle" height="68">
                                    <div class="app-download"
                                         style="font-family:'Source Sans Pro', sans-serif;font-style:normal;font-weight:700;font-size:15px;line-height:19px;text-align:center;color:#333333;margin-bottom:5px;">
                                        Download App From
                                    </div>
                                    <div style="text-align:center;">
                                        <a href="{{ config('app.download.googleplay') }}" class="download-btn"
                                           style="text-decoration:none;">
                                            <img border="0"
                                                 src="{{ asset(theme()->getMediaUrlPath() . 'misc/googleplaybutton.png') }}" title="Get it on
                                    Google Play" alt="Get it on Google Play"
                                                 height="41" style="border-width:0;">
                                        </a>
                                        <a href="{{ config('app.download.appstore') }}" class="download-btn"
                                           style="text-decoration:none;">
                                            <img border="0"
                                                 src="{{ asset(theme()->getMediaUrlPath() . 'misc/appstorebutton.png') }}" title="Download on
                                    the App Store" alt="Download on the App Store" height="41" style="border-width:0;">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
                <tr>
                    <td align="center" style="padding-top: 32px; padding-bottom: 16px; background-color: #F5F5F5;">
                        <a href="{{ config('app.url') }}" target="_blank" class="app-url"
                           style="font-family:'Source Sans Pro', sans-serif;font-style:normal;font-weight:normal;font-size:12px;line-height:15px;color:#333333;text-align:center;text-decoration:none;">{{ config('app.url') }}</a>
                        <div class="app-copyright"
                             style="font-family:'Source Sans Pro', sans-serif;font-style:normal;font-weight:normal;font-size:12px;line-height:15px;text-align:center;color:#333333;margin-top:8px;">
                            © {{ date('Y') }} {{ config('app.company') }}. @lang('All rights reserved.')
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

</html>
