function commonDataTableOpts() {
  return {
    // stateSave: true,
    dom: 'frtlip',
    processing: true,
    serverSide: true,
    lengthMenu: [ [ 10, 25, 50, 100], [ 10, 25, 50, 100] ],
    searching: false,
    ajax: {
      headers: { 'Accept': "application/ld+json" },
      data: function(d) {
        var url = new URL(window.location);
        var urlParams = url.searchParams;
        urlParams.forEach((value, key) => {
          urlParams.delete(key);
        });

        var params = {
          itemsPerPage: d.length,
          page: (d.start / d.length) + 1,
          order: {}
        };
        var order_key = d.columns[d.order[0].column].data;
        params.order[order_key] = d.order[0].dir.toUpperCase();

        // Filters
        $('.dataTableFilters').serializeArray().forEach(filter => {
          // console.log(filter);

          if (filter.value != '') {
            urlParams.set(filter.name, filter.value);
            params[filter.name] = filter.value;
          }
          
          // If multiple values:
          // urlParams.append('id', '101');
          // urlParams.append('id', '102');
        });

        window.history.pushState({ additionalInformation: 'Updated the URL with JS' }, 'JS filter', url);

        return params;
      },
      dataFilter: function(data) {
        var json = jQuery.parseJSON( data );
        json.recordsTotal = json['hydra:totalItems'];;
        json.recordsFiltered = json['hydra:totalItems'];;
        json.data = json['hydra:member'];
        return JSON.stringify( json );
      }
    },
  };
}

function rogerInitFilters(filterId, dataTableId) {
  var form = $('#' + filterId);

  // Prefill filters
  var url = new URL(window.location);
  var urlParams = url.searchParams;

  $('#' + filterId + ' :input').each(function() {
    var val = '';
    var elementName = $(this).attr('name');
    var elementNamePartial = elementName + '_partial';

    if (val = urlParams.get($(this).attr('name')))
      $(this).val(val);

    if (val = urlParams.get($(this).attr('name') + '_partial')) {
      $(this).val(val);
      $(this).attr('name', $(this).attr('name') + '_partial');
    }
  });

  form.append('<button type="submit" class="btn btn-primary">Submit</button>');
  form.append('<button type="button" class="btn btn-secondary form-clear">Clear</button>');

  // mmmmhh... faire un input caché pour savoir si c'est recherche partial ou exact
  // https://api-platform.com/docs/core/filters/#creating-custom-filters
  $('.autocomplete').each(function() {
    var element = $(this);
    var url = element.attr('autocomplete-source');
    var elementName = element.attr('name');
    var filterName = element.attr('autocomplete-name');
    var group = element.attr('autocomplete-group');

    const regexp = /^(.*)\.(.*)$/g;
    var found = [...filterName.matchAll(regexp)];

    element.autocomplete({
      minLength: 3,
      source: function(request, response) {
        var q = request.term;
        var filters = {};
        filters[filterName + '_partial'] = q;
        filters['groups'] = [ group ];
        $.ajax({
          url: url,
          dataType: 'json',
          headers: { 'Accept': "application/ld+json" },
          data: filters,
          success: function(data) {
            if (responseField = element.attr('autocomplete-response-field')) {
              if (responseField.split('.').length == 2 ) {
                item = responseField.split('.');
                response(data['hydra:member'].map(x => x[item[0]][item[1]]));
              } else {
                response(data['hydra:member'].map(x => x[responseField]));
              }
            }
            else if (found.length === 0) {
              response(data['hydra:member'].map(x => x[filterName] ));
            } else {
              response(data['hydra:member'].map(x => x[found[0][1]][found[0][2]] ));
            }
            
          }
        });
      },
      create: function(event, ui) {
        if (element.val() == '')
          element.attr('name', elementName + '_partial');
      },
      change: function( event, ui ) {
        element.attr('name', elementName + '_partial');
      },
      select: function( event, ui ) {
        element.attr('name', elementName);
        element.val(ui.item.value);
        element.parents('form').submit();
      }
    });
  });



  form.on('submit', function(e) {
    e.preventDefault();
    $('#' + dataTableId).DataTable().draw();
  });
}

function rogerInit(id, dataTableOpts) {
  var filter_id = id + '-filters';

  rogerInitFilters(filter_id, id);
  $('#' + id).DataTable(dataTableOpts);
  
}

$('.dropdown').hover(function(){ 
  $('.dropdown-toggle', this).trigger('click'); 
});

$(document).on('click', '.form-clear', function(e){
  var form = $(this).parents('form');
  form[0].reset();
  form.submit();
});
