@extends('layouts.base')
@section('title', 'Hadiths')
@section('main', 'Hadiths Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Users List Table -->
            <div class="card">
                <div class="card-header ">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title ">
                            All Hadiths
                        </h5>
                        <div
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">

                            <div class="dt-buttons btn-group flex-wrap">
                                <a href="{{ route('dashboard-hadith-add') }}" class="btn btn-primary"><span><i
                                            class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                                            class="d-none d-sm-inline-block"></span>Add new Hadith</span></a>

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



                                </tr>
                            </thead>
                            <tbody id="searchResults">
                                @foreach ($hadiths as $hadith)
                                    <tr class="odd">

                                        <td>
                                            <h6 class="fw-bold">
                                                Book : {{ $hadith->book->name }}
                                            </h6>

                                            <h6 class="fw-bold">
                                                Title : {{ $hadith->category->title }}
                                            </h6>
                                            <p>Chapter : {{ $hadith->chapter }}</p>
                                            {{ $hadith->hadith }}
                                            <br>

                                            <button data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $hadith->id }}" class="btn btn-danger btn-sm" style="float: right">Delete</button>

                                            <div class="modal fade" id="deleteModal{{ $hadith->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content deleteModal verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you
                                                                sure you want to delete
                                                                this hadith?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">After delete this hadith user cannot
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
                                                                        href="{{ route('dashboard-hadith-delete' ,$hadith->id) }}">Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
    
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach



                            </tbody>
                        </table>


                        <div id="paginationContainer">
                            <div class="row mx-2">
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status"
                                        aria-live="polite">Showing {{ $hadiths->firstItem() }} to
                                        {{ $hadiths->lastItem() }}
                                        of
                                        {{ $hadiths->total() }} entries</div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="paginationLinks">
                                        {{-- <h1>{{ @json($data) }}</h1> --}}
                                        @if ($hadiths->hasPages())
                                            {{ $hadiths->links() }}
                                        @endif


                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <!-- Offcanvas to add new user -->








            </div>
        </div>
    @endsection
    @section('script')


    @endsection
