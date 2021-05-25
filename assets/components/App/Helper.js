
const Helper = new function () {
    const timeoutStorage = {};
    return {
        addTimeout: (id, func, timeout = 1000) => {
            clearTimeout(timeoutStorage[id]);
            timeoutStorage[id] = setTimeout(func, timeout);
        },
        fetchJsonPost: (url, body) => {
            return fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(body)
            }).then(response => response.json())
        }
    }
}

export default Helper;
