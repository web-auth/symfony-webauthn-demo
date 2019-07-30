function handlePublicKeyCreationOptions(
    data,
    successCallback,
    failureCallback
) {
    fetch('/attestation/options', {
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

function handlePublicKeyCreationResult(data, successCallback, failureCallback) {
    fetch('/attestation/result', {
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
    handlePublicKeyCreationOptions, handlePublicKeyCreationResult,
};
