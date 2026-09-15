@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="card">
  <ul class="list-group list-group-flush">
    @forelse($notifications as $n)
      <li class="list-group-item {{ $n->is_read ? '' : 'bg-light' }}">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <strong>{{ $n->title }}</strong>
            <p class="mb-1 small">{{ $n->message }}</p>
            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
          </div>
          @if(!$n->is_read)
            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
              @csrf
              <button class="btn btn-sm btn-outline-primary">Open</button>
            </form>
          @endif
        </div>
      </li>
    @empty
      <li class="list-group-item text-center text-muted py-4">No notifications.</li>
    @endforelse
  </ul>
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection