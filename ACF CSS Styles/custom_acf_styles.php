<?php

$snippet_name = "custom_acf_styles";
$version = "<!#FV> 0.0.2 </#FV>";

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
		
	
		
		.acf-field p.description {
		background-color: #EAEAEA;
		padding: 5px;
		border-radius: 6px;
		}
		
		.acf-field.acf-field-group {
		padding: 0px;
		margin: 0px;
	}
		
	/* Label Styling !!! */
		.acf-field-group > .acf-label {
		margin: 0 10px 10px;
		}
		
	/* Hide label inside ACF group */
		.mk-group-label-hide > .acf-label {
		display: none;
	}
	
	/* Special Group Label */
		.mk-group-highlight > .acf-label label {
		padding: 5px 5px 5px 5px;
		background-color: #f5d771!important;
		color: orangered!important;
		border-radius: 6px;
	}
		.mk-group-highlight {
			background-color: #fff7d3;
			margin: 5px !important;
		}
	
	/* Grey Background */	
		.mk-acf-backg-grey {
		background-color: #FAFAFA;
		border-radius: 8px;
		}

		.acf-field .acf-label label {
		display: inline;
		margin: 0 0 3px;
		padding: 5px 5px 5px 5px;
		background-color: #EAEAEA;
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
			border: 1px solid #bdbdbd;
			box-shadow: 0 0 13px -5px #00000080;
		}
		
		
		/* In evidenza background */
		.mk-in-evidenza {
		background-color: #FFF7D3;		
		}
			
		#mk-in-evidenza.acf-field .acf-label label {
		display: inline;
		margin: 0 0 3px;
		padding: 5px 5px 5px 5px;
		background-color: #f5d771;
		color: orangered;
		border-radius: 6px;
		}
		
		#mk-in-evidenza.acf-field p.description {
		background-color: #f5d771;
		color: orangered;
		padding: 5px;
		border-radius: 6px;
		}
		
		/* Hide ACF element */
		.mk-acf-hide {
			display: none;
		}
	
	</style>
	<?php
	}
	
	add_action('acf/input/admin_head', 'mk_acf_custom_styles');