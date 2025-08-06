<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingCancellationMail;
use App\Mail\TicketDeliveryMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingStatusNotification;

class NotificationService
{
    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation(Booking $booking)
    {
        try {
            // Send email to customer
            if ($booking->customer && $booking->customer->email) {
                Mail::to($booking->customer->email)
                    ->send(new BookingConfirmationMail($booking));
            }

            // Send in-app notification if user exists
            if ($booking->user) {
                $booking->user->notify(new BookingStatusNotification($booking, 'confirmed'));
            }

            Log::info('Booking confirmation sent', ['booking_id' => $booking->id]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send booking cancellation notification
     */
    public function sendBookingCancellation(Booking $booking)
    {
        try {
            // Send email to customer
            if ($booking->customer && $booking->customer->email) {
                Mail::to($booking->customer->email)
                    ->send(new BookingCancellationMail($booking));
            }

            // Send in-app notification if user exists
            if ($booking->user) {
                $booking->user->notify(new BookingStatusNotification($booking, 'cancelled'));
            }

            Log::info('Booking cancellation sent', ['booking_id' => $booking->id]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send ticket delivery notification
     */
    public function sendTicketDelivery(Booking $booking)
    {
        try {
            // Send email with tickets to customer
            if ($booking->customer && $booking->customer->email) {
                Mail::to($booking->customer->email)
                    ->send(new TicketDeliveryMail($booking));
            }

            // Send in-app notification if user exists
            if ($booking->user) {
                $booking->user->notify(new BookingStatusNotification($booking, 'tickets_delivered'));
            }

            Log::info('Ticket delivery notification sent', ['booking_id' => $booking->id]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to send ticket delivery notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceived(Booking $booking)
    {
        try {
            // Send in-app notification if user exists
            if ($booking->user) {
                $booking->user->notify(new BookingStatusNotification($booking, 'payment_received'));
            }

            Log::info('Payment received notification sent', ['booking_id' => $booking->id]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to send payment received notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send booking reminder notification
     */
    public function sendBookingReminder(Booking $booking, $hoursBeforeShow = 24)
    {
        try {
            $message = "Reminder: Your show '{$booking->show->title}' is starting in {$hoursBeforeShow} hours!";

            // Send email reminder
            if ($booking->customer && $booking->customer->email) {
                // You can create a specific reminder mail class
                // For now, using basic notification
            }

            // Send in-app notification if user exists
            if ($booking->user) {
                $booking->user->notify(new BookingStatusNotification($booking, 'reminder', $message));
            }

            Log::info('Booking reminder sent', [
                'booking_id' => $booking->id,
                'hours_before' => $hoursBeforeShow
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to send booking reminder', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send admin notification for new booking
     */
    public function sendAdminBookingNotification(Booking $booking)
    {
        try {
            // Get admin users
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new BookingStatusNotification($booking, 'new_booking_admin'));
            }

            Log::info('Admin booking notification sent', ['booking_id' => $booking->id]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to send admin booking notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotification(array $userIds, $title, $message, $type = 'info')
    {
        try {
            $users = User::whereIn('id', $userIds)->get();

            Notification::send($users, new BookingStatusNotification(null, $type, $message));

            Log::info('Bulk notification sent', [
                'user_count' => count($userIds),
                'type' => $type
            ]);

            return ['success' => true, 'sent_count' => count($userIds)];

        } catch (\Exception $e) {
            Log::error('Failed to send bulk notification', [
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Queue notifications for later sending
     */
    public function queueNotification($type, $data, $sendAt = null)
    {
        // This would typically queue notifications for scheduled sending
        // For now, just log the action
        Log::info('Notification queued', [
            'type' => $type,
            'send_at' => $sendAt,
            'data' => $data
        ]);

        return ['success' => true, 'queued' => true];
    }

    /**
     * Get notification preferences for user
     */
    public function getNotificationPreferences(User $user)
    {
        // This would typically get user's notification preferences from database
        // For now, return default preferences
        return [
            'email_bookings' => true,
            'email_reminders' => true,
            'email_cancellations' => true,
            'push_notifications' => false,
            'sms_notifications' => false,
        ];
    }

    /**
     * Update notification preferences for user
     */
    public function updateNotificationPreferences(User $user, array $preferences)
    {
        try {
            // This would typically save preferences to database
            // For now, just log the action
            Log::info('Notification preferences updated', [
                'user_id' => $user->id,
                'preferences' => $preferences
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Failed to update notification preferences', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
