<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Property;
use App\Models\User;
use App\Models\AuditLog;
use PDF;

class MonthlyReportGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $properties = Property::with('landlord')->get();

        foreach ($properties as $property) {
            $landlord = $property->landlord;

            // TODO: Generate landlord statement PDF
            // TODO: Generate investor statements if applicable
            
            // Send email to landlord
            dispatch(new SendEmailJob(
                recipient: $landlord->email,
                subject: 'Monthly Property Statement - ' . $property->name,
                body: "Your monthly statement for {$property->name} is attached.",
                modelType: 'Property',
                modelId: $property->id
            ));
        }

        AuditLog::create([
            'action' => 'monthly_report',
            'model_type' => 'Property',
            'model_id' => 0,
            'description' => "Generated monthly reports for " . $properties->count() . " properties",
            'user_id' => null,
            'status' => 'completed',
        ]);
    }
}
