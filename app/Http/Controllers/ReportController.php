<?php

namespace App\Http\Controllers;
use App\Models\Broker;
use App\Models\Client;
use App\Models\Emailhistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function currentMonthClientCountPerBroker(Request $req)
    {

        // Assuming you have Broker and Client models and clients table has broker_id and created_at columns
        // Check if report_month is provided in the request, otherwise use current month
        $reportMonth = $req->input('report_month');
        $month = '';
        if ($reportMonth) {
            // report_month is in format YYYY-MM
            [$year, $month] = explode('-', $reportMonth);
            $startOfMonth = now()->setDate($year, $month, 1)->startOfMonth();
            $endOfMonth = now()->setDate($year, $month, 1)->endOfMonth();
            $month = $req->input('report_month');
        } else {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $month = Carbon::now()->format('F Y');
        }
        $brokersQuery = Broker::query();
        if ($req->input('broker_id')) {
            $brokersQuery->where('id', $req->input('broker_id'));
        }
        $brokers = $brokersQuery->withCount([
            'clients' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            }
        ])->get();

        // Returns each broker with clients_count for current month
        $BorkerReports = $brokers->map(function ($broker) {
            return [
                'id' => $broker->id,
                'broker_reminder_days' => $broker->reminder_days ?? 30,
                'phone' => $broker->contact_person,
                'broker_name' => $broker->broker_name,
                'current_month_client_count' => $broker->clients_count,
            ];
        });
        $brokers = Broker::get();
        return view('reports.broker', compact('brokers', 'BorkerReports', 'month'));
    }
   public function clientAssetDocumentReport(Request $req)
{
    $reportMonth = $req->input('report_month');
    $monthLabel = '';

    $clientsQuery = Client::query();

    if ($req->filled('broker_id')) {
        $clientsQuery->where('broker_id', $req->input('broker_id'));
    }

    if ($req->filled('client_name')) {
        $clientName = trim($req->input('client_name'));
        $clientsQuery->where('client_name', 'like', "%{$clientName}%");
    }

    if ($reportMonth) {
        [$year, $month] = explode('-', $reportMonth);
        $startOfMonth = now()->setDate($year, $month, 1)->startOfMonth();
        $endOfMonth = now()->setDate($year, $month, 1)->endOfMonth();
        $monthLabel = Carbon::createFromDate($year, $month)->format('F Y');

        $clientsQuery->with([
            'broker',
            'assets' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            },
            'documents' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            }
        ])
        ->where(function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereHas('assets', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })->orWhereHas('documents', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            });
        });
    } else {
        $monthLabel = Carbon::now()->format('F Y');
        $clientsQuery->with(['broker', 'assets', 'documents']);
    }

    $clients = $clientsQuery->get();

    $reports = $clients->map(function ($client) {
        return [
            'client_id' => $client->id,
            'client_name' => $client->client_name,
            'broker_name' => $client->broker->broker_name ?? 'N/A',
            // 'broker_reminder_days' => $client->broker->reminder_days ?? 30,
            'client_reminder_days' => $client->reminder_days ?? 30,
            'space' => $client->space ?? 'N/A',
            'assets_count' => $client->assets->count(),
            'documents_count' => $client->documents->count(),
        ];
    });

    $brokers = Broker::get();

    return view('reports.adminview', [
        'reports' => $reports,
        'month' => $monthLabel,
        'brokers' => $brokers,
    ]);
}


    // for exporting the client report as PDF
public function downloadClientReportPdf(Request $req, $username)
{
    $reportMonth = $req->input('report_month');
    $monthLabel = '';
    if ($reportMonth) {
        [$year, $month] = explode('-', $reportMonth);
        $startOfMonth = now()->setDate($year, $month, 1)->startOfMonth();
        $endOfMonth = now()->setDate($year, $month, 1)->endOfMonth();
        $monthLabel = Carbon::createFromDate($year, $month)->format('F Y');
    } else {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $monthLabel = Carbon::now()->format('F Y');
        $year = Carbon::now()->format('Y');
    }

    $clientsQuery = Client::query();

    if ($req->input('broker_id')) {
        $clientsQuery->where('broker_id', $req->input('broker_id'));
    }
    if ($req->filled('client_name')) {
        $clientName = trim($req->input('client_name'));
        $clientsQuery->where('client_name', 'like', "%{$clientName}%");
    }

    $clients = $clientsQuery->with([
        'broker',
        'assets' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        },
        'documents' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }
    ])->get();

    $reports = $clients->map(function ($client) {
        return [
            'id' => $client->id, // 👈 Add this
            'client_name' => $client->client_name,
            'broker_name' => $client->broker->broker_name ?? 'N/A',
            'space' => $client->space ?? '0 GB',
            'assets_count' => $client->assets->count(),
            'documents_count' => $client->documents->count(),
        ];
    });

    // 🔢 Summary values
    $totalClients = $reports->count();
    $totalAssets = $reports->sum('assets_count');
    $totalDocuments = $reports->sum('documents_count');

    // 🧮 Total Space Calculation from "1 GB", "2.5 gb", etc.
    $totalSpace = $reports->sum(function ($client) {
        preg_match('/(\d+(\.\d+)?)/', strtolower($client['space']), $matches);
        return isset($matches[1]) ? (float) $matches[1] : 0;
    });

    $pdf = \PDF::loadView('reports.client_pdf', [
        'reports' => $reports,
        'month' => $monthLabel,
        'year' => $year ?? now()->format('Y'),
        'totalClients' => $totalClients,
        'totalAssets' => $totalAssets,
        'totalDocuments' => $totalDocuments,
        'totalSpace' => $totalSpace,
    ]);

    $filename = 'client-report-' . now()->format('F-Y') . '-' . now()->format('d-m-Y') . '.pdf';
    return $pdf->download($filename);
}

}
