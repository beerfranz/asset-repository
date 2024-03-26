
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

  var result = '';

  Object.keys(values).forEach(function(key) {
    value = values[key];

    result += value.identifier + ': ' + value.value + '\n';
  });

  return result;
}

function renderFrequency(frequency) {
  if (frequency === undefined)
    return '-';

  return frequency.description + ': ' + frequency.nextIterationDate;
}