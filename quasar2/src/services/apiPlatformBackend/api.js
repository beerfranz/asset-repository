import { api as http } from 'boot/axios'

const ENTRYPOINT = "https://localhost";
const MIME_TYPE = "application/ld+json";

async function apiGet(path) {
	return await http.get(`${ENTRYPOINT}${path}`);
}

function apiCollectionGetData(response) {
	return response.data['hydra:member']
}

function apiCollectionGetTotalItems(response) {
	return response.data['hydra:totalItems']
}

class Api {

	_path = '/';
	_backend_api = ENTRYPOINT;

	constructor({ path = '/'} ) {
		this._path = path;
	}

	getEndpoint() {
		return this._backend_api + this._path
	}

	get() {
		return http.get(this.getEndpoint());
	}

	getData(response) {
		return response.data['hydra:member']
	}

	getTotalItems(response) {
		return response.data['hydra:totalItems']
	}
}

export {
	Api,
	apiGet,
	apiCollectionGetData,
	apiCollectionGetTotalItems,
}