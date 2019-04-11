function handlePublicKeyCreationOptions(
  data,
  successCallback,
  failureCallback
) {
  fetch("/register/options", {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
    .then(response => {
      return response.json();
    })
    .then(json => successCallback(json))
    .catch(err => failureCallback(err));
}

function handlePublicKeyCreationResult(data, successCallback, failureCallback) {
  fetch("/register", {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
    .then(response => {
      console.log(response.text());

      return response.json();
    })
    .then(json => successCallback(json))
    .catch(err => failureCallback(err));
}

export { handlePublicKeyCreationOptions, handlePublicKeyCreationResult };
