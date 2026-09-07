{{-- Шаблон письма рассылки. Тело ({!! $body !!}) — HTML из админки.
     Ниже — подпись. Полный шаблон (шапка, контакты и т.п.) заказчик доработает сам. --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f2f0ea;">
    <div style="max-width:640px; margin:0 auto; padding:24px; font-family:Arial, Helvetica, sans-serif; color:#1b1b18; font-size:15px; line-height:1.55;">
        <div>{!! $body !!}</div>

        <div style="margin-top:32px; text-align:right; color:#414141;">
            <i>С уважением,<br>
            управляющий ЧТУП «РешениеСтройДизайн»<br>
            Минчуков Сергей Александрович</i>
        </div>
    </div>
</body>
</html>
