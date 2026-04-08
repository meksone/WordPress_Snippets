<?php
defined( 'ABSPATH' ) || exit;

/**
 * Applies the saved config to the live WP admin menu globals.
 *
 * Execution order (priority 999, after all plugins register their menus):
 *   1. Register any custom top-level groups
 *   2. Move items from top-level into submenu of a target
 *      → sub-items of the moved item are placed after it, visually indented
 *        via <span class="mksc-nested-item"> + CSS (WP admin = 2 levels max)
 *   3. Apply renames to $menu
 *   4. Hide remaining items (slugs marked hidden and NOT moved)
 */
class MK_Sidebar_Cleaner_Rules_Engine {

	private MK_Sidebar_Cleaner_Config $config;

	public function __construct( MK_Sidebar_Cleaner_Config $config ) {
		$this->config = $config;
	}

	public function hook(): void {
		add_action( 'admin_menu', [ $this, 'apply' ], 999 );
		add_action( 'admin_head', [ $this, 'admin_head_output' ] );
	}

	public function apply(): void {
		// Skip on our own settings page so the full menu is visible for editing.
		if ( ( $_GET['page'] ?? '' ) === MK_Sidebar_Cleaner_Config::PAGE_SLUG ) return;

		$cfg = $this->config->get_active();
		if ( empty( $cfg ) ) return;

		$moved         = $cfg['moved']         ?? [];
		$custom_groups = $cfg['custom_groups'] ?? [];

		$this->register_custom_groups( $custom_groups );

		$custom_slugs = array_column( $custom_groups, 'slug' );
		$this->apply_moves( $moved, $custom_slugs );
		$this->apply_renames( $cfg['renamed'] ?? [] );

		// Hidden slugs that are also moved: move takes precedence.
		$hidden = array_values(
			array_filter(
				$cfg['hidden'] ?? [],
				fn( $slug ) => ! isset( $moved[ $slug ] )
			)
		);
		$this->apply_hides( $hidden );
		$this->apply_order( $cfg['order'] ?? [], $custom_slugs );
	}

	// -------------------------------------------------------------------------

	private function register_custom_groups( array $groups ): void {
		foreach ( $groups as $g ) {
			add_menu_page(
				$g['name'],
				$g['name'],
				'manage_options',
				$g['slug'],
				'__return_null',
				$g['icon'] ?? 'dashicons-category',
				$g['position'] ?? 30
			);
		}
	}

	private function apply_moves( array $moved, array $custom_slugs = [] ): void {
		global $menu, $submenu;
		if ( empty( $moved ) ) return;

		foreach ( $moved as $source_slug => $target_slug ) {
			// Locate the item in the top-level menu.
			$item_data = null;
			$item_pos  = null;
			foreach ( $menu as $pos => $item ) {
				if ( ( $item[2] ?? '' ) === $source_slug ) {
					$item_data = $item;
					$item_pos  = $pos;
					break;
				}
			}
			if ( $item_data === null ) continue; // plugin may not be active

			unset( $menu[ $item_pos ] );

			if ( ! isset( $submenu[ $target_slug ] ) ) {
				$submenu[ $target_slug ] = [];
			}

			// Determine next available position in the target submenu.
			$next = empty( $submenu[ $target_slug ] )
				? 100
				: ( max( array_keys( $submenu[ $target_slug ] ) ) + 10 );

			$is_custom_target  = in_array( $target_slug, $custom_slugs, true );
			$has_own_submenus  = ! empty( $submenu[ $source_slug ] );

			// Add the top-level item itself as a submenu entry (parent row).
			// When inside a custom group AND it has children, mark it so JS can
			// wire up the click-to-expand behaviour.
			if ( $is_custom_target && $has_own_submenus ) {
				$parent_label = '<span class="mksc-moved-parent" data-mksc-group="'
					. esc_attr( $source_slug ) . '">'
					. wp_strip_all_tags( $item_data[0] )
					. '</span>';
			} else {
				$parent_label = wp_strip_all_tags( $item_data[0] );
			}

			$submenu[ $target_slug ][ $next ] = [
				$parent_label,
				$item_data[1] ?? 'read',
				$source_slug,
			];
			$next += 5;

			// Add the moved item's own sub-items immediately after, wrapped in a
			// span so CSS can indent them and JS can toggle them.
			// We do NOT unset $submenu[$source_slug] so WP can still resolve
			// current-menu-item highlighting for those child pages.
			foreach ( (array) ( $submenu[ $source_slug ] ?? [] ) as $sub ) {
				$sub[0] = '<span class="mksc-nested-item" data-mksc-child="'
					. esc_attr( $source_slug ) . '">'
					. wp_strip_all_tags( $sub[0] )
					. '</span>';
				$submenu[ $target_slug ][ $next ] = $sub;
				$next += 5;
			}
		}
	}

