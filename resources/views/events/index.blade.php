<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Event Management</h2></x-slot>
    <main class="py-8 max-w-5xl mx-auto space-y-6">
        @if(session('status'))<p role="status" class="p-3 bg-green-100">{{ session('status') }}</p>@endif
        <section class="bg-white p-6 shadow rounded"><h3 class="font-bold text-lg">Create an event</h3>
            <form method="post" action="{{ route('events.store') }}" class="grid md:grid-cols-2 gap-3 mt-3">@csrf
                <label>Title<input name="title" required maxlength="160" class="block w-full rounded border"></label>
                <label>Venue<input name="venue" required maxlength="200" class="block w-full rounded border"></label>
                <label>Starts<input type="datetime-local" name="starts_at" required class="block w-full rounded border"></label>
                <label>Ends<input type="datetime-local" name="ends_at" required class="block w-full rounded border"></label>
                <label>Capacity (optional)<input type="number" name="capacity" min="1" max="1000000" class="block w-full rounded border"></label>
                <label class="md:col-span-2">Description<textarea name="description" required maxlength="10000" rows="3" class="block w-full rounded border"></textarea></label>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded justify-self-start">Publish event</button>
            </form>
        </section>
        <section class="bg-white p-6 shadow rounded">
            <form method="get" class="flex gap-2 mb-4"><label class="sr-only" for="q">Search events</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Search events" class="rounded border"><button class="px-3 py-2 bg-gray-800 text-white rounded">Search</button></form>
            <h3 class="font-bold text-lg">Published events</h3>
            <div class="grid md:grid-cols-2 gap-4 mt-3">
                @forelse($events as $event)<article class="border rounded p-4"><h4 class="font-semibold"><a class="text-indigo-700 underline" href="{{ route('events.show',$event) }}">{{ $event->title }}</a></h4><p>{{ $event->starts_at->format('M j, Y g:i A') }} · {{ $event->venue }}</p><p>By {{ $event->organizer->name }} · {{ $event->registrations_count }}{{ $event->capacity ? '/'.$event->capacity : '' }} attendees</p>@if($myEventIds->contains($event->id))<span class="text-green-700">Registered</span>@endif</article>
                @empty<p>No upcoming published events.</p>@endforelse
            </div>{{ $events->links() }}
        </section>
    </main>
</x-app-layout>
