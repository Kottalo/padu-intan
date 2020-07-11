function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

var projectMultiSelectConfig = {
  selectAllText: '全部',
  enableFiltering: true,
  disableIfEmpty: true,
  buttonWidth: '100%',
  includeSelectAllOption: true,
  buttonText: function(options, select) {
    if (options.length === 0)
    {
      return '选择工程...';
    }
    else if (options.length > 0)
    {
      var array = [];
      for (i=0;i<options.length;i++)
      {
        array.push(options[i].innerText);
      }

      return array.join(', ');
    }
    else
    {
       var labels = [];
       options.each(function()
       {
         if ($(this).attr('label') !== undefined)
         {
           labels.push($(this).attr('label'));
         }
         else {
           labels.push($(this).html());
         }
       });

       return labels.join(', ') + '';
       }
     }
  };

var supplierMultiSelectConfig = {
  selectAllText: '全部',
  enableFiltering: true,
  disableIfEmpty: true,
  buttonWidth: '100%',
  includeSelectAllOption: true,
  buttonText: function(options, select) {
  if (options.length === 0)
  {
    return '选择材料供应商...';
  }
  else if (options.length > 0)
  {
    var array = [];
    for (i=0;i<options.length;i++)
    {
      array.push(options[i].innerText);
    }

    return array.join(', ');
  }
  else
  {
     var labels = [];
     options.each(function()
     {
       if ($(this).attr('label') !== undefined)
       {
         labels.push($(this).attr('label'));
       }
       else {
         labels.push($(this).html());
       }
     });

     return labels.join(', ') + '';
     }
   }
};
