@extends('layout.layout')
@section('content')
    <div class="d-flex justify-content-end">
        <a href="{{ route('report.clientAssetDocumentReportPdf', [
            'username' => Auth::user()->username,
            'report_month' => request('report_month'),
            'broker_id' => request('broker_id'),
            'client_name' => request('client_name'),
        ]) }}"
            class="btn btn-success mb-3">
            Export PDF
        </a>
    </div>

    <div class="card shadow my-4">
        <div class='card-body'>
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label for="client_name">Client Name</label>
                        <input type="text" placeholder="search Client" class="form-control" name="client_name"
                            value="{{ request('client_name') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="broker_id" class="form-label">Broker</label>
                        <select name="broker_id" id="broker_id" class="form-control">
                            <option value="">Select Broker</option>
                            @foreach ($brokers as $broker)
                                <option value="{{ $broker->id }}"
                                    {{ request('broker_id') == $broker->id ? 'selected' : '' }}>
                                    {{ $broker->broker_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="report_month" class="form-label">Report Month</label>
                        <input type="month" name="report_month" id="report_month" class="form-control"
                            value="{{ request('report_month') }}">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary my-4">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='card shadow my-4'>
        <div class='card-header '>
            <div class='card-title'>
                <h4>List {{ $month }}</h4>
            </div>
        </div>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table table-striped'>
                    <thead>
                        <tr class='thead-dark'>
                            <th>Client Name</th>
                            <th>Broker Name</th>
                            <th>Space</th>
                            <th>Total Assets Items added</th>
                            <th>Tolal Docuemnt added</th>
                            <th>Reminder Days</th>
                            <th>Edit Space</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $broker)
                            <tr>
                                <td>{{ $broker['client_name'] }}</td>
                                <td>{{ $broker['broker_name'] }}</td>
                                <td>{{ $broker['space'] }}</td>
                                <td>{{ $broker['assets_count'] }}</td>
                                <td>{{ $broker['documents_count'] }}</td>
                                <td>
                                    <form action="{{ route('admin.updateReminderDays') }}" method="POST" class="d-flex">
                                        @csrf
                                        <input type="hidden" name="type" value="client">
                                        <input type="hidden" name="id" value="{{ $broker['client_id'] }}">
                                        <input type="number" name="reminder_days"
                                            value="{{ $broker['client_reminder_days'] ?? 30 }}" class="form-control me-2"
                                            min="1" max="365" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('clients.edit.space', ['admin', $broker['client_id']]) }}"
                                        class="btn btn-sm btn-warning">Edit Space</a>
                                </td>


                            </tr>


                            <!-- Edit Space Modal -->
                            <div class="modal fade" id="editSpaceModal" tabindex="-1" aria-labelledby="editSpaceModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="editSpaceForm">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Client Space</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="client_id" id="client_id">
                                                <div class="mb-3">
                                                    <label for="space" class="form-label">Space</label>
                                                    <select class="form-control" id="space" name="space">
                                                        <option value="1gb">1 GB</option>
                                                        <option value="2gb">2 GB</option>
                                                        <option value="5gb">5 GB</option>
                                                        <option value="10gb">10 GB</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Update Space</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Success Modal -->
                            <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content text-center">
                                        <div class="modal-body">
                                            <p class="text-success fw-bold">Client space updated successfully!</p>
                                            <button type="button" class="btn btn-success"
                                                data-bs-dismiss="modal">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
