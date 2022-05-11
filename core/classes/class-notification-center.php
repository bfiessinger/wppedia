<?php

/**
 * Notification Center
 *
 * @since 1.3.0
 */

namespace WPPedia;

use WPPedia_Vendor\MyThemeShop\Notification_Center as NC;
use WPPedia_Vendor\MyThemeShop\Notification;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class Notification_Center extends NC {

	/**
	 * Never expire notification key
	 *
	 * @var string
	 */
	const DISMISS_NEVER_EXPIRE = 'never_expire';

	protected $dismissed_notifications_option = 'wppedia_dismissed_notifications';

	/**
	 * Dismiss a notification until the date from `options[dismiss_until]` is reached
	 *
	 * @param string $notification_id
	 * @param Notification $notification
	 *
	 * @return void
	 */
	public function dismiss_notification( $notification_id, Notification $notification ) {
		$dismiss_until = $notification->args( 'dismiss_until' );
		if ( $dismiss_until && '' !== $dismiss_until ) {
			$dismiss_until = strtotime( $dismiss_until );
			if ( $dismiss_until > time() ) {
				$dismissed_notifications = get_option( $this->dismissed_notifications_option, [] );

				$dismissed_notifications[$notification_id] = $dismiss_until;
				update_option( $this->dismissed_notifications_option, $dismissed_notifications );
			}
		}
	}

	/**
	 * Dismiss a notification by ID
	 *
	 * @param string $notification_id
	 *
	 * @return void
	 */
	public function dismiss_notification_by_id( $notification_id ) {
		$notification = $this->get_notification_by_id( $notification_id );
		if ( ! $notification ) {
			return;
		}

		$this->dismiss_notification( $notification_id, $notification);
	}

	public function undismiss_notification( $notification_id ) {
		$dismissed_notifications = get_option( $this->dismissed_notifications_option, [] );

		if ( isset( $dismissed_notifications[$notification_id] ) ) {
			unset( $dismissed_notifications[$notification_id] );
			update_option( $this->dismissed_notifications_option, $dismissed_notifications );
		}
	}

	/**
	 * Check if a notification is dismissed
	 *
	 * @param string $notification_id
	 *
	 * @return boolean
	 */
	public function is_dismissed( $notification_id ) {
		$dismissed_notifications = get_option($this->dismissed_notifications_option, []);

		/**
		 * First remove any expired notifications
		 */
		$had_expired_notifications = false;
		foreach ( $dismissed_notifications as $dismissed_notification_id => $dismissed_until ) {
			if ( $dismissed_until !== self::DISMISS_NEVER_EXPIRE && $dismissed_until < time() ) {
				unset( $dismissed_notifications[$dismissed_notification_id] );
				$had_expired_notifications = true;
			}
		}

		if ( $had_expired_notifications ) {
			update_option( $this->dismissed_notifications_option, $dismissed_notifications );
		}

		/**
		 * Now check if the notification is dismissed
		 */
		foreach ($dismissed_notifications as $dismissed_id => $dismissed_until) {
			if ( $dismissed_id == $notification_id && $dismissed_until > time() ) {
				return true;
			}
		}

		return false;
	}
}
