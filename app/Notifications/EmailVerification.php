<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

//use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
//use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class EmailVerification extends Notification
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
        $url = url('/verification?id='.$this->id.'&&token='.$this->token);

        return (new MailMessage)
                    ->subject('HIMS - Verifikasi Akun Pengguna')
                    ->greeting('Halo Sahabat,')
                    ->line('Anda mendapat email ini sebagai bagian dari proses verifikasi pengguna aplikasi HIMS. Silahkan klik tombol / link dibawah ini untuk melakukan verifikasi email dan aktivasi akun anda.')
                    ->action('VERIFIKASI', $url)
                    ->line('Jika anda merasa tidak pernah melakukan proses registrasi di aplikasi HIMS, silahkan abaikan email ini.');
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
