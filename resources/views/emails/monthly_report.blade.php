<!DOCTYPE html>
<html>
<head>
    <title>Monthly Broker Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Monthly Broker Report</h2>
    <p><strong>Month:</strong> {{ $month }} {{ $year }}</p>
    <p><strong>Generated on:</strong> {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>Broker Name</th>
                <th>Contact Person</th>
                <th>Total Clients</th>
                <th>Total Space (GB)</th>
                <th>Total Assets</th>
                <th>Total Documents</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $item)
                <tr>
                    <td>{{ $item['broker_name'] }}</td>
                    <td>{{ $item['contact_person'] }}</td>
                    <td>{{ $item['total_clients'] }}</td>
                    <td>{{ $item['total_space_gb'] }}</td>
                    <td>{{ $item['total_assets'] }}</td>
                    <td>{{ $item['total_documents'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
