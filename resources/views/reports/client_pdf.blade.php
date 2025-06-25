<!DOCTYPE html>
<html>
<head>
    <title>Client Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #000; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Client Report - {{ $month }}</h2>
    <h2>Total Summary</h2>
    <table>
    <thead>
        <tr>
            <th>Year</th>
            <th>Month</th>
            <th>Total Clients</th>
            <th>Total Space (GB)</th>
            <th>Total Assets</th>
            <th>Total Documents</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $year }}</td>
            <td>{{ $month }}</td>
            <td>{{ $totalClients }}</td>
            <td>{{ number_format($totalSpace, 2) }}</td>
            <td>{{ $totalAssets }}</td>
            <td>{{ $totalDocuments }}</td>
        </tr>
    </tbody>
</table>
    <h2>Client Details</h2>
    <table>
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Broker Name</th>
                <th>Space</th>
                <th>Assets</th>
                <th>Documents</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $client)
                <tr>
                    <td>{{ $client['client_name'] }}</td>
                    <td>{{ $client['broker_name'] }}</td>
                    <td>{{ $client['space'] }}</td>
                    <td>{{ $client['assets_count'] }}</td>
                    <td>{{ $client['documents_count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
