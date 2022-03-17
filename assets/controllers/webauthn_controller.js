import { Controller } from '@hotwired/stimulus';
import {useLogin, useRegistration} from '@web-auth/webauthn-helper';

'use strict';

export default class extends Controller {
    static values = {
        requestResultUrl: String,
        requestResultHeader: Object,
        requestOptionsUrl: String,
        requestOptionsHeader: Object,
        requestSuccessRedirectUri: String,
        creationResultUrl: String,
        creationResultHeader: Object,
        creationOptionsUrl: String,
        creationOptionsHeader: Object,
        creationSuccessRedirectUri: String,
    };

    connect() {
        const requestOptions = {
            actionUrl: this.requestResultUrlValue || '/request',
            actionHeader: this.requestResultHeaderValue || {},
            optionsUrl: this.requestOptionsUrlValue || '/request/options',
            optionsHeader: this.requestOptionsHeaderValue || {},
        };
        const creationOptions = {
            actionUrl: this.creationResultUrlValue || '/creation',
            actionHeader: this.creationResultHeaderValue || {},
            optionsUrl: this.creationOptionsUrlValue || '/creation/options',
            optionsHeader: this.creationOptionsHeaderValue || {},
        };

        this.webauthnLogin = useLogin(requestOptions);
        this.webauthnRegister = useRegistration(creationOptions);
    }

    request(event) {
        event.preventDefault();
        const data = this._getData();
        this.webauthnLogin(data)
            .then((response)=> {
                this._dispatchEvent('webauthn:request:success', response)
                if (this.requestSuccessRedirectUriValue) {
                    window.location.replace(this.requestSuccessRedirectUriValue);
                }
            })
            .catch((error)=> this._dispatchEvent('webauthn:request:failure', error))
        ;
    }

    create(event) {
        event.preventDefault();
        const data = this._getData();
        this.webauthnRegister(data)
            .then((response)=> {
                this._dispatchEvent('webauthn:creation:success', response)
                if (this.creationSuccessRedirectUriValue) {
                    window.location.replace(this.creationSuccessRedirectUriValue);
                }
            })
            .catch((error)=> this._dispatchEvent('webauthn:creation:failure', error))
        ;
    }

    _dispatchEvent(name, payload) {
        this.element.dispatchEvent(new CustomEvent(name, {detail: payload}));
    }

    _getData()
    {
        let data = new FormData();
        try {
            data = new FormData(this.element);
        } catch (e) {
        }
        const object = {};
        data.forEach((value, key) => {
            if (value !== null && value !== '') {
                object[key] = value
            }
        });

        return object;
    }
}
