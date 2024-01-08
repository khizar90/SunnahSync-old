@extends('layouts.base')
@section('title', 'Mosques')
@section('main', 'Mosque Management')
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
                            Mosque list
                        </h5>
                        <div
                        class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">

                        <div class="dt-buttons btn-group flex-wrap">
                            <a href="{{ route('dashboard-stream-') }}" class="btn btn-primary"><span><i
                                        class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                                        class="d-none d-sm-inline-block"></span>Add new Stream</span></a>

                        </div>
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
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($streams as $stream)
                                    <tr class="odd">
                                        <td>{{ $stream->title }}</td>
                                        <td>
                                            <a href="{{ $stream->link }}" target="_blank"
                                                class="text-body">{{ $stream->link }}
                                            </a>
                                        </td>
                                        <td><img src="{{ $stream->image }}" alt="" width="200"></td>
                                        <td>
                                            <a href="" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $stream->id }}"
                                                class="text-body delete-record">
                                                <i class="ti ti-trash x`ti-sm mx-2"></i>
                                            </a>
                                        </td>
                                        <div class="modal fade" id="deleteModal{{ $stream->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                <div class="modal-content deleteModal verifymodal">
                                                    <div class="modal-header">
                                                        <div class="modal-title" id="modalCenterTitle">Are you
                                                            sure you want to delete
                                                            this stream?
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="body">After delete this stream user cannot
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
                                                                    href="{{ route('dashboard-stream-delete' ,$stream->id) }}">Delete</a>
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
