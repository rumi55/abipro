@php
$template = report_template('header');
@endphp
@empty($template)

@else
@endif
{!! $template !!}
