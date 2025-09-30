define(['jquery'], function ($) {
  'use strict';

  return function (Sidebar) {
    return Sidebar.extend({
      _removeItem: function (elem) {
        // 🚫 Prevent Magento's built-in confirmation
        // 🔔 Dispatch your own event for custom modal
        $(document).trigger('custom:minicartRemove', [$(elem)]);
        return false;
      }
    });
  };
});
