import { initPlugins } from "./plugins/plugins";
import { onShopifyAuth } from "./security/security";
import { initForms } from "./forms/forms";
import { initModal } from "./modal/modal";
import { learnMoreLink } from "./utils";

(function($) {

  $(function() {

    // Only invoke this function if on the Auth page ...
    if(window.location.href.indexOf("auth") > -1) {
      onShopifyAuth();
    }

    initPlugins($);
    initForms($);
    initModal($);

    learnMoreLink($);

  });

})(jQuery);
