@component('mail::message')
# {{ __('Task export failed') }}

{{ __('We apologize, but your task export request could not be completed.') }}

{{ __('Please try again later or contact support if the problem persists.') }}

{{ __('Reference ID: :batchId', ['batchId' => $batchId]) }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent 