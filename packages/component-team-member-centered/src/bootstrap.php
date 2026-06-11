<?php
/**
 * balefireict/component-team-member-centered — bootstrap.
 *
 * Defines thin global function wrappers (keeping the original rockerbox
 * global function names so existing themes keep working), registers the
 * parent + child shortcodes, wires vc_map on vc_before_init, and registers
 * the WPBakeryShortCodesContainer subclass on vc_after_init.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package Balefire\Component\TeamMemberCentered
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bma_team_grid_shortcode' ) ) {
	/**
	 * Render the parent [bma_team_grid] shortcode.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Children shortcodes.
	 * @return string HTML output.
	 */
	function bma_team_grid_shortcode( $atts, $content = null ): string {
		return \Balefire\Component\TeamMemberCentered\TeamMemberCentered::render( (array) $atts, $content );
	}
}

if ( ! function_exists( 'bma_team_member_shortcode' ) ) {
	/**
	 * Render one [bma_team_member] child.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	function bma_team_member_shortcode( $atts ): string {
		return \Balefire\Component\TeamMemberCentered\TeamMemberCentered::renderMember( (array) $atts );
	}
}

$bma_team_member_centered_boot = function (): void {
	\Balefire\Component\TeamMemberCentered\TeamMemberCentered::register();
	if ( function_exists( 'vc_map' ) ) {
		add_action( 'vc_before_init', array( \Balefire\Component\TeamMemberCentered\TeamMemberCentered::class, 'vcMap' ) );
		add_action( 'vc_after_init', array( \Balefire\Component\TeamMemberCentered\TeamMemberCentered::class, 'registerContainerClass' ) );
	}
};

// WP load order: plugins_loaded fires BEFORE theme functions.php. When this
// autoloader is required from a theme, the hook has already fired - boot now.
// vc_before_init hooks 'init', which is always later, so vcMap still lands.
if ( did_action( 'plugins_loaded' ) ) {
	$bma_team_member_centered_boot();
} else {
	add_action( 'plugins_loaded', $bma_team_member_centered_boot, 20 );
}
unset( $bma_team_member_centered_boot );
