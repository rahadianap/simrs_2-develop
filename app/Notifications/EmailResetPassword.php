<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailResetPassword extends Notification
{
    use Queueable;
    public $token;
    public $id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id,$token)
    {
        $this->token = $token;
        $this->id = $id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/reset/verification?id='.$this->id.'&&token='.$this->token);

        return (new MailMessage)
                    ->subject('HIMS - Verifikasi Reset Password')
                    ->greeting('Halo Sahabat,')
                    ->line('Anda mendapat email ini sebagai bagian dari proses verifikasi RESET PASSWORD pengguna aplikasi HIMS dengan token : ')
                    ->line('<strong>'.$this->token.'</strong>') 
                    ->line('Silahkan klik tombol / link dibawah ini untuk melakukan reset password.')
                    ->action('RESET PASSWORD', $url)
                    ->line('Jika anda merasa tidak pernah melakukan permintaan reset password di aplikasi HIMS, silahkan abaikan email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
