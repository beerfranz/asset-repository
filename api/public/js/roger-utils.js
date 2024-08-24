var RogerUtils = {
	iconOk: function(options) {
		return this.icon({...options, ...{ icon: 'glyphicon-ok' }});
	},
	iconKo: function(options) {
		return this.icon({...options, ...{ icon: 'glyphicon-remove' }});
	},

	iconEdit: function(options) {
		return this.icon({...options, ...{ icon: 'glyphicon-pencil', classes: 'text-warning', title: 'Edit' }});
	},

	iconShare: function(options) {
		return this.icon({...options, ...{ icon: 'glyphicon-share' }});
	},

	iconAdd: function(options) {
		return this.icon({...options, ...{ icon: 'glyphicon-plus', classes: 'text-success', title: 'Add' }});
	},

	iconRemove: function(options) {
		return this.icon({...options, ...{ icon: 'glyphicon-trash', classes: 'text-danger', title: 'Remove' }});
	},

	icon: function(options) {
		if (!options.hasOwnProperty('icon'))
			options.icon = 'glyphicon-ok';

		if (!options.hasOwnProperty('title'))
			options.title = '';

		if (!options.hasOwnProperty('classes'))
			options.classes = 'text-secondary';

		return `<span class="glyphicon glyphicon ${options.icon} ${options.classes}" aria-hidden="true" data-toggle="tooltip" title="${options.title}"></span>`;
	}
}
