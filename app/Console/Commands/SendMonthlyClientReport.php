<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Broker;
use Illuminate\Support\Facades\Mail;
use PDF;

class SendMonthlyClientReport extends Command
{
    protected $signature = 'report:monthly-client-count';
    protected $description = 'Send monthly report to admin with client, space, asset, and document summary';

    public function handle()
    {
        $brokers = Broker::with(['clients.assets', 'clients.documents'])->get();

        $reportData = $brokers->map(function ($broker) {
            $totalClients = $broker->clients->count();

            $totalAssets = $broker->clients->sum(fn($client) => optional($client->assets)->count() ?? 0);
            $totalDocuments = $broker->clients->sum(fn($client) => optional($client->documents)->count() ?? 0);
            $spacePerClient = $broker->clients->sum(fn($client) => (float) $client->space);
            $totalSpaceGB = round($spacePerClient / 1024, 2);

            return [
                'broker_name' => $broker->broker_name,
                'contact_person' => $broker->contact_person,
                'total_clients' => $totalClients,
                'total_assets' => $totalAssets,
                'total_documents' => $totalDocuments,
                'total_space_gb' => $totalSpaceGB
            ];
        });


        $month = now()->format('F');
        $year = now()->format('Y');
        $date = now()->format('d M Y');

        $pdf = PDF::loadView('emails.monthly_report', [
            'reportData' => $reportData,
            'month' => $month,
            'year' => $year,
            'date' => $date
        ]);

        Mail::send([], [], function ($message) use ($pdf, $month, $year) {
            $message->to('sohaibahmad3277@gmail.com') // Replace with admin email
                ->subject("Monthly Report - $month $year")
                ->attachData($pdf->output(), "monthly_report_{$month}_{$year}.pdf");
        });

        $this->info("Monthly report sent to admin.");
    }
}
