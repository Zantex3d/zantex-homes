<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AuditLog;
use Illuminate\Mail\Mailable;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $recipient,
        public string $subject,
        public string $body,
        public string $modelType,
        public int $modelId
    ) {}

    public function handle(): void
    {
        try {
            Mail::raw($this->body, function ($message) {
                $message->to($this->recipient)
                    ->subject($this->subject);
            });

            AuditLog::create([
                'action' => 'email_sent',
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
                'description' => "Email sent to {$this->recipient}",
                'user_id' => null,
                'status' => 'completed',
            ]);
        } catch (\Exception $e) {
            AuditLog::create([
                'action' => 'email_failed',
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
                'description' => "Email failed: {$e->getMessage()}",
                'user_id' => null,
                'status' => 'failed',
            ]);
            throw $e;
        }
    }
}
