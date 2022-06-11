@extends('layouts.admin')
@section('title', 'Prices')
@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Prices</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="mr-2">

          </div>
        </div>
      </div>
      <div class="table-responsive">

            @if (count($prices) > 0)
            <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Merchant</th>
                    <th>Product</th>
                    <th>Last update</th>
                    <th>Price</th>
                    <th>Shipping</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
              @foreach ($prices as $price)
                <tr>
                  <td>{{ $price->id }}</td>
                  <td>{{ $price->merchant->name }}</td>
                  <td>{{ $price->product->title }}</td>
                  <td>{{ $price->updated_at }}</td>
                  <td>£{{ $price->amount }}</td>
                  <td>£{{ $price->shipping }}</td>

                  <td>
                    <a class="btn btn-sm btn-outline-info" href="/admin/prices/{{ $price->id }}/edit"><span data-feather="edit"></span></a>
                    <a class="btn btn-sm btn-outline-danger" href=""><span data-feather="trash"></span></a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
            @else
                <h1>No prices found.</h1>
            @endif


        {{ $prices->links() }}
      </div>

@endsection
