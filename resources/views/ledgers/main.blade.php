@extends('layouts.app')
@section('content')
    
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row row-customized">
            <div class="col">
                <h1 class="page-title">Ledger</h1>
            </div>
        </div>
        <br>
        {{-- Buttons --}}
        <div class="block-btns-wrapper">
           
            <div class="row">
                <div class="col-lg-3">
                    <a href="{{route('customer-ledger.index')}}" class="btn btn-block btn-primary btn-lg ">
                        Customer Ledger
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{route('vendor-ledger.index')}}" class="btn btn-block btn-primary btn-lg ">
                        Vednor Ledger
                    </a>
                </div>
                
            </div>
        </div>
        {{-- Buttons --}}

    

    </div>

    
    </div>
    <style>
        .block-btns-wrapper a.btn{
            display: block;
        }
    </style>
@endsection