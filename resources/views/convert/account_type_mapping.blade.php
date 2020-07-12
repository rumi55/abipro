<form action="{{route('convert.account_type_mapping.save')}}" method="POST" >
@csrf 
@method('PUT')
    <div class="card-body">    
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>{{__('Account No.')}}</th>
                        <th>{{__('Account Name')}}</th>
                        <th>{{__('Account Type')}}</th>
                    </tr>
                </thead>
                <tbody>
                @if(count($accounts)==0)
                    <tr><td class="text-center" colspan="5">
                    {{__('No account available.')}}
                    <div class="mt-5">
                    <a href="{{route('accounts.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('New Account')}}</a>
                    <a href="{{route('accounts.import')}}" class="btn btn-primary" ><i class="fas fa-upload"></i> {{__('Import Account')}}</a>
                    </div>
                    </td></tr>
                @endif
                @foreach($accounts as $account)
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-tree-level="{{$account->tree_level}}" data-has-children="{{$account->has_children}}" data-parent="{{$account->account_parent_id}}">
                        <td>{{$account->account_no}}</td>
                        <td>{{tt($account,'account_name')}}</td>
                        <td>
                            <select data-value="{{$account->account_type_id}}" style="width:100%" class="form-control account-type-select select2" name="account_type_id[{{$account->id}}]">
                            </select>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-right">
        <button class="btn btn-primary">{{__('Save')}} <i class="fas fa-arrow-right"></i></button>
    </div>
</form>

@push('css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(function () {
    $.ajax({
        url: "{{route('json.output', 'account_types')}}",
        dataType: 'json',
        success: function(res){
            var data = $.map(res, function (item) {
                return {
                    id: item.id,
                    text: item.name
                }
            })
            $('.account-type-select').each(function(e){
                $(this).val(null).empty();
            });
            $('.select2').select2({
                theme: 'bootstrap4',
                data:data, 
                placeholder: "{{__('Select Account Type')}}"
            });
            $('.account-type-select').each(function(e){
                var val = $(this).attr('data-value');
                $(this).val(val);
                $(this).trigger('change');
            });
        }
    });
    
})

</script>
@endpush