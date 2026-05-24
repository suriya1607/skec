@component('mail::message')
# New Contact Form Submission

## Sender Information
- **Name:** {{ $contactData['name'] }}
- **Email:** [{{ $contactData['email'] }}](mailto:{{ $contactData['email'] }})
@if($contactData['phone'] ?? null)
- **Phone:** {{ $contactData['phone'] }}
@endif
- **Subject:** {{ ucfirst(str_replace('_', ' ', $contactData['subject'])) }}

## Message

{!! nl2br(e($contactData['message'])) !!}

---

You can reply directly to this email to contact {{ $contactData['name'] }}.

Thanks,  
{{ config('app.name') }}
@endcomponent
