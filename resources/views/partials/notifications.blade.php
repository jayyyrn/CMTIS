@php
    $unreadNotifs = \App\Models\AppNotification::where('user_id', auth()->id())
        ->where('is_read', false)->latest()->take(5)->get();
    $unreadCount = \App\Models\AppNotification::where('user_id', auth()->id())
        ->where('is_read', false)->count();
@endphp
<div class="dropdown">
  <button class="btn btn-light position-relative" data-bs-toggle="dropdown">
    <i class="bi bi-bell"></i>
    @if($unreadCount > 0)
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;">
        {{ $unreadCount }}
      </span>
    @endif
  </button>
  <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 340px;">
    <li class="dropdown-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Notifications</span>
      @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
          @csrf
          <button class="btn btn-sm btn-link p-0 text-decoration-none">Mark all read</button>
        </form>
      @endif
    </li>
    <li><hr class="dropdown-divider"></li>
    @forelse($unreadNotifs as $n)
      <li>
        <form method="POST" action="{{ route('notifications.read', $n->id) }}">
          @csrf
          <button class="dropdown-item text-wrap py-2">
            <strong class="d-block" style="font-size:0.9rem;">{{ $n->title }}</strong>
            <small class="text-muted d-block">{{ $n->message }}</small>
            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
          </button>
        </form>
      </li>
    @empty
      <li><span class="dropdown-item text-muted text-center py-3">No new notifications</span></li>
    @endforelse
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item text-center text-primary fw-semibold" href="{{ route('notifications.index') }}">View all</a></li>
  </ul>
</div>