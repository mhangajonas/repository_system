<?php

namespace App\Notifications;

use App\Models\Repository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentStatusUpdated extends Notification
{
    use Queueable;

    public $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $statusMessages = [
            'pending_library' => [
                'subject' => 'Kazi Yako Imepitishwa na Supervisor',
                'line' => 'Hongera! Msimamizi (Supervisor) wako amepitisha kazi yako ya "' . $this->repository->title . '". Hivi sasa ipo kwenye ukatabio (Library) kwa ajili ya Uhakiki wa Mwisho.',
                'color' => 'primary',
            ],
            'revision_requested' => [
                'subject' => 'Marekebisho Yanahitajika kwenye Kazi Yako',
                'line' => 'Kazi yako ya "' . $this->repository->title . '" inahitaji marekebisho madogo kutoka kwa Supervisor wako kabla ya kuendelea.',
                'color' => 'error',
            ],
            'approved' => [
                'subject' => 'Kazi Yako Imethibitishwa na Kuwekwa Maktaba!',
                'line' => 'Kazi yako ya "' . $this->repository->title . '" imethibitishwa kikamilifu na kupewa Accession Number: ' . $this->repository->accession_number,
                'color' => 'success',
            ],
            'rejected' => [
                'subject' => 'Kazi Yako Imekataliwa',
                'line' => 'Afadhali uwasiliane na kitengo kinachohusika au mwalimu wako kuhusu kazi yako ya "' . $this->repository->title . '".',
                'color' => 'error',
            ],
        ];

        $currentStatus = $this->repository->status;
        $info = $statusMessages[$currentStatus] ?? [
            'subject' => 'Hali ya Kazi Yako Imebadilika',
            'line' => 'Hali ya kazi yako ya "' . $this->repository->title . '" imebadilishwa kuwa ' . $currentStatus,
            'color' => 'primary',
        ];

        return (new MailMessage)
            ->subject($info['subject'])
            ->greeting('Jambo ' . $notifiable->name . ',')
            ->line($info['line'])
            ->action('Angalia Dashboard', route('dashboard'))
            ->line('Ahsante kwa kutumia Mfumo wa Maktaba (URMS).');
    }
}