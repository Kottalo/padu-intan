function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  };
}(jQuery));

$(document).ready(function() {
  $(".pos-number").inputFilter(function(value) {
    return /^[0-9]*$/.test(value);    // Allow digits only, using a RegExp
  });

  $(".pos-number-dot").inputFilter(function(value) {
    return /^[0-9]*\.?[0-9]*$/.test(value);    // Allow digits only, using a RegExp
  });

  $(".neg-number").inputFilter(function(value) {
    return /^-?[0-9]*$/.test(value);    // Allow digits only, using a RegExp
  });

  $(".neg-number-dot").inputFilter(function(value) {
    return /^-?[0-9]*\.?[0-9]*$/.test(value);    // Allow digits only, using a RegExp
  });
});

var projectMultiSelectConfig = {
  selectAllText: '全部',
  enableFiltering: true,
  disableIfEmpty: true,
  buttonWidth: '100%',
  includeSelectAllOption: true,
  buttonText: function(options, select) {
    var selectedIds = Object.keys(select[0].selectedOptions).map(function(value) {
      return select[0].selectedOptions[value].value
    })

    axios.post('/suppliers/getSuppliersByProjectIds', {projectIds: selectedIds})
    .then(function(res) {
      $('#supplierIds').find('option').remove();

      res.data.forEach(function(value) {
        var o = new Option(value.name, value.id);

        $("#supplierIds").append(o);
      });
      $('#supplierIds').multiselect('rebuild');
      $('#supplierIds').multiselect('select', supplierIds);
    });

    if (options.length === 0)
    {
      return '选择工程...';
    }
    else if (options.length > 0 && options.length < select[0].options.length)
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
       return "全部工程";
    }
  }
}

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
    else if (options.length > 0 && options.length < select[0].options.length)
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
       return "全部供应商";
    }
  }
};
