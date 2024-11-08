<script>
/*
* Sticky Header Show/Hide on scroll 
* Use mk-header ID on the main sticky container for the menu
* When the header is hidden, the class hide-header will be added
*/


jQuery(document).ready(function($) {
    //console.log('Document ready - Waiting 500ms before initialization...');
    
    setTimeout(function() {
        //console.log('Starting header scroll script after delay');
        
        (function(jQuery) {
            var header = jQuery('#mk-header.elementor-sticky--effects');
            var lastScrollTop = 0;
            var threshold = 50;
            
            // Check if header element exists
            if (header.length === 0) {
                console.error('Header element (#mk-header.elementor-sticky--effects) not found!');
                //console.log('Make sure the header has both the mk-header ID and becomes sticky');
            } else {
                console.log('Header element found:', header);
            }

            jQuery(window).on('scroll', function() {
                // Re-check for the header with sticky class on each scroll
                // This is important because the sticky class might be added after page load
                header = jQuery('#mk-header.elementor-sticky--effects');
                
                if (header.length === 0) {
                    // Skip if header is not sticky yet
                    return;
                }
                
                var scrollTop = jQuery(this).scrollTop();
                
                // Log scroll position for debugging
                //console.log('Current scroll position:', scrollTop);
                
                // Only trigger if we've scrolled more than the threshold
                if(Math.abs(scrollTop - lastScrollTop) <= threshold) {
                    //console.log('Scroll threshold not met. Threshold:', threshold);
                    return;
                }
                
                if (scrollTop > lastScrollTop && scrollTop > threshold) {
                    // Scrolling down
                    header.addClass('hide-header');
                    /*console.log('Scrolling DOWN - Header hidden', {
                        scrollTop: scrollTop,
                        lastScrollTop: lastScrollTop,
                        difference: scrollTop - lastScrollTop,
                        headerElement: header
                    });*/
                } else {
                    // Scrolling up
                    header.removeClass('hide-header');
                    /*console.log('Scrolling UP - Header visible', {
                        scrollTop: scrollTop,
                        lastScrollTop: lastScrollTop,
                        difference: scrollTop - lastScrollTop,
                        headerElement: header
                    });*/
                }
                
                lastScrollTop = scrollTop;
            });
            
            // Log window height and document height for reference
            //console.log('Window height:', jQuery(window).height());
            //console.log('Document height:', jQuery(document).height());
            
        })(jQuery);
    }, 500); // 500ms delay
});
</script>

<style>
#mk-header.elementor-sticky--effects.hide-header {
    transform: translateY(-100%);
    transition: transform 0.3s ease-in-out;
}

/* Ensure the header has a higher z-index when sticky */
#mk-header.elementor-sticky--effects {
    z-index: 999;
}
</style>