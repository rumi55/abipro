@if ($message = Session::get('error'))
<script type="text/javascript">
$(function(){
    Swal.fire({
        title: '{{__("Error")}}!',
        text: '{!! $message !!}',
        icon: 'error',
    })
})
</script>
@endif
@if ($message = Session::get('info'))
<script type="text/javascript">
$(function(){
    Swal.fire({
        title: '{{__("Info")}}!',
        text: '{!! $message !!}',
        icon: 'info',
    })
})
</script>
@endif
@if ($message = Session::get('warning'))
<script type="text/javascript">
$(function(){
    Swal.fire({
        title: '{{__("Warning")}}!',
        text: '{!! $message !!}',
        icon: 'warning',
    })
})
</script>
@endif
@if ($message = Session::get('success'))
<script type="text/javascript">
$(function(){
    Swal.fire({
        title: '{{__("Success")}}!',
        text: '{!! $message !!}',
        icon: 'success',
    })
})
</script>
@endif