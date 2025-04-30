@component('mail::message')
# {{ __('Your task export is ready') }}

{{ __('Your task export has been completed and is ready for download.') }}

@component('mail::button', ['url' => $downloadUrl])
{{ __('Download Export') }}
@endcomponent

{{ __('The file will be available for download for the next 24 hours.') }}

{{ __('If you did not request this export, please ignore this email.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent 