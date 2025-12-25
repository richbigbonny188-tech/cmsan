'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

(function (root) {

  // Store setTimeout reference so promise-polyfill will be unaffected by
  // other code modifying setTimeout (like sinon.useFakeTimers())
  var setTimeoutFunc = setTimeout;

  function noop() {}

  // Polyfill for Function.prototype.bind
  function bind(fn, thisArg) {
    return function () {
      fn.apply(thisArg, arguments);
    };
  }

  function Promise(fn) {
    if (_typeof(this) !== 'object') throw new TypeError('Promises must be constructed via new');
    if (typeof fn !== 'function') throw new TypeError('not a function');
    this._state = 0;
    this._handled = false;
    this._value = undefined;
    this._deferreds = [];

    doResolve(fn, this);
  }

  function handle(self, deferred) {
    while (self._state === 3) {
      self = self._value;
    }
    if (self._state === 0) {
      self._deferreds.push(deferred);
      return;
    }
    self._handled = true;
    Promise._immediateFn(function () {
      var cb = self._state === 1 ? deferred.onFulfilled : deferred.onRejected;
      if (cb === null) {
        (self._state === 1 ? resolve : reject)(deferred.promise, self._value);
        return;
      }
      var ret;
      try {
        ret = cb(self._value);
      } catch (e) {
        reject(deferred.promise, e);
        return;
      }
      resolve(deferred.promise, ret);
    });
  }

  function resolve(self, newValue) {
    try {
      // Promise Resolution Procedure: https://github.com/promises-aplus/promises-spec#the-promise-resolution-procedure
      if (newValue === self) throw new TypeError('A promise cannot be resolved with itself.');
      if (newValue && ((typeof newValue === 'undefined' ? 'undefined' : _typeof(newValue)) === 'object' || typeof newValue === 'function')) {
        var then = newValue.then;
        if (newValue instanceof Promise) {
          self._state = 3;
          self._value = newValue;
          finale(self);
          return;
        } else if (typeof then === 'function') {
          doResolve(bind(then, newValue), self);
          return;
        }
      }
      self._state = 1;
      self._value = newValue;
      finale(self);
    } catch (e) {
      reject(self, e);
    }
  }

  function reject(self, newValue) {
    self._state = 2;
    self._value = newValue;
    finale(self);
  }

  function finale(self) {
    if (self._state === 2 && self._deferreds.length === 0) {
      Promise._immediateFn(function () {
        if (!self._handled) {
          Promise._unhandledRejectionFn(self._value);
        }
      });
    }

    for (var i = 0, len = self._deferreds.length; i < len; i++) {
      handle(self, self._deferreds[i]);
    }
    self._deferreds = null;
  }

  function Handler(onFulfilled, onRejected, promise) {
    this.onFulfilled = typeof onFulfilled === 'function' ? onFulfilled : null;
    this.onRejected = typeof onRejected === 'function' ? onRejected : null;
    this.promise = promise;
  }

  /**
   * Take a potentially misbehaving resolver function and make sure
   * onFulfilled and onRejected are only called once.
   *
   * Makes no guarantees about asynchrony.
   */
  function doResolve(fn, self) {
    var done = false;
    try {
      fn(function (value) {
        if (done) return;
        done = true;
        resolve(self, value);
      }, function (reason) {
        if (done) return;
        done = true;
        reject(self, reason);
      });
    } catch (ex) {
      if (done) return;
      done = true;
      reject(self, ex);
    }
  }

  Promise.prototype['catch'] = function (onRejected) {
    return this.then(null, onRejected);
  };

  Promise.prototype.then = function (onFulfilled, onRejected) {
    var prom = new this.constructor(noop);

    handle(this, new Handler(onFulfilled, onRejected, prom));
    return prom;
  };

  Promise.all = function (arr) {
    var args = Array.prototype.slice.call(arr);

    return new Promise(function (resolve, reject) {
      if (args.length === 0) return resolve([]);
      var remaining = args.length;

      function res(i, val) {
        try {
          if (val && ((typeof val === 'undefined' ? 'undefined' : _typeof(val)) === 'object' || typeof val === 'function')) {
            var then = val.then;
            if (typeof then === 'function') {
              then.call(val, function (val) {
                res(i, val);
              }, reject);
              return;
            }
          }
          args[i] = val;
          if (--remaining === 0) {
            resolve(args);
          }
        } catch (ex) {
          reject(ex);
        }
      }

      for (var i = 0; i < args.length; i++) {
        res(i, args[i]);
      }
    });
  };

  Promise.resolve = function (value) {
    if (value && (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value.constructor === Promise) {
      return value;
    }

    return new Promise(function (resolve) {
      resolve(value);
    });
  };

  Promise.reject = function (value) {
    return new Promise(function (resolve, reject) {
      reject(value);
    });
  };

  Promise.race = function (values) {
    return new Promise(function (resolve, reject) {
      for (var i = 0, len = values.length; i < len; i++) {
        values[i].then(resolve, reject);
      }
    });
  };

  // Use polyfill for setImmediate for performance gains
  Promise._immediateFn = typeof setImmediate === 'function' && function (fn) {
    setImmediate(fn);
  } || function (fn) {
    setTimeoutFunc(fn, 0);
  };

  Promise._unhandledRejectionFn = function _unhandledRejectionFn(err) {
    if (typeof console !== 'undefined' && console) {
      console.warn('Possible Unhandled Promise Rejection:', err); // eslint-disable-line no-console
    }
  };

  /**
   * Set the immediate function to execute callbacks
   * @param fn {function} Function to execute
   * @deprecated
   */
  Promise._setImmediateFn = function _setImmediateFn(fn) {
    Promise._immediateFn = fn;
  };

  /**
   * Change the function to execute on unhandled rejection
   * @param {function} fn Function to execute on unhandled rejection
   * @deprecated
   */
  Promise._setUnhandledRejectionFn = function _setUnhandledRejectionFn(fn) {
    Promise._unhandledRejectionFn = fn;
  };

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = Promise;
  } else if (root && !root.Promise) {
    root.Promise = Promise;
  }
})(undefined);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvdmVuZG9yL3Byb21pc2UuanMiXSwibmFtZXMiOlsicm9vdCIsInNldFRpbWVvdXRGdW5jIiwic2V0VGltZW91dCIsIm5vb3AiLCJiaW5kIiwiZm4iLCJ0aGlzQXJnIiwiYXBwbHkiLCJhcmd1bWVudHMiLCJQcm9taXNlIiwiVHlwZUVycm9yIiwiX3N0YXRlIiwiX2hhbmRsZWQiLCJfdmFsdWUiLCJ1bmRlZmluZWQiLCJfZGVmZXJyZWRzIiwiZG9SZXNvbHZlIiwiaGFuZGxlIiwic2VsZiIsImRlZmVycmVkIiwicHVzaCIsIl9pbW1lZGlhdGVGbiIsImNiIiwib25GdWxmaWxsZWQiLCJvblJlamVjdGVkIiwicmVzb2x2ZSIsInJlamVjdCIsInByb21pc2UiLCJyZXQiLCJlIiwibmV3VmFsdWUiLCJ0aGVuIiwiZmluYWxlIiwibGVuZ3RoIiwiX3VuaGFuZGxlZFJlamVjdGlvbkZuIiwiaSIsImxlbiIsIkhhbmRsZXIiLCJkb25lIiwidmFsdWUiLCJyZWFzb24iLCJleCIsInByb3RvdHlwZSIsInByb20iLCJjb25zdHJ1Y3RvciIsImFsbCIsImFyciIsImFyZ3MiLCJBcnJheSIsInNsaWNlIiwiY2FsbCIsInJlbWFpbmluZyIsInJlcyIsInZhbCIsInJhY2UiLCJ2YWx1ZXMiLCJzZXRJbW1lZGlhdGUiLCJlcnIiLCJjb25zb2xlIiwid2FybiIsIl9zZXRJbW1lZGlhdGVGbiIsIl9zZXRVbmhhbmRsZWRSZWplY3Rpb25GbiIsIm1vZHVsZSIsImV4cG9ydHMiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQSxDQUFDLFVBQVVBLElBQVYsRUFBZ0I7O0FBRWY7QUFDQTtBQUNBLE1BQUlDLGlCQUFpQkMsVUFBckI7O0FBRUEsV0FBU0MsSUFBVCxHQUFnQixDQUFFOztBQUVsQjtBQUNBLFdBQVNDLElBQVQsQ0FBY0MsRUFBZCxFQUFrQkMsT0FBbEIsRUFBMkI7QUFDekIsV0FBTyxZQUFZO0FBQ2pCRCxTQUFHRSxLQUFILENBQVNELE9BQVQsRUFBa0JFLFNBQWxCO0FBQ0QsS0FGRDtBQUdEOztBQUVELFdBQVNDLE9BQVQsQ0FBaUJKLEVBQWpCLEVBQXFCO0FBQ25CLFFBQUksUUFBTyxJQUFQLE1BQWdCLFFBQXBCLEVBQThCLE1BQU0sSUFBSUssU0FBSixDQUFjLHNDQUFkLENBQU47QUFDOUIsUUFBSSxPQUFPTCxFQUFQLEtBQWMsVUFBbEIsRUFBOEIsTUFBTSxJQUFJSyxTQUFKLENBQWMsZ0JBQWQsQ0FBTjtBQUM5QixTQUFLQyxNQUFMLEdBQWMsQ0FBZDtBQUNBLFNBQUtDLFFBQUwsR0FBZ0IsS0FBaEI7QUFDQSxTQUFLQyxNQUFMLEdBQWNDLFNBQWQ7QUFDQSxTQUFLQyxVQUFMLEdBQWtCLEVBQWxCOztBQUVBQyxjQUFVWCxFQUFWLEVBQWMsSUFBZDtBQUNEOztBQUVELFdBQVNZLE1BQVQsQ0FBZ0JDLElBQWhCLEVBQXNCQyxRQUF0QixFQUFnQztBQUM5QixXQUFPRCxLQUFLUCxNQUFMLEtBQWdCLENBQXZCLEVBQTBCO0FBQ3hCTyxhQUFPQSxLQUFLTCxNQUFaO0FBQ0Q7QUFDRCxRQUFJSyxLQUFLUCxNQUFMLEtBQWdCLENBQXBCLEVBQXVCO0FBQ3JCTyxXQUFLSCxVQUFMLENBQWdCSyxJQUFoQixDQUFxQkQsUUFBckI7QUFDQTtBQUNEO0FBQ0RELFNBQUtOLFFBQUwsR0FBZ0IsSUFBaEI7QUFDQUgsWUFBUVksWUFBUixDQUFxQixZQUFZO0FBQy9CLFVBQUlDLEtBQUtKLEtBQUtQLE1BQUwsS0FBZ0IsQ0FBaEIsR0FBb0JRLFNBQVNJLFdBQTdCLEdBQTJDSixTQUFTSyxVQUE3RDtBQUNBLFVBQUlGLE9BQU8sSUFBWCxFQUFpQjtBQUNmLFNBQUNKLEtBQUtQLE1BQUwsS0FBZ0IsQ0FBaEIsR0FBb0JjLE9BQXBCLEdBQThCQyxNQUEvQixFQUF1Q1AsU0FBU1EsT0FBaEQsRUFBeURULEtBQUtMLE1BQTlEO0FBQ0E7QUFDRDtBQUNELFVBQUllLEdBQUo7QUFDQSxVQUFJO0FBQ0ZBLGNBQU1OLEdBQUdKLEtBQUtMLE1BQVIsQ0FBTjtBQUNELE9BRkQsQ0FFRSxPQUFPZ0IsQ0FBUCxFQUFVO0FBQ1ZILGVBQU9QLFNBQVNRLE9BQWhCLEVBQXlCRSxDQUF6QjtBQUNBO0FBQ0Q7QUFDREosY0FBUU4sU0FBU1EsT0FBakIsRUFBMEJDLEdBQTFCO0FBQ0QsS0FkRDtBQWVEOztBQUVELFdBQVNILE9BQVQsQ0FBaUJQLElBQWpCLEVBQXVCWSxRQUF2QixFQUFpQztBQUMvQixRQUFJO0FBQ0Y7QUFDQSxVQUFJQSxhQUFhWixJQUFqQixFQUF1QixNQUFNLElBQUlSLFNBQUosQ0FBYywyQ0FBZCxDQUFOO0FBQ3ZCLFVBQUlvQixhQUFhLFFBQU9BLFFBQVAseUNBQU9BLFFBQVAsT0FBb0IsUUFBcEIsSUFBZ0MsT0FBT0EsUUFBUCxLQUFvQixVQUFqRSxDQUFKLEVBQWtGO0FBQ2hGLFlBQUlDLE9BQU9ELFNBQVNDLElBQXBCO0FBQ0EsWUFBSUQsb0JBQW9CckIsT0FBeEIsRUFBaUM7QUFDL0JTLGVBQUtQLE1BQUwsR0FBYyxDQUFkO0FBQ0FPLGVBQUtMLE1BQUwsR0FBY2lCLFFBQWQ7QUFDQUUsaUJBQU9kLElBQVA7QUFDQTtBQUNELFNBTEQsTUFLTyxJQUFJLE9BQU9hLElBQVAsS0FBZ0IsVUFBcEIsRUFBZ0M7QUFDckNmLG9CQUFVWixLQUFLMkIsSUFBTCxFQUFXRCxRQUFYLENBQVYsRUFBZ0NaLElBQWhDO0FBQ0E7QUFDRDtBQUNGO0FBQ0RBLFdBQUtQLE1BQUwsR0FBYyxDQUFkO0FBQ0FPLFdBQUtMLE1BQUwsR0FBY2lCLFFBQWQ7QUFDQUUsYUFBT2QsSUFBUDtBQUNELEtBbEJELENBa0JFLE9BQU9XLENBQVAsRUFBVTtBQUNWSCxhQUFPUixJQUFQLEVBQWFXLENBQWI7QUFDRDtBQUNGOztBQUVELFdBQVNILE1BQVQsQ0FBZ0JSLElBQWhCLEVBQXNCWSxRQUF0QixFQUFnQztBQUM5QlosU0FBS1AsTUFBTCxHQUFjLENBQWQ7QUFDQU8sU0FBS0wsTUFBTCxHQUFjaUIsUUFBZDtBQUNBRSxXQUFPZCxJQUFQO0FBQ0Q7O0FBRUQsV0FBU2MsTUFBVCxDQUFnQmQsSUFBaEIsRUFBc0I7QUFDcEIsUUFBSUEsS0FBS1AsTUFBTCxLQUFnQixDQUFoQixJQUFxQk8sS0FBS0gsVUFBTCxDQUFnQmtCLE1BQWhCLEtBQTJCLENBQXBELEVBQXVEO0FBQ3JEeEIsY0FBUVksWUFBUixDQUFxQixZQUFXO0FBQzlCLFlBQUksQ0FBQ0gsS0FBS04sUUFBVixFQUFvQjtBQUNsQkgsa0JBQVF5QixxQkFBUixDQUE4QmhCLEtBQUtMLE1BQW5DO0FBQ0Q7QUFDRixPQUpEO0FBS0Q7O0FBRUQsU0FBSyxJQUFJc0IsSUFBSSxDQUFSLEVBQVdDLE1BQU1sQixLQUFLSCxVQUFMLENBQWdCa0IsTUFBdEMsRUFBOENFLElBQUlDLEdBQWxELEVBQXVERCxHQUF2RCxFQUE0RDtBQUMxRGxCLGFBQU9DLElBQVAsRUFBYUEsS0FBS0gsVUFBTCxDQUFnQm9CLENBQWhCLENBQWI7QUFDRDtBQUNEakIsU0FBS0gsVUFBTCxHQUFrQixJQUFsQjtBQUNEOztBQUVELFdBQVNzQixPQUFULENBQWlCZCxXQUFqQixFQUE4QkMsVUFBOUIsRUFBMENHLE9BQTFDLEVBQW1EO0FBQ2pELFNBQUtKLFdBQUwsR0FBbUIsT0FBT0EsV0FBUCxLQUF1QixVQUF2QixHQUFvQ0EsV0FBcEMsR0FBa0QsSUFBckU7QUFDQSxTQUFLQyxVQUFMLEdBQWtCLE9BQU9BLFVBQVAsS0FBc0IsVUFBdEIsR0FBbUNBLFVBQW5DLEdBQWdELElBQWxFO0FBQ0EsU0FBS0csT0FBTCxHQUFlQSxPQUFmO0FBQ0Q7O0FBRUQ7Ozs7OztBQU1BLFdBQVNYLFNBQVQsQ0FBbUJYLEVBQW5CLEVBQXVCYSxJQUF2QixFQUE2QjtBQUMzQixRQUFJb0IsT0FBTyxLQUFYO0FBQ0EsUUFBSTtBQUNGakMsU0FBRyxVQUFVa0MsS0FBVixFQUFpQjtBQUNsQixZQUFJRCxJQUFKLEVBQVU7QUFDVkEsZUFBTyxJQUFQO0FBQ0FiLGdCQUFRUCxJQUFSLEVBQWNxQixLQUFkO0FBQ0QsT0FKRCxFQUlHLFVBQVVDLE1BQVYsRUFBa0I7QUFDbkIsWUFBSUYsSUFBSixFQUFVO0FBQ1ZBLGVBQU8sSUFBUDtBQUNBWixlQUFPUixJQUFQLEVBQWFzQixNQUFiO0FBQ0QsT0FSRDtBQVNELEtBVkQsQ0FVRSxPQUFPQyxFQUFQLEVBQVc7QUFDWCxVQUFJSCxJQUFKLEVBQVU7QUFDVkEsYUFBTyxJQUFQO0FBQ0FaLGFBQU9SLElBQVAsRUFBYXVCLEVBQWI7QUFDRDtBQUNGOztBQUVEaEMsVUFBUWlDLFNBQVIsQ0FBa0IsT0FBbEIsSUFBNkIsVUFBVWxCLFVBQVYsRUFBc0I7QUFDakQsV0FBTyxLQUFLTyxJQUFMLENBQVUsSUFBVixFQUFnQlAsVUFBaEIsQ0FBUDtBQUNELEdBRkQ7O0FBSUFmLFVBQVFpQyxTQUFSLENBQWtCWCxJQUFsQixHQUF5QixVQUFVUixXQUFWLEVBQXVCQyxVQUF2QixFQUFtQztBQUMxRCxRQUFJbUIsT0FBTyxJQUFLLEtBQUtDLFdBQVYsQ0FBdUJ6QyxJQUF2QixDQUFYOztBQUVBYyxXQUFPLElBQVAsRUFBYSxJQUFJb0IsT0FBSixDQUFZZCxXQUFaLEVBQXlCQyxVQUF6QixFQUFxQ21CLElBQXJDLENBQWI7QUFDQSxXQUFPQSxJQUFQO0FBQ0QsR0FMRDs7QUFPQWxDLFVBQVFvQyxHQUFSLEdBQWMsVUFBVUMsR0FBVixFQUFlO0FBQzNCLFFBQUlDLE9BQU9DLE1BQU1OLFNBQU4sQ0FBZ0JPLEtBQWhCLENBQXNCQyxJQUF0QixDQUEyQkosR0FBM0IsQ0FBWDs7QUFFQSxXQUFPLElBQUlyQyxPQUFKLENBQVksVUFBVWdCLE9BQVYsRUFBbUJDLE1BQW5CLEVBQTJCO0FBQzVDLFVBQUlxQixLQUFLZCxNQUFMLEtBQWdCLENBQXBCLEVBQXVCLE9BQU9SLFFBQVEsRUFBUixDQUFQO0FBQ3ZCLFVBQUkwQixZQUFZSixLQUFLZCxNQUFyQjs7QUFFQSxlQUFTbUIsR0FBVCxDQUFhakIsQ0FBYixFQUFnQmtCLEdBQWhCLEVBQXFCO0FBQ25CLFlBQUk7QUFDRixjQUFJQSxRQUFRLFFBQU9BLEdBQVAseUNBQU9BLEdBQVAsT0FBZSxRQUFmLElBQTJCLE9BQU9BLEdBQVAsS0FBZSxVQUFsRCxDQUFKLEVBQW1FO0FBQ2pFLGdCQUFJdEIsT0FBT3NCLElBQUl0QixJQUFmO0FBQ0EsZ0JBQUksT0FBT0EsSUFBUCxLQUFnQixVQUFwQixFQUFnQztBQUM5QkEsbUJBQUttQixJQUFMLENBQVVHLEdBQVYsRUFBZSxVQUFVQSxHQUFWLEVBQWU7QUFDNUJELG9CQUFJakIsQ0FBSixFQUFPa0IsR0FBUDtBQUNELGVBRkQsRUFFRzNCLE1BRkg7QUFHQTtBQUNEO0FBQ0Y7QUFDRHFCLGVBQUtaLENBQUwsSUFBVWtCLEdBQVY7QUFDQSxjQUFJLEVBQUVGLFNBQUYsS0FBZ0IsQ0FBcEIsRUFBdUI7QUFDckIxQixvQkFBUXNCLElBQVI7QUFDRDtBQUNGLFNBZEQsQ0FjRSxPQUFPTixFQUFQLEVBQVc7QUFDWGYsaUJBQU9lLEVBQVA7QUFDRDtBQUNGOztBQUVELFdBQUssSUFBSU4sSUFBSSxDQUFiLEVBQWdCQSxJQUFJWSxLQUFLZCxNQUF6QixFQUFpQ0UsR0FBakMsRUFBc0M7QUFDcENpQixZQUFJakIsQ0FBSixFQUFPWSxLQUFLWixDQUFMLENBQVA7QUFDRDtBQUNGLEtBM0JNLENBQVA7QUE0QkQsR0EvQkQ7O0FBaUNBMUIsVUFBUWdCLE9BQVIsR0FBa0IsVUFBVWMsS0FBVixFQUFpQjtBQUNqQyxRQUFJQSxTQUFTLFFBQU9BLEtBQVAseUNBQU9BLEtBQVAsT0FBaUIsUUFBMUIsSUFBc0NBLE1BQU1LLFdBQU4sS0FBc0JuQyxPQUFoRSxFQUF5RTtBQUN2RSxhQUFPOEIsS0FBUDtBQUNEOztBQUVELFdBQU8sSUFBSTlCLE9BQUosQ0FBWSxVQUFVZ0IsT0FBVixFQUFtQjtBQUNwQ0EsY0FBUWMsS0FBUjtBQUNELEtBRk0sQ0FBUDtBQUdELEdBUkQ7O0FBVUE5QixVQUFRaUIsTUFBUixHQUFpQixVQUFVYSxLQUFWLEVBQWlCO0FBQ2hDLFdBQU8sSUFBSTlCLE9BQUosQ0FBWSxVQUFVZ0IsT0FBVixFQUFtQkMsTUFBbkIsRUFBMkI7QUFDNUNBLGFBQU9hLEtBQVA7QUFDRCxLQUZNLENBQVA7QUFHRCxHQUpEOztBQU1BOUIsVUFBUTZDLElBQVIsR0FBZSxVQUFVQyxNQUFWLEVBQWtCO0FBQy9CLFdBQU8sSUFBSTlDLE9BQUosQ0FBWSxVQUFVZ0IsT0FBVixFQUFtQkMsTUFBbkIsRUFBMkI7QUFDNUMsV0FBSyxJQUFJUyxJQUFJLENBQVIsRUFBV0MsTUFBTW1CLE9BQU90QixNQUE3QixFQUFxQ0UsSUFBSUMsR0FBekMsRUFBOENELEdBQTlDLEVBQW1EO0FBQ2pEb0IsZUFBT3BCLENBQVAsRUFBVUosSUFBVixDQUFlTixPQUFmLEVBQXdCQyxNQUF4QjtBQUNEO0FBQ0YsS0FKTSxDQUFQO0FBS0QsR0FORDs7QUFRQTtBQUNBakIsVUFBUVksWUFBUixHQUF3QixPQUFPbUMsWUFBUCxLQUF3QixVQUF4QixJQUFzQyxVQUFVbkQsRUFBVixFQUFjO0FBQUVtRCxpQkFBYW5ELEVBQWI7QUFBbUIsR0FBMUUsSUFDckIsVUFBVUEsRUFBVixFQUFjO0FBQ1pKLG1CQUFlSSxFQUFmLEVBQW1CLENBQW5CO0FBQ0QsR0FISDs7QUFLQUksVUFBUXlCLHFCQUFSLEdBQWdDLFNBQVNBLHFCQUFULENBQStCdUIsR0FBL0IsRUFBb0M7QUFDbEUsUUFBSSxPQUFPQyxPQUFQLEtBQW1CLFdBQW5CLElBQWtDQSxPQUF0QyxFQUErQztBQUM3Q0EsY0FBUUMsSUFBUixDQUFhLHVDQUFiLEVBQXNERixHQUF0RCxFQUQ2QyxDQUNlO0FBQzdEO0FBQ0YsR0FKRDs7QUFNQTs7Ozs7QUFLQWhELFVBQVFtRCxlQUFSLEdBQTBCLFNBQVNBLGVBQVQsQ0FBeUJ2RCxFQUF6QixFQUE2QjtBQUNyREksWUFBUVksWUFBUixHQUF1QmhCLEVBQXZCO0FBQ0QsR0FGRDs7QUFJQTs7Ozs7QUFLQUksVUFBUW9ELHdCQUFSLEdBQW1DLFNBQVNBLHdCQUFULENBQWtDeEQsRUFBbEMsRUFBc0M7QUFDdkVJLFlBQVF5QixxQkFBUixHQUFnQzdCLEVBQWhDO0FBQ0QsR0FGRDs7QUFJQSxNQUFJLE9BQU95RCxNQUFQLEtBQWtCLFdBQWxCLElBQWlDQSxPQUFPQyxPQUE1QyxFQUFxRDtBQUNuREQsV0FBT0MsT0FBUCxHQUFpQnRELE9BQWpCO0FBQ0QsR0FGRCxNQUVPLElBQUlULFFBQVEsQ0FBQ0EsS0FBS1MsT0FBbEIsRUFBMkI7QUFDaENULFNBQUtTLE9BQUwsR0FBZUEsT0FBZjtBQUNEO0FBRUYsQ0F4T0QiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC92ZW5kb3IvcHJvbWlzZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiAocm9vdCkge1xuXG4gIC8vIFN0b3JlIHNldFRpbWVvdXQgcmVmZXJlbmNlIHNvIHByb21pc2UtcG9seWZpbGwgd2lsbCBiZSB1bmFmZmVjdGVkIGJ5XG4gIC8vIG90aGVyIGNvZGUgbW9kaWZ5aW5nIHNldFRpbWVvdXQgKGxpa2Ugc2lub24udXNlRmFrZVRpbWVycygpKVxuICB2YXIgc2V0VGltZW91dEZ1bmMgPSBzZXRUaW1lb3V0O1xuXG4gIGZ1bmN0aW9uIG5vb3AoKSB7fVxuICBcbiAgLy8gUG9seWZpbGwgZm9yIEZ1bmN0aW9uLnByb3RvdHlwZS5iaW5kXG4gIGZ1bmN0aW9uIGJpbmQoZm4sIHRoaXNBcmcpIHtcbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgZm4uYXBwbHkodGhpc0FyZywgYXJndW1lbnRzKTtcbiAgICB9O1xuICB9XG5cbiAgZnVuY3Rpb24gUHJvbWlzZShmbikge1xuICAgIGlmICh0eXBlb2YgdGhpcyAhPT0gJ29iamVjdCcpIHRocm93IG5ldyBUeXBlRXJyb3IoJ1Byb21pc2VzIG11c3QgYmUgY29uc3RydWN0ZWQgdmlhIG5ldycpO1xuICAgIGlmICh0eXBlb2YgZm4gIT09ICdmdW5jdGlvbicpIHRocm93IG5ldyBUeXBlRXJyb3IoJ25vdCBhIGZ1bmN0aW9uJyk7XG4gICAgdGhpcy5fc3RhdGUgPSAwO1xuICAgIHRoaXMuX2hhbmRsZWQgPSBmYWxzZTtcbiAgICB0aGlzLl92YWx1ZSA9IHVuZGVmaW5lZDtcbiAgICB0aGlzLl9kZWZlcnJlZHMgPSBbXTtcblxuICAgIGRvUmVzb2x2ZShmbiwgdGhpcyk7XG4gIH1cblxuICBmdW5jdGlvbiBoYW5kbGUoc2VsZiwgZGVmZXJyZWQpIHtcbiAgICB3aGlsZSAoc2VsZi5fc3RhdGUgPT09IDMpIHtcbiAgICAgIHNlbGYgPSBzZWxmLl92YWx1ZTtcbiAgICB9XG4gICAgaWYgKHNlbGYuX3N0YXRlID09PSAwKSB7XG4gICAgICBzZWxmLl9kZWZlcnJlZHMucHVzaChkZWZlcnJlZCk7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHNlbGYuX2hhbmRsZWQgPSB0cnVlO1xuICAgIFByb21pc2UuX2ltbWVkaWF0ZUZuKGZ1bmN0aW9uICgpIHtcbiAgICAgIHZhciBjYiA9IHNlbGYuX3N0YXRlID09PSAxID8gZGVmZXJyZWQub25GdWxmaWxsZWQgOiBkZWZlcnJlZC5vblJlamVjdGVkO1xuICAgICAgaWYgKGNiID09PSBudWxsKSB7XG4gICAgICAgIChzZWxmLl9zdGF0ZSA9PT0gMSA/IHJlc29sdmUgOiByZWplY3QpKGRlZmVycmVkLnByb21pc2UsIHNlbGYuX3ZhbHVlKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIHJldDtcbiAgICAgIHRyeSB7XG4gICAgICAgIHJldCA9IGNiKHNlbGYuX3ZhbHVlKTtcbiAgICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgcmVqZWN0KGRlZmVycmVkLnByb21pc2UsIGUpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICByZXNvbHZlKGRlZmVycmVkLnByb21pc2UsIHJldCk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiByZXNvbHZlKHNlbGYsIG5ld1ZhbHVlKSB7XG4gICAgdHJ5IHtcbiAgICAgIC8vIFByb21pc2UgUmVzb2x1dGlvbiBQcm9jZWR1cmU6IGh0dHBzOi8vZ2l0aHViLmNvbS9wcm9taXNlcy1hcGx1cy9wcm9taXNlcy1zcGVjI3RoZS1wcm9taXNlLXJlc29sdXRpb24tcHJvY2VkdXJlXG4gICAgICBpZiAobmV3VmFsdWUgPT09IHNlbGYpIHRocm93IG5ldyBUeXBlRXJyb3IoJ0EgcHJvbWlzZSBjYW5ub3QgYmUgcmVzb2x2ZWQgd2l0aCBpdHNlbGYuJyk7XG4gICAgICBpZiAobmV3VmFsdWUgJiYgKHR5cGVvZiBuZXdWYWx1ZSA9PT0gJ29iamVjdCcgfHwgdHlwZW9mIG5ld1ZhbHVlID09PSAnZnVuY3Rpb24nKSkge1xuICAgICAgICB2YXIgdGhlbiA9IG5ld1ZhbHVlLnRoZW47XG4gICAgICAgIGlmIChuZXdWYWx1ZSBpbnN0YW5jZW9mIFByb21pc2UpIHtcbiAgICAgICAgICBzZWxmLl9zdGF0ZSA9IDM7XG4gICAgICAgICAgc2VsZi5fdmFsdWUgPSBuZXdWYWx1ZTtcbiAgICAgICAgICBmaW5hbGUoc2VsZik7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9IGVsc2UgaWYgKHR5cGVvZiB0aGVuID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgZG9SZXNvbHZlKGJpbmQodGhlbiwgbmV3VmFsdWUpLCBzZWxmKTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHNlbGYuX3N0YXRlID0gMTtcbiAgICAgIHNlbGYuX3ZhbHVlID0gbmV3VmFsdWU7XG4gICAgICBmaW5hbGUoc2VsZik7XG4gICAgfSBjYXRjaCAoZSkge1xuICAgICAgcmVqZWN0KHNlbGYsIGUpO1xuICAgIH1cbiAgfVxuXG4gIGZ1bmN0aW9uIHJlamVjdChzZWxmLCBuZXdWYWx1ZSkge1xuICAgIHNlbGYuX3N0YXRlID0gMjtcbiAgICBzZWxmLl92YWx1ZSA9IG5ld1ZhbHVlO1xuICAgIGZpbmFsZShzZWxmKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGZpbmFsZShzZWxmKSB7XG4gICAgaWYgKHNlbGYuX3N0YXRlID09PSAyICYmIHNlbGYuX2RlZmVycmVkcy5sZW5ndGggPT09IDApIHtcbiAgICAgIFByb21pc2UuX2ltbWVkaWF0ZUZuKGZ1bmN0aW9uKCkge1xuICAgICAgICBpZiAoIXNlbGYuX2hhbmRsZWQpIHtcbiAgICAgICAgICBQcm9taXNlLl91bmhhbmRsZWRSZWplY3Rpb25GbihzZWxmLl92YWx1ZSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cblxuICAgIGZvciAodmFyIGkgPSAwLCBsZW4gPSBzZWxmLl9kZWZlcnJlZHMubGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcbiAgICAgIGhhbmRsZShzZWxmLCBzZWxmLl9kZWZlcnJlZHNbaV0pO1xuICAgIH1cbiAgICBzZWxmLl9kZWZlcnJlZHMgPSBudWxsO1xuICB9XG5cbiAgZnVuY3Rpb24gSGFuZGxlcihvbkZ1bGZpbGxlZCwgb25SZWplY3RlZCwgcHJvbWlzZSkge1xuICAgIHRoaXMub25GdWxmaWxsZWQgPSB0eXBlb2Ygb25GdWxmaWxsZWQgPT09ICdmdW5jdGlvbicgPyBvbkZ1bGZpbGxlZCA6IG51bGw7XG4gICAgdGhpcy5vblJlamVjdGVkID0gdHlwZW9mIG9uUmVqZWN0ZWQgPT09ICdmdW5jdGlvbicgPyBvblJlamVjdGVkIDogbnVsbDtcbiAgICB0aGlzLnByb21pc2UgPSBwcm9taXNlO1xuICB9XG5cbiAgLyoqXG4gICAqIFRha2UgYSBwb3RlbnRpYWxseSBtaXNiZWhhdmluZyByZXNvbHZlciBmdW5jdGlvbiBhbmQgbWFrZSBzdXJlXG4gICAqIG9uRnVsZmlsbGVkIGFuZCBvblJlamVjdGVkIGFyZSBvbmx5IGNhbGxlZCBvbmNlLlxuICAgKlxuICAgKiBNYWtlcyBubyBndWFyYW50ZWVzIGFib3V0IGFzeW5jaHJvbnkuXG4gICAqL1xuICBmdW5jdGlvbiBkb1Jlc29sdmUoZm4sIHNlbGYpIHtcbiAgICB2YXIgZG9uZSA9IGZhbHNlO1xuICAgIHRyeSB7XG4gICAgICBmbihmdW5jdGlvbiAodmFsdWUpIHtcbiAgICAgICAgaWYgKGRvbmUpIHJldHVybjtcbiAgICAgICAgZG9uZSA9IHRydWU7XG4gICAgICAgIHJlc29sdmUoc2VsZiwgdmFsdWUpO1xuICAgICAgfSwgZnVuY3Rpb24gKHJlYXNvbikge1xuICAgICAgICBpZiAoZG9uZSkgcmV0dXJuO1xuICAgICAgICBkb25lID0gdHJ1ZTtcbiAgICAgICAgcmVqZWN0KHNlbGYsIHJlYXNvbik7XG4gICAgICB9KTtcbiAgICB9IGNhdGNoIChleCkge1xuICAgICAgaWYgKGRvbmUpIHJldHVybjtcbiAgICAgIGRvbmUgPSB0cnVlO1xuICAgICAgcmVqZWN0KHNlbGYsIGV4KTtcbiAgICB9XG4gIH1cblxuICBQcm9taXNlLnByb3RvdHlwZVsnY2F0Y2gnXSA9IGZ1bmN0aW9uIChvblJlamVjdGVkKSB7XG4gICAgcmV0dXJuIHRoaXMudGhlbihudWxsLCBvblJlamVjdGVkKTtcbiAgfTtcblxuICBQcm9taXNlLnByb3RvdHlwZS50aGVuID0gZnVuY3Rpb24gKG9uRnVsZmlsbGVkLCBvblJlamVjdGVkKSB7XG4gICAgdmFyIHByb20gPSBuZXcgKHRoaXMuY29uc3RydWN0b3IpKG5vb3ApO1xuXG4gICAgaGFuZGxlKHRoaXMsIG5ldyBIYW5kbGVyKG9uRnVsZmlsbGVkLCBvblJlamVjdGVkLCBwcm9tKSk7XG4gICAgcmV0dXJuIHByb207XG4gIH07XG5cbiAgUHJvbWlzZS5hbGwgPSBmdW5jdGlvbiAoYXJyKSB7XG4gICAgdmFyIGFyZ3MgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChhcnIpO1xuXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKGZ1bmN0aW9uIChyZXNvbHZlLCByZWplY3QpIHtcbiAgICAgIGlmIChhcmdzLmxlbmd0aCA9PT0gMCkgcmV0dXJuIHJlc29sdmUoW10pO1xuICAgICAgdmFyIHJlbWFpbmluZyA9IGFyZ3MubGVuZ3RoO1xuXG4gICAgICBmdW5jdGlvbiByZXMoaSwgdmFsKSB7XG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgaWYgKHZhbCAmJiAodHlwZW9mIHZhbCA9PT0gJ29iamVjdCcgfHwgdHlwZW9mIHZhbCA9PT0gJ2Z1bmN0aW9uJykpIHtcbiAgICAgICAgICAgIHZhciB0aGVuID0gdmFsLnRoZW47XG4gICAgICAgICAgICBpZiAodHlwZW9mIHRoZW4gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgdGhlbi5jYWxsKHZhbCwgZnVuY3Rpb24gKHZhbCkge1xuICAgICAgICAgICAgICAgIHJlcyhpLCB2YWwpO1xuICAgICAgICAgICAgICB9LCByZWplY3QpO1xuICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICAgIGFyZ3NbaV0gPSB2YWw7XG4gICAgICAgICAgaWYgKC0tcmVtYWluaW5nID09PSAwKSB7XG4gICAgICAgICAgICByZXNvbHZlKGFyZ3MpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSBjYXRjaCAoZXgpIHtcbiAgICAgICAgICByZWplY3QoZXgpO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgYXJncy5sZW5ndGg7IGkrKykge1xuICAgICAgICByZXMoaSwgYXJnc1tpXSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH07XG5cbiAgUHJvbWlzZS5yZXNvbHZlID0gZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgaWYgKHZhbHVlICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUuY29uc3RydWN0b3IgPT09IFByb21pc2UpIHtcbiAgICAgIHJldHVybiB2YWx1ZTtcbiAgICB9XG5cbiAgICByZXR1cm4gbmV3IFByb21pc2UoZnVuY3Rpb24gKHJlc29sdmUpIHtcbiAgICAgIHJlc29sdmUodmFsdWUpO1xuICAgIH0pO1xuICB9O1xuXG4gIFByb21pc2UucmVqZWN0ID0gZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKGZ1bmN0aW9uIChyZXNvbHZlLCByZWplY3QpIHtcbiAgICAgIHJlamVjdCh2YWx1ZSk7XG4gICAgfSk7XG4gIH07XG5cbiAgUHJvbWlzZS5yYWNlID0gZnVuY3Rpb24gKHZhbHVlcykge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZShmdW5jdGlvbiAocmVzb2x2ZSwgcmVqZWN0KSB7XG4gICAgICBmb3IgKHZhciBpID0gMCwgbGVuID0gdmFsdWVzLmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XG4gICAgICAgIHZhbHVlc1tpXS50aGVuKHJlc29sdmUsIHJlamVjdCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH07XG5cbiAgLy8gVXNlIHBvbHlmaWxsIGZvciBzZXRJbW1lZGlhdGUgZm9yIHBlcmZvcm1hbmNlIGdhaW5zXG4gIFByb21pc2UuX2ltbWVkaWF0ZUZuID0gKHR5cGVvZiBzZXRJbW1lZGlhdGUgPT09ICdmdW5jdGlvbicgJiYgZnVuY3Rpb24gKGZuKSB7IHNldEltbWVkaWF0ZShmbik7IH0pIHx8XG4gICAgZnVuY3Rpb24gKGZuKSB7XG4gICAgICBzZXRUaW1lb3V0RnVuYyhmbiwgMCk7XG4gICAgfTtcblxuICBQcm9taXNlLl91bmhhbmRsZWRSZWplY3Rpb25GbiA9IGZ1bmN0aW9uIF91bmhhbmRsZWRSZWplY3Rpb25GbihlcnIpIHtcbiAgICBpZiAodHlwZW9mIGNvbnNvbGUgIT09ICd1bmRlZmluZWQnICYmIGNvbnNvbGUpIHtcbiAgICAgIGNvbnNvbGUud2FybignUG9zc2libGUgVW5oYW5kbGVkIFByb21pc2UgUmVqZWN0aW9uOicsIGVycik7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tY29uc29sZVxuICAgIH1cbiAgfTtcblxuICAvKipcbiAgICogU2V0IHRoZSBpbW1lZGlhdGUgZnVuY3Rpb24gdG8gZXhlY3V0ZSBjYWxsYmFja3NcbiAgICogQHBhcmFtIGZuIHtmdW5jdGlvbn0gRnVuY3Rpb24gdG8gZXhlY3V0ZVxuICAgKiBAZGVwcmVjYXRlZFxuICAgKi9cbiAgUHJvbWlzZS5fc2V0SW1tZWRpYXRlRm4gPSBmdW5jdGlvbiBfc2V0SW1tZWRpYXRlRm4oZm4pIHtcbiAgICBQcm9taXNlLl9pbW1lZGlhdGVGbiA9IGZuO1xuICB9O1xuXG4gIC8qKlxuICAgKiBDaGFuZ2UgdGhlIGZ1bmN0aW9uIHRvIGV4ZWN1dGUgb24gdW5oYW5kbGVkIHJlamVjdGlvblxuICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBmbiBGdW5jdGlvbiB0byBleGVjdXRlIG9uIHVuaGFuZGxlZCByZWplY3Rpb25cbiAgICogQGRlcHJlY2F0ZWRcbiAgICovXG4gIFByb21pc2UuX3NldFVuaGFuZGxlZFJlamVjdGlvbkZuID0gZnVuY3Rpb24gX3NldFVuaGFuZGxlZFJlamVjdGlvbkZuKGZuKSB7XG4gICAgUHJvbWlzZS5fdW5oYW5kbGVkUmVqZWN0aW9uRm4gPSBmbjtcbiAgfTtcbiAgXG4gIGlmICh0eXBlb2YgbW9kdWxlICE9PSAndW5kZWZpbmVkJyAmJiBtb2R1bGUuZXhwb3J0cykge1xuICAgIG1vZHVsZS5leHBvcnRzID0gUHJvbWlzZTtcbiAgfSBlbHNlIGlmIChyb290ICYmICFyb290LlByb21pc2UpIHtcbiAgICByb290LlByb21pc2UgPSBQcm9taXNlO1xuICB9XG5cbn0pKHRoaXMpO1xuIl19
