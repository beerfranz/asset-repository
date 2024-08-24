
function renderTaskStatus(data) {
  if (data.hasOwnProperty('allowedNextStatuses')) {
    let options = `<option value="${data.status}">${data.status}</option>`;
    options += data.allowedNextStatuses.map(status => `<option value="${status}">${status}</option>`);
    return `<select class="updateTaskStatusOnChange" data-identifier="${data.identifier}" name="status">${options}</select>`;
  } else {
    return renderNullableString(data.status, '-');
  }
}

$(document).on('change', '.updateTaskStatusOnChange', function (e) {
  const status = $(this).val();
  const identifier = $(this).attr('data-identifier');

  $.ajax({ ...commonAjaxOptions(), ...{
    url: `/tasks/${identifier}`,
    method: 'PATCH',
    data: JSON.stringify({
      status: status
    }),
    headers: {
      'Accept': 'application/ld+json',
      'Content-Type': 'application/merge-patch+json'
    },
    success: function(data) {
      $('.table').DataTable().draw();
    }
  }});
});

formFields = [
  { name: 'identifier', label: 'Identifier' },
  { name: 'title', label: 'Title' },
  { name: 'taskType', label: 'Type', type: 'select', draw: getTaskTypeList },
  { name: 'description', label: 'Description', type: 'textarea' },
  { name: 'status', label: 'Status', type: 'select', onPopulate: onPopulateStatus },
  { name: 'owner', label: 'Owner' },
  { name: 'assignedTo', label: 'Assigned To' },
];

function getTaskTypeList(e, form) {
  $.ajax({ ...commonAjaxOptions(), ...{
    url: '/task_types',
    success: function(data) {
      data['hydra:member'].forEach(o => {
        e.addOption({ label: o.identifier, value: o['@id'] });
      });
      if (typeof e.initValue === 'function') {
        e.initValue();
      }
    }
  }});
}

function onPopulateStatus(data) {
  let options = [];
  data.allowedNextStatuses.forEach(s => {
    options.push({ attributes: { label: s, value: s }});
  });
  options.push({ attributes: { label: data.status, value: data.status } });
  this.setOptions(options);
}

function form(id) {
  var method = 'POST';
  var action = '/tasks';
  var fields = formFields;

  RogerForm.initModal({ method: method, action: action, fields: fields });
}

function remove(id) {
  var method = 'DELETE';
  var action = `/tasks/${id}`;

  var fields = [
    { name: 'identifier', label: 'Identifier' }
  ];

  RogerForm.initModal({ method: method, action: action, fields: fields, populate: true });
}

function update(id) {
  var method = 'PUT';
  var action = `/tasks/${id}`;

  var fields = formFields;

  RogerForm.initModal({ method: method, action: action, fields: fields, populate: true });
}