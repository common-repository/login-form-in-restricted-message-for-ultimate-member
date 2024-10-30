<?php
/**
 * Plugin Name:       Login Form in Restricted Message for Ultimate Member
 * Description:       By default, Ultimate Member doesn't support login form shortcode inside restricted access messages. This plugin enables this feature so that you can use your login form shortcode inside restricted access messages.
 * Version:           1.0
 * Author:            realestatetips
 * Author URI:        https://www.realestatetipsblog.com/
 */

if(UM()) //Check if Ultimate Member exist
{
	remove_filter( 'the_content', array( UM()->access(), 'filter_restricted_post_content' ), 999999, 1 );
	add_filter( 'the_content', function( $content ){

			if ( current_user_can( 'administrator' ) ) {
				return $content;
			}

			$id = get_the_ID();
			if ( ! $id || is_admin() ) {
				return $content;
			}

			$ignore = apply_filters( 'um_ignore_restricted_content', false, $id );
			if ( $ignore ) {
				return $content;
			}

			if ( UM()->access()->is_restricted( $id ) ) {
				$restriction = UM()->access()->get_post_privacy_settings( $id );

				if ( ! isset( $restriction['_um_restrict_by_custom_message'] ) || '0' == $restriction['_um_restrict_by_custom_message'] ) {
					$content = stripslashes( UM()->options()->get( 'restricted_access_message' ) );
				} elseif ( '1' == $restriction['_um_restrict_by_custom_message'] ) {
					$content = ! empty( $restriction['_um_restrict_custom_message'] ) ? stripslashes( $restriction['_um_restrict_custom_message'] ) : '';
				}

		  $content = do_shortcode($content);
			}

		return $content;

	}, 999999, 1 );
}