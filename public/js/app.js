function select2Load(selector, url, data={}){
  $.ajax({
    url: url,
    dataType: 'json',
    data: data,
    success: function(res){
      $(selector).select2({theme: 'bootstrap4',data:res});
      var val = $(selector).attr('data-selected');
      if($(selector).prop('multiple') && val!=""){
        $(selector).val(val.split(','));
      }else{
        $(selector).val(val);
      }
      $(selector).trigger('change');
    }
  })
}