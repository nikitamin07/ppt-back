{{-- Шаблон письма рассылки: шапка и подпись — по фирменному бланку, тело ({!! $body !!}) — HTML из админки. --}}
@php($siteUrl = rtrim(config('app.url'), '/'))
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f2f0ea;">
    <div style="max-width:640px; margin:0 auto; padding:24px; font-family:Arial, Helvetica, sans-serif; color:#1b1b18; font-size:15px; line-height:1.55;">

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
            <tr>
                <td style="width:100px; vertical-align:top; padding-right:16px;">
                    <a href="{{ $siteUrl }}">
                        <img src="{{ $siteUrl }}/img/logo_black.png" width="84" alt="ППТ.бел" style="display:block; width:84px; height:auto; border:0;">
                    </a>
                </td>
                <td style="vertical-align:top; font-size:13px; line-height:1.5;">
                    <div style="font-weight:700; margin-bottom:4px;">Частное предприятие «РешениеСтройДизайн»</div>
                    <div>Адрес: 213817 Могилевская область, г. Бобруйск, ул. Ванцетти, д. 4, комната 3</div>
                    <div>Р/с: BY 71 ALFA 3012 2638 2100 1027 0000 в ЗАО «АЛЬФА-БАНК» 220013 г. Минск, ул. Сурганова 43-47. Код: ALFABY2X</div>
                    <div>УНП: 791217541</div>
                    <div>Официальный сайт: <a href="{{ $siteUrl }}" style="color:#1b1b18;">https://ппт.бел</a></div>
                    <div>E-mail: <a href="mailto:7206856@mail.ru" style="color:#1b1b18;">7206856@mail.ru</a></div>
                    <div>Телефон: +375 (29) 69-18-417</div>
                </td>
            </tr>
        </table>
        <hr style="border:none; border-top:2px solid #1b1b18; margin:0 0 24px;">

        <div>{!! $body !!}</div>

        <div style="margin-top:32px; font-size:14px; line-height:1.6;">
            <ul style="padding-left:20px; margin:0 0 16px;">
                <li>Весь материал полностью соответствует СТБ, ГОСТ, ТУ;</li>
                <li>На каждую партию товара выдается Сертификат и паспорт качества РБ;</li>
                <li>Условия оплаты: предоплата, частичная предоплата, возможна отсрочка платежа;</li>
                <li>Осуществляем доставку по всей Республики Беларусь;</li>
                <li>Работаем по постановлению МАиС №125 от 19.12.2023 г.</li>
            </ul>
            <p style="margin:0 0 24px;">Частное предприятие «РешениеСтройДизайн» является эксклюзивным дилером заводов-производителей строительных материалов, что позволяет в короткие сроки поставить качественный материал своим партнерам напрямую по низким ценам. Политика компании направлена прежде всего на долгосрочное сотрудничество и уважение Бизнеса своих Партнеров.</p>
            <div style="text-align:right;">
                <div style="font-style:italic; font-weight:700;">С уважением, Директор Минчуков С.А.</div>
                <div style="color:#373737; font-weight:700;">+375 (29) 69-18-417</div>
                <div><a href="mailto:7206856@mail.ru" style="color:#373737; font-weight:700; text-decoration:underline;">7206856@mail.ru</a></div>
            </div>
        </div>
    </div>
</body>
</html>
