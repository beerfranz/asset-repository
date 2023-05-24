#!/usr/bin/env sh

for yaml in $(find . -type f -name *.yml); do
	echo "convert ${yaml} to json"
	yq . ${yaml} > ${yaml}.json
done

for json in $(find . -type f -name *.json); do
	echo "find file ${json}"
	data_file=/tmp/data.json

	method=$(jq -r .request.method ${json})
	path=$(jq -r .request.path ${json})
	jq -r .data ${json} > ${data_file}

	http_code=$(curl -X ${method} -s -o /dev/null -w "%{http_code}" -k -H 'Content-type: application/json' http://caddy:8080${path} -d @${data_file})
	case $http_code in
		"200"|"201")
		;;
		*) echo "Error, bad response code ${http_code}";
			echo "request:"
			echo "curl -X ${method} -s -o /dev/null -w "%{http_code}" -k -H 'Content-type: application/json' http://caddy:8080${path} -d @${data_file}"
			echo "data:"
			cat ${data_file}
		exit 1
	esac
	# curl -k -H 'Content-type: application/json' -H 'Host: localhost' https://caddy/assets
done

for tmp in $(find . -type f -name *.yml.json); do
	rm ${tmp}
done
