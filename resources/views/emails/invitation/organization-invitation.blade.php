@component('mail::message')
# You've been invited to {{ $organizationName }}

Hello {{ $invitationName }},

{{ $inviterName }} has invited you to join **{{ $organizationName }}** as {{ $roles }}.

@component('mail::button', ['url' => $acceptUrl])
Accept Invitation
@endcomponent

This invitation will expire on {{ $expiresAt }}.

If you have any questions, please contact your organization administrator.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
