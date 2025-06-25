<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Broker;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderEmail;
use Carbon\Carbon;

class SendReminderEmails extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send asset update reminders to brokers and clients based on their reminder_days';

    public function handle()
    {
        $now = Carbon::now();

        // Send to Brokers
        Broker::with('user')->chunk(100, function ($brokers) use ($now) {
            foreach ($brokers as $broker) {
                $days = $broker->reminder_days ?? 30;
                $last = $broker->last_reminded_at;

                if (!$last || $now->diffInDays(Carbon::parse($last)) >= $days) {
                    if ($broker->user && $broker->user->email) {
                        Mail::to($broker->user->email)->send(new ReminderEmail('broker', $broker));
                        $broker->last_reminded_at = $now;
                        $broker->save();
                        $this->info("Reminder sent to broker: {$broker->user->email}");
                    }
                }
            }
        });

        // Send to Clients
        Client::with('user')->chunk(100, function ($clients) use ($now) {
            foreach ($clients as $client) {
                $days = $client->reminder_days ?? 30;
                $last = $client->last_reminded_at;

                if (!$last || $now->diffInDays(Carbon::parse($last)) >= $days) {
                    if ($client->user && $client->user->email) {
                        Mail::to($client->user->email)->send(new ReminderEmail('client', $client));
                        $client->last_reminded_at = $now;
                        $client->save();
                        $this->info("Reminder sent to client: {$client->user->email}");
                    }
                }
            }
        });

        return Command::SUCCESS;
    }
}
