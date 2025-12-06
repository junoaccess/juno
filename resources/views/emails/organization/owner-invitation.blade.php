<x-mail::message>
# Welcome to {{ $organizationName }}!

Hello {{ $ownerName }},

Great news! You've been made the **Owner** of **{{ $organizationName }}** on {{ config('app.name') }}.

As the owner, you have full access to manage your organization, including:

- **Managing users and teams**
- **Creating and assigning roles**
- **Setting up permissions**
- **Inviting new members**
- **Configuring organization settings**

To get started, please set up your account by clicking the button below:

<x-mail::button :url="$loginUrl">
Set Up Your Account
</x-mail::button>

This link will expire in 7 days for security reasons.

**Your Details:**
- **Email:** {{ $ownerEmail }}
- **Organization:** {{ $organizationName }}
- **Role:** Owner (Full Access)

If you have any questions or need assistance, please don't hesitate to reach out to our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
