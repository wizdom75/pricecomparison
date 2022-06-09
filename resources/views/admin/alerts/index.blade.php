@extends('layouts.admin')
@section('title', 'Users')
@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Users</h1>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-sm">

            @if (count($alerts)>0)
              <thead>
                <tr>
                  <th>#</th>
                  <th>Product name</th>
                  <th>Email</th>
                  <th>Target price</th>
                  <th>Current price</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
            @foreach ($alerts as $alert)
              <tr>
                <td>{{ $alert->id }}</td>
                <td>{{ $alert->product->title }}</td>
                <td>{{ $alert->email }}</td>
                <td>£{{ $alert->target_price }}</td>
                <td>£{{ $alert->product->min_price }}</td>
                <td>
                  <a class="btn btn-sm btn-outline-warning" href="/admin/alerts/{{ $alert->id }}/edit"><span data-feather="edit"></span></a>
                </td>
              </tr>
              @endforeach
            @else
                <h1>No alert found</h1>
            @endif
          </tbody>
        </table>
        {{ $alerts->links() }}
      </div>

@endsection
