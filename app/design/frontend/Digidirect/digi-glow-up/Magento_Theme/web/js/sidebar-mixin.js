define(['jquery'], function ($) {
  'use strict';

  return function (Sidebar) {
    // If Sidebar supports extend (UI class), use extend
    if (Sidebar && typeof Sidebar.extend === 'function') {
      return Sidebar.extend({
        _removeItem: function (elem) {
          // trigger custom event instead of Magento confirm
          $(document).trigger('custom:minicartRemove', [$(elem)]);
          return false;
        }
      });
    }

    // If Sidebar is a constructor or object, try to patch prototype
    try {
      if (Sidebar && Sidebar.prototype && typeof Sidebar.prototype._removeItem === 'function') {
        Sidebar.prototype._removeItem = function (elem) {
          $(document).trigger('custom:minicartRemove', [$(elem)]);
          return false;
        };
        return Sidebar;
      }

      // If Sidebar is a function (factory), patch its prototype if present
      if (typeof Sidebar === 'function') {
        if (!Sidebar.prototype) {
          Sidebar.prototype = {};
        }
        Sidebar.prototype._removeItem = function (elem) {
          $(document).trigger('custom:minicartRemove', [$(elem)]);
          return false;
        };
        return Sidebar;
      }
    } catch (e) {
      // fallback - keep original Sidebar
      // eslint-disable-next-line no-console
      console.warn('sidebar-mixin: fallback patch failed', e);
    }

    // Final fallback: return a wrapper that triggers our event if `_removeItem` exists on instance
    return function () {
      var instance = Sidebar.apply(this, arguments);
      try {
        if (instance && typeof instance._removeItem === 'function') {
          var orig = instance._removeItem;
          instance._removeItem = function (elem) {
            $(document).trigger('custom:minicartRemove', [$(elem)]);
            return false;
          };
        }
      } catch (e) {
        // ignore
      }
      return instance;
    };
  };
});
