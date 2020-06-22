<div class="table-responsive">
    <table id="data-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <td>{{$column['label']}}</td>
                @endforeach
            </tr>
        </thead>
    </table>
</div>