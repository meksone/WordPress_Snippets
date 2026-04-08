/**
 * MK Sidebar Cleaner — drag-and-drop admin UI
 *
 * Uses jQuery UI Sortable (already bundled in WP admin).
 * Each .mksc-zone is a connected sortable list. Dropping an item into a zone
 * records which parent slug it should be moved under.
 *
 * On form submit, the current DOM state is serialized to JSON and placed in
 * the hidden #mksc-state-{scope} input, which the PHP handler decodes.
 */
jQuery( function ( $ ) {
	'use strict';

	var i18n = window.mkscI18n || {};

	// -----------------------------------------------------------------------
	// Sortable initialisation
	// -----------------------------------------------------------------------

	/**
	 * (Re-)initialise all .mksc-zone lists inside a given container so they
	 * are connected to each other but isolated from zones in other containers
	 * (i.e. the other tab's form).
	 */
	function initZones( $container ) {
		var $zones = $container.find( '.mksc-zone' );

		$zones.sortable( {
			connectWith:          $zones,
			handle:               '.mksc-drag-handle',
			placeholder:          'mksc-placeholder',
			tolerance:            'pointer',
			forcePlaceholderSize: true,
			start: function ( e, ui ) {
				ui.placeholder.height( ui.item.outerHeight() );
			},
			over: function () {
				$( this ).closest( '.mksc-zone-col' ).addClass( 'mksc-drop-active' );
			},
			out: function () {
				$( this ).closest( '.mksc-zone-col' ).removeClass( 'mksc-drop-active' );
			},
			stop: function () {
				$( this ).closest( '.mksc-zone-col' ).removeClass( 'mksc-drop-active' );
			}
		} ).disableSelection();
	}

	// Run on page load.
	$( '.mksc-zones' ).each( function () {
		initZones( $( this ) );
	} );

	// -----------------------------------------------------------------------
	// Collapsible subitems — toggle + localStorage state
	// -----------------------------------------------------------------------

	var EXPAND_KEY = 'mksc_expanded';

	function getExpanded() {
		try { return JSON.parse( localStorage.getItem( EXPAND_KEY ) || '[]' ); }
		catch ( e ) { return []; }
	}

	function saveExpanded( slugs ) {
		try { localStorage.setItem( EXPAND_KEY, JSON.stringify( slugs ) ); }
		catch ( e ) {}
	}

	function openItem( $btn ) {
		$btn.addClass( 'mksc-toggle--open' ).html( '&#x25BC;' );
		$btn.closest( '.mksc-item' ).find( '.mksc-subitems' ).removeAttr( 'hidden' );
	}

	function closeItem( $btn ) {
		$btn.removeClass( 'mksc-toggle--open' ).html( '&#x25B6;' );
		$btn.closest( '.mksc-item' ).find( '.mksc-subitems' ).attr( 'hidden', '' );
	}

	// Restore expanded state on load.
	getExpanded().forEach( function ( slug ) {
		openItem( $( '.mksc-toggle[data-slug="' + slug + '"]' ) );
	} );

	$( document ).on( 'click', '.mksc-toggle', function () {
		var $btn     = $( this );
		var slug     = $btn.data( 'slug' );
		var isOpen   = $btn.hasClass( 'mksc-toggle--open' );
		var expanded = getExpanded().filter( function ( s ) { return s !== slug; } );

		if ( isOpen ) {
			closeItem( $btn );
		} else {
			openItem( $btn );
			expanded.push( slug );
		}
		saveExpanded( expanded );
	} );

	// -----------------------------------------------------------------------
	// Position input — sync data attribute on server-rendered zones
	// -----------------------------------------------------------------------

	$( document ).on( 'change input', '.mksc-position-input', function () {
		var $col = $( this ).closest( '.mksc-zone-col' );
		var val  = parseInt( $( this ).val(), 10 ) || 30;
		$col.data( 'custom-position', val );
		$col.attr( 'data-custom-position', val );
	} );

	// -----------------------------------------------------------------------
	// Toggle hidden state
	// -----------------------------------------------------------------------

	$( document ).on( 'change', '.mksc-hide-cb', function () {
		$( this ).closest( '.mksc-item' ).toggleClass( 'mksc-item--hidden', this.checked );
	} );

	// -----------------------------------------------------------------------
	// Inline rename — double-click on item name to edit
	// -----------------------------------------------------------------------

	$( document ).on( 'dblclick', '.mksc-item-name', function () {
		var $name    = $( this );
		var $item    = $name.closest( '.mksc-item' );
		var current  = $item.data( 'custom-name' ) || $name.text();

		// Don't open two editors at once.
		if ( $name.find( 'input' ).length ) return;

		var $input = $( '<input>' )
			.attr( { type: 'text', class: 'mksc-inline-edit' } )
			.val( current );

		$name.empty().append( $input );
		$input.trigger( 'focus' ).trigger( 'select' );

		function commit() {
			var newName = $input.val().trim() || current;
			$item.data( 'custom-name', newName );
			$item.attr( 'data-custom-name', newName );
			$name.text( newName );
		}

		$input.on( 'blur', commit );
		$input.on( 'keydown', function ( e ) {
			if ( e.key === 'Enter' )  { e.preventDefault(); commit(); }
			if ( e.key === 'Escape' ) { $name.text( current ); }
		} );
	} );

	// -----------------------------------------------------------------------
	// Inline rename — double-click on zone header to rename group
	// -----------------------------------------------------------------------

	$( document ).on( 'dblclick', '.mksc-zone-col--custom .mksc-zone-header', function ( e ) {
		// Ignore clicks on the delete button.
		if ( $( e.target ).hasClass( 'mksc-btn-delete-zone' ) ) return;

		var $header  = $( this );
		var $col     = $header.closest( '.mksc-zone-col' );
		var current  = $col.data( 'custom-name' );

		// Don't open two editors at once.
		if ( $header.find( '.mksc-inline-edit' ).length ) return;

		// Save the delete button and icon before clearing.
		var $deleteBtn = $header.find( '.mksc-btn-delete-zone' ).detach();
		var $icon      = $header.find( '.dashicons' ).detach();

		var $input = $( '<input>' )
			.attr( { type: 'text', class: 'mksc-inline-edit mksc-inline-edit--header' } )
			.val( current );

		$header.empty().append( $icon ).append( $input ).append( $deleteBtn );
		$input.trigger( 'focus' ).trigger( 'select' );

		function commit() {
			var newName = $input.val().trim() || current;
			$col.data( 'custom-name', newName );
			$col.attr( 'data-custom-name', newName );
			$header.empty()
				.append( $icon )
				.append( document.createTextNode( ' ' + newName + ' ' ) )
				.append( $deleteBtn );
		}

		$input.on( 'blur', commit );
		$input.on( 'keydown', function ( e ) {
			if ( e.key === 'Enter' )  { e.preventDefault(); commit(); }
			if ( e.key === 'Escape' ) { commit(); }
		} );
	} );

	// -----------------------------------------------------------------------
	// Add custom group
	// -----------------------------------------------------------------------

	$( document ).on( 'click', '.mksc-btn-add-zone', function () {
		var name = prompt( i18n.addGroupPrompt || 'Enter a name for the new group:' );
		if ( ! name || ! name.trim() ) return;
		name = name.trim();

		var slug       = 'mk-group-' + Date.now();
		var $container = $( this ).closest( '.mksc-zones' );
		var position   = 30;

		appendCustomZone( $container, slug, name, 'dashicons-category', position, [] );

		// Also add the custom group as a positionable item in the Main Sidebar zone.
		var $mainZone = $container.find( '.mksc-zone[data-target=""]' );
		var $item = buildItemEl( {
			slug:   slug,
			name:   name,
			hidden: false,
			is_custom_group: true
		} );
		$mainZone.append( $item );
	} );

	function appendCustomZone( $container, slug, name, icon, position, items ) {
		var $addWrap = $container.find( '.mksc-add-zone-wrap' );

		var $col = $( '<div>' )
			.addClass( 'mksc-zone-col mksc-zone-col--custom' )
			.attr( {
				'data-zone-target':    slug,
				'data-custom-slug':    slug,
				'data-custom-name':    name,
				'data-custom-icon':    icon,
				'data-custom-position': position
			} );

		var $header = $( '<div>' ).addClass( 'mksc-zone-header' );
		$header.append( $( '<span>' ).addClass( 'dashicons ' + escAttr( icon ) ) );
		$header.append( document.createTextNode( ' ' + name + ' ' ) );
		$header.append(
			$( '<button>' )
				.attr( { type: 'button', title: 'Delete group' } )
				.addClass( 'mksc-btn-delete-zone' )
				.html( '&#x2715;' )
		);

		var $posWrap = $( '<div>' ).addClass( 'mksc-zone-position' );
		$posWrap.append( $( '<label>' ).text( 'Pos: ' ) );
		$posWrap.append(
			$( '<input>' )
				.attr( { type: 'number', min: 0, max: 200, step: 1 } )
				.addClass( 'mksc-position-input' )
				.val( position )
				.on( 'change input', function () {
					$col.data( 'custom-position', parseInt( $( this ).val(), 10 ) || 30 );
					$col.attr( 'data-custom-position', parseInt( $( this ).val(), 10 ) || 30 );
				} )
		);

		var $list = $( '<ul>' )
			.addClass( 'mksc-zone' )
			.attr( 'data-target', slug );

		items.forEach( function ( item ) {
			$list.append( buildItemEl( item ) );
		} );

		$col.append( $header ).append( $posWrap ).append( $list );
		$addWrap.before( $col );

		// Destroy and re-init sortable so the new zone is connected.
		$container.find( '.mksc-zone' ).sortable( 'destroy' );
		initZones( $container );
	}

	// -----------------------------------------------------------------------
	// Delete custom group
	// -----------------------------------------------------------------------

	$( document ).on( 'click', '.mksc-btn-delete-zone', function () {
		if ( ! confirm( i18n.deleteGroupConfirm || 'Delete this group? Items inside will return to the Main Sidebar.' ) ) return;

		var $col       = $( this ).closest( '.mksc-zone-col' );
		var $container = $col.closest( '.mksc-zones' );
		var $mainZone  = $container.find( '.mksc-zone[data-target=""]' );
		var slug       = $col.data( 'custom-slug' );

		// Return items to Main Sidebar before removing the column.
		$col.find( '.mksc-item' ).each( function () {
			$mainZone.append( $( this ) );
		} );

		// Also remove the custom group's own entry from the Main Sidebar.
		$mainZone.find( '.mksc-item[data-slug="' + slug + '"]' ).remove();

		$col.find( '.mksc-zone' ).sortable( 'destroy' );
		$col.remove();

		$container.find( '.mksc-zone' ).sortable( 'destroy' );
		initZones( $container );
	} );

	// -----------------------------------------------------------------------
	// Serialize state on form submit
	// -----------------------------------------------------------------------

	$( document ).on( 'submit', '.mksc-form', function () {
		var $form      = $( this );
		var scope      = $form.find( 'input[name="mk_scope"]' ).val();
		var $container = $form.find( '.mksc-zones' );

		var state = {
			hidden:        [],
			moved:         {},
			custom_groups: [],
			renamed:       {},
			order:         {}   // zone_target → [slug, ...] in drag order
		};

		// Collect custom group metadata.
		$container.find( '.mksc-zone-col--custom' ).each( function () {
			var $col = $( this );
			state.custom_groups.push( {
				slug:     $col.data( 'custom-slug' ),
				name:     $col.data( 'custom-name' ),
				icon:     $col.data( 'custom-icon' )     || 'dashicons-category',
				position: parseInt( $col.data( 'custom-position' ), 10 ) || 30
			} );
		} );

		// Collect item placement, order, hidden flags, and custom names.
		$container.find( '.mksc-zone' ).each( function () {
			var target = $( this ).data( 'target' ) || '';
			var zoneKey = target === '' ? '__main__' : target;
			state.order[ zoneKey ] = [];

			$( this ).find( '.mksc-item' ).each( function () {
				var $item  = $( this );
				var slug   = $item.data( 'slug' );
				var hidden = $item.find( '.mksc-hide-cb' ).is( ':checked' );

				// Record slug order for this zone.
				state.order[ zoneKey ].push( slug );

				if ( hidden ) {
					state.hidden.push( slug );
				}
				if ( target !== '' ) {
					state.moved[ slug ] = target;
				}

				// Check if the item has been renamed (custom-name differs from data-name).
				var origName   = $item.data( 'name' );
				var customName = $item.data( 'custom-name' );
				if ( customName && customName !== origName ) {
					state.renamed[ slug ] = customName;
				}
			} );
		} );

		$form.find( '#mksc-state-' + scope ).val( JSON.stringify( state ) );
	} );

	// -----------------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------------

	function buildItemEl( item ) {
		var hiddenClass = item.hidden ? ' mksc-item--hidden' : '';
		var customClass = item.is_custom_group ? ' mksc-item--custom-group' : '';
		var displayName = item.custom_name || item.name;
		var $li = $( '<li>' )
			.addClass( 'mksc-item' + hiddenClass + customClass )
			.attr( {
				'data-slug': item.slug,
				'data-name': item.name || '',
				'data-custom-name': displayName
			} );

		$li.append( $( '<span>' ).addClass( 'mksc-drag-handle' ).attr( 'title', 'Drag to move' ).html( '&#x2630;' ) );
		$li.append(
			$( '<span>' ).addClass( 'mksc-item-name' )
				.attr( 'title', 'Double-click to rename' )
				.text( displayName )
		);

		var $label = $( '<label>' ).addClass( 'mksc-hide-toggle' ).attr( 'title', 'Hide this item' );
		$label.append( $( '<input>' ).attr( { type: 'checkbox', class: 'mksc-hide-cb' } ).prop( 'checked', !! item.hidden ) );
		$label.append( $( '<span>' ).addClass( 'mksc-hide-icon' ).html( '&#x1F6AB;' ) );
		$li.append( $label );

		return $li;
	}

	function escAttr( str ) {
		return String( str )
			.replace( /&/g,  '&amp;'  )
			.replace( /"/g,  '&quot;' )
			.replace( /</g,  '&lt;'   )
			.replace( />/g,  '&gt;'   );
	}
} );
