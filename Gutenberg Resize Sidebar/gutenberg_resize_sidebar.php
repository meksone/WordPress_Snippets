<?php
$snippet_name = "gutenberg_resize_sidebar";
$version = "<!#FV> 0.0.4 </#FV>";

/* Resize Gutenberg sidebar
 * reference https://www.toastplugins.co.uk/changing-the-width-of-the-wordpress-gutenberg-editor/
 * fixed, the original code doesn't work in WP 6.6.2
 * */


/*function toast_enqueue_jquery_ui(){
	wp_enqueue_script( 'jquery-ui-resizable');
}

add_action('admin_enqueue_scripts', 'toast_enqueue_jquery_ui');
*/


function execute_on_post_edit_screen($post_types) {
    global $typenow;

    // If no post types are specified, execute the function
    if (!$post_types) {
		add_action('admin_head', 'toast_resizable_sidebar');	
        return;
    }

    // If post types are specified, check if the current post type is in the array
    if (is_array($post_types) && in_array($typenow, $post_types)) {
		add_action('admin_head', 'toast_resizable_sidebar');
    }
}

function load_on_specific_screens() {
    $screen = get_current_screen();

    if ($screen->base == 'post') {
        $post_types = array('post', 'page', 'film'); // Specify the post types here
        execute_on_post_edit_screen($post_types);
    }
}

add_action('current_screen', 'load_on_specific_screens');


function toast_resizable_sidebar(){ ?>
	<style>
.interface-interface-skeleton__sidebar .interface-complementary-area, .interface-interface-skeleton__sidebar .interface-complementary-area__fill{width:100% !important;}
.edit-post-layout:not(.is-sidebar-opened) .interface-interface-skeleton__sidebar{display:none;}
.edit-site-layout:not(.is-sidebar-opened) .interface-interface-skeleton__sidebar{display:none;}
.is-sidebar-opened .interface-interface-skeleton__sidebar{width:40%;}

/*UI Styles*/
.ui-dialog .ui-resizable-n {
	height: 2px;
	top: 0;
}
.ui-dialog .ui-resizable-e {
	width: 2px;
	right: 0;
}
.ui-dialog .ui-resizable-s {
	height: 2px;
	bottom: 0;
}
.ui-dialog .ui-resizable-w {
	width: 2px;
	left: 0;
}
.ui-dialog .ui-resizable-se,
.ui-dialog .ui-resizable-sw,
.ui-dialog .ui-resizable-ne,
.ui-dialog .ui-resizable-nw {
	width: 7px;
	height: 7px;
}
.ui-dialog .ui-resizable-se {
	right: 0;
	bottom: 0;
}
.ui-dialog .ui-resizable-sw {
	left: 0;
	bottom: 0;
}
.ui-dialog .ui-resizable-ne {
	right: 0;
	top: 0;
}
.ui-dialog .ui-resizable-nw {
	left: 0;
	top: 0;
}
.ui-draggable .ui-dialog-titlebar {
	cursor: move;
}
.ui-draggable-handle {
	-ms-touch-action: none;
	touch-action: none;
}
.ui-resizable {
	position: relative;
}
.ui-resizable-handle {
	position: absolute;
	font-size: 0.1px;
	display: block;
	-ms-touch-action: none;
	touch-action: none;
}
.ui-resizable-disabled .ui-resizable-handle,
.ui-resizable-autohide .ui-resizable-handle {
	display: none;
}
.ui-resizable-n {
	cursor: n-resize;
	height: 30px;
	width: 100%;
	top: -15px;
	left: 0;
}
.ui-resizable-s {
	cursor: s-resize;
	height: 30px;
	width: 100%;
	bottom: -15px;
	left: 0;
}
.ui-resizable-e {
	cursor: e-resize;
	width: 30px;
	right: -15px;
	top: 0;
	height: 100%;
}
.ui-resizable-w {
	cursor: w-resize;
	width: 30px;
	left: -15px;
	top: 0;
	height: 100%;
}
.ui-resizable-se {
	cursor: se-resize;
	width: 12px;
	height: 12px;
	right: 1px;
	bottom: 1px;
}
.ui-resizable-sw {
	cursor: sw-resize;
	width: 9px;
	height: 9px;
	left: -5px;
	bottom: -5px;
}
.ui-resizable-nw {
	cursor: nw-resize;
	width: 9px;
	height: 9px;
	left: -5px;
	top: -5px;
}
.ui-resizable-ne {
	cursor: ne-resize;
	width: 9px;
	height: 9px;
	right: -5px;
	top: -5px;
}

.interface-interface-skeleton__sidebar.ui-resizable-resizing{position: relative !important;}
.interface-interface-skeleton__sidebar.ui-resizable-resizing:after{content:'';width:100%;height:100%;position:absolute;top:0;left:0;background:url('/wp-content/uploads/2024/09/bim-logo-blue.svg') #ffffff;background-size:300px auto;background-position:center;background-repeat:no-repeat;}
		
	</style>

	<script>
		/*
		jQuery(document).ready(function(){
	
    		setTimeout(function(){
				var customWidth = localStorage.getItem('toast_sidebar_width');
	    		jQuery('.interface-complementary-area__fill').width( customWidth );
				
				console.log("sidebar width is: " + customWidth);
        		jQuery('.interface-complementary-area__fill').resizable({
            		handles: 'w',
            		resize: function(event, ui) {
                		jQuery(this).css({'left': 0});
                		localStorage.setItem('toast_sidebar_width', jQuery(this).width());
           				}
        		});
    		}, 600) // END TimeOut
			*/
				/* Trigger after Gutenberg saves - listener */
		/*
		wp.data.subscribe(function () {
		  var isSavingPost = wp.data.select('core/editor').isSavingPost();
		  var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

		  if (isSavingPost && !isAutosavingPost) {
			console.log("Item Saved!");
			jQuery('.interface-complementary-area__fill').width(localStorage.getItem('toast_sidebar_width'));
			console.log("Sidebar restored!");

		  }
		}) // END listener
		});
		*/
	</script>

	<script>
		jQuery(window).ready(function(){
    setInterval(function(){
        jQuery('.interface-interface-skeleton__sidebar').width(localStorage.getItem('toast_rs_personal_sidebar_width'))

        jQuery('.interface-interface-skeleton__sidebar').resizable({
            handles: 'w',
            resize: function(event, ui) {
                jQuery(this).css({'left': 0});
                localStorage.setItem('toast_rs_personal_sidebar_width', jQuery(this).width());
           }
        });
        
        determine_if_sidebar_open();

    }, 500);

    jQuery('body').on('click', '.interface-pinned-items button', function(){
        determine_if_sidebar_open();
    });

    function determine_if_sidebar_open(){
        var sidebar_enabled = false;
        jQuery('.interface-pinned-items button').each(function(){
            if(jQuery(this).hasClass('is-pressed')){
              sidebar_enabled = true;
            }
        })
        if(sidebar_enabled){
            jQuery('.edit-post-layout, .edit-site-layout').addClass('is-sidebar-opened');
        }else{
            jQuery('.edit-post-layout, .edit-site-layout').removeClass('is-sidebar-opened');
        }
    }
			
		wp.data.subscribe(function () {
		  var isSavingPost = wp.data.select('core/editor').isSavingPost();
		  var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

		  if (isSavingPost && !isAutosavingPost) {
			console.log("Item Saved!");
			jQuery('.interface-complementary-area__fill').width(localStorage.getItem('toast_sidebar_width'));
			console.log("Sidebar restored!");

		  }
		}) // END listener

})
		</script>


	
<?php }