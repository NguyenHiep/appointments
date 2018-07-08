<?php
namespace AppointFox\Admin\Controller;

require_once AFX_PATH . 'models/Appointment.php';
require_once AFX_PATH . 'models/Notification.php';

use AppointFox\Model\Notification as Notification;

/**
 * Notifications controller class
 */
class NotificationsController {

	/**
	 * __construct function
	 */
	public function __construct() {

		add_action( 'afx_run_notifications', array($this,'send_notifications'));

		add_action( 'init', array( $this, 'register_hourly_notifications_events' ) );
	}

	/**
	 * Send notifications
	 *
	 * @return void
	 */
	public function send_notifications() {
		$sent = Notification::notifyReminders();
	}

	public function register_hourly_notifications_events() {
		// Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'afx_run_notifications' ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'hourly', 'afx_run_notifications' );
		}
	}
}