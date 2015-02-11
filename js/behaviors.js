/**
 * @file Implementation of Drupal behavior.
 */

(function($) {
/**
 * Wrapper function for Google Analytics events
 */
function ga_event(params) {
  if (typeof _gaq === "object") {
    params.splice(0, 0, "_trackEvent");
    _gaq.push(params);
  }
  else if (typeof ga === "function") {
    params.splice(0, 0, 'send', 'event');
    ga.apply(null, params);
  }
}

/**
 * jQuery when the DOM is ready.
 */
$(document).ready(function(){
  // Give external links target="_blank"
  var $a = $('a');
  $a.each(function(i) {
    if (this.href.length && this.hostname !== window.location.hostname) {
      $(this).attr('target','_blank');
    }
  });
});

})(jQuery);
