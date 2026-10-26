<x-mail::message>
# Hola, {{ $name }}

Recibimos una solicitud para restablecer la contraseña de tu cuenta en **Ollinfit**. Usa este código para continuar:

<x-mail::panel>
<div style="text-align:center;font-size:32px;font-weight:800;letter-spacing:10px;color:#f97316;">{{ $code }}</div>
</x-mail::panel>

El código vence en **{{ $expiresMinutes }} minutos** y solo puede usarse una vez.

Si tú no solicitaste este cambio, puedes ignorar este correo: tu contraseña seguirá siendo la misma.

Saludos,<br>
El equipo de Ollinfit
</x-mail::message>
