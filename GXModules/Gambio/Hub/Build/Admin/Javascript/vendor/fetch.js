'use strict';

(function (self) {
  'use strict';

  if (self.fetch) {
    return;
  }

  var support = {
    searchParams: 'URLSearchParams' in self,
    iterable: 'Symbol' in self && 'iterator' in Symbol,
    blob: 'FileReader' in self && 'Blob' in self && function () {
      try {
        new Blob();
        return true;
      } catch (e) {
        return false;
      }
    }(),
    formData: 'FormData' in self,
    arrayBuffer: 'ArrayBuffer' in self
  };

  if (support.arrayBuffer) {
    var viewClasses = ['[object Int8Array]', '[object Uint8Array]', '[object Uint8ClampedArray]', '[object Int16Array]', '[object Uint16Array]', '[object Int32Array]', '[object Uint32Array]', '[object Float32Array]', '[object Float64Array]'];

    var isDataView = function isDataView(obj) {
      return obj && DataView.prototype.isPrototypeOf(obj);
    };

    var isArrayBufferView = ArrayBuffer.isView || function (obj) {
      return obj && viewClasses.indexOf(Object.prototype.toString.call(obj)) > -1;
    };
  }

  function normalizeName(name) {
    if (typeof name !== 'string') {
      name = String(name);
    }
    if (/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(name)) {
      throw new TypeError('Invalid character in header field name');
    }
    return name.toLowerCase();
  }

  function normalizeValue(value) {
    if (typeof value !== 'string') {
      value = String(value);
    }
    return value;
  }

  // Build a destructive iterator for the value list
  function iteratorFor(items) {
    var iterator = {
      next: function next() {
        var value = items.shift();
        return { done: value === undefined, value: value };
      }
    };

    if (support.iterable) {
      iterator[Symbol.iterator] = function () {
        return iterator;
      };
    }

    return iterator;
  }

  function Headers(headers) {
    this.map = {};

    if (headers instanceof Headers) {
      headers.forEach(function (value, name) {
        this.append(name, value);
      }, this);
    } else if (Array.isArray(headers)) {
      headers.forEach(function (header) {
        this.append(header[0], header[1]);
      }, this);
    } else if (headers) {
      Object.getOwnPropertyNames(headers).forEach(function (name) {
        this.append(name, headers[name]);
      }, this);
    }
  }

  Headers.prototype.append = function (name, value) {
    name = normalizeName(name);
    value = normalizeValue(value);
    var oldValue = this.map[name];
    this.map[name] = oldValue ? oldValue + ',' + value : value;
  };

  Headers.prototype['delete'] = function (name) {
    delete this.map[normalizeName(name)];
  };

  Headers.prototype.get = function (name) {
    name = normalizeName(name);
    return this.has(name) ? this.map[name] : null;
  };

  Headers.prototype.has = function (name) {
    return this.map.hasOwnProperty(normalizeName(name));
  };

  Headers.prototype.set = function (name, value) {
    this.map[normalizeName(name)] = normalizeValue(value);
  };

  Headers.prototype.forEach = function (callback, thisArg) {
    for (var name in this.map) {
      if (this.map.hasOwnProperty(name)) {
        callback.call(thisArg, this.map[name], name, this);
      }
    }
  };

  Headers.prototype.keys = function () {
    var items = [];
    this.forEach(function (value, name) {
      items.push(name);
    });
    return iteratorFor(items);
  };

  Headers.prototype.values = function () {
    var items = [];
    this.forEach(function (value) {
      items.push(value);
    });
    return iteratorFor(items);
  };

  Headers.prototype.entries = function () {
    var items = [];
    this.forEach(function (value, name) {
      items.push([name, value]);
    });
    return iteratorFor(items);
  };

  if (support.iterable) {
    Headers.prototype[Symbol.iterator] = Headers.prototype.entries;
  }

  function consumed(body) {
    if (body.bodyUsed) {
      return Promise.reject(new TypeError('Already read'));
    }
    body.bodyUsed = true;
  }

  function fileReaderReady(reader) {
    return new Promise(function (resolve, reject) {
      reader.onload = function () {
        resolve(reader.result);
      };
      reader.onerror = function () {
        reject(reader.error);
      };
    });
  }

  function readBlobAsArrayBuffer(blob) {
    var reader = new FileReader();
    var promise = fileReaderReady(reader);
    reader.readAsArrayBuffer(blob);
    return promise;
  }

  function readBlobAsText(blob) {
    var reader = new FileReader();
    var promise = fileReaderReady(reader);
    reader.readAsText(blob);
    return promise;
  }

  function readArrayBufferAsText(buf) {
    var view = new Uint8Array(buf);
    var chars = new Array(view.length);

    for (var i = 0; i < view.length; i++) {
      chars[i] = String.fromCharCode(view[i]);
    }
    return chars.join('');
  }

  function bufferClone(buf) {
    if (buf.slice) {
      return buf.slice(0);
    } else {
      var view = new Uint8Array(buf.byteLength);
      view.set(new Uint8Array(buf));
      return view.buffer;
    }
  }

  function Body() {
    this.bodyUsed = false;

    this._initBody = function (body) {
      this._bodyInit = body;
      if (!body) {
        this._bodyText = '';
      } else if (typeof body === 'string') {
        this._bodyText = body;
      } else if (support.blob && Blob.prototype.isPrototypeOf(body)) {
        this._bodyBlob = body;
      } else if (support.formData && FormData.prototype.isPrototypeOf(body)) {
        this._bodyFormData = body;
      } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
        this._bodyText = body.toString();
      } else if (support.arrayBuffer && support.blob && isDataView(body)) {
        this._bodyArrayBuffer = bufferClone(body.buffer);
        // IE 10-11 can't handle a DataView body.
        this._bodyInit = new Blob([this._bodyArrayBuffer]);
      } else if (support.arrayBuffer && (ArrayBuffer.prototype.isPrototypeOf(body) || isArrayBufferView(body))) {
        this._bodyArrayBuffer = bufferClone(body);
      } else {
        throw new Error('unsupported BodyInit type');
      }

      if (!this.headers.get('content-type')) {
        if (typeof body === 'string') {
          this.headers.set('content-type', 'text/plain;charset=UTF-8');
        } else if (this._bodyBlob && this._bodyBlob.type) {
          this.headers.set('content-type', this._bodyBlob.type);
        } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
          this.headers.set('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
        }
      }
    };

    if (support.blob) {
      this.blob = function () {
        var rejected = consumed(this);
        if (rejected) {
          return rejected;
        }

        if (this._bodyBlob) {
          return Promise.resolve(this._bodyBlob);
        } else if (this._bodyArrayBuffer) {
          return Promise.resolve(new Blob([this._bodyArrayBuffer]));
        } else if (this._bodyFormData) {
          throw new Error('could not read FormData body as blob');
        } else {
          return Promise.resolve(new Blob([this._bodyText]));
        }
      };

      this.arrayBuffer = function () {
        if (this._bodyArrayBuffer) {
          return consumed(this) || Promise.resolve(this._bodyArrayBuffer);
        } else {
          return this.blob().then(readBlobAsArrayBuffer);
        }
      };
    }

    this.text = function () {
      var rejected = consumed(this);
      if (rejected) {
        return rejected;
      }

      if (this._bodyBlob) {
        return readBlobAsText(this._bodyBlob);
      } else if (this._bodyArrayBuffer) {
        return Promise.resolve(readArrayBufferAsText(this._bodyArrayBuffer));
      } else if (this._bodyFormData) {
        throw new Error('could not read FormData body as text');
      } else {
        return Promise.resolve(this._bodyText);
      }
    };

    if (support.formData) {
      this.formData = function () {
        return this.text().then(decode);
      };
    }

    this.json = function () {
      return this.text().then(JSON.parse);
    };

    return this;
  }

  // HTTP methods whose capitalization should be normalized
  var methods = ['DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT'];

  function normalizeMethod(method) {
    var upcased = method.toUpperCase();
    return methods.indexOf(upcased) > -1 ? upcased : method;
  }

  function Request(input, options) {
    options = options || {};
    var body = options.body;

    if (input instanceof Request) {
      if (input.bodyUsed) {
        throw new TypeError('Already read');
      }
      this.url = input.url;
      this.credentials = input.credentials;
      if (!options.headers) {
        this.headers = new Headers(input.headers);
      }
      this.method = input.method;
      this.mode = input.mode;
      if (!body && input._bodyInit != null) {
        body = input._bodyInit;
        input.bodyUsed = true;
      }
    } else {
      this.url = String(input);
    }

    this.credentials = options.credentials || this.credentials || 'omit';
    if (options.headers || !this.headers) {
      this.headers = new Headers(options.headers);
    }
    this.method = normalizeMethod(options.method || this.method || 'GET');
    this.mode = options.mode || this.mode || null;
    this.referrer = null;

    if ((this.method === 'GET' || this.method === 'HEAD') && body) {
      throw new TypeError('Body not allowed for GET or HEAD requests');
    }
    this._initBody(body);
  }

  Request.prototype.clone = function () {
    return new Request(this, { body: this._bodyInit });
  };

  function decode(body) {
    var form = new FormData();
    body.trim().split('&').forEach(function (bytes) {
      if (bytes) {
        var split = bytes.split('=');
        var name = split.shift().replace(/\+/g, ' ');
        var value = split.join('=').replace(/\+/g, ' ');
        form.append(decodeURIComponent(name), decodeURIComponent(value));
      }
    });
    return form;
  }

  function parseHeaders(rawHeaders) {
    var headers = new Headers();
    // Replace instances of \r\n and \n followed by at least one space or horizontal tab with a space
    // https://tools.ietf.org/html/rfc7230#section-3.2
    var preProcessedHeaders = rawHeaders.replace(/\r?\n[\t ]+/g, ' ');
    preProcessedHeaders.split(/\r?\n/).forEach(function (line) {
      var parts = line.split(':');
      var key = parts.shift().trim();
      if (key) {
        var value = parts.join(':').trim();
        headers.append(key, value);
      }
    });
    return headers;
  }

  Body.call(Request.prototype);

  function Response(bodyInit, options) {
    if (!options) {
      options = {};
    }

    this.type = 'default';
    this.status = options.status === undefined ? 200 : options.status;
    this.ok = this.status >= 200 && this.status < 300;
    this.statusText = 'statusText' in options ? options.statusText : 'OK';
    this.headers = new Headers(options.headers);
    this.url = options.url || '';
    this._initBody(bodyInit);
  }

  Body.call(Response.prototype);

  Response.prototype.clone = function () {
    return new Response(this._bodyInit, {
      status: this.status,
      statusText: this.statusText,
      headers: new Headers(this.headers),
      url: this.url
    });
  };

  Response.error = function () {
    var response = new Response(null, { status: 0, statusText: '' });
    response.type = 'error';
    return response;
  };

  var redirectStatuses = [301, 302, 303, 307, 308];

  Response.redirect = function (url, status) {
    if (redirectStatuses.indexOf(status) === -1) {
      throw new RangeError('Invalid status code');
    }

    return new Response(null, { status: status, headers: { location: url } });
  };

  self.Headers = Headers;
  self.Request = Request;
  self.Response = Response;

  self.fetch = function (input, init) {
    return new Promise(function (resolve, reject) {
      var request = new Request(input, init);
      var xhr = new XMLHttpRequest();

      xhr.onload = function () {
        var options = {
          status: xhr.status,
          statusText: xhr.statusText,
          headers: parseHeaders(xhr.getAllResponseHeaders() || '')
        };
        options.url = 'responseURL' in xhr ? xhr.responseURL : options.headers.get('X-Request-URL');
        var body = 'response' in xhr ? xhr.response : xhr.responseText;
        resolve(new Response(body, options));
      };

      xhr.onerror = function () {
        reject(new TypeError('Network request failed'));
      };

      xhr.ontimeout = function () {
        reject(new TypeError('Network request failed'));
      };

      xhr.open(request.method, request.url, true);

      if (request.credentials === 'include') {
        xhr.withCredentials = true;
      } else if (request.credentials === 'omit') {
        xhr.withCredentials = false;
      }

      if ('responseType' in xhr && support.blob) {
        xhr.responseType = 'blob';
      }

      request.headers.forEach(function (value, name) {
        xhr.setRequestHeader(name, value);
      });

      xhr.send(typeof request._bodyInit === 'undefined' ? null : request._bodyInit);
    });
  };
  self.fetch.polyfill = true;
})(typeof self !== 'undefined' ? self : undefined);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvdmVuZG9yL2ZldGNoLmpzIl0sIm5hbWVzIjpbInNlbGYiLCJmZXRjaCIsInN1cHBvcnQiLCJzZWFyY2hQYXJhbXMiLCJpdGVyYWJsZSIsIlN5bWJvbCIsImJsb2IiLCJCbG9iIiwiZSIsImZvcm1EYXRhIiwiYXJyYXlCdWZmZXIiLCJ2aWV3Q2xhc3NlcyIsImlzRGF0YVZpZXciLCJvYmoiLCJEYXRhVmlldyIsInByb3RvdHlwZSIsImlzUHJvdG90eXBlT2YiLCJpc0FycmF5QnVmZmVyVmlldyIsIkFycmF5QnVmZmVyIiwiaXNWaWV3IiwiaW5kZXhPZiIsIk9iamVjdCIsInRvU3RyaW5nIiwiY2FsbCIsIm5vcm1hbGl6ZU5hbWUiLCJuYW1lIiwiU3RyaW5nIiwidGVzdCIsIlR5cGVFcnJvciIsInRvTG93ZXJDYXNlIiwibm9ybWFsaXplVmFsdWUiLCJ2YWx1ZSIsIml0ZXJhdG9yRm9yIiwiaXRlbXMiLCJpdGVyYXRvciIsIm5leHQiLCJzaGlmdCIsImRvbmUiLCJ1bmRlZmluZWQiLCJIZWFkZXJzIiwiaGVhZGVycyIsIm1hcCIsImZvckVhY2giLCJhcHBlbmQiLCJBcnJheSIsImlzQXJyYXkiLCJoZWFkZXIiLCJnZXRPd25Qcm9wZXJ0eU5hbWVzIiwib2xkVmFsdWUiLCJnZXQiLCJoYXMiLCJoYXNPd25Qcm9wZXJ0eSIsInNldCIsImNhbGxiYWNrIiwidGhpc0FyZyIsImtleXMiLCJwdXNoIiwidmFsdWVzIiwiZW50cmllcyIsImNvbnN1bWVkIiwiYm9keSIsImJvZHlVc2VkIiwiUHJvbWlzZSIsInJlamVjdCIsImZpbGVSZWFkZXJSZWFkeSIsInJlYWRlciIsInJlc29sdmUiLCJvbmxvYWQiLCJyZXN1bHQiLCJvbmVycm9yIiwiZXJyb3IiLCJyZWFkQmxvYkFzQXJyYXlCdWZmZXIiLCJGaWxlUmVhZGVyIiwicHJvbWlzZSIsInJlYWRBc0FycmF5QnVmZmVyIiwicmVhZEJsb2JBc1RleHQiLCJyZWFkQXNUZXh0IiwicmVhZEFycmF5QnVmZmVyQXNUZXh0IiwiYnVmIiwidmlldyIsIlVpbnQ4QXJyYXkiLCJjaGFycyIsImxlbmd0aCIsImkiLCJmcm9tQ2hhckNvZGUiLCJqb2luIiwiYnVmZmVyQ2xvbmUiLCJzbGljZSIsImJ5dGVMZW5ndGgiLCJidWZmZXIiLCJCb2R5IiwiX2luaXRCb2R5IiwiX2JvZHlJbml0IiwiX2JvZHlUZXh0IiwiX2JvZHlCbG9iIiwiRm9ybURhdGEiLCJfYm9keUZvcm1EYXRhIiwiVVJMU2VhcmNoUGFyYW1zIiwiX2JvZHlBcnJheUJ1ZmZlciIsIkVycm9yIiwidHlwZSIsInJlamVjdGVkIiwidGhlbiIsInRleHQiLCJkZWNvZGUiLCJqc29uIiwiSlNPTiIsInBhcnNlIiwibWV0aG9kcyIsIm5vcm1hbGl6ZU1ldGhvZCIsIm1ldGhvZCIsInVwY2FzZWQiLCJ0b1VwcGVyQ2FzZSIsIlJlcXVlc3QiLCJpbnB1dCIsIm9wdGlvbnMiLCJ1cmwiLCJjcmVkZW50aWFscyIsIm1vZGUiLCJyZWZlcnJlciIsImNsb25lIiwiZm9ybSIsInRyaW0iLCJzcGxpdCIsImJ5dGVzIiwicmVwbGFjZSIsImRlY29kZVVSSUNvbXBvbmVudCIsInBhcnNlSGVhZGVycyIsInJhd0hlYWRlcnMiLCJwcmVQcm9jZXNzZWRIZWFkZXJzIiwibGluZSIsInBhcnRzIiwia2V5IiwiUmVzcG9uc2UiLCJib2R5SW5pdCIsInN0YXR1cyIsIm9rIiwic3RhdHVzVGV4dCIsInJlc3BvbnNlIiwicmVkaXJlY3RTdGF0dXNlcyIsInJlZGlyZWN0IiwiUmFuZ2VFcnJvciIsImxvY2F0aW9uIiwiaW5pdCIsInJlcXVlc3QiLCJ4aHIiLCJYTUxIdHRwUmVxdWVzdCIsImdldEFsbFJlc3BvbnNlSGVhZGVycyIsInJlc3BvbnNlVVJMIiwicmVzcG9uc2VUZXh0Iiwib250aW1lb3V0Iiwib3BlbiIsIndpdGhDcmVkZW50aWFscyIsInJlc3BvbnNlVHlwZSIsInNldFJlcXVlc3RIZWFkZXIiLCJzZW5kIiwicG9seWZpbGwiXSwibWFwcGluZ3MiOiI7O0FBQUEsQ0FBQyxVQUFTQSxJQUFULEVBQWU7QUFDZDs7QUFFQSxNQUFJQSxLQUFLQyxLQUFULEVBQWdCO0FBQ2Q7QUFDRDs7QUFFRCxNQUFJQyxVQUFVO0FBQ1pDLGtCQUFjLHFCQUFxQkgsSUFEdkI7QUFFWkksY0FBVSxZQUFZSixJQUFaLElBQW9CLGNBQWNLLE1BRmhDO0FBR1pDLFVBQU0sZ0JBQWdCTixJQUFoQixJQUF3QixVQUFVQSxJQUFsQyxJQUEyQyxZQUFXO0FBQzFELFVBQUk7QUFDRixZQUFJTyxJQUFKO0FBQ0EsZUFBTyxJQUFQO0FBQ0QsT0FIRCxDQUdFLE9BQU1DLENBQU4sRUFBUztBQUNULGVBQU8sS0FBUDtBQUNEO0FBQ0YsS0FQK0MsRUFIcEM7QUFXWkMsY0FBVSxjQUFjVCxJQVhaO0FBWVpVLGlCQUFhLGlCQUFpQlY7QUFabEIsR0FBZDs7QUFlQSxNQUFJRSxRQUFRUSxXQUFaLEVBQXlCO0FBQ3ZCLFFBQUlDLGNBQWMsQ0FDaEIsb0JBRGdCLEVBRWhCLHFCQUZnQixFQUdoQiw0QkFIZ0IsRUFJaEIscUJBSmdCLEVBS2hCLHNCQUxnQixFQU1oQixxQkFOZ0IsRUFPaEIsc0JBUGdCLEVBUWhCLHVCQVJnQixFQVNoQix1QkFUZ0IsQ0FBbEI7O0FBWUEsUUFBSUMsYUFBYSxTQUFiQSxVQUFhLENBQVNDLEdBQVQsRUFBYztBQUM3QixhQUFPQSxPQUFPQyxTQUFTQyxTQUFULENBQW1CQyxhQUFuQixDQUFpQ0gsR0FBakMsQ0FBZDtBQUNELEtBRkQ7O0FBSUEsUUFBSUksb0JBQW9CQyxZQUFZQyxNQUFaLElBQXNCLFVBQVNOLEdBQVQsRUFBYztBQUMxRCxhQUFPQSxPQUFPRixZQUFZUyxPQUFaLENBQW9CQyxPQUFPTixTQUFQLENBQWlCTyxRQUFqQixDQUEwQkMsSUFBMUIsQ0FBK0JWLEdBQS9CLENBQXBCLElBQTJELENBQUMsQ0FBMUU7QUFDRCxLQUZEO0FBR0Q7O0FBRUQsV0FBU1csYUFBVCxDQUF1QkMsSUFBdkIsRUFBNkI7QUFDM0IsUUFBSSxPQUFPQSxJQUFQLEtBQWdCLFFBQXBCLEVBQThCO0FBQzVCQSxhQUFPQyxPQUFPRCxJQUFQLENBQVA7QUFDRDtBQUNELFFBQUksNkJBQTZCRSxJQUE3QixDQUFrQ0YsSUFBbEMsQ0FBSixFQUE2QztBQUMzQyxZQUFNLElBQUlHLFNBQUosQ0FBYyx3Q0FBZCxDQUFOO0FBQ0Q7QUFDRCxXQUFPSCxLQUFLSSxXQUFMLEVBQVA7QUFDRDs7QUFFRCxXQUFTQyxjQUFULENBQXdCQyxLQUF4QixFQUErQjtBQUM3QixRQUFJLE9BQU9BLEtBQVAsS0FBaUIsUUFBckIsRUFBK0I7QUFDN0JBLGNBQVFMLE9BQU9LLEtBQVAsQ0FBUjtBQUNEO0FBQ0QsV0FBT0EsS0FBUDtBQUNEOztBQUVEO0FBQ0EsV0FBU0MsV0FBVCxDQUFxQkMsS0FBckIsRUFBNEI7QUFDMUIsUUFBSUMsV0FBVztBQUNiQyxZQUFNLGdCQUFXO0FBQ2YsWUFBSUosUUFBUUUsTUFBTUcsS0FBTixFQUFaO0FBQ0EsZUFBTyxFQUFDQyxNQUFNTixVQUFVTyxTQUFqQixFQUE0QlAsT0FBT0EsS0FBbkMsRUFBUDtBQUNEO0FBSlksS0FBZjs7QUFPQSxRQUFJN0IsUUFBUUUsUUFBWixFQUFzQjtBQUNwQjhCLGVBQVM3QixPQUFPNkIsUUFBaEIsSUFBNEIsWUFBVztBQUNyQyxlQUFPQSxRQUFQO0FBQ0QsT0FGRDtBQUdEOztBQUVELFdBQU9BLFFBQVA7QUFDRDs7QUFFRCxXQUFTSyxPQUFULENBQWlCQyxPQUFqQixFQUEwQjtBQUN4QixTQUFLQyxHQUFMLEdBQVcsRUFBWDs7QUFFQSxRQUFJRCxtQkFBbUJELE9BQXZCLEVBQWdDO0FBQzlCQyxjQUFRRSxPQUFSLENBQWdCLFVBQVNYLEtBQVQsRUFBZ0JOLElBQWhCLEVBQXNCO0FBQ3BDLGFBQUtrQixNQUFMLENBQVlsQixJQUFaLEVBQWtCTSxLQUFsQjtBQUNELE9BRkQsRUFFRyxJQUZIO0FBR0QsS0FKRCxNQUlPLElBQUlhLE1BQU1DLE9BQU4sQ0FBY0wsT0FBZCxDQUFKLEVBQTRCO0FBQ2pDQSxjQUFRRSxPQUFSLENBQWdCLFVBQVNJLE1BQVQsRUFBaUI7QUFDL0IsYUFBS0gsTUFBTCxDQUFZRyxPQUFPLENBQVAsQ0FBWixFQUF1QkEsT0FBTyxDQUFQLENBQXZCO0FBQ0QsT0FGRCxFQUVHLElBRkg7QUFHRCxLQUpNLE1BSUEsSUFBSU4sT0FBSixFQUFhO0FBQ2xCbkIsYUFBTzBCLG1CQUFQLENBQTJCUCxPQUEzQixFQUFvQ0UsT0FBcEMsQ0FBNEMsVUFBU2pCLElBQVQsRUFBZTtBQUN6RCxhQUFLa0IsTUFBTCxDQUFZbEIsSUFBWixFQUFrQmUsUUFBUWYsSUFBUixDQUFsQjtBQUNELE9BRkQsRUFFRyxJQUZIO0FBR0Q7QUFDRjs7QUFFRGMsVUFBUXhCLFNBQVIsQ0FBa0I0QixNQUFsQixHQUEyQixVQUFTbEIsSUFBVCxFQUFlTSxLQUFmLEVBQXNCO0FBQy9DTixXQUFPRCxjQUFjQyxJQUFkLENBQVA7QUFDQU0sWUFBUUQsZUFBZUMsS0FBZixDQUFSO0FBQ0EsUUFBSWlCLFdBQVcsS0FBS1AsR0FBTCxDQUFTaEIsSUFBVCxDQUFmO0FBQ0EsU0FBS2dCLEdBQUwsQ0FBU2hCLElBQVQsSUFBaUJ1QixXQUFXQSxXQUFTLEdBQVQsR0FBYWpCLEtBQXhCLEdBQWdDQSxLQUFqRDtBQUNELEdBTEQ7O0FBT0FRLFVBQVF4QixTQUFSLENBQWtCLFFBQWxCLElBQThCLFVBQVNVLElBQVQsRUFBZTtBQUMzQyxXQUFPLEtBQUtnQixHQUFMLENBQVNqQixjQUFjQyxJQUFkLENBQVQsQ0FBUDtBQUNELEdBRkQ7O0FBSUFjLFVBQVF4QixTQUFSLENBQWtCa0MsR0FBbEIsR0FBd0IsVUFBU3hCLElBQVQsRUFBZTtBQUNyQ0EsV0FBT0QsY0FBY0MsSUFBZCxDQUFQO0FBQ0EsV0FBTyxLQUFLeUIsR0FBTCxDQUFTekIsSUFBVCxJQUFpQixLQUFLZ0IsR0FBTCxDQUFTaEIsSUFBVCxDQUFqQixHQUFrQyxJQUF6QztBQUNELEdBSEQ7O0FBS0FjLFVBQVF4QixTQUFSLENBQWtCbUMsR0FBbEIsR0FBd0IsVUFBU3pCLElBQVQsRUFBZTtBQUNyQyxXQUFPLEtBQUtnQixHQUFMLENBQVNVLGNBQVQsQ0FBd0IzQixjQUFjQyxJQUFkLENBQXhCLENBQVA7QUFDRCxHQUZEOztBQUlBYyxVQUFReEIsU0FBUixDQUFrQnFDLEdBQWxCLEdBQXdCLFVBQVMzQixJQUFULEVBQWVNLEtBQWYsRUFBc0I7QUFDNUMsU0FBS1UsR0FBTCxDQUFTakIsY0FBY0MsSUFBZCxDQUFULElBQWdDSyxlQUFlQyxLQUFmLENBQWhDO0FBQ0QsR0FGRDs7QUFJQVEsVUFBUXhCLFNBQVIsQ0FBa0IyQixPQUFsQixHQUE0QixVQUFTVyxRQUFULEVBQW1CQyxPQUFuQixFQUE0QjtBQUN0RCxTQUFLLElBQUk3QixJQUFULElBQWlCLEtBQUtnQixHQUF0QixFQUEyQjtBQUN6QixVQUFJLEtBQUtBLEdBQUwsQ0FBU1UsY0FBVCxDQUF3QjFCLElBQXhCLENBQUosRUFBbUM7QUFDakM0QixpQkFBUzlCLElBQVQsQ0FBYytCLE9BQWQsRUFBdUIsS0FBS2IsR0FBTCxDQUFTaEIsSUFBVCxDQUF2QixFQUF1Q0EsSUFBdkMsRUFBNkMsSUFBN0M7QUFDRDtBQUNGO0FBQ0YsR0FORDs7QUFRQWMsVUFBUXhCLFNBQVIsQ0FBa0J3QyxJQUFsQixHQUF5QixZQUFXO0FBQ2xDLFFBQUl0QixRQUFRLEVBQVo7QUFDQSxTQUFLUyxPQUFMLENBQWEsVUFBU1gsS0FBVCxFQUFnQk4sSUFBaEIsRUFBc0I7QUFBRVEsWUFBTXVCLElBQU4sQ0FBVy9CLElBQVg7QUFBa0IsS0FBdkQ7QUFDQSxXQUFPTyxZQUFZQyxLQUFaLENBQVA7QUFDRCxHQUpEOztBQU1BTSxVQUFReEIsU0FBUixDQUFrQjBDLE1BQWxCLEdBQTJCLFlBQVc7QUFDcEMsUUFBSXhCLFFBQVEsRUFBWjtBQUNBLFNBQUtTLE9BQUwsQ0FBYSxVQUFTWCxLQUFULEVBQWdCO0FBQUVFLFlBQU11QixJQUFOLENBQVd6QixLQUFYO0FBQW1CLEtBQWxEO0FBQ0EsV0FBT0MsWUFBWUMsS0FBWixDQUFQO0FBQ0QsR0FKRDs7QUFNQU0sVUFBUXhCLFNBQVIsQ0FBa0IyQyxPQUFsQixHQUE0QixZQUFXO0FBQ3JDLFFBQUl6QixRQUFRLEVBQVo7QUFDQSxTQUFLUyxPQUFMLENBQWEsVUFBU1gsS0FBVCxFQUFnQk4sSUFBaEIsRUFBc0I7QUFBRVEsWUFBTXVCLElBQU4sQ0FBVyxDQUFDL0IsSUFBRCxFQUFPTSxLQUFQLENBQVg7QUFBMkIsS0FBaEU7QUFDQSxXQUFPQyxZQUFZQyxLQUFaLENBQVA7QUFDRCxHQUpEOztBQU1BLE1BQUkvQixRQUFRRSxRQUFaLEVBQXNCO0FBQ3BCbUMsWUFBUXhCLFNBQVIsQ0FBa0JWLE9BQU82QixRQUF6QixJQUFxQ0ssUUFBUXhCLFNBQVIsQ0FBa0IyQyxPQUF2RDtBQUNEOztBQUVELFdBQVNDLFFBQVQsQ0FBa0JDLElBQWxCLEVBQXdCO0FBQ3RCLFFBQUlBLEtBQUtDLFFBQVQsRUFBbUI7QUFDakIsYUFBT0MsUUFBUUMsTUFBUixDQUFlLElBQUluQyxTQUFKLENBQWMsY0FBZCxDQUFmLENBQVA7QUFDRDtBQUNEZ0MsU0FBS0MsUUFBTCxHQUFnQixJQUFoQjtBQUNEOztBQUVELFdBQVNHLGVBQVQsQ0FBeUJDLE1BQXpCLEVBQWlDO0FBQy9CLFdBQU8sSUFBSUgsT0FBSixDQUFZLFVBQVNJLE9BQVQsRUFBa0JILE1BQWxCLEVBQTBCO0FBQzNDRSxhQUFPRSxNQUFQLEdBQWdCLFlBQVc7QUFDekJELGdCQUFRRCxPQUFPRyxNQUFmO0FBQ0QsT0FGRDtBQUdBSCxhQUFPSSxPQUFQLEdBQWlCLFlBQVc7QUFDMUJOLGVBQU9FLE9BQU9LLEtBQWQ7QUFDRCxPQUZEO0FBR0QsS0FQTSxDQUFQO0FBUUQ7O0FBRUQsV0FBU0MscUJBQVQsQ0FBK0JqRSxJQUEvQixFQUFxQztBQUNuQyxRQUFJMkQsU0FBUyxJQUFJTyxVQUFKLEVBQWI7QUFDQSxRQUFJQyxVQUFVVCxnQkFBZ0JDLE1BQWhCLENBQWQ7QUFDQUEsV0FBT1MsaUJBQVAsQ0FBeUJwRSxJQUF6QjtBQUNBLFdBQU9tRSxPQUFQO0FBQ0Q7O0FBRUQsV0FBU0UsY0FBVCxDQUF3QnJFLElBQXhCLEVBQThCO0FBQzVCLFFBQUkyRCxTQUFTLElBQUlPLFVBQUosRUFBYjtBQUNBLFFBQUlDLFVBQVVULGdCQUFnQkMsTUFBaEIsQ0FBZDtBQUNBQSxXQUFPVyxVQUFQLENBQWtCdEUsSUFBbEI7QUFDQSxXQUFPbUUsT0FBUDtBQUNEOztBQUVELFdBQVNJLHFCQUFULENBQStCQyxHQUEvQixFQUFvQztBQUNsQyxRQUFJQyxPQUFPLElBQUlDLFVBQUosQ0FBZUYsR0FBZixDQUFYO0FBQ0EsUUFBSUcsUUFBUSxJQUFJckMsS0FBSixDQUFVbUMsS0FBS0csTUFBZixDQUFaOztBQUVBLFNBQUssSUFBSUMsSUFBSSxDQUFiLEVBQWdCQSxJQUFJSixLQUFLRyxNQUF6QixFQUFpQ0MsR0FBakMsRUFBc0M7QUFDcENGLFlBQU1FLENBQU4sSUFBV3pELE9BQU8wRCxZQUFQLENBQW9CTCxLQUFLSSxDQUFMLENBQXBCLENBQVg7QUFDRDtBQUNELFdBQU9GLE1BQU1JLElBQU4sQ0FBVyxFQUFYLENBQVA7QUFDRDs7QUFFRCxXQUFTQyxXQUFULENBQXFCUixHQUFyQixFQUEwQjtBQUN4QixRQUFJQSxJQUFJUyxLQUFSLEVBQWU7QUFDYixhQUFPVCxJQUFJUyxLQUFKLENBQVUsQ0FBVixDQUFQO0FBQ0QsS0FGRCxNQUVPO0FBQ0wsVUFBSVIsT0FBTyxJQUFJQyxVQUFKLENBQWVGLElBQUlVLFVBQW5CLENBQVg7QUFDQVQsV0FBSzNCLEdBQUwsQ0FBUyxJQUFJNEIsVUFBSixDQUFlRixHQUFmLENBQVQ7QUFDQSxhQUFPQyxLQUFLVSxNQUFaO0FBQ0Q7QUFDRjs7QUFFRCxXQUFTQyxJQUFULEdBQWdCO0FBQ2QsU0FBSzdCLFFBQUwsR0FBZ0IsS0FBaEI7O0FBRUEsU0FBSzhCLFNBQUwsR0FBaUIsVUFBUy9CLElBQVQsRUFBZTtBQUM5QixXQUFLZ0MsU0FBTCxHQUFpQmhDLElBQWpCO0FBQ0EsVUFBSSxDQUFDQSxJQUFMLEVBQVc7QUFDVCxhQUFLaUMsU0FBTCxHQUFpQixFQUFqQjtBQUNELE9BRkQsTUFFTyxJQUFJLE9BQU9qQyxJQUFQLEtBQWdCLFFBQXBCLEVBQThCO0FBQ25DLGFBQUtpQyxTQUFMLEdBQWlCakMsSUFBakI7QUFDRCxPQUZNLE1BRUEsSUFBSTFELFFBQVFJLElBQVIsSUFBZ0JDLEtBQUtRLFNBQUwsQ0FBZUMsYUFBZixDQUE2QjRDLElBQTdCLENBQXBCLEVBQXdEO0FBQzdELGFBQUtrQyxTQUFMLEdBQWlCbEMsSUFBakI7QUFDRCxPQUZNLE1BRUEsSUFBSTFELFFBQVFPLFFBQVIsSUFBb0JzRixTQUFTaEYsU0FBVCxDQUFtQkMsYUFBbkIsQ0FBaUM0QyxJQUFqQyxDQUF4QixFQUFnRTtBQUNyRSxhQUFLb0MsYUFBTCxHQUFxQnBDLElBQXJCO0FBQ0QsT0FGTSxNQUVBLElBQUkxRCxRQUFRQyxZQUFSLElBQXdCOEYsZ0JBQWdCbEYsU0FBaEIsQ0FBMEJDLGFBQTFCLENBQXdDNEMsSUFBeEMsQ0FBNUIsRUFBMkU7QUFDaEYsYUFBS2lDLFNBQUwsR0FBaUJqQyxLQUFLdEMsUUFBTCxFQUFqQjtBQUNELE9BRk0sTUFFQSxJQUFJcEIsUUFBUVEsV0FBUixJQUF1QlIsUUFBUUksSUFBL0IsSUFBdUNNLFdBQVdnRCxJQUFYLENBQTNDLEVBQTZEO0FBQ2xFLGFBQUtzQyxnQkFBTCxHQUF3QlosWUFBWTFCLEtBQUs2QixNQUFqQixDQUF4QjtBQUNBO0FBQ0EsYUFBS0csU0FBTCxHQUFpQixJQUFJckYsSUFBSixDQUFTLENBQUMsS0FBSzJGLGdCQUFOLENBQVQsQ0FBakI7QUFDRCxPQUpNLE1BSUEsSUFBSWhHLFFBQVFRLFdBQVIsS0FBd0JRLFlBQVlILFNBQVosQ0FBc0JDLGFBQXRCLENBQW9DNEMsSUFBcEMsS0FBNkMzQyxrQkFBa0IyQyxJQUFsQixDQUFyRSxDQUFKLEVBQW1HO0FBQ3hHLGFBQUtzQyxnQkFBTCxHQUF3QlosWUFBWTFCLElBQVosQ0FBeEI7QUFDRCxPQUZNLE1BRUE7QUFDTCxjQUFNLElBQUl1QyxLQUFKLENBQVUsMkJBQVYsQ0FBTjtBQUNEOztBQUVELFVBQUksQ0FBQyxLQUFLM0QsT0FBTCxDQUFhUyxHQUFiLENBQWlCLGNBQWpCLENBQUwsRUFBdUM7QUFDckMsWUFBSSxPQUFPVyxJQUFQLEtBQWdCLFFBQXBCLEVBQThCO0FBQzVCLGVBQUtwQixPQUFMLENBQWFZLEdBQWIsQ0FBaUIsY0FBakIsRUFBaUMsMEJBQWpDO0FBQ0QsU0FGRCxNQUVPLElBQUksS0FBSzBDLFNBQUwsSUFBa0IsS0FBS0EsU0FBTCxDQUFlTSxJQUFyQyxFQUEyQztBQUNoRCxlQUFLNUQsT0FBTCxDQUFhWSxHQUFiLENBQWlCLGNBQWpCLEVBQWlDLEtBQUswQyxTQUFMLENBQWVNLElBQWhEO0FBQ0QsU0FGTSxNQUVBLElBQUlsRyxRQUFRQyxZQUFSLElBQXdCOEYsZ0JBQWdCbEYsU0FBaEIsQ0FBMEJDLGFBQTFCLENBQXdDNEMsSUFBeEMsQ0FBNUIsRUFBMkU7QUFDaEYsZUFBS3BCLE9BQUwsQ0FBYVksR0FBYixDQUFpQixjQUFqQixFQUFpQyxpREFBakM7QUFDRDtBQUNGO0FBQ0YsS0EvQkQ7O0FBaUNBLFFBQUlsRCxRQUFRSSxJQUFaLEVBQWtCO0FBQ2hCLFdBQUtBLElBQUwsR0FBWSxZQUFXO0FBQ3JCLFlBQUkrRixXQUFXMUMsU0FBUyxJQUFULENBQWY7QUFDQSxZQUFJMEMsUUFBSixFQUFjO0FBQ1osaUJBQU9BLFFBQVA7QUFDRDs7QUFFRCxZQUFJLEtBQUtQLFNBQVQsRUFBb0I7QUFDbEIsaUJBQU9oQyxRQUFRSSxPQUFSLENBQWdCLEtBQUs0QixTQUFyQixDQUFQO0FBQ0QsU0FGRCxNQUVPLElBQUksS0FBS0ksZ0JBQVQsRUFBMkI7QUFDaEMsaUJBQU9wQyxRQUFRSSxPQUFSLENBQWdCLElBQUkzRCxJQUFKLENBQVMsQ0FBQyxLQUFLMkYsZ0JBQU4sQ0FBVCxDQUFoQixDQUFQO0FBQ0QsU0FGTSxNQUVBLElBQUksS0FBS0YsYUFBVCxFQUF3QjtBQUM3QixnQkFBTSxJQUFJRyxLQUFKLENBQVUsc0NBQVYsQ0FBTjtBQUNELFNBRk0sTUFFQTtBQUNMLGlCQUFPckMsUUFBUUksT0FBUixDQUFnQixJQUFJM0QsSUFBSixDQUFTLENBQUMsS0FBS3NGLFNBQU4sQ0FBVCxDQUFoQixDQUFQO0FBQ0Q7QUFDRixPQWZEOztBQWlCQSxXQUFLbkYsV0FBTCxHQUFtQixZQUFXO0FBQzVCLFlBQUksS0FBS3dGLGdCQUFULEVBQTJCO0FBQ3pCLGlCQUFPdkMsU0FBUyxJQUFULEtBQWtCRyxRQUFRSSxPQUFSLENBQWdCLEtBQUtnQyxnQkFBckIsQ0FBekI7QUFDRCxTQUZELE1BRU87QUFDTCxpQkFBTyxLQUFLNUYsSUFBTCxHQUFZZ0csSUFBWixDQUFpQi9CLHFCQUFqQixDQUFQO0FBQ0Q7QUFDRixPQU5EO0FBT0Q7O0FBRUQsU0FBS2dDLElBQUwsR0FBWSxZQUFXO0FBQ3JCLFVBQUlGLFdBQVcxQyxTQUFTLElBQVQsQ0FBZjtBQUNBLFVBQUkwQyxRQUFKLEVBQWM7QUFDWixlQUFPQSxRQUFQO0FBQ0Q7O0FBRUQsVUFBSSxLQUFLUCxTQUFULEVBQW9CO0FBQ2xCLGVBQU9uQixlQUFlLEtBQUttQixTQUFwQixDQUFQO0FBQ0QsT0FGRCxNQUVPLElBQUksS0FBS0ksZ0JBQVQsRUFBMkI7QUFDaEMsZUFBT3BDLFFBQVFJLE9BQVIsQ0FBZ0JXLHNCQUFzQixLQUFLcUIsZ0JBQTNCLENBQWhCLENBQVA7QUFDRCxPQUZNLE1BRUEsSUFBSSxLQUFLRixhQUFULEVBQXdCO0FBQzdCLGNBQU0sSUFBSUcsS0FBSixDQUFVLHNDQUFWLENBQU47QUFDRCxPQUZNLE1BRUE7QUFDTCxlQUFPckMsUUFBUUksT0FBUixDQUFnQixLQUFLMkIsU0FBckIsQ0FBUDtBQUNEO0FBQ0YsS0FmRDs7QUFpQkEsUUFBSTNGLFFBQVFPLFFBQVosRUFBc0I7QUFDcEIsV0FBS0EsUUFBTCxHQUFnQixZQUFXO0FBQ3pCLGVBQU8sS0FBSzhGLElBQUwsR0FBWUQsSUFBWixDQUFpQkUsTUFBakIsQ0FBUDtBQUNELE9BRkQ7QUFHRDs7QUFFRCxTQUFLQyxJQUFMLEdBQVksWUFBVztBQUNyQixhQUFPLEtBQUtGLElBQUwsR0FBWUQsSUFBWixDQUFpQkksS0FBS0MsS0FBdEIsQ0FBUDtBQUNELEtBRkQ7O0FBSUEsV0FBTyxJQUFQO0FBQ0Q7O0FBRUQ7QUFDQSxNQUFJQyxVQUFVLENBQUMsUUFBRCxFQUFXLEtBQVgsRUFBa0IsTUFBbEIsRUFBMEIsU0FBMUIsRUFBcUMsTUFBckMsRUFBNkMsS0FBN0MsQ0FBZDs7QUFFQSxXQUFTQyxlQUFULENBQXlCQyxNQUF6QixFQUFpQztBQUMvQixRQUFJQyxVQUFVRCxPQUFPRSxXQUFQLEVBQWQ7QUFDQSxXQUFRSixRQUFReEYsT0FBUixDQUFnQjJGLE9BQWhCLElBQTJCLENBQUMsQ0FBN0IsR0FBa0NBLE9BQWxDLEdBQTRDRCxNQUFuRDtBQUNEOztBQUVELFdBQVNHLE9BQVQsQ0FBaUJDLEtBQWpCLEVBQXdCQyxPQUF4QixFQUFpQztBQUMvQkEsY0FBVUEsV0FBVyxFQUFyQjtBQUNBLFFBQUl2RCxPQUFPdUQsUUFBUXZELElBQW5COztBQUVBLFFBQUlzRCxpQkFBaUJELE9BQXJCLEVBQThCO0FBQzVCLFVBQUlDLE1BQU1yRCxRQUFWLEVBQW9CO0FBQ2xCLGNBQU0sSUFBSWpDLFNBQUosQ0FBYyxjQUFkLENBQU47QUFDRDtBQUNELFdBQUt3RixHQUFMLEdBQVdGLE1BQU1FLEdBQWpCO0FBQ0EsV0FBS0MsV0FBTCxHQUFtQkgsTUFBTUcsV0FBekI7QUFDQSxVQUFJLENBQUNGLFFBQVEzRSxPQUFiLEVBQXNCO0FBQ3BCLGFBQUtBLE9BQUwsR0FBZSxJQUFJRCxPQUFKLENBQVkyRSxNQUFNMUUsT0FBbEIsQ0FBZjtBQUNEO0FBQ0QsV0FBS3NFLE1BQUwsR0FBY0ksTUFBTUosTUFBcEI7QUFDQSxXQUFLUSxJQUFMLEdBQVlKLE1BQU1JLElBQWxCO0FBQ0EsVUFBSSxDQUFDMUQsSUFBRCxJQUFTc0QsTUFBTXRCLFNBQU4sSUFBbUIsSUFBaEMsRUFBc0M7QUFDcENoQyxlQUFPc0QsTUFBTXRCLFNBQWI7QUFDQXNCLGNBQU1yRCxRQUFOLEdBQWlCLElBQWpCO0FBQ0Q7QUFDRixLQWZELE1BZU87QUFDTCxXQUFLdUQsR0FBTCxHQUFXMUYsT0FBT3dGLEtBQVAsQ0FBWDtBQUNEOztBQUVELFNBQUtHLFdBQUwsR0FBbUJGLFFBQVFFLFdBQVIsSUFBdUIsS0FBS0EsV0FBNUIsSUFBMkMsTUFBOUQ7QUFDQSxRQUFJRixRQUFRM0UsT0FBUixJQUFtQixDQUFDLEtBQUtBLE9BQTdCLEVBQXNDO0FBQ3BDLFdBQUtBLE9BQUwsR0FBZSxJQUFJRCxPQUFKLENBQVk0RSxRQUFRM0UsT0FBcEIsQ0FBZjtBQUNEO0FBQ0QsU0FBS3NFLE1BQUwsR0FBY0QsZ0JBQWdCTSxRQUFRTCxNQUFSLElBQWtCLEtBQUtBLE1BQXZCLElBQWlDLEtBQWpELENBQWQ7QUFDQSxTQUFLUSxJQUFMLEdBQVlILFFBQVFHLElBQVIsSUFBZ0IsS0FBS0EsSUFBckIsSUFBNkIsSUFBekM7QUFDQSxTQUFLQyxRQUFMLEdBQWdCLElBQWhCOztBQUVBLFFBQUksQ0FBQyxLQUFLVCxNQUFMLEtBQWdCLEtBQWhCLElBQXlCLEtBQUtBLE1BQUwsS0FBZ0IsTUFBMUMsS0FBcURsRCxJQUF6RCxFQUErRDtBQUM3RCxZQUFNLElBQUloQyxTQUFKLENBQWMsMkNBQWQsQ0FBTjtBQUNEO0FBQ0QsU0FBSytELFNBQUwsQ0FBZS9CLElBQWY7QUFDRDs7QUFFRHFELFVBQVFsRyxTQUFSLENBQWtCeUcsS0FBbEIsR0FBMEIsWUFBVztBQUNuQyxXQUFPLElBQUlQLE9BQUosQ0FBWSxJQUFaLEVBQWtCLEVBQUVyRCxNQUFNLEtBQUtnQyxTQUFiLEVBQWxCLENBQVA7QUFDRCxHQUZEOztBQUlBLFdBQVNZLE1BQVQsQ0FBZ0I1QyxJQUFoQixFQUFzQjtBQUNwQixRQUFJNkQsT0FBTyxJQUFJMUIsUUFBSixFQUFYO0FBQ0FuQyxTQUFLOEQsSUFBTCxHQUFZQyxLQUFaLENBQWtCLEdBQWxCLEVBQXVCakYsT0FBdkIsQ0FBK0IsVUFBU2tGLEtBQVQsRUFBZ0I7QUFDN0MsVUFBSUEsS0FBSixFQUFXO0FBQ1QsWUFBSUQsUUFBUUMsTUFBTUQsS0FBTixDQUFZLEdBQVosQ0FBWjtBQUNBLFlBQUlsRyxPQUFPa0csTUFBTXZGLEtBQU4sR0FBY3lGLE9BQWQsQ0FBc0IsS0FBdEIsRUFBNkIsR0FBN0IsQ0FBWDtBQUNBLFlBQUk5RixRQUFRNEYsTUFBTXRDLElBQU4sQ0FBVyxHQUFYLEVBQWdCd0MsT0FBaEIsQ0FBd0IsS0FBeEIsRUFBK0IsR0FBL0IsQ0FBWjtBQUNBSixhQUFLOUUsTUFBTCxDQUFZbUYsbUJBQW1CckcsSUFBbkIsQ0FBWixFQUFzQ3FHLG1CQUFtQi9GLEtBQW5CLENBQXRDO0FBQ0Q7QUFDRixLQVBEO0FBUUEsV0FBTzBGLElBQVA7QUFDRDs7QUFFRCxXQUFTTSxZQUFULENBQXNCQyxVQUF0QixFQUFrQztBQUNoQyxRQUFJeEYsVUFBVSxJQUFJRCxPQUFKLEVBQWQ7QUFDQTtBQUNBO0FBQ0EsUUFBSTBGLHNCQUFzQkQsV0FBV0gsT0FBWCxDQUFtQixjQUFuQixFQUFtQyxHQUFuQyxDQUExQjtBQUNBSSx3QkFBb0JOLEtBQXBCLENBQTBCLE9BQTFCLEVBQW1DakYsT0FBbkMsQ0FBMkMsVUFBU3dGLElBQVQsRUFBZTtBQUN4RCxVQUFJQyxRQUFRRCxLQUFLUCxLQUFMLENBQVcsR0FBWCxDQUFaO0FBQ0EsVUFBSVMsTUFBTUQsTUFBTS9GLEtBQU4sR0FBY3NGLElBQWQsRUFBVjtBQUNBLFVBQUlVLEdBQUosRUFBUztBQUNQLFlBQUlyRyxRQUFRb0csTUFBTTlDLElBQU4sQ0FBVyxHQUFYLEVBQWdCcUMsSUFBaEIsRUFBWjtBQUNBbEYsZ0JBQVFHLE1BQVIsQ0FBZXlGLEdBQWYsRUFBb0JyRyxLQUFwQjtBQUNEO0FBQ0YsS0FQRDtBQVFBLFdBQU9TLE9BQVA7QUFDRDs7QUFFRGtELE9BQUtuRSxJQUFMLENBQVUwRixRQUFRbEcsU0FBbEI7O0FBRUEsV0FBU3NILFFBQVQsQ0FBa0JDLFFBQWxCLEVBQTRCbkIsT0FBNUIsRUFBcUM7QUFDbkMsUUFBSSxDQUFDQSxPQUFMLEVBQWM7QUFDWkEsZ0JBQVUsRUFBVjtBQUNEOztBQUVELFNBQUtmLElBQUwsR0FBWSxTQUFaO0FBQ0EsU0FBS21DLE1BQUwsR0FBY3BCLFFBQVFvQixNQUFSLEtBQW1CakcsU0FBbkIsR0FBK0IsR0FBL0IsR0FBcUM2RSxRQUFRb0IsTUFBM0Q7QUFDQSxTQUFLQyxFQUFMLEdBQVUsS0FBS0QsTUFBTCxJQUFlLEdBQWYsSUFBc0IsS0FBS0EsTUFBTCxHQUFjLEdBQTlDO0FBQ0EsU0FBS0UsVUFBTCxHQUFrQixnQkFBZ0J0QixPQUFoQixHQUEwQkEsUUFBUXNCLFVBQWxDLEdBQStDLElBQWpFO0FBQ0EsU0FBS2pHLE9BQUwsR0FBZSxJQUFJRCxPQUFKLENBQVk0RSxRQUFRM0UsT0FBcEIsQ0FBZjtBQUNBLFNBQUs0RSxHQUFMLEdBQVdELFFBQVFDLEdBQVIsSUFBZSxFQUExQjtBQUNBLFNBQUt6QixTQUFMLENBQWUyQyxRQUFmO0FBQ0Q7O0FBRUQ1QyxPQUFLbkUsSUFBTCxDQUFVOEcsU0FBU3RILFNBQW5COztBQUVBc0gsV0FBU3RILFNBQVQsQ0FBbUJ5RyxLQUFuQixHQUEyQixZQUFXO0FBQ3BDLFdBQU8sSUFBSWEsUUFBSixDQUFhLEtBQUt6QyxTQUFsQixFQUE2QjtBQUNsQzJDLGNBQVEsS0FBS0EsTUFEcUI7QUFFbENFLGtCQUFZLEtBQUtBLFVBRmlCO0FBR2xDakcsZUFBUyxJQUFJRCxPQUFKLENBQVksS0FBS0MsT0FBakIsQ0FIeUI7QUFJbEM0RSxXQUFLLEtBQUtBO0FBSndCLEtBQTdCLENBQVA7QUFNRCxHQVBEOztBQVNBaUIsV0FBUy9ELEtBQVQsR0FBaUIsWUFBVztBQUMxQixRQUFJb0UsV0FBVyxJQUFJTCxRQUFKLENBQWEsSUFBYixFQUFtQixFQUFDRSxRQUFRLENBQVQsRUFBWUUsWUFBWSxFQUF4QixFQUFuQixDQUFmO0FBQ0FDLGFBQVN0QyxJQUFULEdBQWdCLE9BQWhCO0FBQ0EsV0FBT3NDLFFBQVA7QUFDRCxHQUpEOztBQU1BLE1BQUlDLG1CQUFtQixDQUFDLEdBQUQsRUFBTSxHQUFOLEVBQVcsR0FBWCxFQUFnQixHQUFoQixFQUFxQixHQUFyQixDQUF2Qjs7QUFFQU4sV0FBU08sUUFBVCxHQUFvQixVQUFTeEIsR0FBVCxFQUFjbUIsTUFBZCxFQUFzQjtBQUN4QyxRQUFJSSxpQkFBaUJ2SCxPQUFqQixDQUF5Qm1ILE1BQXpCLE1BQXFDLENBQUMsQ0FBMUMsRUFBNkM7QUFDM0MsWUFBTSxJQUFJTSxVQUFKLENBQWUscUJBQWYsQ0FBTjtBQUNEOztBQUVELFdBQU8sSUFBSVIsUUFBSixDQUFhLElBQWIsRUFBbUIsRUFBQ0UsUUFBUUEsTUFBVCxFQUFpQi9GLFNBQVMsRUFBQ3NHLFVBQVUxQixHQUFYLEVBQTFCLEVBQW5CLENBQVA7QUFDRCxHQU5EOztBQVFBcEgsT0FBS3VDLE9BQUwsR0FBZUEsT0FBZjtBQUNBdkMsT0FBS2lILE9BQUwsR0FBZUEsT0FBZjtBQUNBakgsT0FBS3FJLFFBQUwsR0FBZ0JBLFFBQWhCOztBQUVBckksT0FBS0MsS0FBTCxHQUFhLFVBQVNpSCxLQUFULEVBQWdCNkIsSUFBaEIsRUFBc0I7QUFDakMsV0FBTyxJQUFJakYsT0FBSixDQUFZLFVBQVNJLE9BQVQsRUFBa0JILE1BQWxCLEVBQTBCO0FBQzNDLFVBQUlpRixVQUFVLElBQUkvQixPQUFKLENBQVlDLEtBQVosRUFBbUI2QixJQUFuQixDQUFkO0FBQ0EsVUFBSUUsTUFBTSxJQUFJQyxjQUFKLEVBQVY7O0FBRUFELFVBQUk5RSxNQUFKLEdBQWEsWUFBVztBQUN0QixZQUFJZ0QsVUFBVTtBQUNab0Isa0JBQVFVLElBQUlWLE1BREE7QUFFWkUsc0JBQVlRLElBQUlSLFVBRko7QUFHWmpHLG1CQUFTdUYsYUFBYWtCLElBQUlFLHFCQUFKLE1BQStCLEVBQTVDO0FBSEcsU0FBZDtBQUtBaEMsZ0JBQVFDLEdBQVIsR0FBYyxpQkFBaUI2QixHQUFqQixHQUF1QkEsSUFBSUcsV0FBM0IsR0FBeUNqQyxRQUFRM0UsT0FBUixDQUFnQlMsR0FBaEIsQ0FBb0IsZUFBcEIsQ0FBdkQ7QUFDQSxZQUFJVyxPQUFPLGNBQWNxRixHQUFkLEdBQW9CQSxJQUFJUCxRQUF4QixHQUFtQ08sSUFBSUksWUFBbEQ7QUFDQW5GLGdCQUFRLElBQUltRSxRQUFKLENBQWF6RSxJQUFiLEVBQW1CdUQsT0FBbkIsQ0FBUjtBQUNELE9BVEQ7O0FBV0E4QixVQUFJNUUsT0FBSixHQUFjLFlBQVc7QUFDdkJOLGVBQU8sSUFBSW5DLFNBQUosQ0FBYyx3QkFBZCxDQUFQO0FBQ0QsT0FGRDs7QUFJQXFILFVBQUlLLFNBQUosR0FBZ0IsWUFBVztBQUN6QnZGLGVBQU8sSUFBSW5DLFNBQUosQ0FBYyx3QkFBZCxDQUFQO0FBQ0QsT0FGRDs7QUFJQXFILFVBQUlNLElBQUosQ0FBU1AsUUFBUWxDLE1BQWpCLEVBQXlCa0MsUUFBUTVCLEdBQWpDLEVBQXNDLElBQXRDOztBQUVBLFVBQUk0QixRQUFRM0IsV0FBUixLQUF3QixTQUE1QixFQUF1QztBQUNyQzRCLFlBQUlPLGVBQUosR0FBc0IsSUFBdEI7QUFDRCxPQUZELE1BRU8sSUFBSVIsUUFBUTNCLFdBQVIsS0FBd0IsTUFBNUIsRUFBb0M7QUFDekM0QixZQUFJTyxlQUFKLEdBQXNCLEtBQXRCO0FBQ0Q7O0FBRUQsVUFBSSxrQkFBa0JQLEdBQWxCLElBQXlCL0ksUUFBUUksSUFBckMsRUFBMkM7QUFDekMySSxZQUFJUSxZQUFKLEdBQW1CLE1BQW5CO0FBQ0Q7O0FBRURULGNBQVF4RyxPQUFSLENBQWdCRSxPQUFoQixDQUF3QixVQUFTWCxLQUFULEVBQWdCTixJQUFoQixFQUFzQjtBQUM1Q3dILFlBQUlTLGdCQUFKLENBQXFCakksSUFBckIsRUFBMkJNLEtBQTNCO0FBQ0QsT0FGRDs7QUFJQWtILFVBQUlVLElBQUosQ0FBUyxPQUFPWCxRQUFRcEQsU0FBZixLQUE2QixXQUE3QixHQUEyQyxJQUEzQyxHQUFrRG9ELFFBQVFwRCxTQUFuRTtBQUNELEtBeENNLENBQVA7QUF5Q0QsR0ExQ0Q7QUEyQ0E1RixPQUFLQyxLQUFMLENBQVcySixRQUFYLEdBQXNCLElBQXRCO0FBQ0QsQ0FqZEQsRUFpZEcsT0FBTzVKLElBQVAsS0FBZ0IsV0FBaEIsR0FBOEJBLElBQTlCLFlBamRIIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvdmVuZG9yL2ZldGNoLmpzIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKHNlbGYpIHtcbiAgJ3VzZSBzdHJpY3QnO1xuXG4gIGlmIChzZWxmLmZldGNoKSB7XG4gICAgcmV0dXJuXG4gIH1cblxuICB2YXIgc3VwcG9ydCA9IHtcbiAgICBzZWFyY2hQYXJhbXM6ICdVUkxTZWFyY2hQYXJhbXMnIGluIHNlbGYsXG4gICAgaXRlcmFibGU6ICdTeW1ib2wnIGluIHNlbGYgJiYgJ2l0ZXJhdG9yJyBpbiBTeW1ib2wsXG4gICAgYmxvYjogJ0ZpbGVSZWFkZXInIGluIHNlbGYgJiYgJ0Jsb2InIGluIHNlbGYgJiYgKGZ1bmN0aW9uKCkge1xuICAgICAgdHJ5IHtcbiAgICAgICAgbmV3IEJsb2IoKVxuICAgICAgICByZXR1cm4gdHJ1ZVxuICAgICAgfSBjYXRjaChlKSB7XG4gICAgICAgIHJldHVybiBmYWxzZVxuICAgICAgfVxuICAgIH0pKCksXG4gICAgZm9ybURhdGE6ICdGb3JtRGF0YScgaW4gc2VsZixcbiAgICBhcnJheUJ1ZmZlcjogJ0FycmF5QnVmZmVyJyBpbiBzZWxmXG4gIH1cblxuICBpZiAoc3VwcG9ydC5hcnJheUJ1ZmZlcikge1xuICAgIHZhciB2aWV3Q2xhc3NlcyA9IFtcbiAgICAgICdbb2JqZWN0IEludDhBcnJheV0nLFxuICAgICAgJ1tvYmplY3QgVWludDhBcnJheV0nLFxuICAgICAgJ1tvYmplY3QgVWludDhDbGFtcGVkQXJyYXldJyxcbiAgICAgICdbb2JqZWN0IEludDE2QXJyYXldJyxcbiAgICAgICdbb2JqZWN0IFVpbnQxNkFycmF5XScsXG4gICAgICAnW29iamVjdCBJbnQzMkFycmF5XScsXG4gICAgICAnW29iamVjdCBVaW50MzJBcnJheV0nLFxuICAgICAgJ1tvYmplY3QgRmxvYXQzMkFycmF5XScsXG4gICAgICAnW29iamVjdCBGbG9hdDY0QXJyYXldJ1xuICAgIF1cblxuICAgIHZhciBpc0RhdGFWaWV3ID0gZnVuY3Rpb24ob2JqKSB7XG4gICAgICByZXR1cm4gb2JqICYmIERhdGFWaWV3LnByb3RvdHlwZS5pc1Byb3RvdHlwZU9mKG9iailcbiAgICB9XG5cbiAgICB2YXIgaXNBcnJheUJ1ZmZlclZpZXcgPSBBcnJheUJ1ZmZlci5pc1ZpZXcgfHwgZnVuY3Rpb24ob2JqKSB7XG4gICAgICByZXR1cm4gb2JqICYmIHZpZXdDbGFzc2VzLmluZGV4T2YoT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKG9iaikpID4gLTFcbiAgICB9XG4gIH1cblxuICBmdW5jdGlvbiBub3JtYWxpemVOYW1lKG5hbWUpIHtcbiAgICBpZiAodHlwZW9mIG5hbWUgIT09ICdzdHJpbmcnKSB7XG4gICAgICBuYW1lID0gU3RyaW5nKG5hbWUpXG4gICAgfVxuICAgIGlmICgvW15hLXowLTlcXC0jJCUmJyorLlxcXl9gfH5dL2kudGVzdChuYW1lKSkge1xuICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignSW52YWxpZCBjaGFyYWN0ZXIgaW4gaGVhZGVyIGZpZWxkIG5hbWUnKVxuICAgIH1cbiAgICByZXR1cm4gbmFtZS50b0xvd2VyQ2FzZSgpXG4gIH1cblxuICBmdW5jdGlvbiBub3JtYWxpemVWYWx1ZSh2YWx1ZSkge1xuICAgIGlmICh0eXBlb2YgdmFsdWUgIT09ICdzdHJpbmcnKSB7XG4gICAgICB2YWx1ZSA9IFN0cmluZyh2YWx1ZSlcbiAgICB9XG4gICAgcmV0dXJuIHZhbHVlXG4gIH1cblxuICAvLyBCdWlsZCBhIGRlc3RydWN0aXZlIGl0ZXJhdG9yIGZvciB0aGUgdmFsdWUgbGlzdFxuICBmdW5jdGlvbiBpdGVyYXRvckZvcihpdGVtcykge1xuICAgIHZhciBpdGVyYXRvciA9IHtcbiAgICAgIG5leHQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICB2YXIgdmFsdWUgPSBpdGVtcy5zaGlmdCgpXG4gICAgICAgIHJldHVybiB7ZG9uZTogdmFsdWUgPT09IHVuZGVmaW5lZCwgdmFsdWU6IHZhbHVlfVxuICAgICAgfVxuICAgIH1cblxuICAgIGlmIChzdXBwb3J0Lml0ZXJhYmxlKSB7XG4gICAgICBpdGVyYXRvcltTeW1ib2wuaXRlcmF0b3JdID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiBpdGVyYXRvclxuICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBpdGVyYXRvclxuICB9XG5cbiAgZnVuY3Rpb24gSGVhZGVycyhoZWFkZXJzKSB7XG4gICAgdGhpcy5tYXAgPSB7fVxuXG4gICAgaWYgKGhlYWRlcnMgaW5zdGFuY2VvZiBIZWFkZXJzKSB7XG4gICAgICBoZWFkZXJzLmZvckVhY2goZnVuY3Rpb24odmFsdWUsIG5hbWUpIHtcbiAgICAgICAgdGhpcy5hcHBlbmQobmFtZSwgdmFsdWUpXG4gICAgICB9LCB0aGlzKVxuICAgIH0gZWxzZSBpZiAoQXJyYXkuaXNBcnJheShoZWFkZXJzKSkge1xuICAgICAgaGVhZGVycy5mb3JFYWNoKGZ1bmN0aW9uKGhlYWRlcikge1xuICAgICAgICB0aGlzLmFwcGVuZChoZWFkZXJbMF0sIGhlYWRlclsxXSlcbiAgICAgIH0sIHRoaXMpXG4gICAgfSBlbHNlIGlmIChoZWFkZXJzKSB7XG4gICAgICBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhoZWFkZXJzKS5mb3JFYWNoKGZ1bmN0aW9uKG5hbWUpIHtcbiAgICAgICAgdGhpcy5hcHBlbmQobmFtZSwgaGVhZGVyc1tuYW1lXSlcbiAgICAgIH0sIHRoaXMpXG4gICAgfVxuICB9XG5cbiAgSGVhZGVycy5wcm90b3R5cGUuYXBwZW5kID0gZnVuY3Rpb24obmFtZSwgdmFsdWUpIHtcbiAgICBuYW1lID0gbm9ybWFsaXplTmFtZShuYW1lKVxuICAgIHZhbHVlID0gbm9ybWFsaXplVmFsdWUodmFsdWUpXG4gICAgdmFyIG9sZFZhbHVlID0gdGhpcy5tYXBbbmFtZV1cbiAgICB0aGlzLm1hcFtuYW1lXSA9IG9sZFZhbHVlID8gb2xkVmFsdWUrJywnK3ZhbHVlIDogdmFsdWVcbiAgfVxuXG4gIEhlYWRlcnMucHJvdG90eXBlWydkZWxldGUnXSA9IGZ1bmN0aW9uKG5hbWUpIHtcbiAgICBkZWxldGUgdGhpcy5tYXBbbm9ybWFsaXplTmFtZShuYW1lKV1cbiAgfVxuXG4gIEhlYWRlcnMucHJvdG90eXBlLmdldCA9IGZ1bmN0aW9uKG5hbWUpIHtcbiAgICBuYW1lID0gbm9ybWFsaXplTmFtZShuYW1lKVxuICAgIHJldHVybiB0aGlzLmhhcyhuYW1lKSA/IHRoaXMubWFwW25hbWVdIDogbnVsbFxuICB9XG5cbiAgSGVhZGVycy5wcm90b3R5cGUuaGFzID0gZnVuY3Rpb24obmFtZSkge1xuICAgIHJldHVybiB0aGlzLm1hcC5oYXNPd25Qcm9wZXJ0eShub3JtYWxpemVOYW1lKG5hbWUpKVxuICB9XG5cbiAgSGVhZGVycy5wcm90b3R5cGUuc2V0ID0gZnVuY3Rpb24obmFtZSwgdmFsdWUpIHtcbiAgICB0aGlzLm1hcFtub3JtYWxpemVOYW1lKG5hbWUpXSA9IG5vcm1hbGl6ZVZhbHVlKHZhbHVlKVxuICB9XG5cbiAgSGVhZGVycy5wcm90b3R5cGUuZm9yRWFjaCA9IGZ1bmN0aW9uKGNhbGxiYWNrLCB0aGlzQXJnKSB7XG4gICAgZm9yICh2YXIgbmFtZSBpbiB0aGlzLm1hcCkge1xuICAgICAgaWYgKHRoaXMubWFwLmhhc093blByb3BlcnR5KG5hbWUpKSB7XG4gICAgICAgIGNhbGxiYWNrLmNhbGwodGhpc0FyZywgdGhpcy5tYXBbbmFtZV0sIG5hbWUsIHRoaXMpXG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgSGVhZGVycy5wcm90b3R5cGUua2V5cyA9IGZ1bmN0aW9uKCkge1xuICAgIHZhciBpdGVtcyA9IFtdXG4gICAgdGhpcy5mb3JFYWNoKGZ1bmN0aW9uKHZhbHVlLCBuYW1lKSB7IGl0ZW1zLnB1c2gobmFtZSkgfSlcbiAgICByZXR1cm4gaXRlcmF0b3JGb3IoaXRlbXMpXG4gIH1cblxuICBIZWFkZXJzLnByb3RvdHlwZS52YWx1ZXMgPSBmdW5jdGlvbigpIHtcbiAgICB2YXIgaXRlbXMgPSBbXVxuICAgIHRoaXMuZm9yRWFjaChmdW5jdGlvbih2YWx1ZSkgeyBpdGVtcy5wdXNoKHZhbHVlKSB9KVxuICAgIHJldHVybiBpdGVyYXRvckZvcihpdGVtcylcbiAgfVxuXG4gIEhlYWRlcnMucHJvdG90eXBlLmVudHJpZXMgPSBmdW5jdGlvbigpIHtcbiAgICB2YXIgaXRlbXMgPSBbXVxuICAgIHRoaXMuZm9yRWFjaChmdW5jdGlvbih2YWx1ZSwgbmFtZSkgeyBpdGVtcy5wdXNoKFtuYW1lLCB2YWx1ZV0pIH0pXG4gICAgcmV0dXJuIGl0ZXJhdG9yRm9yKGl0ZW1zKVxuICB9XG5cbiAgaWYgKHN1cHBvcnQuaXRlcmFibGUpIHtcbiAgICBIZWFkZXJzLnByb3RvdHlwZVtTeW1ib2wuaXRlcmF0b3JdID0gSGVhZGVycy5wcm90b3R5cGUuZW50cmllc1xuICB9XG5cbiAgZnVuY3Rpb24gY29uc3VtZWQoYm9keSkge1xuICAgIGlmIChib2R5LmJvZHlVc2VkKSB7XG4gICAgICByZXR1cm4gUHJvbWlzZS5yZWplY3QobmV3IFR5cGVFcnJvcignQWxyZWFkeSByZWFkJykpXG4gICAgfVxuICAgIGJvZHkuYm9keVVzZWQgPSB0cnVlXG4gIH1cblxuICBmdW5jdGlvbiBmaWxlUmVhZGVyUmVhZHkocmVhZGVyKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKGZ1bmN0aW9uKHJlc29sdmUsIHJlamVjdCkge1xuICAgICAgcmVhZGVyLm9ubG9hZCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXNvbHZlKHJlYWRlci5yZXN1bHQpXG4gICAgICB9XG4gICAgICByZWFkZXIub25lcnJvciA9IGZ1bmN0aW9uKCkge1xuICAgICAgICByZWplY3QocmVhZGVyLmVycm9yKVxuICAgICAgfVxuICAgIH0pXG4gIH1cblxuICBmdW5jdGlvbiByZWFkQmxvYkFzQXJyYXlCdWZmZXIoYmxvYikge1xuICAgIHZhciByZWFkZXIgPSBuZXcgRmlsZVJlYWRlcigpXG4gICAgdmFyIHByb21pc2UgPSBmaWxlUmVhZGVyUmVhZHkocmVhZGVyKVxuICAgIHJlYWRlci5yZWFkQXNBcnJheUJ1ZmZlcihibG9iKVxuICAgIHJldHVybiBwcm9taXNlXG4gIH1cblxuICBmdW5jdGlvbiByZWFkQmxvYkFzVGV4dChibG9iKSB7XG4gICAgdmFyIHJlYWRlciA9IG5ldyBGaWxlUmVhZGVyKClcbiAgICB2YXIgcHJvbWlzZSA9IGZpbGVSZWFkZXJSZWFkeShyZWFkZXIpXG4gICAgcmVhZGVyLnJlYWRBc1RleHQoYmxvYilcbiAgICByZXR1cm4gcHJvbWlzZVxuICB9XG5cbiAgZnVuY3Rpb24gcmVhZEFycmF5QnVmZmVyQXNUZXh0KGJ1Zikge1xuICAgIHZhciB2aWV3ID0gbmV3IFVpbnQ4QXJyYXkoYnVmKVxuICAgIHZhciBjaGFycyA9IG5ldyBBcnJheSh2aWV3Lmxlbmd0aClcblxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgdmlldy5sZW5ndGg7IGkrKykge1xuICAgICAgY2hhcnNbaV0gPSBTdHJpbmcuZnJvbUNoYXJDb2RlKHZpZXdbaV0pXG4gICAgfVxuICAgIHJldHVybiBjaGFycy5qb2luKCcnKVxuICB9XG5cbiAgZnVuY3Rpb24gYnVmZmVyQ2xvbmUoYnVmKSB7XG4gICAgaWYgKGJ1Zi5zbGljZSkge1xuICAgICAgcmV0dXJuIGJ1Zi5zbGljZSgwKVxuICAgIH0gZWxzZSB7XG4gICAgICB2YXIgdmlldyA9IG5ldyBVaW50OEFycmF5KGJ1Zi5ieXRlTGVuZ3RoKVxuICAgICAgdmlldy5zZXQobmV3IFVpbnQ4QXJyYXkoYnVmKSlcbiAgICAgIHJldHVybiB2aWV3LmJ1ZmZlclxuICAgIH1cbiAgfVxuXG4gIGZ1bmN0aW9uIEJvZHkoKSB7XG4gICAgdGhpcy5ib2R5VXNlZCA9IGZhbHNlXG5cbiAgICB0aGlzLl9pbml0Qm9keSA9IGZ1bmN0aW9uKGJvZHkpIHtcbiAgICAgIHRoaXMuX2JvZHlJbml0ID0gYm9keVxuICAgICAgaWYgKCFib2R5KSB7XG4gICAgICAgIHRoaXMuX2JvZHlUZXh0ID0gJydcbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIGJvZHkgPT09ICdzdHJpbmcnKSB7XG4gICAgICAgIHRoaXMuX2JvZHlUZXh0ID0gYm9keVxuICAgICAgfSBlbHNlIGlmIChzdXBwb3J0LmJsb2IgJiYgQmxvYi5wcm90b3R5cGUuaXNQcm90b3R5cGVPZihib2R5KSkge1xuICAgICAgICB0aGlzLl9ib2R5QmxvYiA9IGJvZHlcbiAgICAgIH0gZWxzZSBpZiAoc3VwcG9ydC5mb3JtRGF0YSAmJiBGb3JtRGF0YS5wcm90b3R5cGUuaXNQcm90b3R5cGVPZihib2R5KSkge1xuICAgICAgICB0aGlzLl9ib2R5Rm9ybURhdGEgPSBib2R5XG4gICAgICB9IGVsc2UgaWYgKHN1cHBvcnQuc2VhcmNoUGFyYW1zICYmIFVSTFNlYXJjaFBhcmFtcy5wcm90b3R5cGUuaXNQcm90b3R5cGVPZihib2R5KSkge1xuICAgICAgICB0aGlzLl9ib2R5VGV4dCA9IGJvZHkudG9TdHJpbmcoKVxuICAgICAgfSBlbHNlIGlmIChzdXBwb3J0LmFycmF5QnVmZmVyICYmIHN1cHBvcnQuYmxvYiAmJiBpc0RhdGFWaWV3KGJvZHkpKSB7XG4gICAgICAgIHRoaXMuX2JvZHlBcnJheUJ1ZmZlciA9IGJ1ZmZlckNsb25lKGJvZHkuYnVmZmVyKVxuICAgICAgICAvLyBJRSAxMC0xMSBjYW4ndCBoYW5kbGUgYSBEYXRhVmlldyBib2R5LlxuICAgICAgICB0aGlzLl9ib2R5SW5pdCA9IG5ldyBCbG9iKFt0aGlzLl9ib2R5QXJyYXlCdWZmZXJdKVxuICAgICAgfSBlbHNlIGlmIChzdXBwb3J0LmFycmF5QnVmZmVyICYmIChBcnJheUJ1ZmZlci5wcm90b3R5cGUuaXNQcm90b3R5cGVPZihib2R5KSB8fCBpc0FycmF5QnVmZmVyVmlldyhib2R5KSkpIHtcbiAgICAgICAgdGhpcy5fYm9keUFycmF5QnVmZmVyID0gYnVmZmVyQ2xvbmUoYm9keSlcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcigndW5zdXBwb3J0ZWQgQm9keUluaXQgdHlwZScpXG4gICAgICB9XG5cbiAgICAgIGlmICghdGhpcy5oZWFkZXJzLmdldCgnY29udGVudC10eXBlJykpIHtcbiAgICAgICAgaWYgKHR5cGVvZiBib2R5ID09PSAnc3RyaW5nJykge1xuICAgICAgICAgIHRoaXMuaGVhZGVycy5zZXQoJ2NvbnRlbnQtdHlwZScsICd0ZXh0L3BsYWluO2NoYXJzZXQ9VVRGLTgnKVxuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuX2JvZHlCbG9iICYmIHRoaXMuX2JvZHlCbG9iLnR5cGUpIHtcbiAgICAgICAgICB0aGlzLmhlYWRlcnMuc2V0KCdjb250ZW50LXR5cGUnLCB0aGlzLl9ib2R5QmxvYi50eXBlKVxuICAgICAgICB9IGVsc2UgaWYgKHN1cHBvcnQuc2VhcmNoUGFyYW1zICYmIFVSTFNlYXJjaFBhcmFtcy5wcm90b3R5cGUuaXNQcm90b3R5cGVPZihib2R5KSkge1xuICAgICAgICAgIHRoaXMuaGVhZGVycy5zZXQoJ2NvbnRlbnQtdHlwZScsICdhcHBsaWNhdGlvbi94LXd3dy1mb3JtLXVybGVuY29kZWQ7Y2hhcnNldD1VVEYtOCcpXG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAoc3VwcG9ydC5ibG9iKSB7XG4gICAgICB0aGlzLmJsb2IgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgdmFyIHJlamVjdGVkID0gY29uc3VtZWQodGhpcylcbiAgICAgICAgaWYgKHJlamVjdGVkKSB7XG4gICAgICAgICAgcmV0dXJuIHJlamVjdGVkXG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGhpcy5fYm9keUJsb2IpIHtcbiAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKHRoaXMuX2JvZHlCbG9iKVxuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuX2JvZHlBcnJheUJ1ZmZlcikge1xuICAgICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUobmV3IEJsb2IoW3RoaXMuX2JvZHlBcnJheUJ1ZmZlcl0pKVxuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuX2JvZHlGb3JtRGF0YSkge1xuICAgICAgICAgIHRocm93IG5ldyBFcnJvcignY291bGQgbm90IHJlYWQgRm9ybURhdGEgYm9keSBhcyBibG9iJylcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKG5ldyBCbG9iKFt0aGlzLl9ib2R5VGV4dF0pKVxuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIHRoaXMuYXJyYXlCdWZmZXIgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKHRoaXMuX2JvZHlBcnJheUJ1ZmZlcikge1xuICAgICAgICAgIHJldHVybiBjb25zdW1lZCh0aGlzKSB8fCBQcm9taXNlLnJlc29sdmUodGhpcy5fYm9keUFycmF5QnVmZmVyKVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHJldHVybiB0aGlzLmJsb2IoKS50aGVuKHJlYWRCbG9iQXNBcnJheUJ1ZmZlcilcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cblxuICAgIHRoaXMudGV4dCA9IGZ1bmN0aW9uKCkge1xuICAgICAgdmFyIHJlamVjdGVkID0gY29uc3VtZWQodGhpcylcbiAgICAgIGlmIChyZWplY3RlZCkge1xuICAgICAgICByZXR1cm4gcmVqZWN0ZWRcbiAgICAgIH1cblxuICAgICAgaWYgKHRoaXMuX2JvZHlCbG9iKSB7XG4gICAgICAgIHJldHVybiByZWFkQmxvYkFzVGV4dCh0aGlzLl9ib2R5QmxvYilcbiAgICAgIH0gZWxzZSBpZiAodGhpcy5fYm9keUFycmF5QnVmZmVyKSB7XG4gICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUocmVhZEFycmF5QnVmZmVyQXNUZXh0KHRoaXMuX2JvZHlBcnJheUJ1ZmZlcikpXG4gICAgICB9IGVsc2UgaWYgKHRoaXMuX2JvZHlGb3JtRGF0YSkge1xuICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ2NvdWxkIG5vdCByZWFkIEZvcm1EYXRhIGJvZHkgYXMgdGV4dCcpXG4gICAgICB9IGVsc2Uge1xuICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKHRoaXMuX2JvZHlUZXh0KVxuICAgICAgfVxuICAgIH1cblxuICAgIGlmIChzdXBwb3J0LmZvcm1EYXRhKSB7XG4gICAgICB0aGlzLmZvcm1EYXRhID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiB0aGlzLnRleHQoKS50aGVuKGRlY29kZSlcbiAgICAgIH1cbiAgICB9XG5cbiAgICB0aGlzLmpzb24gPSBmdW5jdGlvbigpIHtcbiAgICAgIHJldHVybiB0aGlzLnRleHQoKS50aGVuKEpTT04ucGFyc2UpXG4gICAgfVxuXG4gICAgcmV0dXJuIHRoaXNcbiAgfVxuXG4gIC8vIEhUVFAgbWV0aG9kcyB3aG9zZSBjYXBpdGFsaXphdGlvbiBzaG91bGQgYmUgbm9ybWFsaXplZFxuICB2YXIgbWV0aG9kcyA9IFsnREVMRVRFJywgJ0dFVCcsICdIRUFEJywgJ09QVElPTlMnLCAnUE9TVCcsICdQVVQnXVxuXG4gIGZ1bmN0aW9uIG5vcm1hbGl6ZU1ldGhvZChtZXRob2QpIHtcbiAgICB2YXIgdXBjYXNlZCA9IG1ldGhvZC50b1VwcGVyQ2FzZSgpXG4gICAgcmV0dXJuIChtZXRob2RzLmluZGV4T2YodXBjYXNlZCkgPiAtMSkgPyB1cGNhc2VkIDogbWV0aG9kXG4gIH1cblxuICBmdW5jdGlvbiBSZXF1ZXN0KGlucHV0LCBvcHRpb25zKSB7XG4gICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge31cbiAgICB2YXIgYm9keSA9IG9wdGlvbnMuYm9keVxuXG4gICAgaWYgKGlucHV0IGluc3RhbmNlb2YgUmVxdWVzdCkge1xuICAgICAgaWYgKGlucHV0LmJvZHlVc2VkKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ0FscmVhZHkgcmVhZCcpXG4gICAgICB9XG4gICAgICB0aGlzLnVybCA9IGlucHV0LnVybFxuICAgICAgdGhpcy5jcmVkZW50aWFscyA9IGlucHV0LmNyZWRlbnRpYWxzXG4gICAgICBpZiAoIW9wdGlvbnMuaGVhZGVycykge1xuICAgICAgICB0aGlzLmhlYWRlcnMgPSBuZXcgSGVhZGVycyhpbnB1dC5oZWFkZXJzKVxuICAgICAgfVxuICAgICAgdGhpcy5tZXRob2QgPSBpbnB1dC5tZXRob2RcbiAgICAgIHRoaXMubW9kZSA9IGlucHV0Lm1vZGVcbiAgICAgIGlmICghYm9keSAmJiBpbnB1dC5fYm9keUluaXQgIT0gbnVsbCkge1xuICAgICAgICBib2R5ID0gaW5wdXQuX2JvZHlJbml0XG4gICAgICAgIGlucHV0LmJvZHlVc2VkID0gdHJ1ZVxuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLnVybCA9IFN0cmluZyhpbnB1dClcbiAgICB9XG5cbiAgICB0aGlzLmNyZWRlbnRpYWxzID0gb3B0aW9ucy5jcmVkZW50aWFscyB8fCB0aGlzLmNyZWRlbnRpYWxzIHx8ICdvbWl0J1xuICAgIGlmIChvcHRpb25zLmhlYWRlcnMgfHwgIXRoaXMuaGVhZGVycykge1xuICAgICAgdGhpcy5oZWFkZXJzID0gbmV3IEhlYWRlcnMob3B0aW9ucy5oZWFkZXJzKVxuICAgIH1cbiAgICB0aGlzLm1ldGhvZCA9IG5vcm1hbGl6ZU1ldGhvZChvcHRpb25zLm1ldGhvZCB8fCB0aGlzLm1ldGhvZCB8fCAnR0VUJylcbiAgICB0aGlzLm1vZGUgPSBvcHRpb25zLm1vZGUgfHwgdGhpcy5tb2RlIHx8IG51bGxcbiAgICB0aGlzLnJlZmVycmVyID0gbnVsbFxuXG4gICAgaWYgKCh0aGlzLm1ldGhvZCA9PT0gJ0dFVCcgfHwgdGhpcy5tZXRob2QgPT09ICdIRUFEJykgJiYgYm9keSkge1xuICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignQm9keSBub3QgYWxsb3dlZCBmb3IgR0VUIG9yIEhFQUQgcmVxdWVzdHMnKVxuICAgIH1cbiAgICB0aGlzLl9pbml0Qm9keShib2R5KVxuICB9XG5cbiAgUmVxdWVzdC5wcm90b3R5cGUuY2xvbmUgPSBmdW5jdGlvbigpIHtcbiAgICByZXR1cm4gbmV3IFJlcXVlc3QodGhpcywgeyBib2R5OiB0aGlzLl9ib2R5SW5pdCB9KVxuICB9XG5cbiAgZnVuY3Rpb24gZGVjb2RlKGJvZHkpIHtcbiAgICB2YXIgZm9ybSA9IG5ldyBGb3JtRGF0YSgpXG4gICAgYm9keS50cmltKCkuc3BsaXQoJyYnKS5mb3JFYWNoKGZ1bmN0aW9uKGJ5dGVzKSB7XG4gICAgICBpZiAoYnl0ZXMpIHtcbiAgICAgICAgdmFyIHNwbGl0ID0gYnl0ZXMuc3BsaXQoJz0nKVxuICAgICAgICB2YXIgbmFtZSA9IHNwbGl0LnNoaWZ0KCkucmVwbGFjZSgvXFwrL2csICcgJylcbiAgICAgICAgdmFyIHZhbHVlID0gc3BsaXQuam9pbignPScpLnJlcGxhY2UoL1xcKy9nLCAnICcpXG4gICAgICAgIGZvcm0uYXBwZW5kKGRlY29kZVVSSUNvbXBvbmVudChuYW1lKSwgZGVjb2RlVVJJQ29tcG9uZW50KHZhbHVlKSlcbiAgICAgIH1cbiAgICB9KVxuICAgIHJldHVybiBmb3JtXG4gIH1cblxuICBmdW5jdGlvbiBwYXJzZUhlYWRlcnMocmF3SGVhZGVycykge1xuICAgIHZhciBoZWFkZXJzID0gbmV3IEhlYWRlcnMoKVxuICAgIC8vIFJlcGxhY2UgaW5zdGFuY2VzIG9mIFxcclxcbiBhbmQgXFxuIGZvbGxvd2VkIGJ5IGF0IGxlYXN0IG9uZSBzcGFjZSBvciBob3Jpem9udGFsIHRhYiB3aXRoIGEgc3BhY2VcbiAgICAvLyBodHRwczovL3Rvb2xzLmlldGYub3JnL2h0bWwvcmZjNzIzMCNzZWN0aW9uLTMuMlxuICAgIHZhciBwcmVQcm9jZXNzZWRIZWFkZXJzID0gcmF3SGVhZGVycy5yZXBsYWNlKC9cXHI/XFxuW1xcdCBdKy9nLCAnICcpXG4gICAgcHJlUHJvY2Vzc2VkSGVhZGVycy5zcGxpdCgvXFxyP1xcbi8pLmZvckVhY2goZnVuY3Rpb24obGluZSkge1xuICAgICAgdmFyIHBhcnRzID0gbGluZS5zcGxpdCgnOicpXG4gICAgICB2YXIga2V5ID0gcGFydHMuc2hpZnQoKS50cmltKClcbiAgICAgIGlmIChrZXkpIHtcbiAgICAgICAgdmFyIHZhbHVlID0gcGFydHMuam9pbignOicpLnRyaW0oKVxuICAgICAgICBoZWFkZXJzLmFwcGVuZChrZXksIHZhbHVlKVxuICAgICAgfVxuICAgIH0pXG4gICAgcmV0dXJuIGhlYWRlcnNcbiAgfVxuXG4gIEJvZHkuY2FsbChSZXF1ZXN0LnByb3RvdHlwZSlcblxuICBmdW5jdGlvbiBSZXNwb25zZShib2R5SW5pdCwgb3B0aW9ucykge1xuICAgIGlmICghb3B0aW9ucykge1xuICAgICAgb3B0aW9ucyA9IHt9XG4gICAgfVxuXG4gICAgdGhpcy50eXBlID0gJ2RlZmF1bHQnXG4gICAgdGhpcy5zdGF0dXMgPSBvcHRpb25zLnN0YXR1cyA9PT0gdW5kZWZpbmVkID8gMjAwIDogb3B0aW9ucy5zdGF0dXNcbiAgICB0aGlzLm9rID0gdGhpcy5zdGF0dXMgPj0gMjAwICYmIHRoaXMuc3RhdHVzIDwgMzAwXG4gICAgdGhpcy5zdGF0dXNUZXh0ID0gJ3N0YXR1c1RleHQnIGluIG9wdGlvbnMgPyBvcHRpb25zLnN0YXR1c1RleHQgOiAnT0snXG4gICAgdGhpcy5oZWFkZXJzID0gbmV3IEhlYWRlcnMob3B0aW9ucy5oZWFkZXJzKVxuICAgIHRoaXMudXJsID0gb3B0aW9ucy51cmwgfHwgJydcbiAgICB0aGlzLl9pbml0Qm9keShib2R5SW5pdClcbiAgfVxuXG4gIEJvZHkuY2FsbChSZXNwb25zZS5wcm90b3R5cGUpXG5cbiAgUmVzcG9uc2UucHJvdG90eXBlLmNsb25lID0gZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIG5ldyBSZXNwb25zZSh0aGlzLl9ib2R5SW5pdCwge1xuICAgICAgc3RhdHVzOiB0aGlzLnN0YXR1cyxcbiAgICAgIHN0YXR1c1RleHQ6IHRoaXMuc3RhdHVzVGV4dCxcbiAgICAgIGhlYWRlcnM6IG5ldyBIZWFkZXJzKHRoaXMuaGVhZGVycyksXG4gICAgICB1cmw6IHRoaXMudXJsXG4gICAgfSlcbiAgfVxuXG4gIFJlc3BvbnNlLmVycm9yID0gZnVuY3Rpb24oKSB7XG4gICAgdmFyIHJlc3BvbnNlID0gbmV3IFJlc3BvbnNlKG51bGwsIHtzdGF0dXM6IDAsIHN0YXR1c1RleHQ6ICcnfSlcbiAgICByZXNwb25zZS50eXBlID0gJ2Vycm9yJ1xuICAgIHJldHVybiByZXNwb25zZVxuICB9XG5cbiAgdmFyIHJlZGlyZWN0U3RhdHVzZXMgPSBbMzAxLCAzMDIsIDMwMywgMzA3LCAzMDhdXG5cbiAgUmVzcG9uc2UucmVkaXJlY3QgPSBmdW5jdGlvbih1cmwsIHN0YXR1cykge1xuICAgIGlmIChyZWRpcmVjdFN0YXR1c2VzLmluZGV4T2Yoc3RhdHVzKSA9PT0gLTEpIHtcbiAgICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdJbnZhbGlkIHN0YXR1cyBjb2RlJylcbiAgICB9XG5cbiAgICByZXR1cm4gbmV3IFJlc3BvbnNlKG51bGwsIHtzdGF0dXM6IHN0YXR1cywgaGVhZGVyczoge2xvY2F0aW9uOiB1cmx9fSlcbiAgfVxuXG4gIHNlbGYuSGVhZGVycyA9IEhlYWRlcnNcbiAgc2VsZi5SZXF1ZXN0ID0gUmVxdWVzdFxuICBzZWxmLlJlc3BvbnNlID0gUmVzcG9uc2VcblxuICBzZWxmLmZldGNoID0gZnVuY3Rpb24oaW5wdXQsIGluaXQpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2UoZnVuY3Rpb24ocmVzb2x2ZSwgcmVqZWN0KSB7XG4gICAgICB2YXIgcmVxdWVzdCA9IG5ldyBSZXF1ZXN0KGlucHV0LCBpbml0KVxuICAgICAgdmFyIHhociA9IG5ldyBYTUxIdHRwUmVxdWVzdCgpXG5cbiAgICAgIHhoci5vbmxvYWQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB7XG4gICAgICAgICAgc3RhdHVzOiB4aHIuc3RhdHVzLFxuICAgICAgICAgIHN0YXR1c1RleHQ6IHhoci5zdGF0dXNUZXh0LFxuICAgICAgICAgIGhlYWRlcnM6IHBhcnNlSGVhZGVycyh4aHIuZ2V0QWxsUmVzcG9uc2VIZWFkZXJzKCkgfHwgJycpXG4gICAgICAgIH1cbiAgICAgICAgb3B0aW9ucy51cmwgPSAncmVzcG9uc2VVUkwnIGluIHhociA/IHhoci5yZXNwb25zZVVSTCA6IG9wdGlvbnMuaGVhZGVycy5nZXQoJ1gtUmVxdWVzdC1VUkwnKVxuICAgICAgICB2YXIgYm9keSA9ICdyZXNwb25zZScgaW4geGhyID8geGhyLnJlc3BvbnNlIDogeGhyLnJlc3BvbnNlVGV4dFxuICAgICAgICByZXNvbHZlKG5ldyBSZXNwb25zZShib2R5LCBvcHRpb25zKSlcbiAgICAgIH1cblxuICAgICAgeGhyLm9uZXJyb3IgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgcmVqZWN0KG5ldyBUeXBlRXJyb3IoJ05ldHdvcmsgcmVxdWVzdCBmYWlsZWQnKSlcbiAgICAgIH1cblxuICAgICAgeGhyLm9udGltZW91dCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICByZWplY3QobmV3IFR5cGVFcnJvcignTmV0d29yayByZXF1ZXN0IGZhaWxlZCcpKVxuICAgICAgfVxuXG4gICAgICB4aHIub3BlbihyZXF1ZXN0Lm1ldGhvZCwgcmVxdWVzdC51cmwsIHRydWUpXG5cbiAgICAgIGlmIChyZXF1ZXN0LmNyZWRlbnRpYWxzID09PSAnaW5jbHVkZScpIHtcbiAgICAgICAgeGhyLndpdGhDcmVkZW50aWFscyA9IHRydWVcbiAgICAgIH0gZWxzZSBpZiAocmVxdWVzdC5jcmVkZW50aWFscyA9PT0gJ29taXQnKSB7XG4gICAgICAgIHhoci53aXRoQ3JlZGVudGlhbHMgPSBmYWxzZVxuICAgICAgfVxuXG4gICAgICBpZiAoJ3Jlc3BvbnNlVHlwZScgaW4geGhyICYmIHN1cHBvcnQuYmxvYikge1xuICAgICAgICB4aHIucmVzcG9uc2VUeXBlID0gJ2Jsb2InXG4gICAgICB9XG5cbiAgICAgIHJlcXVlc3QuaGVhZGVycy5mb3JFYWNoKGZ1bmN0aW9uKHZhbHVlLCBuYW1lKSB7XG4gICAgICAgIHhoci5zZXRSZXF1ZXN0SGVhZGVyKG5hbWUsIHZhbHVlKVxuICAgICAgfSlcblxuICAgICAgeGhyLnNlbmQodHlwZW9mIHJlcXVlc3QuX2JvZHlJbml0ID09PSAndW5kZWZpbmVkJyA/IG51bGwgOiByZXF1ZXN0Ll9ib2R5SW5pdClcbiAgICB9KVxuICB9XG4gIHNlbGYuZmV0Y2gucG9seWZpbGwgPSB0cnVlXG59KSh0eXBlb2Ygc2VsZiAhPT0gJ3VuZGVmaW5lZCcgPyBzZWxmIDogdGhpcyk7XG4iXX0=
