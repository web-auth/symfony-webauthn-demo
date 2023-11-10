/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/@simplewebauthn/browser@8.3.4/dist/bundle/index.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
function e(e){const t=new Uint8Array(e);let r="";for(const e of t)r+=String.fromCharCode(e);return btoa(r).replace(/\+/g,"-").replace(/\//g,"_").replace(/=/g,"")}function t(e){const t=e.replace(/-/g,"+").replace(/_/g,"/"),r=(4-t.length%4)%4,n=t.padEnd(t.length+r,"="),o=atob(n),a=new ArrayBuffer(o.length),i=new Uint8Array(a);for(let e=0;e<o.length;e++)i[e]=o.charCodeAt(e);return a}function r(){return void 0!==window?.PublicKeyCredential&&"function"==typeof window.PublicKeyCredential}function n(e){const{id:r}=e;return{...e,id:t(r),transports:e.transports}}function o(e){return"localhost"===e||/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i.test(e)}class a extends Error{constructor({message:e,code:t,cause:r,name:n}){super(e,{cause:r}),this.name=n??r.name,this.code=t}}const i=new class{createNewAbortSignal(){if(this.controller){const e=new Error("Cancelling existing WebAuthn API call for new one");e.name="AbortError",this.controller.abort(e)}const e=new AbortController;return this.controller=e,e.signal}cancelCeremony(){if(this.controller){const e=new Error("Manually cancelling existing WebAuthn API call");e.name="AbortError",this.controller.abort(e),this.controller=void 0}}},s=["cross-platform","platform"];function c(e){if(e&&!(s.indexOf(e)<0))return e}async function l(s){if(!r())throw new Error("WebAuthn is not supported in this browser");var l;const d={publicKey:{...s,challenge:t(s.challenge),user:{...s.user,id:(l=s.user.id,(new TextEncoder).encode(l))},excludeCredentials:s.excludeCredentials?.map(n)}};let h;d.signal=i.createNewAbortSignal();try{h=await navigator.credentials.create(d)}catch(e){throw function({error:e,options:t}){const{publicKey:r}=t;if(!r)throw Error("options was missing required publicKey property");if("AbortError"===e.name){if(t.signal instanceof AbortSignal)return new a({message:"Registration ceremony was sent an abort signal",code:"ERROR_CEREMONY_ABORTED",cause:e})}else if("ConstraintError"===e.name){if(!0===r.authenticatorSelection?.requireResidentKey)return new a({message:"Discoverable credentials were required but no available authenticator supported it",code:"ERROR_AUTHENTICATOR_MISSING_DISCOVERABLE_CREDENTIAL_SUPPORT",cause:e});if("required"===r.authenticatorSelection?.userVerification)return new a({message:"User verification was required but no available authenticator supported it",code:"ERROR_AUTHENTICATOR_MISSING_USER_VERIFICATION_SUPPORT",cause:e})}else{if("InvalidStateError"===e.name)return new a({message:"The authenticator was previously registered",code:"ERROR_AUTHENTICATOR_PREVIOUSLY_REGISTERED",cause:e});if("NotAllowedError"===e.name)return new a({message:e.message,code:"ERROR_PASSTHROUGH_SEE_CAUSE_PROPERTY",cause:e});if("NotSupportedError"===e.name)return 0===r.pubKeyCredParams.filter((e=>"public-key"===e.type)).length?new a({message:'No entry in pubKeyCredParams was of type "public-key"',code:"ERROR_MALFORMED_PUBKEYCREDPARAMS",cause:e}):new a({message:"No available authenticator supported any of the specified pubKeyCredParams algorithms",code:"ERROR_AUTHENTICATOR_NO_SUPPORTED_PUBKEYCREDPARAMS_ALG",cause:e});if("SecurityError"===e.name){const t=window.location.hostname;if(!o(t))return new a({message:`${window.location.hostname} is an invalid domain`,code:"ERROR_INVALID_DOMAIN",cause:e});if(r.rp.id!==t)return new a({message:`The RP ID "${r.rp.id}" is invalid for this domain`,code:"ERROR_INVALID_RP_ID",cause:e})}else if("TypeError"===e.name){if(r.user.id.byteLength<1||r.user.id.byteLength>64)return new a({message:"User ID was not between 1 and 64 characters",code:"ERROR_INVALID_USER_ID_LENGTH",cause:e})}else if("UnknownError"===e.name)return new a({message:"The authenticator was unable to process the specified options, or could not create a new credential",code:"ERROR_AUTHENTICATOR_GENERAL_ERROR",cause:e})}return e}({error:e,options:d})}if(!h)throw new Error("Registration was not completed");const{id:w,rawId:R,response:p,type:E}=h;let f,g,A,m;if("function"==typeof p.getTransports&&(f=p.getTransports()),"function"==typeof p.getPublicKeyAlgorithm)try{g=p.getPublicKeyAlgorithm()}catch(e){u("getPublicKeyAlgorithm()",e)}if("function"==typeof p.getPublicKey)try{const t=p.getPublicKey();null!==t&&(A=e(t))}catch(e){u("getPublicKey()",e)}if("function"==typeof p.getAuthenticatorData)try{m=e(p.getAuthenticatorData())}catch(e){u("getAuthenticatorData()",e)}return{id:w,rawId:e(R),response:{attestationObject:e(p.attestationObject),clientDataJSON:e(p.clientDataJSON),transports:f,publicKeyAlgorithm:g,publicKey:A,authenticatorData:m},type:E,clientExtensionResults:h.getClientExtensionResults(),authenticatorAttachment:c(h.authenticatorAttachment)}}function u(e,t){console.warn(`The browser extension that intercepted this WebAuthn API call incorrectly implemented ${e}. You should report this error to them.\n`,t)}function d(){const e=window.PublicKeyCredential;return void 0===e.isConditionalMediationAvailable?new Promise((e=>e(!1))):e.isConditionalMediationAvailable()}async function h(s,l=!1){if(!r())throw new Error("WebAuthn is not supported in this browser");let u;0!==s.allowCredentials?.length&&(u=s.allowCredentials?.map(n));const h={...s,challenge:t(s.challenge),allowCredentials:u},w={};if(l){if(!await d())throw Error("Browser does not support WebAuthn autofill");if(document.querySelectorAll("input[autocomplete$='webauthn']").length<1)throw Error('No <input> with "webauthn" as the only or last value in its `autocomplete` attribute was detected');w.mediation="conditional",h.allowCredentials=[]}let R;w.publicKey=h,w.signal=i.createNewAbortSignal();try{R=await navigator.credentials.get(w)}catch(e){throw function({error:e,options:t}){const{publicKey:r}=t;if(!r)throw Error("options was missing required publicKey property");if("AbortError"===e.name){if(t.signal instanceof AbortSignal)return new a({message:"Authentication ceremony was sent an abort signal",code:"ERROR_CEREMONY_ABORTED",cause:e})}else{if("NotAllowedError"===e.name)return new a({message:e.message,code:"ERROR_PASSTHROUGH_SEE_CAUSE_PROPERTY",cause:e});if("SecurityError"===e.name){const t=window.location.hostname;if(!o(t))return new a({message:`${window.location.hostname} is an invalid domain`,code:"ERROR_INVALID_DOMAIN",cause:e});if(r.rpId!==t)return new a({message:`The RP ID "${r.rpId}" is invalid for this domain`,code:"ERROR_INVALID_RP_ID",cause:e})}else if("UnknownError"===e.name)return new a({message:"The authenticator was unable to process the specified options, or could not create a new assertion signature",code:"ERROR_AUTHENTICATOR_GENERAL_ERROR",cause:e})}return e}({error:e,options:w})}if(!R)throw new Error("Authentication was not completed");const{id:p,rawId:E,response:f,type:g}=R;let A;var m;return f.userHandle&&(m=f.userHandle,A=new TextDecoder("utf-8").decode(m)),{id:p,rawId:e(E),response:{authenticatorData:e(f.authenticatorData),clientDataJSON:e(f.clientDataJSON),signature:e(f.signature),userHandle:A},type:g,clientExtensionResults:R.getClientExtensionResults(),authenticatorAttachment:c(R.authenticatorAttachment)}}function w(){return r()?PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable():new Promise((e=>e(!1)))}export{i as WebAuthnAbortService,t as base64URLStringToBuffer,r as browserSupportsWebAuthn,d as browserSupportsWebAuthnAutofill,e as bufferToBase64URLString,w as platformAuthenticatorIsAvailable,h as startAuthentication,l as startRegistration};export default null;
