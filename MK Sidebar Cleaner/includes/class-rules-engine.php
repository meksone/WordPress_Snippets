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

			// Add the moved item's own sub-items immediately after, visually indented.
			// Custom group targets: wrap with mksc-nested-item so JS can collapse/expand.
			// Built-in targets (Settings, Tools): wrap with mksc-flat-child — always
			// visible, just indented. The collapsible JS only acts on [data-mksc-child]
			// so flat children are never hidden.
			// We do NOT unset $submenu[$source_slug] so WP can still resolve
			// current-menu-item highlighting for those child pages.
			foreach ( (array) ( $submenu[ $source_slug ] ?? [] ) as $sub ) {
				// Fix broken links: WP renders href='{slug}' (broken) when it can't find
				// get_plugin_page_hook($slug, $new_parent) — because these pages were
				// registered under a different parent. Replace bare plugin-page slugs with
				// a canonical URL so WP uses it as-is without hook lookup.
				$child_slug = $sub[2] ?? '';
				if (
					$child_slug !== ''
					&& false === strpos( $child_slug, '.php' )
					&& false === strpos( $child_slug, 'http' )
				) {
					$canonical = function_exists( 'menu_page_url' )
						? menu_page_url( $child_slug, false )
						: '';
					// Canonical URL (absolute, contains 'http') → WP uses as-is.
					// Fallback: relative 'admin.php?page=...' (contains '.php') → WP uses correctly.
					$sub[2] = $canonical ?: ( 'admin.php?page=' . $child_slug );
				}

				if ( $is_custom_target ) {
					$sub[0] = '<span class="mksc-nested-item" data-mksc-child="'
						. esc_attr( $source_slug ) . '">'
						. wp_strip_all_tags( $sub[0] )
						. '</span>';
				} else {
					$sub[0] = '<span class="mksc-flat-child">'
						. wp_strip_all_tags( $sub[0] )
						. '</span>';
				}
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
			// Separate WP separator entries (empty slug or 'separator*') from real items.
			// Separators must stay at their original position keys; only real items
			// get redistributed to avoid corrupting the menu structure.
			$separator_entries = [];
			$by_slug           = [];
			$real_positions    = [];

			foreach ( $menu as $pos => $item ) {
				$slug = $item[2] ?? '';
				if ( $slug === '' || str_starts_with( $slug, 'separator' ) ) {
					$separator_entries[ $pos ] = $item;
				} else {
					$by_slug[ $slug ] = $item;
					$real_positions[] = $pos;
				}
			}
			sort( $real_positions );
			$pos_pool = $real_positions;

			// Start with separators locked to their original positions.
			$new_menu = $separator_entries;
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

		// --- Built-in target zones (Settings, Tools) ---
		// Reorder entries added by apply_moves; existing WP-native entries keep
		// their original positions (they are not in zone_order).
		$builtin_targets = [ 'options-general.php', 'tools.php' ];
		foreach ( $builtin_targets as $bt_slug ) {
			$zone_order = $order[ $bt_slug ] ?? [];
			if ( empty( $zone_order ) || empty( $submenu[ $bt_slug ] ) ) continue;

			// Split entries: those we placed (flat-child or parent from apply_moves)
			// vs native WP entries. Native entries keep their original positions;
			// only the moved entries are resequenced.
			$native     = [];
			$by_moved   = []; // source_slug → [ parent_entry, flat_child_entries... ]
			$last_moved = null;

			foreach ( $submenu[ $bt_slug ] as $pos => $entry ) {
				$label = $entry[0] ?? '';
				if ( strpos( $label, 'mksc-flat-child' ) !== false ) {
					// Child of a moved item — attach to last seen moved parent.
					if ( ! empty( $last_moved ) ) {
						$by_moved[ $last_moved ][] = $entry;
					}
				} elseif ( in_array( $entry[2] ?? '', $zone_order, true ) ) {
					// Parent entry placed by apply_moves.
					$last_moved                    = $entry[2];
					$by_moved[ $last_moved ]       = isset( $by_moved[ $last_moved ] )
						? array_merge( [ $entry ], $by_moved[ $last_moved ] )
						: [ $entry ];
				} else {
					$native[ $pos ] = $entry;
					$last_moved     = null;
				}
			}

			// Determine insertion position: append after the last native entry.
			$next = empty( $native ) ? 10 : ( max( array_keys( $native ) ) + 10 );

			$new_sub = $native;
			foreach ( $zone_order as $slug ) {
				if ( ! isset( $by_moved[ $slug ] ) ) continue;
				foreach ( $by_moved[ $slug ] as $entry ) {
					$new_sub[ $next ] = $entry;
					$next += 5;
				}
				unset( $by_moved[ $slug ] );
			}
			// Append any moved entries not in zone_order.
			foreach ( $by_moved as $group_entries ) {
				foreach ( $group_entries as $entry ) {
					$new_sub[ $next ] = $entry;
					$next += 5;
				}
			}
			$submenu[ $bt_slug ] = $new_sub;
		}

		// --- Custom group submenus ---
		foreach ( $custom_slugs as $group_slug ) {
			$zone_order = $order[ $group_slug ] ?? [];
			if ( empty( $zone_order ) || empty( $submenu[ $group_slug ] ) ) continue;

			// Build lookup: source_slug → [parent_entry, child_entries...].
			// Use the data-mksc-child attribute set by apply_moves to reliably
			// distinguish children from parents — avoids false-positive slug matches
			// (e.g. WooCommerce registers a submenu entry whose slug equals 'woocommerce').
			$entries = $submenu[ $group_slug ];
			$groups  = []; // source_slug → [ parent_entry, child_entry, ... ]

			foreach ( $entries as $entry ) {
				$label = $entry[0] ?? '';
				if ( strpos( $label, 'mksc-nested-item' ) !== false ) {
					// Child entry — route to its parent via data-mksc-child.
					if ( preg_match( '/data-mksc-child="([^"]+)"/', $label, $m ) ) {
						$parent_slug              = $m[1];
						$groups[ $parent_slug ][] = $entry;
					}
				} else {
					// Parent entry.
					$entry_slug = $entry[2] ?? '';
					if ( ! isset( $groups[ $entry_slug ] ) ) {
						$groups[ $entry_slug ] = [ $entry ];
					} else {
						// Parent arrived after pre-registered children — put it first.
						array_unshift( $groups[ $entry_slug ], $entry );
					}
				}
			}

			// Rebuild submenu: ordered items first, then any items not in zone_order.
			$new_sub = [];
			$next    = 100;
			foreach ( $zone_order as $slug ) {
				if ( ! isset( $groups[ $slug ] ) ) continue;
				foreach ( $groups[ $slug ] as $entry ) {
					$new_sub[ $next ] = $entry;
					$next += 5;
				}
				unset( $groups[ $slug ] );
			}
			// Append items that exist in the submenu but were not in zone_order
			// (e.g. newly activated plugins added since the last save).
			foreach ( $groups as $group_entries ) {
				foreach ( $group_entries as $entry ) {
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
/* Indent moved-item children inside custom groups (collapsible, hidden by default) */
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

/* Indent moved-item children inside built-in targets (Settings, Tools) — always visible */
#adminmenu .mksc-flat-child {
	display: block;
	padding-left: 14px;
	position: relative;
	opacity: .85;
}
#adminmenu .mksc-flat-child::before {
	content: "\2514";
	position: absolute;
	left: 3px;
	top: 0;
	font-size: 9px;
	line-height: inherit;
	opacity: .45;
}
#adminmenu li.current a .mksc-flat-child { opacity: 1; }

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
