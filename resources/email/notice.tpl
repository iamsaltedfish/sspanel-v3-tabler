<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>title</title>
    <style>
        html,
        body,
        a,
        span,
        div[style*="margin: 16px 0"] {
            border: 0 !important;
            margin: 0 !important;
            outline: 0 !important;
            padding: 0 !important;
            text-decoration: none !important;
        }

        a,
        span,
        td,
        th {
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        span.st-Delink a {
            color: #525F7f !important;
            text-decoration: none !important;
        }

        span.st-Delink.st-Delink--preheader a {
            color: #ffffff !important;
            text-decoration: none !important;
        }

        span.st-Delink.st-Delink--title a {
            color: #32325d !important;
            text-decoration: none !important;
        }

        span.st-Delink.st-Delink--footer a {
            color: #8898aa !important;
            text-decoration: none !important;
        }

        table.st-Header td.st-Header-background div.st-Header-area {
            height: 76px !important;
            width: 600px !important;
            background-repeat: no-repeat !important;
            background-size: 600px 76px !important;
        }

        table.st-Header td.st-Header-logo div.st-Header-area {
            height: 21px !important;
            width: 49px !important;
            background-repeat: no-repeat !important;
            background-size: 49px 21px !important;
        }

        table.st-Header td.st-Header-logo.st-Header-logo--atlasAzlo div.st-Header-area {
            height: 21px !important;
            width: 216px !important;
            background-repeat: no-repeat !important;
            background-size: 216px 21px !important;
        }

        @media (-webkit-min-device-pixel-ratio: 1.25),
        (min-resolution: 120dpi),
        all and (max-width: 768px) {
            body[override] div.st-Target.st-Target--mobile img {
                display: none !important;
                margin: 0 !important;
                max-height: 0 !important;
                min-height: 0 !important;
                padding: 0 !important;
                font-size: 0 !important;
                line-height: 0 !important;
            }
        }

        @media all and (max-width: 600px) {

            body[override] table.st-Wrapper,
            body[override] table.st-Width.st-Width--mobile {
                min-width: 100% !important;
                width: 100% !important;
            }

            body[override] td.st-Spacer.st-Spacer--gutter {
                width: 32px !important;
            }

            body[override] td.st-Spacer.st-Spacer--kill {
                width: 0 !important;
            }

            body[override] td.st-Spacer.st-Spacer--emailEnd {
                height: 32px !important;
            }

            body[override] td.st-Font.st-Font--title,
            body[override] td.st-Font.st-Font--title span,
            body[override] td.st-Font.st-Font--title a {
                font-size: 28px !important;
                line-height: 36px !important;
            }

            body[override] td.st-Font.st-Font--header,
            body[override] td.st-Font.st-Font--header span,
            body[override] td.st-Font.st-Font--header a {
                font-size: 24px !important;
                line-height: 32px !important;
            }

            body[override] td.st-Font.st-Font--body,
            body[override] td.st-Font.st-Font--body span,
            body[override] td.st-Font.st-Font--body a {
                font-size: 18px !important;
                line-height: 28px !important;
            }

            body[override] td.st-Font.st-Font--caption,
            body[override] td.st-Font.st-Font--caption span,
            body[override] td.st-Font.st-Font--caption a {
                font-size: 14px !important;
                line-height: 20px !important;
            }

            body[override] table.st-Header td.st-Header-background div.st-Header-area {
                margin: 0 auto !important;
                width: auto !important;
                background-position: 0 0 !important;
            }

            body[override] table.st-Header.st-Header--simplified td.st-Header-logo {
                width: auto !important;
            }

            body[override] table.st-Header.st-Header--simplified td.st-Header-spacing {
                width: 0 !important;
            }

            body[override] table.st-Divider td.st-Spacer.st-Spacer--gutter,
            body[override] tr.st-Divider td.st-Spacer.st-Spacer--gutter {
                background-color: #e6ebf1;
            }

            body[override] table.st-Blocks table.st-Blocks-inner {
                border-radius: 0 !important;
            }

            body[override] table.st-Blocks table.st-Blocks-inner table.st-Blocks-item td.st-Blocks-item-cell {
                display: block !important;
            }

            body[override] table.st-Hero-Container td.st-Spacer--divider {
                display: none !important;
                margin: 0 !important;
                max-height: 0 !important;
                min-height: 0 !important;
                padding: 0 !important;
                font-size: 0 !important;
                line-height: 0 !important;
            }

            body[override] table.st-Hero-Responsive {
                margin: 16px auto !important;
            }

            body[override] table.st-Button {
                margin: 0 auto !important;
                width: 100% !important;
            }

            body[override] table.st-Button td.st-Button-area,
            body[override] table.st-Button td.st-Button-area a.st-Button-link,
            body[override] table.st-Button td.st-Button-area span.st-Button-internal {
                height: 44px !important;
                line-height: 44px !important;
                font-size: 18px !important;
                vertical-align: middle !important;
            }
        }

        @media (-webkit-min-device-pixel-ratio: 1.25),
        (min-resolution: 120dpi),
        all and (max-width: 768px) {
            body[override] div.st-Target.st-Target--mobile img {
                display: none !important;
                margin: 0 !important;
                max-height: 0 !important;
                min-height: 0 !important;
                padding: 0 !important;
                font-size: 0 !important;
                line-height: 0 !important;
            }
        }
    </style>
</head>

<body class="st-Email" bgcolor="#f6f9fc"
    style="border: 0; margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; min-width: 100%; width: 100%;"
    override="fix">
    <table class="st-Background" bgcolor="#f6f9fc" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="border: 0; margin: 0; padding: 0;">
        <tbody>
            <tr>
                <td style="border: 0; margin: 0; padding: 0;">
                    <table class="st-Wrapper" align="center" bgcolor="#ffffff" border="0" cellpadding="0"
                        cellspacing="0" width="600"
                        style="border-bottom-left-radius: 5px; border-bottom-right-radius: 5px; margin: 0 auto; min-width: 600px;">
                        <tbody>
                            <tr>
                                <td style="border: 0; margin: 0; padding: 0;">
                                    <div style="background-color:#f6f9fc; padding-top:40px;"><br /></div>
                                    <table class="Section Divider Divider--small" width="100%"
                                        style="border: 0;border-collapse: collapse;margin: 0;padding: 0;background-color: #ffffff;">
                                        <tbody>
                                            <tr>
                                                <td class="Spacer Spacer--divider" height="40"
                                                    style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;color: #ffffff;font-size: 1px;line-height: 1px;">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="st-Copy st-Copy--caption st-Width st-Width--mobile" border="0"
                                        cellpadding="0" cellspacing="0" width="600" style="min-width: 600px;"><br />
                                        <tbody>
                                            <tr>
                                                <td class="Content Title-copy Font Font--title" align="center"
                                                    style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;width: 472px;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif;vertical-align: middle;color: #32325d;font-size: 24px;line-height: 32px;">
                                                    {$title}</td>
                                            </tr>
                                            <tr>
                                                <td class="st-Spacer st-Spacer--stacked" colspan="3" height="12"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; ">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="st-Spacer st-Spacer--standalone st-Width st-Width--mobile" border="0"
                                        cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td height="20"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; ">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="st-Copy st-Width st-Width--mobile" border="0" cellpadding="0"
                                        cellspacing="0" width="600" style="min-width: 600px;">
                                        <tbody>
                                            <tr>
                                                <td class="st-Spacer st-Spacer--gutter"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; "
                                                    width="64">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                                <td
                                                    style="border: 0; margin: 0; padding: 0; color: #525F7f !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif; font-size: 16px; line-height: 24px;">
                                                    {$content}</td>
                                                <td class="st-Spacer st-Spacer--gutter"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; "
                                                    width="64">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="st-Spacer st-Spacer--stacked" colspan="3" height="12"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; ">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="st-Divider st-Width st-Width--mobile" border="0" cellpadding="0"
                                        cellspacing="0" width="600" style="min-width: 600px;">
                                        <tbody>
                                            <tr>
                                                <td class="st-Spacer st-Spacer--divider" colspan="3" height="20"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; ">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="st-Spacer st-Spacer--gutter"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; "
                                                    width="64">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                                <td bgcolor="#e6ebf1" height="1"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; ">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                                <td class="st-Spacer st-Spacer--gutter"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; "
                                                    width="64">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="st-Spacer st-Spacer--divider" colspan="3" height="31"
                                                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; ">
                                                    <div class="st-Spacer st-Spacer--filler"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="Section Copy"
                                        style="border: 0;border-collapse: collapse;margin: 0;padding: 0;background-color: #ffffff;">
                                        <tbody>
                                            <tr>
                                                <td class="Spacer Spacer--gutter" width="64"
                                                    style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;color: #ffffff;font-size: 1px;line-height: 1px;">
                                                </td>
                                                <td class="Content Footer-legal Font Font--caption Font--mute"
                                                    style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;width: 472px;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif;vertical-align: middle;color: #8898aa;font-size: 12px;line-height: 16px;">
                                                    {$concluding_remarks}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="Section Divider Divider--small" width="100%"
                                        style="border: 0;border-collapse: collapse;margin: 0;padding: 0;background-color: #ffffff;">
                                        <tbody>
                                            <tr>
                                                <td class="Spacer Spacer--divider" height="40"
                                                    style="border: 0;border-collapse: collapse;margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;color: #ffffff;font-size: 1px;line-height: 1px;">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="st-Spacer st-Spacer--emailEnd" height="64"
                    style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; ">
                    <div class="st-Spacer st-Spacer--filler">&nbsp;</div>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>