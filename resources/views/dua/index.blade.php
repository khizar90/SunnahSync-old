@extends('layouts.base')
@section('title', 'duas')
@section('main', 'Duas Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Users List Table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-3">
                            Duas
                        </h5>
                        <div
                        class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">

                    </div>

                    </div>
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible mt-1" role="alert">
                            {{ session()->get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mt-1" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session()->has('delete'))
                        <div class="alert alert-danger alert-dismissible mt-1" role="alert">
                            {{ session()->get('delete') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="table border-top dataTable" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>title</th>
                                    <th>image</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($duas as $dua)
                                    <tr class="odd">
                                        <td>{{ $dua->category}}</td>
                                        <td>{{ $dua->sub_category }}</td>
                                        <td><img src="{{ $dua->image }}" alt="" width="150px" height="150px"></td>
                                        <td>
                                            <a href="" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $dua->id }}"
                                                class="text-body delete-record">
                                                <i class="ti ti-trash x`ti-sm mx-2"></i>
                                            </a>
                                        </td>
                                        <div class="modal fade" id="deleteModal{{ $dua->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                <div class="modal-content deleteModal verifymodal">
                                                    <div class="modal-header">
                                                        <div class="modal-title" id="modalCenterTitle">Are you
                                                            sure you want to delete
                                                            this dua?
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="body">After delete this dua user cannot
                                                            see this</div>
                                                    </div>
                                                    <hr class="hr">

                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="first">
                                                                <a href="" class="btn"
                                                                    data-bs-dismiss="modal"
                                                                    style="color: #a8aaae ">Cancel</a>
                                                            </div>
                                                            <div class="second">
                                                                <a class="btn text-center"
                                                                    href="{{ route('dashboard-dua-delete' ,$dua->id) }}">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
