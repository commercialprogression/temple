/**
 * @file Implementation of Drupal behavior.
 */

(function($) {
/**
 * jQuery when the DOM is ready.
 */
$(document).ready(function(){
  // Activate fancy tooltips on non-touch screens.
  if (!('ontouchstart' in window) && !('onmsgesturechange' in window)) {
    $('[title]').tipsy({
      delayIn: 350, 
      delayOut: 200
    });
  }
});

})(jQuery);
