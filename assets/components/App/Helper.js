
const Helper = new function () {
    const timeoutStorage = {};
    return {
        addTimeout: (id, func, timeout) => {
            clearTimeout(timeoutStorage[id]);
            timeoutStorage[id] = setTimeout(func, timeout);
        },
        fetchJson: (url, params = null) => {
            if (params) {
                // json parse used for removing undefined fields
                params = JSON.parse(JSON.stringify(params));
                url += '?' + new URLSearchParams(params);
            }
            return fetch(url).then(response => response.json());
        },
        fetchJsonPost: (url, body) => {
            return fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(body)
            }).then(response => response.json());
        }
    }
}

export default Helper;
