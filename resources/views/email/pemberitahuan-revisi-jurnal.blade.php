<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<?php

$style = [
    /* Layout ------------------------------ */

    'body' => 'margin: 0; padding: 0; width: 100%;',
    'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #FFF;',
    'kontainer' => 'border: 1px solid #EDEFF2;width: auto;max-width: 570px;margin: 0 auto;padding: 5px;',
    /* Masthead ----------------------- */

    'email-body' => 'width: 100%; margin: 0; padding: 0; background-color: #FFF;',
    'email-body_inner' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0;',
    'email-body_cell' => 'padding: 5px;',

    'email-footer' => 'margin: 0 auto; padding: 0; text-align: center;background-color: #EDEFF2',
    'email-footer_cell' => 'color: #AEAEAE; padding: 0px; text-align: center;',

    /* Body ------------------------------ */

    'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
    'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',
    'table' => 'border: 1px solid #EDEDED',
    /* Type ------------------------------ */

    'anchor' => 'color: #e74c3c;text-decoration:none',
    'header-1' => 'margin-top: 0; color: #2F3133; font-size: 16px; font-weight: bold; text-align: left;',
    'paragraph' => 'margin-top: 0; color: #74787E; font-size: 14px; line-height: 1.5em;',
    'paragraph-sub' => 'color: #74787E; font-size: 12px; line-height: 1.5em;',
    'paragraph-center' => 'text-align: center;',

    /* Buttons ------------------------------ */

    'button' => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

    'button--green' => 'background-color: #22BC66;',
    'button--red' => 'background-color: #dc4d2f;',
    'button--blue' => 'background-color: #3869D4;',
    'h5' => 'font-size: 14px;margin: 5px',
    'ol' => 'line-height: 2em;padding-left: 25px'
];
?>

<?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

<body style="{{ $style['body'] }}">
    <div style="{{ $style['kontainer'] }}">
        <table width="100%" cellpadding="0" cellspacing="0" style="{{ $style['table'] }}">
            <tr>
                <td style="{{ $style['email-wrapper'] }}" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- Email Body -->
                        <tr>
                            <td style="{{ $style['email-body'] }}" width="100%">
                                <table style="{{ $style['email-body_inner'] }}" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="{{ $fontFamily }} {{ $style['email-body_cell'] }}">
                                            <p style="border-bottom: 1px solid #74787E"><img src="http://siakad.stienobel-indonesia.ac.id/resources/assets/img/brand-merah-hitam.png"/></p>
                                            <!-- Greeting -->
                                            <h1 style="{{ $style['header-1'] }}">
                                                {{ $header }}
                                            </h1>

                                            <!-- Intro -->
                                            <p style="{{ $style['paragraph'] }}">
                                                Divisi Jurnal STIE Nobel Indonesia telah memeriksa jurnal yang anda kirim dan menyatakan bahwa anda perlu merevisi Jurnal tersebut.
                                            </p>
                                            <p style="{{ $style['paragraph'] }}">
                                                Berikut adalah pesan dari pemeriksa terkait revisi yang perlu anda lakukan:<br>
                                                <?= nl2br($msg) ?>
                                            </p>
                                            <p style="{{ $style['paragraph'] }}">
                                                Setelah selesai merevisi, silahkan upload kembali jurnal anda di http://siakad.stienobel-indonesia.ac.id atau menyetor langsung pada divisi Jurnal.
                                            </p>
                                            <p style="{{ $style['paragraph'] }}">
                                                Kami infokan bahwa Template Jurnal dapat anda temukan di http://e-jurnal.stienobel-indonesia.ac.id/index.php/massaro
                                            </p>
                                            <p style="{{ $style['paragraph'] }}">
                                                Terima kasih, <br>
                                                <b>Divisi Jurnal STIE Nobel Indonesia</b>
                                            </p>

                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td>
                                <table style="{{ $style['email-footer'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="{{ $fontFamily }} {{ $style['email-footer_cell'] }}">
                                            <p style="{{ $style['paragraph-sub'] }}">
                                                &copy; {{ date('Y') }}
                                                SIAKAD STIE Nobel Indonesia
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
