
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

function renderNullableString(data, fallbackContent = '') {
  if (data === undefined)
    return fallbackContent;
  else
    return data;
}

function renderWithStyles(data, styles) {
  return `<span class="${styles}">${data}</span>`;
}

function renderRisk(risk) {
  if (risk === undefined)
    return '-';

  var value = '-';
  var styles = '';

  if (risk.value !== undefined) {
    value = risk.value;
  }

  if (risk.trigger !== undefined) {
    styles = `text-${risk.trigger}`;
  }

  return renderWithStyles(value, styles);
}

function renderIndicatorValues(values) {
  if (values === undefined)
    return '-';

  var result = '<table class="table-striped table table-bordered table-hover table-condensed">';

  result += '<tr>';
  Object.keys(values).forEach(function(key) {
    value = values[key];
    result += '<th>' + value.identifier + '</th>';
  });
  result += '</tr><tr>';

  Object.keys(values).forEach(function(key) {
    value = values[key];

    result += '<td class="bg-' + value.level + '">' + renderIndicatorValue(value.value, { level: value.level, taskIdentifier: value.taskIdentifier, isValidated: value.isValidated, validator: value.validator }) + '</td>';
  });

  result += '</tr></table>';

  return result;
}

function renderIndicatorValue(value, options) {
  let tpl = value;
  if (options.taskIdentifier !== undefined && options.taskIdentifier !== null)
    tpl = '<a href="/tasks/' + options.taskIdentifier + '">' + tpl + '</a>';

  if (options.isValidated === true)
    tpl = tpl + '&nbsp;<span class="glyphicon glyphicon-ok-sign text-success" aria-hidden="true" data-toggle="tooltip" title="Validated by ' + options.validator + '"></span>';
  else
    tpl = tpl + '&nbsp;<span class="glyphicon glyphicon-exclamation-sign text-danger" aria-hidden="true" data-toggle="tooltip" title="Not validated"></span>';

  if (options.trigger !== undefined && options.trigger.printLevel !== undefined)
    tpl = '<div class="bg-' + options.trigger.printLevel + '">' + tpl + '</div>';

  return tpl;
}

function renderFrequency(frequency) {
  if (frequency === undefined)
    return '-';

  var output;

  if (frequency.description === undefined)
    output = '-'
  else
    output = frequency.description;

  if (frequency.nextIterationAt !== undefined)
    output = '<span data-toggle="tooltip" title="Next iteration: ' + new Date(frequency.nextIterationAt).toISOString() + '">' + output + '</span>';

  return output;
}

function renderJson(json) {
  return JSON.stringify(json);
}

// Update/Delete buttons
function renderUD(options) {
  let tpl = '';

  if (options.hasOwnProperty('edit')) {
    if (! options.edit.hasOwnProperty('enabled'))
      options.edit.enabled = false;
    if (! options.edit.hasOwnProperty('function'))
      options.edit.enabled = false;
  } else {
    options['edit'] = Array();
    options['edit']['enabled'] = false;
  }
  
  if (! options.edit.hasOwnProperty('function'))
    options.edit.function = '';

  if (options.hasOwnProperty('remove')) {
    if (! options.remove.hasOwnProperty('enabled'))
      options.remove.enabled = false;
    if (! options.remove.hasOwnProperty('function'))
      options.remove.enabled = false;
  } else {
    options['remove'] = Array();
    options['remove']['enabled'] = false;
  }
  
  if (! options.remove.hasOwnProperty('function'))
    options.remove.function = '';

  if (options.edit.enabled === true)
    tpl += `<a href="#" onClick="${options.edit.function}">${RogerUtils.iconEdit()}</a>`;

  if (options.remove.enabled === true)
    tpl += `<a href="#" onClick="${options.remove.function}">${RogerUtils.iconRemove()}</a>`;

  return tpl;
}