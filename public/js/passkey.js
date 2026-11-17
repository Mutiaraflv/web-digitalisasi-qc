/*
 * QCMS — Login dengan wajah (Passkey / WebAuthn).
 * Pemindaian wajah dilakukan oleh perangkat (Face ID, Windows Hello, Android).
 * File ini hanya menjembatani browser <-> server. Tanpa library tambahan.
 */
(function () {
  'use strict';

  function b64urlEncode(buffer) {
    var bytes = new Uint8Array(buffer);
    var bin = '';
    for (var i = 0; i < bytes.length; i++) bin += String.fromCharCode(bytes[i]);
    return btoa(bin).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
  }

  function b64urlDecode(str) {
    str = String(str).replace(/-/g, '+').replace(/_/g, '/');
    while (str.length % 4) str += '=';
    var bin = atob(str);
    var out = new Uint8Array(bin.length);
    for (var i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
    return out.buffer;
  }

  function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  async function postJson(url, body) {
    var res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken()
      },
      body: JSON.stringify(body || {})
    });

    var data = {};
    try { data = await res.json(); } catch (e) { /* respons non-JSON */ }

    if (!res.ok) {
      var msg = data.message;
      if (data.errors) {
        var first = Object.values(data.errors)[0];
        if (first && first[0]) msg = first[0];
      }
      if (res.status === 419) msg = msg || 'Sesi halaman kedaluwarsa. Muat ulang halaman.';
      if (res.status === 429) msg = 'Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi.';
      var err = new Error(msg || ('HTTP ' + res.status));
      err.fromServer = true;
      throw err;
    }
    return data;
  }

  function isSupported() {
    return !!(window.PublicKeyCredential && navigator.credentials && navigator.credentials.create);
  }

  function isSecure() {
    return window.isSecureContext === true;
  }

  /** Pengguna menutup dialog, menolak, atau waktu habis. */
  function isCancelled(err) {
    return !!err && (err.name === 'NotAllowedError' || err.name === 'AbortError');
  }

  async function login(opts) {
    var o = await postJson(opts.optionsUrl);

    var cred = await navigator.credentials.get({
      publicKey: {
        challenge: b64urlDecode(o.challenge),
        rpId: o.rpId,
        allowCredentials: [],
        userVerification: o.userVerification || 'required',
        timeout: o.timeout || 120000
      }
    });

    var r = cred.response;
    return postJson(opts.verifyUrl, {
      rawId: b64urlEncode(cred.rawId),
      remember: !!opts.remember,
      response: {
        clientDataJSON: b64urlEncode(r.clientDataJSON),
        authenticatorData: b64urlEncode(r.authenticatorData),
        signature: b64urlEncode(r.signature),
        userHandle: r.userHandle ? b64urlEncode(r.userHandle) : null
      }
    });
  }

  async function register(opts) {
    var o = await postJson(opts.optionsUrl);

    var publicKey = Object.assign({}, o, {
      challenge: b64urlDecode(o.challenge),
      user: Object.assign({}, o.user, { id: b64urlDecode(o.user.id) }),
      excludeCredentials: (o.excludeCredentials || []).map(function (c) {
        return { type: c.type, id: b64urlDecode(c.id) };
      })
    });

    var cred = await navigator.credentials.create({ publicKey: publicKey });
    var r = cred.response;

    if (typeof r.getPublicKey !== 'function' || typeof r.getAuthenticatorData !== 'function') {
      var e = new Error('unsupported');
      e.name = 'UnsupportedError';
      throw e;
    }

    var pub = r.getPublicKey();
    if (!pub) {
      var e2 = new Error('unsupported');
      e2.name = 'UnsupportedError';
      throw e2;
    }

    return postJson(opts.storeUrl, {
      name: opts.name || '',
      rawId: b64urlEncode(cred.rawId),
      response: {
        clientDataJSON: b64urlEncode(r.clientDataJSON),
        authenticatorData: b64urlEncode(r.getAuthenticatorData()),
        publicKey: b64urlEncode(pub),
        publicKeyAlgorithm: r.getPublicKeyAlgorithm(),
        transports: typeof r.getTransports === 'function' ? r.getTransports() : []
      }
    });
  }

  /** Tebakan nama perangkat untuk ditampilkan di daftar Profil. */
  function guessDeviceName() {
    var ua = navigator.userAgent || '';
    var browser = /Edg\//.test(ua) ? 'Edge'
      : /OPR\//.test(ua) ? 'Opera'
      : /SamsungBrowser/.test(ua) ? 'Samsung Internet'
      : /Firefox\//.test(ua) ? 'Firefox'
      : /Chrome\//.test(ua) ? 'Chrome'
      : /Safari\//.test(ua) ? 'Safari' : '';
    var device = /iPhone/.test(ua) ? 'iPhone'
      : /iPad/.test(ua) ? 'iPad'
      : /Android/.test(ua) ? 'HP Android'
      : /Windows/.test(ua) ? 'Laptop Windows'
      : /Macintosh|Mac OS X/.test(ua) ? 'Mac'
      : /Linux/.test(ua) ? 'Linux' : 'Perangkat';
    return browser ? device + ' (' + browser + ')' : device;
  }

  window.QcPasskey = {
    isSupported: isSupported,
    isSecure: isSecure,
    isCancelled: isCancelled,
    login: login,
    register: register,
    guessDeviceName: guessDeviceName
  };
})();