	private function apply_order( array $order, array $custom_slugs ): void {
		global $menu, $submenu;

		// --- Top-level menu (Main Sidebar zone, stored under '__main__') ---
		$main_order = $order['__main__'] ?? [];
		if ( ! empty( $main_order ) && is_array( $menu ) ) {
			// Build a lookup: slug → current menu entry.
			$by_slug = [];
			foreach ( $menu as $pos => $item ) {
				$slug = $item[2] ?? '';
				if ( $slug !== '' ) {
					$by_slug[ $slug ] = $item;
				}
			}

			// Collect the existing position keys so we can redistribute them
			// in the same numeric range WP expects (keeps separators working).
			$existing_positions = array_keys( $menu );
			sort( $existing_positions );
			$pos_pool = $existing_positions;

			$new_menu = [];
			$pool_idx = 0;

			// First: items explicitly ordered by the user.
			foreach ( $main_order as $slug ) {
				if ( isset( $by_slug[ $slug ] ) && $pool_idx < count( $pos_pool ) ) {
					$new_menu[ $pos_pool[ $pool_idx++ ] ] = $by_slug[ $slug ];
					unset( $by_slug[ $slug ] );
				}
			}
			// Then: any remaining items not in the order list (newly added plugins etc.).
			foreach ( $by_slug as $item ) {
				if ( $pool_idx < count( $pos_pool ) ) {
					$new_menu[ $pos_pool[ $pool_idx++ ] ] = $item;
				}
			}

			$menu = $new_menu;
		}

		// --- Custom group submenus ---
		foreach ( $custom_slugs as $group_slug ) {
			$zone_order = $order[ $group_slug ] ?? [];
			if ( empty( $zone_order ) || empty( $submenu[ $group_slug ] ) ) continue;

			// Build lookup: source_slug → list of submenu entries (parent + children).
			// Entries whose slug matches a zone_order item are "parents";
			// entries with data-mksc-child span are their children.
			// Simplest approach: rebuild the submenu in zone_order sequence,
			// grouping each parent with the children that immediately follow it.
			$entries   = $submenu[ $group_slug ];
			$groups    = []; // source_slug → [ parent_entry, child_entry, ... ]
			$last_slug = null;
			foreach ( $entries as $entry ) {
				// Detect parent: its slug (index 2) matches a zone_order item.
				$entry_slug = $entry[2] ?? '';
				if ( in_array( $entry_slug, $zone_order, true ) ) {
					$last_slug            = $entry_slug;
					$groups[ $last_slug ] = [ $entry ];
				} elseif ( $last_slug !== null ) {
					$groups[ $last_slug ][] = $entry;
				}
			}

			// Rebuild submenu in desired order.
			$new_sub = [];
			$next    = 100;
			foreach ( $zone_order as $slug ) {
				if ( ! isset( $groups[ $slug ] ) ) continue;
				foreach ( $groups[ $slug ] as $entry ) {
					$new_sub[ $next ] = $entry;
					$next += 5;
				}
			}
			$submenu[ $group_slug ] = $new_sub;
		}
	}

