function handlePublicKeyRequestOptions(data, successCallback, failureCallback) {
    fetch('/assertion/options', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(json => {
            console.log(json); return json;
        })
        .then(json => successCallback(json))
        .catch(err => failureCallback(err));
}

function handlePublicKeyRequestResult(data, successCallback, failureCallback) {
    fetch('/assertion/result', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(json => {
            console.log(json); return json;
        })
        .then(json => successCallback(json))
        .catch(err => failureCallback(err));
}

export {
    handlePublicKeyRequestOptions, handlePublicKeyRequestResult,
};
