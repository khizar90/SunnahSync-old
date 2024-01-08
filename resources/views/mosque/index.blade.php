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
                                    <th>Mosque Name</th>
                                    <th>Scholar Name</th>
                                    <th>Location</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mosques as $mosque)
                                    <tr class="odd">
                                        <td>{{ $mosque->name }}</td>
                                        <td>{{ $mosque->scholar }}</td>
                                        <td>{{ $mosque->location }}</td>
                                        <td>{{ $mosque->user->email }}</td>
                                        <td>{{ $mosque->created_at }}</td>
                                        <td class="account-status text-start">
                                            @if ($mosque->status == 1)
                                                <button class="badge bg-label-success btn"
                                                    class="text-capitalize">Approved</button>
                                            @else
                                                <button class="badge bg-label-secondary btn" data-bs-toggle="modal"
                                                    data-bs-target="#verifyModal{{ $mosque->id }}"
                                                    text-capitalized="">Pending

                                                </button>


                                                <div class="modal fade"  data-bs-backdrop='static' id="verifyModal{{ $mosque->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-sm"
                                                        role="document">
                                                        <div class="modal-content verifymodal">
                                                            <div class="modal-header">
                                                                <div class="modal-title" id="modalCenterTitle">Are you
                                                                    sure you want to approve
                                                                    this mosque?
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="body">If you will approve this mosque after
                                                                    that user will see use this mosque
                                                                </div>
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
                                                                            href="{{ route('dashboard-mosque-mosque-approve' ,$mosque->id) }}">APPROVED</a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
