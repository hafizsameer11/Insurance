@extends('layout.layout')

@section('content')
<div class="card shadow my-4">
    <div class="card-body">
        <h4>Edit Client Space for {{ $client->client_name }}</h4>
        <form action="{{ route('clients.update.space', [$client->broker->user->username ?? 'admin', $client->id]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="space">Database Space (e.g., 1gb, 2gb)</label>
                <input type="text" name="space" id="space" class="form-control" value="{{ old('space', $client->space) }}" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Space</button>
        </form>
    </div>
</div>
@endsection