	private function apply_hides( array $hidden ): void {
		foreach ( $hidden as $slug ) {
			remove_menu_page( $slug );
		}
	}

	/**
	 * Output CSS + JS for nested/collapsible sidebar items.
	 * Runs on every admin page so the real sidebar is always correctly styled.
	 */
	public function admin_head_output(): void {
		$cfg = $this->config->get_active();
		if ( empty( $cfg['moved'] ) ) return;
		?>
<style id="mksc-nested-css">
/* Indent moved-item children */
#adminmenu .mksc-nested-item {
	display: block;
	padding-left: 14px;
	position: relative;
	opacity: .85;
}
#adminmenu .mksc-nested-item::before {
	content: "\2514"; /* └ */
	position: absolute;
	left: 3px;
	top: 0;
	font-size: 9px;
	line-height: inherit;
	opacity: .45;
}
#adminmenu li.current a .mksc-nested-item { opacity: 1; }

/* Collapsible parent row inside custom groups */
#adminmenu .mksc-moved-parent {
	display: flex;
	align-items: center;
	gap: 5px;
	cursor: pointer;
}
#adminmenu .mksc-moved-parent::before {
	content: "\25B6"; /* ▶ */
	font-size: 8px;
	opacity: .5;
	transition: transform .15s;
	flex-shrink: 0;
}
#adminmenu .mksc-moved-parent.mksc-open::before {
	transform: rotate(90deg);
}
/* Child rows hidden by default (JS overrides on load) */
#adminmenu li.mksc-child-hidden { display: none; }
</style>
<script id="mksc-sidebar-js">
jQuery( function( $ ) {
	var STORE_KEY = 'mksc_open_groups';

	function getOpen() {
		try { return JSON.parse( localStorage.getItem( STORE_KEY ) || '[]' ); }
		catch(e) { return []; }
	}
	function saveOpen( arr ) {
		try { localStorage.setItem( STORE_KEY, JSON.stringify( arr ) ); }
		catch(e) {}
	}

	// Mark child <li> elements that belong to a given group slug.
	function childItems( slug ) {
		return $( '#adminmenu [data-mksc-child="' + slug + '"]' ).closest( 'li' );
	}

	function openGroup( slug ) {
		childItems( slug ).removeClass( 'mksc-child-hidden' );
		$( '#adminmenu [data-mksc-group="' + slug + '"]' ).addClass( 'mksc-open' );
	}

	function closeGroup( slug ) {
		childItems( slug ).addClass( 'mksc-child-hidden' );
		$( '#adminmenu [data-mksc-group="' + slug + '"]' ).removeClass( 'mksc-open' );
	}

	// Hide all child rows on load, then restore saved open state.
	$( '#adminmenu [data-mksc-child]' ).closest( 'li' ).addClass( 'mksc-child-hidden' );

	var open = getOpen();
	open.forEach( function( slug ) { openGroup( slug ); } );

	// Click handler on the parent span (not its <a>, so we stop propagation).
	$( '#adminmenu' ).on( 'click', '[data-mksc-group]', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var slug    = $( this ).data( 'mksc-group' );
		var isOpen  = $( this ).hasClass( 'mksc-open' );
		var saved   = getOpen().filter( function(s) { return s !== slug; } );

		if ( isOpen ) {
			closeGroup( slug );
		} else {
			openGroup( slug );
			saved.push( slug );
		}
		saveOpen( saved );
	} );
} );
</script>
		<?php
	}

	private function apply_renames( array $renamed ): void {
		global $menu;
		if ( empty( $renamed ) || ! is_array( $menu ) ) return;

		foreach ( $menu as $pos => &$item ) {
			$slug = $item[2] ?? '';
			if ( isset( $renamed[ $slug ] ) ) {
				$item[0] = esc_html( $renamed[ $slug ] );
			}
		}
		unset( $item );
	}
}
