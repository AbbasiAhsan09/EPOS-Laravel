@extends('layouts.app')
@section('content')
    
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row row-customized">
            <div class="col">
                <h1 class="page-title">Purchase</h1>
            </div>
        </div>
        <br>
        {{-- Buttons --}}
        <div class="block-btns-wrapper">
            <div class="row">
                <div class="col-lg-3">
                    <a href="{{url('/purchase/request')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Requisition
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{url('/purchase/quotation')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Quotation
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{url('/purchase/order')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Orders
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{url('/purchase/invoice')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Invoice
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