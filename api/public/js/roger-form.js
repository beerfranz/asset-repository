var RogerForm = {
  id: '',
  initModal: function(options) {

    if (options.hasOwnProperty('id'))
      this.id = options.id;
    else
      this.id = Math.floor(Math.random() * 1000);

    $('#modal-body').html(`<form id="${this.id}" method="${options.method}" action="${options.action}"></form>`);

    options.fields.forEach(o => this.addField(o));
    $('#modal-body').append('<p class="txt-danger hidden" id="modal-form-error"></p>')
    this.addSubmitButton();
    $('#modal').modal('show');
  },

  addField: function(options) {
    var fieldId = this.id + '_' + options.name;
    
    if (!options.hasOwnProperty('label'))
      options.label = options.name;

    $('#' + this.id).append(`
      <div class="form-group">
        <label for="${fieldId}">${options.label}</label>
        <input type="text" name="${options.name}" value="${options.value}" id="${fieldId}" class="form-control" />
      </div>  
    `);
  },

  addSubmitButton: function() {
    $('#modal-save-btn').removeClass('hidden');
    $('#modal-save-btn').attr('onClick', 'RogerForm.submit({formId: "'+this.id+'"});');
  },

  submit: function(options) {
    if (options.hasOwnProperty('formId')) {
      var formData = new FormData(document.getElementById(options.formId));

      var data = {};

      formData.forEach(function(value, key){
        data[key] = value;
      });

      options.data = data;

      var form = $('#' + options.formId);
      options.url = form.attr('action');
      options.method = form.attr('method');
    }

    if (!options.hasOwnProperty('method'))
      options.method = 'POST';

    options.headers = { 'Accept': 'application/ld+json' };

    if (options.method === 'PATCH')
      options.headers['Content-Type'] = 'application/merge-patch+json';
    else
      options.headers['Content-Type'] = 'application/ld+json';

    $.ajax({
      url: options.url,
      method: options.method,
      data: JSON.stringify(options.data),
      dataType: 'json',
      headers: options.headers,
      success: function(data) {
        $('#modal').modal('hide');
        $('#values').DataTable().draw();
      },
      error: function(data) {
        $('#modal-form-error').html('Unexpected error');
        $('#modal-form-error').removeClass('hidden');
      }
    });

  },
}
