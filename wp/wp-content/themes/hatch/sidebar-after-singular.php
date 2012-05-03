<?php
/**
 * After Singular Sidebar Template
 *
 * Displays any widgets for the After Singular dynamic sidebar if they are available.
 *
 * @package Hatch
 * @subpackage Template
 */

if ( is_active_sidebar( 'after-singular' ) ) : ?>

	<div id="sidebar-after-singular" class="sidebar">

		<?php dynamic_sidebar( 'after-singular' ); ?>

	</div><!-- #sidebar-after-singular -->

<?php endif; ?>