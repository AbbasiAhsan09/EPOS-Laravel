@extends('layouts.app')
@section('content')
    
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row row-customized">
            <div class="col">
                <h1 class="page-title">Reports</h1>
            </div>
        </div>
        <br>
        {{-- Buttons --}}
        <div class="block-btns-wrapper">
            <h3>Purchase Reports</h3>
            <div class="row">
                <div class="col-lg-3">
                    <a href="{{url('/reports/purchase-report')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Reports
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{route('purchase-report.detail')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Reports (Product Wise)
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{route('purchase-report.summary')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Summary (Overall)
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="{{url('/reports/purchase-report?filter_deleted=true')}}" class="btn btn-block btn-primary btn-lg ">
                        Purchase Reports (Deleted)
                    </a>
                </div>
            </div>
        </div>
        {{-- Buttons --}}

        <hr>
            {{-- Buttons --}}
            <div class="block-btns-wrapper">
                <h3>Sales Reports</h3>
                <div class="row">
                    <div class="col-lg-3">
                        <a href="{{route('sales-report.index')}}" class="btn btn-block btn-primary btn-lg ">
                            Sales Reports
                        </a>
                    </div>
                    <div class="col-lg-3">
                        <a href="{{  route('sales-report.detail')   }}" class="btn btn-block btn-primary btn-lg ">
                            Sales Reports (Product Wise)
                        </a>
                    </div>
                    <div class="col-lg-3">
                        <a href="{{route('sales-report.summary')}}" class="btn btn-block btn-primary btn-lg ">
                            Sales Summary (Overall)
                        </a>
                    </div>
                    <div class="col-lg-3">
                        <a href="{{url('/reports/sales-report?filter_deleted=true')}}" class="btn btn-block btn-primary btn-lg ">
                            Sales Reports (Deleted)
                        </a>
                    </div>
                </div>
            </div>
            {{-- Buttons --}}


            <hr>
            {{-- Buttons --}}
            <div class="block-btns-wrapper">
                <h3>Inventory Reports</h3>
                <div class="row">
                    <div class="col-lg-3">
                        <a href="{{route('inventory-report.index')}}" class="btn btn-block btn-primary btn-lg ">
                            Inventory Balance Report
                        </a>
                    </div>
                   
                    {{-- <div class="col-lg-3">
                        <a href="{{url('/reports/inventory-report')}}" class="btn btn-block btn-primary btn-lg ">
                            Sales Reports (Overall)
                        </a>
                    </div>
                    <div class="col-lg-3">
                        <a href="{{url('/reports/inventory-report')}}" class="btn btn-block btn-primary btn-lg ">
                            Sales Reports (Deleted)
                        </a>
                    </div> --}}
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