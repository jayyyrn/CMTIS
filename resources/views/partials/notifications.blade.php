@php
    $unreadNotifs = \App\Models\AppNotification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->latest()
        ->take(5)
        ->get();
    $unreadCount = \App\Models\AppNotification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->count();
@endphp
<div class="dropdown">
  <button class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-bell"></i>
    @if($unreadCount > 0)
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ $unreadCount }}
      </span>
    @endif
  </button>
  <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
    <li class="dropdown-header d-flex justify-content-between">
      <span>Notifications</span>
      @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">
          @csrf
          <button class="btn btn-sm btn-link p-0">Mark all read</button>
        </form>
      @endif
    </li>
    <li><hr class="dropdown-divider"></li>
    @forelse($unreadNotifs as $n)
      <li>
        <form method="POST" action="{{ route('notifications.read', $n->id) }}">
          @csrf
          <button class="dropdown-item text-wrap">
            <strong>{{ $n->title }}</strong><br>
            <small class="text-muted">{{ $n->message }}</small><br>
            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
          </button>
        </form>
      </li>
    @empty
      <li><span class="dropdown-item text-muted">No new notifications</span></li>
    @endforelse
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item text-center" href="{{ route('notifications.index') }}">View all</a></li>
  </ul>
</div>