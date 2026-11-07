<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationQueue;
use App\Models\AuditLog;

class SendSmsWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $phone,
        public string $message,
        public string $provider = 'whatsapp'
    ) {}

    public function handle(): void
    {
        try {
            // TODO: Integrate with actual SMS/WhatsApp provider (Twilio, Termii, etc)
            // For now, log as pending
            
            NotificationQueue::create([
                'phone' => $this->phone,
                'message' => $this->message,
                'provider' => $this->provider,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            AuditLog::create([
                'action' => 'sms_sent',
                'model_type' => 'NotificationQueue',
                'model_id' => 0,
                'description' => "SMS/WhatsApp sent to {$this->phone}",
                'user_id' => null,
                'status' => 'completed',
            ]);
        } catch (\Exception $e) {
            NotificationQueue::create([
                'phone' => $this->phone,
                'message' => $this->message,
                'provider' => $this->provider,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
