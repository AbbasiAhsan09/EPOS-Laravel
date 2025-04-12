@extends('layouts.app')
@section('content')
    @include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/hGkaaHxzxlo'])

    <div class="page-wrapper">
        <div class="container">

            <div class="row row-customized">
                <div class="col">
                    <h1 class="page-title">Unit Management</h1>
                </div>
                <div class="col">
                    <div class="btn-grp">

                        <div class="row .row-customized">
                            <div class="col-lg-8">
                             

                            </div>
                            <div class="col-lg-4">
                                <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#newStoreModal">New UOM</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav nav-pills my-3">
                @foreach ($unit_types as $unit_type)
                <li class="nav-item">
                    <a class="uom-link nav-link{{ request()->query('unit_type') == $unit_type->id ? ' active' : '' }}"
                       href="{{ url('/unit') }}?unit_type={{ $unit_type->id }}">
                        {{ $unit_type->name }}
                    </a>
                </li>
                @endforeach
              
              </ul>
            <table class="table table-sm table-responsive-sm table-striped table-bordered table-hover table-sm">
                <thead>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Example</th>
                    <th>Status</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newStoreModalLabel">Create New UOM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('add.uom') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="">Unit of Measurment</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" name="uom" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="">Base Unit</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" name="base_unit" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="">Base Unit Value</label>
                                <div class="input-group input-group-outline">
                                    <input type="number" min="1" step="0.01" class="form-control"
                                        name="base_unit_value" required>
                                </div>
                            </div>


                        </div>

                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal --}}
@endsection
