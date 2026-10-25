<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $event->title }}</h2></x-slot>
    <main class="py-8 max-w-4xl mx-auto space-y-6">
        @if(session('status'))<p role="status" class="p-3 bg-green-100">{{ session('status') }}</p>@endif
        @if($errors->any())<p role="alert" class="p-3 bg-red-100">{{ $errors->first() }}</p>@endif
        <section class="bg-white p-6 shadow rounded"><p class="whitespace-pre-wrap">{{ $event->description }}</p><dl class="grid sm:grid-cols-2 gap-3 mt-5"><div><dt class="font-semibold">When</dt><dd>{{ $event->starts_at->format('M j, Y g:i A') }} – {{ $event->ends_at->format('M j, Y g:i A') }}</dd></div><div><dt class="font-semibold">Where</dt><dd>{{ $event->venue }}</dd></div><div><dt class="font-semibold">Organizer</dt><dd>{{ $event->organizer->name }}</dd></div><div><dt class="font-semibold">Attendance</dt><dd>{{ $event->registrations_count }}{{ $event->capacity ? '/'.$event->capacity : '' }}</dd></div></dl>
            @if($event->organizer_id !== auth()->id() && $event->status === 'published')
                @if($registered)<form method="post" action="{{ route('events.cancel',$event) }}" class="mt-5">@csrf @method('DELETE')<button class="px-4 py-2 bg-gray-700 text-white rounded">Cancel registration</button></form>
                @elseif(!$event->capacity || $event->registrations_count < $event->capacity)<form method="post" action="{{ route('events.register',$event) }}" class="mt-5">@csrf<button class="px-4 py-2 bg-indigo-600 text-white rounded">Register</button></form>
                @else<p class="mt-5 font-semibold">Event is full.</p>@endif
            @endif
            @if($event->organizer_id === auth()->id())<form method="post" action="{{ route('events.status',$event) }}" class="mt-5 flex gap-2">@csrf @method('PATCH')<label>Status<select name="status" class="ml-2 rounded border">@foreach(['published','cancelled','completed'] as $status)<option value="{{ $status }}" @selected($event->status===$status)>{{ ucfirst($status) }}</option>@endforeach</select></label><button class="px-3 py-2 bg-gray-800 text-white rounded">Update</button></form><h3 class="font-bold mt-6">Attendees</h3><ul class="list-disc ml-6">@forelse($attendees as $attendee)<li>{{ $attendee->name }} · {{ $attendee->email }}</li>@empty<li>No registrations yet.</li>@endforelse</ul>@endif
        </section>
    </main>
</x-app-layout>
