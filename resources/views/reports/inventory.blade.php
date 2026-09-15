@extends('layouts.app')
@section('title', 'Inventory Report')
@section('content')
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Item</th><th>Category</th><th>On Hand</th><th>Total Released</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($items as $i)
        <tr>
          <td>{{ $i->item_name }}</td>
          <td>{{ $i->category ?? '—' }}</td>
          <td>{{ $i->qty_on_hand }} {{ $i->unit }}</td>
          <td>{{ $i->total_released ?? 0 }}</td>
          <td>
            @if($i->isLowStock())
              <span class="badge bg-danger">Low</span>
            @else
              <span class="badge bg-success">OK</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection