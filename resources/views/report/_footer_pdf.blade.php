@php
$template = report_template('footer');
@endphp
<footer>
@empty($template)
    <span class="pagenum"></span>
@else
{!! $template !!}
@endif
</footer>
