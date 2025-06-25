@extends('layout.layout')
@section('content')
    <div class="card shadow my-4">
        <div class='card-body'>
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-5">
                        <label for="report_month" class="form-label">Report Month</label>
                        <input type="month" name="report_month" id="report_month" class="form-control"
                            value="{{ request('report_month') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
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
                            <th>Broker Name</th>
                            <th>Broker Phone</th>
                            <th>Client added</th>
                            <th>Reminder Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($BorkerReports as $broker)
                            <tr>
                                <td>{{ $broker['broker_name'] }}</td>
                                <td>{{ $broker['phone'] }}</td>
                                <td>{{ $broker['current_month_client_count'] }}</td>

                                <td>
                                    <form action="{{ route('admin.updateReminderDays') }}" method="POST" class="d-flex">
                                        @csrf
                                        <input type="hidden" name="type" value="broker">
                                        <input type="hidden" name="id" value="{{ $broker['id'] }}">
                                        <input type="number" name="reminder_days"
                                            value="{{ $broker['broker_reminder_days'] ?? 30 }}" class="form-control me-2"
                                            min="1" max="365" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
