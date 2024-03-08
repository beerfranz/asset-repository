// Define icons
var trash = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>';
var update = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/></svg>';

$(document).ready( function () {

  // Tooltips
  $('[data-toggle="tooltip"]').tooltip()

  // DataTable
  if ($('.dataTable').hasClass('order-desc'))
  {
    var dataTable_order = [[ 0, 'desc' ]];
  } else {
    var dataTable_order = [[ 0, 'asc' ]];
  }
  $('.dataTable').DataTable({
    order: dataTable_order
  });

});

$(document).on('change', '.filter-submit-change', function(e) {
  var form = $(this).parents('form');
  form.submit();
  /*url = window.location;
  window.location = window.location + '?resource_name=backend';*/
});

$(document).on('click', '.add', function(e) {
  var form_url = $(this).attr('data-form');

  $.ajax({
    url: form_url,
    success: function(data) {
      $('#modal-body').html(data);

      // Move title to modal
      $('#modal-body').find('.form-title').appendTo('#modal-header');

      // Move buttons to modal
      $('#modal-body').find('button').prependTo('#modal-footer');
      $('#modal').modal('show');
    }
  });
});

$(document).on('click', '.detail-modal', function(e) {
  var url = $(thisversion).parents('tr').attr('data-detail-url');

  e.preventDefault();

  $.ajax({
    url: url,
    headers: { 'x-expected-partial': 'partial' },
    success: function(data) {
      $('#modal-body').html(data);

      $('#modal').modal('show');
    }
  });
});

$(document).on('click', '#modal-footer button[type=submit]', function(e) {
/*        var action = $('#modal-body').children('form').attr('action');
  var regex = /\{([a-z]*)\}/g;
  $.each(action.match(regex), function (index, value) {
    var label = value.replace('{', '').replace('}', '');
    var form_val = $('#modal-body').children('form').find('input[name=' + label +']').val();
    action = action.replace(value, form_val);
  });
  $('#modal-body').children('form').attr('action', action);
  */
  $('#modal-body').children('form').submit();
});

/**
 * callback populateRow(row) return array of objects.
 *   object:
 *    * value
 *    * type (optional)
 */
function commonApiGet(url, target, populateRow) {
  $.ajax({
    url: url,
    dataType: 'json',
    method: 'GET',
    contentType: 'application/json',
    beforeSend: function() {
      target.empty();
    },
    success: function(data) {
      data.forEach(function(row) {
        content = '<tr>';
        populateRow(row).forEach(e => {
          content += '<td>';
          if (e.link)
            content += '<a href="' + e.link +'">';
          content += e.value 
          if (e.link)
            content += '</a>';
          content += '</td>';
        });
        content += '<td><a class="row-update" data-id="' + row.id + '">' + update + '</a>';
        content += '<a class="row-delete" data-id="' + row.id + '">' + trash + '</a></td>';
        content += '</tr>';
        target.append(content);
      });
    }
  });
}

function renderLabels(data) {
  var labels = [];
  if (data === undefined) {
    return '';
  }
  Object.keys(data).forEach((label) => labels.push(label + '=' + data[label]));

  var result = '';

  labels.forEach(function(d) {
    result += '<span class="badge badge-secondary">' + d + '</span>';
  });
  return result;
}

function renderEnvironments(data) {
  var environments = [];
  var result = '';
  if (data === undefined) {
    return '';
  }
  Object.keys(data).forEach(function(key) {
    value = data[key];

    if (Array.isArray(value) && value.length > 0) {
      value.forEach(e => { environments.push(key + '/' + e) });
    } else {
      environments.push(key);
    }
  });

  environments.forEach(function(d) {
    result += '<span class="badge badge-secondary">' + d + '</span>';
  });
  return result;
}

function renderAsset(data) {
  var render = renderWarningIfEmpty(data);
  if (render === data) {
    return '<a href="/ui/assets/' + data + '">' + data + '</a>';
  } else {
    return render;
  }
}

function renderWarningIfEmpty(data) {
  if (data === undefined || data.length === 0)
    data = '<span class="glyphicon glyphicon-exclamation-sign text-danger" aria-hidden="true" data-toggle="tooltip" title="Undefined"></span>';
  return data;
}

function renderBool(data) {
  if (data === true)
    return '<span class="glyphicon glyphicon-ok-sign text-success" aria-hidden="true"></span>';
  else
    return '<span class="glyphicon glyphicon-remove-sign text-danger" aria-hidden="true"></span>';
}

function renderConformity(data, conformityDetail) {
  classes = conformityDetail === undefined ? 'text-success' : 'text-danger';
  tooltip = conformityDetail === undefined ? '' : 'data-toggle="tooltip" title="' + conformityDetail['assetData'] + '"';
  return '<span class="' + classes + '" ' + tooltip + '>' + data + '</span>';
}

function renderFriendlyName(data) {
  if (data.friendlyName === undefined || data.friendlyName.length === 0) {
    return '';
  } else {
    return ' (' + data.friendlyName + ')';
  }
}
