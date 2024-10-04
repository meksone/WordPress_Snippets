<?php

$snippet_name = "gutenberg_remove_taxonomy_from_edit_screen";
$version = "<!#FV> 0.0.1 </#FV>";

function mk_remove_taxonomy_from_edit_screen()
{
    // Get the current screen
    $screen = get_current_screen();
    

    // Check if we're on the edit screen for the 'corsi' CPT
    if ('corsi' === $screen->post_type && 'post' === $screen->base) {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                // ASCII Art
                var asciiArt = `
%c

░▒▓██████████████▓▒░░▒▓████████▓▒░▒▓█▓▒░░▒▓█▓▒░░▒▓███████▓▒░░▒▓██████▓▒░░▒▓███████▓▒░░▒▓████████▓▒░ 
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓██████▓▒░ ░▒▓███████▓▒░ ░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓██████▓▒░   
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓████████▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓███████▓▒░ ░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓████████▓▒░ 
                                                                                                    
                                                                                                    

                                           
`;
                console.log(asciiArt, 'color: #00ff00; background: #000; font-family: monospace;');
                console.log('%cRemoving the taxonomy panel Tag Corso for corsi CPT', 'color: #00ff00; background: #000; font-family: monospace; font-weight: bold;');
                console.log('%cRemoving the taxonomy panel Categorie Corso for corsi CPT', 'color: #00ff00; background: #000; font-family: monospace; font-weight: bold;');
                wp.data.dispatch('core/edit-post').removeEditorPanel('taxonomy-panel-categoria-corso');
                wp.data.dispatch('core/edit-post').removeEditorPanel('taxonomy-panel-tag-corso');
            });
        </script>
<?php
    }
}
add_action('admin_footer', 'mk_custom_admin_script');