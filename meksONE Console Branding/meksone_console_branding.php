<?php

$snippet_name = "meksone_console_branding";
$version = "<!#FV> 0.0.2 </#FV>";

function mk_meksone_console_branding()
{
    // Get the current screen
    $screen = get_current_screen();
	$screenID = $screen->id;
	$screenBase = $screen->base;
	if(!$screenBase){
		$screenBase = "---";
	};
	$postType = $screen->post_type;
	if(!$postType){
		$postType = "---";
	};
	$screenParentBase = $screen->parent_base;
	if(!$screenParentBase){
		$screenParentBase = "---";
	};
		$screenParentFile = $screen->parent_file;
	if(!$screenParentFile){
		$screenParentFile = "---";
	};
	$screenAction = $screen->action;
	if(!$screenAction){
		$screenAction = "---";
	};
    

    // Check if we're on specific edit screen base url and CPT
    //if ('cptName' === $screen->post_type && 'post' === $screen->base) {
        if (true) {
			// Debug screen
			// echo '<pre>'; print_r( $screen ); echo '</pre>';
 
            // JS start
            ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {

                // ASCII Art
var asciiart01 = `
%c

░▒▓██████████████▓▒░░▒▓████████▓▒░▒▓█▓▒░░▒▓█▓▒░░▒▓███████▓▒░░▒▓██████▓▒░░▒▓███████▓▒░░▒▓████████▓▒░ 
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓██████▓▒░ ░▒▓███████▓▒░ ░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓██████▓▒░   
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░        
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓████████▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓███████▓▒░ ░▒▓██████▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓████████▓▒░ 
`;
				
var asciiart02 = `
%c


░  ░░░░  ░        ░  ░░░░  ░░      ░░░      ░░   ░░░  ░        ░
▒   ▒▒   ▒  ▒▒▒▒▒▒▒  ▒▒▒  ▒▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒    ▒▒  ▒  ▒▒▒▒▒▒▒
▓        ▓      ▓▓▓     ▓▓▓▓▓      ▓▓  ▓▓▓▓  ▓  ▓  ▓  ▓      ▓▓▓
█  █  █  █  ███████  ███  ████████  █  ████  █  ██    █  ███████
█  ████  █        █  ████  ██      ███      ██  ███   █        █
`;

var asciiart03 = `
%c

███╗   ████████████╗  █████████╗██████╗███╗   █████████╗
████╗ ██████╔════██║ ██╔██╔════██╔═══██████╗  ████╔════╝
██╔████╔███████╗ █████╔╝█████████║   ████╔██╗ ███████╗  
██║╚██╔╝████╔══╝ ██╔═██╗╚════████║   ████║╚██╗████╔══╝  
██║ ╚═╝ ███████████║  █████████╚██████╔██║ ╚███████████╗
╚═╝     ╚═╚══════╚═╝  ╚═╚══════╝╚═════╝╚═╝  ╚═══╚══════╝
`;

var asciiart04 = `
%c

 ███▄ ▄███▓█████ ██ ▄█▀ ██████ ▒█████  ███▄    █▓█████ 
▓██▒▀█▀ ██▓█   ▀ ██▄█▒▒██    ▒▒██▒  ██▒██ ▀█   █▓█   ▀ 
▓██    ▓██▒███  ▓███▄░░ ▓██▄  ▒██░  ██▓██  ▀█ ██▒███   
▒██    ▒██▒▓█  ▄▓██ █▄  ▒   ██▒██   ██▓██▒  ▐▌██▒▓█  ▄ 
▒██▒   ░██░▒████▒██▒ █▒██████▒░ ████▓▒▒██░   ▓██░▒████▒
░ ▒░   ░  ░░ ▒░ ▒ ▒▒ ▓▒ ▒▓▒ ▒ ░ ▒░▒░▒░░ ▒░   ▒ ▒░░ ▒░ ░
░  ░      ░░ ░  ░ ░▒ ▒░ ░▒  ░ ░ ░ ▒ ▒░░ ░░   ░ ▒░░ ░  ░
░      ░     ░  ░ ░░ ░░  ░  ░ ░ ░ ░ ▒    ░   ░ ░   ░   
       ░     ░  ░  ░        ░     ░ ░          ░   ░  ░
`;

var color01 = 'color: #00ff00; background: #000; font-family: monospace;'; 
var color02 = 'color: #ff00ff; background: #000; font-family: monospace;'; 
var color03 = 'color: #14d4db; background: #000; font-family: monospace;'; 
var color04 = 'color: #dba614; background: #000; font-family: monospace;';

function randomArt() {
    var arts = [asciiart01,asciiart02,asciiart03,asciiart04];
	var colors = [color01,color02,color03,color04];
	var	asciiart = arts[Math.floor(Math.random() * arts.length)];
	var consoleColor = colors[Math.floor(Math.random() * colors.length)];
	console.log(asciiart, consoleColor);
}

function randomColorText(text, mode){
	var colors = [color01,color02,color03,color04];
	var consoleColor = colors[Math.floor(Math.random() * colors.length)];
	if(mode == 'log'){ console.log(text, consoleColor); };
	if(mode == 'group'){ console.group(text, consoleColor); };
	if(mode == 'warn'){ console.warn(text, consoleColor); };
	if(mode == 'debug'){ console.debug(text, consoleColor); };
	if(mode == 'error'){ console.error(text, 'color: #ff0000; background: #000; font-family: monospace;'); };
}

function tableOutput(ScreenID, ScreenBase, ScreenParentBase, ScreenParentFile, postType, screenAction) {
 	this.ScreenID = ScreenID;
 	this.ScreenBase = ScreenBase;
	this.ScreenParentBase = ScreenParentBase;
	this.ScreenParentFile = ScreenParentFile;
	this.postType = postType;
	this.screenAction = screenAction;
}

const infoPanel = new tableOutput("<?php print($screenID); ?>","<?php print($screenBase); ?>","<?php print($screenParentBase); ?>","<?php print($screenParentFile); ?>","<?php print($postType); ?>","<?php print($screenAction); ?>");

// Output Random ASCII Art
randomArt();
console.groupEnd();

randomColorText( '%c--- meksONE ---', 'log' );
console.groupEnd();

/*
randomColorText( '%c--- group ---', 'group' );
console.groupEnd();
randomColorText( '%c--- warn ---', 'warn' );
console.groupEnd();
randomColorText( '%c--- debug ---', 'debug' );
console.groupEnd();
randomColorText( '%c--- error ---', 'error' );
*/
console.table(infoPanel);
console.groupEnd();

				
            });
        </script>
<?php
// JS END
    }
}
add_action('admin_footer', 'mk_meksone_console_branding');
//add_action('wp_footer', 'mk_meksone_console_branding'); // doesn't work in backend