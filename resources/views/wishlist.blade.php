@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Wishlist</h2>
    
      <div class="shopping-cart">
        @if(Cart::instance("wishlist")->content()->count()>0)
        <div class="cart-table__wrapper">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Product</th>
                <th></th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($items as $item)
                <tr>
                  <td>
                    <div class="shopping-cart__product-item">
                      <img loading="lazy" src="{{asset('uploads/products/thumbnails')}}/{{$item->model->image}}" width="120" height="120" alt="{{$item->name}}" />
                    </div>
                  </td>
                  <td>
                    <div class="shopping-cart__product-item__detail">
                      <h4>{{$item->name}}</h4>
                      <ul class="shopping-cart__product-item__options">
                        <li>Color: Yellow</li>
                        <li>Size: L</li>
                      </ul>
                    </div>
                  </td>
                  <td>
                    <span class="shopping-cart__product-price">${{$item->price}}</span>
                  </td>
                  <td>
                    {{$item->qty}}
                  </td>
      
                  <td>
                    <div class="row">
                      <div class="col-6">
                        <form method="POST" action="{{route('wishlist.move.to.cart',['rowId'=>$item->rowId])}}">
                        @csrf
                        <button type="submit" class="remove-cart btn btn-sm btn-warning">Move to Cart</button>
                        </form>
                      </div>

                      <div class="col-6">
                        <form method="POST" action="{{route('wishlist.item.remove',['rowId'=>$item->rowId])}}">                                    
                        @csrf
                        @method("DELETE")
                        <button type="submit" class="remove-cart btn btn-sm btn-danger">Remove</button>
                        </form> 
                      </div>    
                    </div>                
                  </td>
                </tr>
              @endforeach
    
            </tbody>
          </table>
          <div class="cart-table-footer">
          <form method="POST" action="{{route('wishlist.item.clear')}}">
          @csrf
          @method("DELETE")
            <button class="btn btn-light">CLEAR WISHLIST</button>
          </div>
        </div>
        @else
          <div class="row">
              <div class="col-md-12">
                  <p>No item found in your wishlist</p>
                  <a href="{{route('shop.index')}}" class="btn btn-info">Wishlist Now</a>
              </div>
          </div>
        @endif
      </div>
    </section>
  </main>
@endsection