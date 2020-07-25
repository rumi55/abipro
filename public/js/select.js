function loadSelect(){
    $(".select2").select2({theme: 'bootstrap4', allowClear:true, placeholder:'Select'});
    $(".select2").each(function(){
        var val = $(this).attr('data-value');
        if(val==undefined){
            val = '';
        }
        if($(this).prop('multiple')){
            console.log(val)
            val = val.split(',');
        }
        $(this).val(val);
        $(this).trigger('change');
    })
    $(".select2").change(function(){
        $(this).attr('data-value', $(this).val())
    })
    $(".account").select2({
        theme: 'bootstrap4',
        placeholder: 'Select account',
        minimumInputLength: 2,
        ajax: {
        url: BASE_URL+'/json/accounts',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term,
            filter:{
                has_children: 0
            }
          };
        },
        processResults: function (res) {
          return {
            results:  $.map(res.data, function (item) {
              return {
                text: '('+item.account_no+') '+item.account_name,
                account_parent_name: item.account_parent_name,
                account_type_name: item.account_type.name,
                account_name: item.account_name,
                account_no: item.account_no,
                id: item.id
              }
            })
          };
        },
        cache: true
        },
        templateResult: function(res){
            if (!res.id) {
                return res.text;
            }
            if(res.loading){
                return $('<i class="fas fa-loading fa-spin"></i> Searching...')
            }
            return $(`
            <div class="row">
                <div class="col">${res.account_no}</div>
                <div class="col text-right">${res.account_name}</div>
            </div>
            <div class="row">
                <div class="col">${res.account_parent_name==null?'':res.account_parent_name}</div>
                <div class="col text-right">${res.account_type_name}</div>
            </div>
            `);
        },
      })
    $('.account').each(function(){
        var val = $(this).attr('data-value');
        var opt = $(this);
        $.ajax({
            url:BASE_URL+'/json/accounts',
            data: {filter:{id:val}}
        }).then(function (res) {
            if(res.data.length==1){
                var item = res.data[0];
                var option = new Option('('+item.account_no+') '+item.account_name, item.id, true, true);
                opt.append(option).trigger('change');
                opt.trigger({
                    type: 'select2:select',
                    params: {
                        data:{
                            id: item.id,
                            text: item.account_name,
                            account_parent_name: item.account_parent_name,
                            account_type_name: item.account_type.name,
                            account_name: item.account_name,
                            account_no: item.account_no
                        }
                    }
                });
            }
        });
    })
    $(".contact").select2({
        theme: 'bootstrap4',
        placeholder: 'Select contact',
        minimumInputLength: 2,
        ajax: {
        url: BASE_URL+'/json/contacts',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term
          };
        },
        processResults: function (res) {
          return {
            results:  $.map(res.data, function (item) {
              return {
                text: item.name,
                custom_id: item.custom_id,
                name: item.name,
                email: item.email,
                address: item.address,
                type: item.type,
                id: item.id
              }
            })
          };
        },
        cache: true
        },
        templateResult: function(res){
            if (!res.id) {
                return res.text;
            }
            if(res.loading){
                return $('<i class="fas fa-loading fa-spin"></i> Searching...')
            }
            return $(`
            <div class="row">
                <div class="col">${res.name}</div>
                <div class="col text-right">${res.custom_id}</div>
            </div>
            <div class="row">
                <div class="col">${res.email==null?'':res.address}</div>
                <div class="col text-right">${res.type}</div>
            </div>
            `);
        },
      })
    $('.contact').each(function(){
        var val = $(this).attr('data-value');
        var opt = $(this);
        $.ajax({
            url:BASE_URL+'/json/contacts',
            data: {filter:{id:val}}
        }).then(function (res) {
            if(res.data.length==1){
                var item = res.data[0];
                var option = new Option(item.name, item.id, true, true);
                opt.append(option).trigger('change');
                opt.trigger({
                    type: 'select2:select',
                    params: {
                        data:{
                            text: item.name,
                            custom_id: item.custom_id,
                            name: item.name,
                            email: item.email,
                            address: item.address,
                            type: item.type,
                            id: item.id
                        }
                    }
                });
            }
        });
    })
}
