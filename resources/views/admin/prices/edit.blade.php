@extends('layouts.admin')
@section('title', 'Edit a price')
@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit '{{ $price->merchant->name }}' price for '{{ $price->product->title }}'</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">

          </div>
        </div>
      </div>
      <div class="container">
        {!! Form::open(['action' => ['Admin\PricesController@update', $price->id], 'method' => 'PUT']) !!}

        <div class="form-group">
          {!! Form::label('Product title:') !!}
          {!! Form::text('product_title', $price->product_title, ['placeholder' => 'Product title', 'class'=>'form-control']); !!}
        </div>
        <div class="form-group">
          {!! Form::label('Promo text:') !!}
          {!! Form::text('promo_text', $price->promo_text, ['class' => 'form-control', 'placeholder' => 'promo-text']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('Amount:') !!}
            {!! Form::text('amount', $price->amount, ['class' => 'form-control', 'placeholder' => '5.99']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('Shipping:') !!}
            {!! Form::text('shipping', $price->shipping, ['class' => 'form-control', 'placeholder' => '5.99']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('Buy link (tracking url):') !!}
            {!! Form::text('buy_link', $price->buy_link, ['class' => 'form-control', 'placeholder' => 'https://example.com/?track']) !!}
        </div>

        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}

        {!! Form::close() !!}
      </div>

@endsection
