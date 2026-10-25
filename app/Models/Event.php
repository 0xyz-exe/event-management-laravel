<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['organizer_id', 'title', 'description', 'venue', 'starts_at', 'ends_at', 'capacity', 'status'];
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    public function organizer() { return $this->belongsTo(User::class, 'organizer_id'); }
    public function attendees() { return $this->belongsToMany(User::class, 'event_registrations')->withPivot('registered_at')->withTimestamps(); }
    public function registrations() { return $this->hasMany(EventRegistration::class); }
}
