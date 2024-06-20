var RogerForm = {
  id: '',
  fields: [],
  initModal: function(options) {
    $('#modal-body').html(this.getForm(options));

    options.fields.forEach(o => this.addField(o, options));

    if (options.hasOwnProperty('populate')) {
      this.populate(options);
    }
    $('#modal-body').append('<p class="txt-danger hidden" id="modal-form-error"></p>')
    this.addSubmitButton({ method: options.method });
    $('#modal').modal('show');
  },

  getForm: function(options) {
    if (options.hasOwnProperty('id'))
      this.id = options.id;
    else
      this.id = Math.floor(Math.random() * 1000);

    return `<form id="${this.id}" method="${options.method}" action="${options.action}"></form>`;
  },

  populate: function(options) {
    let formId = this.id;
    $.ajax({
      url: options.action,
      method: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/ld+json' },
      success: function(data) {
        
        for (const [key, value] of Object.entries(data)) {
          try {
            $('#' + formId + '_' + key).get(0).initValue = function() {
              $(this).val(value).change();
            };
            $('#' + formId + '_' + key).get(0).initValue();
          } catch(e) {}
        }
      }
    });
  },

  addField: function(options, formOptions) {
    var fieldId = this.id + '_' + options.name;
    
    if (!options.hasOwnProperty('label'))
      options.label = options.name;

    if (!options.hasOwnProperty('value'))
      options.value = '';

    if (!options.hasOwnProperty('type'))
      options.type = 'text';

    if (options.type === 'text') {
      input = document.createElement("input");
      input.setAttribute('value', options.value);
      input.setAttribute('class', 'form-control');
    }
    if (options.type === 'select') {
      input = document.createElement('select');
      input.setAttribute('class', 'form-control form-select');

      input.addOption = function(o) {
        let option = document.createElement('option');
        option.setAttribute('value', o.value);
        if (o.hasOwnProperty('attributes')) {
          for (const [attr, value] of Object.entries(o.attributes)) {
            option.setAttribute(attr, value);
          }
        }
        option.appendChild(document.createTextNode(o.label));
        this.appendChild(option);
      }

      input.removeOptions = function() {
          this.innerHTML = ''
      }

      input.setOptions = function(a) {
        input.removeOptions();
        a.forEach(o => addOption(o));
      }

      input.addOption({ value: '', label: '---'});
    }
    if (options.type === 'multicheckbox') {
      // let input = document.createElement('div');
      let input = '';
      options.options.forEach((e, i) => {

        // let check = document.createElement('div');
        // check.setAttribute('class', 'form-check');
        // let checkInput = document.createElement('INPUT');
        // checkInput.setAttribute('class', 'form-check-input');
        // checkInput.setAttribute('type', 'checkbox');
        // checkInput.setAttribute('name', options.name);
        // checkInput.setAttribute('value', e.value);
        // checkInput.setAttribute('id', `${fieldId}_${i}`);
        // checkInput.setAttribute('data-type', 'array');
        // let checkLabel = document.createElement('label');
        // checkLabel.setAttribute('class', 'form-check-label');
        // checkLabel.setAttribute('for', `${fieldId}_${i}`);
        // checkLabel.appendChild(document.createTextNode(e.label));
        // check.appendChild(checkInput);
        // check.appendChild(checkLabel);
        // input.appendChild(check);

        input += `
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="${options.name}" value="${e.value}" id="${fieldId}_${i}" data-type="array">
            <label class="form-check-label" for="${fieldId}_${i}">${e.label}</label>
          </div>
        `;
      });
      this.addInputText(fieldId, options.label, input);
      return;
    }

    input.setAttribute('name', options.name);
    input.setAttribute('id', fieldId);

    if (options.hasOwnProperty('events')) {
      $.each(options.events, function(e, f) {
        input.addEventListener(e, f);
      });
    }

    if (!options.hasOwnProperty('disabled')) {
      if (formOptions.method === 'DELETE') {
        input.setAttribute("disabled", true);
        // input.setAttribute("readonly", true);
      }
    } else if (options.disabled === true) {
      input.setAttribute("disabled", true);
      // input.setAttribute("readonly", true);
    }

    if (options.hasOwnProperty('draw')) {
      eval(options.draw)(input, this);
    }
    this.addInputElement(fieldId, options.label, input);

  },

  addInputText: function (fieldId, label, input) {
    $('#' + this.id).append(`
      <div class="form-group">
        <label for="${fieldId}">${label}</label>
        ${input}
      </div>  
    `);
  },

  addInputElement: function(fieldId, label, input) {
    let group = document.createElement('div');
    group.setAttribute('class', 'form-group');
    let groupLabel = document.createElement('label');
    groupLabel.setAttribute('for', fieldId);
    groupLabel.appendChild(document.createTextNode(label));
    group.appendChild(groupLabel);
    group.appendChild(input);

    $('#' + this.id).append(group);
  },

  addSubmitButton: function(options) {
    if (options.method === 'DELETE') {
      $('#modal-save-btn').addClass('hidden');
      $('#modal-remove-btn').removeClass('hidden');
      $('#modal-remove-btn').attr('onClick', 'RogerForm.submit({formId: "'+this.id+'"});');
    } else {
      $('#modal-remove-btn').addClass('hidden');
      $('#modal-save-btn').removeClass('hidden');
      $('#modal-save-btn').attr('onClick', 'RogerForm.submit({formId: "'+this.id+'"});');
    }
  },

  submit: function(options) {
    if (options.hasOwnProperty('formId')) {
      var form = document.getElementById(options.formId);
      var formData = new FormData(form);

      var data = {};

      formData.forEach(function(value, key){
        let e = form.querySelector('[name='+key+']');
        if (e.getAttribute('data-type') == 'array') {
          data[key] = formData.getAll(key);
        } else {
          data[key] = value;  
        }
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

    if (!options.hasOwnProperty('success')) {
      options.success = function(data) {
        $('#modal').modal('hide');
        $('.table').DataTable().draw();
      };
    }

    if (!options.hasOwnProperty('error')) {
      options.error = function(data) {
        $('#modal-form-error').html('Unexpected error:' + data.responseJSON['hydra:description']);
        $('#modal-form-error').removeClass('hidden');
      }
    }

    $.ajax({
      url: options.url,
      method: options.method,
      data: JSON.stringify(options.data),
      dataType: 'json',
      headers: options.headers,
      success: options.success,
      error: options.error,
    });

  },
}
