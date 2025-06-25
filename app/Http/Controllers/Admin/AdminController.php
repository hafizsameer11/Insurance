<?php

namespace App\Http\Controllers\Admin;

use App\Models\Broker;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index(){
        return view('dashboard');
    }

    // emails to clients and brokers (timeframe) editable by admin
    public function updateReminderDays(Request $request)
{
    $request->validate([
        'type' => 'required|in:broker,client',
        'id' => 'required|integer',
        'reminder_days' => 'required|integer|min:1|max:365',
    ]);

    if ($request->type === 'broker') {
        $model = Broker::find($request->id);
    } else {
        $model = Client::find($request->id);
    }

    if ($model) {
        $model->reminder_days = $request->reminder_days;
        $model->save();
        return back()->with('success', 'Reminder days updated.');
    }

    return back()->with('error', 'Record not found.');
}

}
