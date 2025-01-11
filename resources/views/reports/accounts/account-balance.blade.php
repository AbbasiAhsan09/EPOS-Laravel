@extends('layouts.app')
@section('content')

@php
    $types = ['income', 'expenses', 'assets', 'liabilities', 'equity'];
@endphp

    <div class="container-fluid">
        <div class="col-lg-3">
            <h4>Account Balances</h4>
        </div>
        <form>
            <div class="row mb-2 align-items-end">
                <div class="col-lg-3">
                    <label for="">Search</label>
                    <div class="input-group input-group-outline">
                        <input type="search" value="{{ request()->query("search") ?? "" }}" class="form-control" name="search" >
                    </div>
                </div>
                <div class="col-lg-4">
                    <label for="">Account Type</label>
                    <div class="input-group input-group-outline">
                        <select name="type" class="form-control" >
                            <option value="">All</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ request()->query("type") == $type ? "selected" : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <label for="">Report Type</label>
                    <div class="input-group input-group-outline">
                        <select name="report-type" class="form-control" >
                            <option value="">WEB</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary m-0 w-100">Search</button>
                </div>
            </div>
        </form>
        <table class="table table-responsive-sm table-sm table-striped table-bordered ">
            <thead class="table-dark">
                <tr>
                    <th style="width: 100px">S.No</th>
                    <th>Account Title</th>
                    <th>Account Type</th>
                    <th>Current Balance</th>
                    <th style="width: 200px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $key => $account)
                    <tr>
                        <td style="width: 100px">{{ $key + 1 }}</td>
                        <td>{{ $account->title }}</td>
                        <td>{{ ucfirst($account->type) }}</td>
                        <td>
                            <strong>
                                {{ ConfigHelper::getStoreConfig()['symbol'] . ($account->remaining_balance > 0 ? number_format($account->remaining_balance, 2) : '(' . number_format(abs($account->remaining_balance), 2) . ')') }}
                            </strong>
                        </td>
                        <td style="width: 200px">
                            <button class="btn btn-sm " data-bs-toggle="modal"
                                data-bs-target="#exampleModal{{ $account->id }}">
                                Details
                            </button>
                        </td>
                    </tr>

                    <!-- Detail search form Modal -->
                    <div class="modal fade" id="exampleModal{{ $account->id }}" tabindex="-1"
                        aria-labelledby="exampleModal{{ $account->id }}Label" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ url('/reports/accounting/general-ledger') }}" method="GET">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModal{{ $account->id }}Label">Ledger Report for
                                            {{ $account->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input type="hidden" name="accounts[]" value="{{ $account->id }}">
                                                <input type="hidden" name="type" value="pdf">
                                                <label for="">Start Date</label>
                                                <div class="input-group input-group-outline">
                                                    <input type="date" class="form-control" name="from" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="">End Date</label>
                                                <div class="input-group input-group-outline">
                                                    <input type="date" class="form-control" name="to" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Detail search form Modal -->
                @endforeach
            <tfoot class="table-dark">
                <th colspan="3">Total</th>
                <th colspan="2">
                    {{ ConfigHelper::getStoreConfig()['symbol'] . ($total_balance > 0 ? number_format($total_balance, 2) : '(' . number_format(abs($total_balance), 2) . ')') }}
                </th>
            </tfoot>
            </tbody>
        </table>
        {!! $accounts->links('pagination::bootstrap-4') !!}
    </div>
@endsection
