<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with('organizer:id,name')->withCount('registrations')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->query('q')).'%'))
            ->when($request->query('view') === 'mine', fn ($q) => $q->where('organizer_id', $request->user()->id))
            ->where('status', 'published')->orderBy('starts_at')->paginate(12)->withQueryString();
        $myEventIds = $request->user()->eventRegistrations()->pluck('event_id');
        return view('events.index', compact('events', 'myEventIds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:160', 'description' => 'required|string|max:10000',
            'venue' => 'required|string|max:200', 'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at', 'capacity' => 'nullable|integer|min:1|max:1000000',
        ]);
        $event = $request->user()->events()->create($data + ['status' => 'published']);
        return redirect()->route('events.show', $event)->with('status', 'Event published.');
    }

    public function show(Request $request, Event $event)
    {
        abort_unless($event->status === 'published' || $event->organizer_id === $request->user()->id || $event->registrations()->where('user_id', $request->user()->id)->exists(), 404);
        $event->load('organizer:id,name')->loadCount('registrations');
        return view('events.show', ['event' => $event, 'registered' => $event->registrations()->where('user_id', $request->user()->id)->exists(), 'attendees' => $event->organizer_id === $request->user()->id ? $event->attendees()->orderBy('name')->get(['users.id','name','email']) : collect()]);
    }

    public function register(Request $request, Event $event)
    {
        abort_unless($event->status === 'published', 404);
        if ($event->organizer_id === $request->user()->id) return back()->withErrors(['event' => 'Organizers cannot register for their own event.']);
        DB::transaction(function () use ($request, $event) {
            $locked = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'published') throw ValidationException::withMessages(['event' => 'Registration is closed.']);
            if ($locked->registrations()->where('user_id', $request->user()->id)->exists()) return;
            if ($locked->capacity !== null && $locked->registrations()->count() >= $locked->capacity) throw ValidationException::withMessages(['event' => 'Event is full.']);
            $locked->registrations()->create(['user_id' => $request->user()->id, 'registered_at' => now()]);
        });
        return back()->with('status', 'Registration confirmed.');
    }

    public function cancel(Request $request, Event $event)
    {
        $event->registrations()->where('user_id', $request->user()->id)->delete();
        return back()->with('status', 'Registration cancelled.');
    }

    public function status(Request $request, Event $event)
    {
        abort_unless($event->organizer_id === $request->user()->id, 404);
        $data = $request->validate(['status' => 'required|in:published,cancelled,completed']);
        $event->update($data);
        return back()->with('status', 'Event status updated.');
    }
}
