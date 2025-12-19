<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purok;
use App\Models\collection_schedule;
use App\Models\Collector;
use App\Models\Notification;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date_schedule' => 'required|date',
            'purok_id' => 'required|string',
            'lead_collector_id' => 'required|string'
        ]);

        // Store in collection_schedules with purok_id, date, and lead_collector_id
        $scheduleData = [
            'purok_id' => $request->purok_id,
            'date' => $request->date_schedule,
            'lead_collector_id' => $request->lead_collector_id
        ];

        // Check if schedule already exists for this date and purok
        $existingSchedules = collection_schedule::all();
        $existing = $existingSchedules->first(function($schedule) use ($request) {
            return $schedule->date === $request->date_schedule && 
                   $schedule->purok_id === $request->purok_id;
        });

        if ($existing) {
            // Update existing schedule
            $existing->fill($scheduleData);
            $existing->save();
        } else {
            // Create new schedule
            collection_schedule::create($scheduleData);
        }

        return response()->json(['success' => true]);
    }

    // Optional: Load the calendar view
    public function index()
    {
        // Get all puroks and collectors for the dropdowns
        $puroks = Purok::all();
        $collectors = Collector::all();
        
        // Get today's schedule(s)
        $today = now()->toDateString();
        $allSchedules = collection_schedule::all();
        $todaySchedules = $allSchedules->filter(function($schedule) use ($today) {
            $scheduleDate = $schedule->date ?? $schedule->date_schedule ?? '';
            return $scheduleDate === $today;
        });
        
        // Build purok map
        $purokMap = [];
        foreach ($puroks as $purok) {
            $purokId = $purok->key ?? null;
            if ($purokId) {
                $purokMap[$purokId] = $purok;
            }
        }
        
        // Get purok names for today's schedules
        $todayPuroks = $todaySchedules->map(function($schedule) use ($purokMap) {
            $purok = $purokMap[$schedule->purok_id] ?? null;
            return $purok->purok_name ?? 'Unknown';
        })->unique()->values();
        
        // Check if collection has started for today
        $collectionStarted = $todaySchedules->contains(function($schedule) {
            return isset($schedule->collection_started) && $schedule->collection_started === true;
        });
        
        // Get today's schedule key for starting collection
        $todayScheduleKey = $todaySchedules->first()->key ?? null;

        $pageTitle = 'Schedule';
        return view('scheduling', compact('puroks', 'collectors', 'todayPuroks', 'collectionStarted', 'todayScheduleKey', 'pageTitle'));
    }
    
    public function getSchedules()
    {
        // Fetch all schedules from collection_schedules
        $allSchedules = collection_schedule::all();
        
        // Get all puroks to build a lookup map
        $puroks = Purok::all();
        $purokMap = [];
        foreach ($puroks as $purok) {
            $purokId = $purok->key ?? null;
            if ($purokId) {
                $purokMap[$purokId] = $purok;
            }
        }
        
        // Map schedules with purok names
        $schedules = $allSchedules->map(function($schedule) use ($purokMap) {
            $purok = $purokMap[$schedule->purok_id] ?? null;
            return [
                'key' => $schedule->key,
                'purok_name' => $purok->purok_name ?? 'Unknown',
                'date_schedule' => $schedule->date ?? $schedule->date_schedule ?? ''
            ];
        })->filter(function($schedule) {
            return !empty($schedule['date_schedule']);
        })->values();
        
        return response()->json($schedules);
    }
    
    public function destroy(Request $request)
    {
        $request->validate([
            'schedule_key' => 'required|string',
        ]);

        // Find and delete schedule from collection_schedules by key
        $scheduleToDelete = collection_schedule::find($request->schedule_key);

        if ($scheduleToDelete && $scheduleToDelete->key) {
            $scheduleToDelete->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
    
    public function startCollection(Request $request)
    {
        $request->validate([
            'schedule_key' => 'required|string',
        ]);

        // Find the schedule and mark collection as started
        $schedule = collection_schedule::find($request->schedule_key);

        if ($schedule && $schedule->key) {
            $schedule->collection_started = true;
            $schedule->save();
            
            // Create notification for all recipients
            Notification::create([
                'message' => "Today's Collection has started",
                'recipient' => 'all',
                'created_at' => now()->toDateTimeString(),
            ]);
            
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Schedule not found']);
    }
}
