<?php

$snippet_name = "custom_acf_styles";
$version = "<!#FV> 0.0.3 </#FV>";

/*
 * Custom ACF Styles
 *  .mk-acf-backg-grey
 * 	#mk-in-evidenza - yellow label + description
 *  .mk-group-highlight - yellow Group Label + yellow background
 *  .mk-group-label-hide - hide Group Label 
 * */

 function mk_acf_custom_styles() {
	?>
	<style type="text/css">
	
	:root {	
	--mk-acf-full-01: #f5d771;
	--mk-acf-light-01: #fff7d3;
	--mk-acf-grey-01: #FAFAFA;
	--mk-acf-grey-02: #EAEAEA;
	--mk-acf-grey-03: #bdbdbd;
	--mk-acf-orangered: #ff4500;
	}
	
	/* Hide label inside ACF group */
		.mk-group-label-hide > .acf-label {
		display: none;
	}
	
	/* Special Group Label */
		.mk-group-highlight > .acf-label label {
		padding: 5px 5px 5px 5px;
		background-color: var(--mk-acf-full-01)!important;
		color: var(--mk-acf-orangered)!important;
		border-radius: 6px;
	}
		.mk-group-highlight {
		background-color: var(--mk-acf-light-01);
		margin: 5px !important;
		}

		.mk-group-highlight.acf-field p.description {
		background-color: var(--mk-acf-full-01);
		color: var(--mk-acf-orangered);
		/*padding: 5px;
		border-radius: 6px;*/
		}

		.mk-field-highlight > .acf-label label {
		padding: 5px 5px 5px 5px;
		background-color: var(--mk-acf-full-01)!important;
		color: var(--mk-acf-orangered)!important;
		border-radius: 6px;
	}
		.mk-field-highlight {
		/*background-color: var(--mk-acf-light-01);
		margin: 5px !important;
		}*/

		.mk-field-highlight.acf-field p.description {
		background-color: var(--mk-acf-full-01);
		color: var(--mk-acf-orangered);
		}
	
	/* Grey Background */	
		.mk-acf-backg-grey {
		background-color: var(--mk-acf-grey-01);
		border-radius: 8px;
		}

		
		/* In evidenza background */
		.mk-in-evidenza {
		background-color: var(--mk-acf-light-01);		
		}
			
		#mk-in-evidenza.acf-field .acf-label label {
		display: inline;
		margin: 0 0 3px;
		padding: 5px 5px 5px 5px;
		background-color: var(--mk-acf-full-01);
		color: var(--mk-acf-orangered);
		border-radius: 6px;
		}
		
		#mk-in-evidenza.acf-field p.description {
		background-color: var(--mk-acf-full-01);
		color: var(--mk-acf-orangered);
		/*padding: 5px;
		border-radius: 6px;*/
		}
		
		/* Hide ACF element */
		.mk-acf-hide {
			display: none;
		}
	
		/* Default ACF styling overrides */
		.acf-field p.description {
		background-color: var(--mk-acf-grey-02);
		padding: 5px;
		border-radius: 6px;
		}
		
		.acf-field.acf-field-group {
		padding: 0px;
		margin: 0px;
	}

		.acf-field-group > .acf-label {
		margin: 0 10px 10px;
		}
		
		.acf-field .acf-label label {
		display: inline;
		margin: 0 0 3px;
		padding: 5px 5px 5px 5px;
		background-color: var(--mk-acf-grey-02);
		border-radius: 6px;
		}	
		
		.acf-fields.-border {
		border: 0px;
		background: unset;
	}
		.acf-field {
		border: 0px!important;
		border-radius: 6px!important;
		}
		

		/* table formatting */
		td.acf-field.acf-field-text {
		padding: 4px;
		}
		/* icons formatting */
		.acf-repeater .acf-row-handle .acf-icon {
		margin: 2px 0px 0px 0px;
		}
		
		.acf-icon.small, .acf-icon.-small {
		width: 16px;
		height: 16px;
		line-height: 0px;
		font-size: 14px;
		}
		
		input[type=color], input[type=date], input[type=datetime-local], input[type=datetime], 
		input[type=email], input[type=month], input[type=number], input[type=password], 
		input[type=search], input[type=tel], input[type=text], 
		input[type=time], input[type=url], input[type=week], select, textarea {
			border-radius: 6px;
			border: 1px solid var(--mk-acf-grey-03);
			box-shadow: 0 0 13px -5px #00000080;
		}
		
	</style>
	<?php
	}
	
	add_action('acf/input/admin_head', 'mk_acf_custom_styles');