import { api as http } from 'boot/axios'

const ENTRYPOINT = "https://localhost"
const MIME_TYPE = "application/ld+json"

class Api {

	_path = '/'
	_route_name_prefix = ''

	_backend_api = ENTRYPOINT

	constructor({ path = '/', route_name_prefix = '' } ) {
		this._path = path
		this._route_name_prefix = route_name_prefix
	}

	getEndpoint() {
		return this._backend_api + this._path
	}

	getRouteNameCollection() {
		return this._route_name_prefix + '_collection'
	}
	getRouteNameShow() {
		return this._route_name_prefix + '_show'
	}
	getRouteNameEdit() {
		return this._route_name_prefix + '_update'
	}
	getRouteNameCreate() {
		return this._route_name_prefix + '_create'
	}

	get(opts = {}) {
		let params = {};
		if (opts.hasOwnProperty('itemsPerPage'))
			params.itemsPerPage = opts.itemsPerPage
		if (opts.hasOwnProperty('page'))
			params.page = opts.page

		if (opts.hasOwnProperty('sortBy')) {
			if (!opts.hasOwnProperty('sortDesc'))
				opts.sortDesc = false

			params.order = {}
			params.order[opts.sortBy] = opts.sortDesc ? 'DESC' : 'ASC'
		}
		return http.get(this.getEndpoint(), { params: params })
	}

	getOne(id) {
		return http.get(this.getEndpoint() + '/' + id)
	}

	getData(response) {
		return response.data['hydra:member']
	}

	getTotalItems(response) {
		return response.data['hydra:totalItems']
	}

	post(data = {}, opts = {}) {
		return http.post(this.getEndpoint(), data, opts)
	}

	put(id, data = {}, opts = {}) {
		return http.put(this.getEndpoint() + `/${id}`, data, opts)
	}

	patch(id, data = {}, opts = {}) {
		opts.headers['Content-Type'] = 'application/merge-patch+json'
		return http.patch(this.getEndpoint() + `/${id}`, data, opts)
	}

	delete(id, opts = {}) {
		return http.delete(this.getEndpoint() + `/${id}`, opts)
	}

}

export {
	Api
}