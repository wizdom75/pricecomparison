@extends('layouts.app')
@section('title', $brand->name)
@section('content')
<div class="container">
<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-muted" href="/">Home</a></li>
                <li class="breadcrumb-item"><a class="text-muted" href="/brands">Brands</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$brand->name}}</li>
        </ol>
</nav>       
<h1 class="h2 text-muted">{{$brand->name}} Products</h1>
<input type="hidden" id="brand_id" value="{{$brand->id}}">
<div id="brand_products">
                <section v-if="errored">
                        <p>We're sorry, we're not able to retrieve this information at the moment, please try back later</p>
                </section>
                <section v-else class="row">
                        <div v-if="loading" class="spinner-border text-secondary" role="status">
                                <span class="sr-only">Loading...</span>
                        </div>
                
                        <div v-for="product in products" class="col-6 col-sm-6 col-md-3 d-inline mb-3">
                                @include('inc.product-card')
                        </div>
                        {{-- Include the pagination blade template --}}
                        @include('inc.paginate')
                        
                        
                </section>
                </div>
                    
                    <script type="application/javascript">
                    
                        new Vue({
                        el: '#brand_products',
                        data () {
                            return {
                            products: null,
                            loading: true,
                            errored: false,
                            pagination: null
                            }
                        },
                        filters: {
                            currencydecimal (value) {
                            return value.toFixed(2)
                            }
                        },
                        mounted () {
                                let brand_id = document.getElementById("brand_id").value;
                            let baseURL = '/api/brand-products/'+brand_id;
                            let pg = 1;
                            axios
                            .get(baseURL+'?page='+pg)
                            .then(response => {
                                this.products = response.data.data,
                                this.pagination = response.data
                            })
                            .catch(error => {
                                console.log(error)
                                this.errored = true
                            })
                            .finally(() => this.loading = false)
                        },
                        methods: {
                                // Our method to GET results from a Laravel endpoint
                                getResults(val) {
                                        let brand_id = document.getElementById("brand_id").value;
                                        let baseURL = '/api/brand-products/'+brand_id;
                                        axios.get(val)
                                                .then(response => {
                                    this.products = response.data.data,
                                    this.pagination = response.data
                                                });
                                }
                        }
                        });
                    </script> 
</div>
@endsection