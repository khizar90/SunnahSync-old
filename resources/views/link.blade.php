@extends('layouts.base')
@section('title', 'Links')
@section('main', 'Link Management')
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
                        <h5 class="card-title mb-3">Link List</h5>
                        <div class="">
                            <button class="btn btn-secondary add-new btn-primary" tabindex="0"
                                aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                                data-bs-target="#addNewBus"><span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                                        class="d-none d-sm-inline-block">Add New Link</span></span></button>
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
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($links as $link)
                                    <tr class="odd">
                                        <td class="">
                                            {{ $link->name }}
                                        </td>
                                        <td class="">
                                            <a class="text-body text-truncate" href="{{ $link->link }}" target="_blank">
                                                {{ $link->link }}</a>
                                        </td>
                                        <td class="" style="">
                                            <div class="d-flex align-items-center">
                                                <a href="" class="text-body " data-bs-toggle="modal" data-bs-target="#edit{{ $link->id }}">
                                                    <i class="ti ti-edit">
                                                    </i>
                                                </a>
                                                <a data-bs-toggle="modal" data-bs-target="#deleteModal{{ $link->id }}"
                                                    class="text-body ">
                                                    <i class="ti ti-trash x`ti-sm mx-2"></i>
                                                </a>
                                            </div>

                                            <div class="modal fade" data-bs-backdrop='static' id="edit{{ $link->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalCenterTitle">Edit Link</h5>
                                                        </div>
                                                        <form action="{{ route('dashboard-edit-link' , $link->id) }}" id="addBusForm" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col mb-3">
                                                                        <label for="nameWithTitle" class="form-label">Name</label>
                                                                        <input type="text" id="nameWithTitle" name="name" class="form-control"
                                                                            placeholder="Name" required value="{{ $link->name }}" />
                                                                    </div>
                            
                            
                            
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col mb-3">
                                                                        <label for="nameWithTitle" class="form-label">Link</label>
                                                                        <input type="text" id="nameWithTitle" name="link" class="form-control"
                                                                            placeholder="Link" required value="{{ $link->link }}"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-label-secondary" id="closeButton"
                                                                    data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">Edit Link</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>


                                            <div class="modal fade" id="deleteModal{{ $link->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content deleteModal verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you sure you
                                                                want to delete
                                                                this link?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">After deleting the link you will not able to
                                                                use this</div>
                                                        </div>
                                                        <hr class="hr">

                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="first">
                                                                    <a href="" class="btn" data-bs-dismiss="modal"
                                                                        style="color: #a8aaae ">Cancel</a>
                                                                </div>
                                                                <div class="second">
                                                                    <a class="btn text-center"
                                                                        href="{{ route('dashboard-delete-link', $link->id) }}">Delete</a>
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
                    </div>
                </div>
                <div class="modal fade" id="addNewBus" data-bs-backdrop='static' tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Add New link</h5>
                            </div>
                            <form action="{{ route('dashboard-add-link') }}" id="addBusForm" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="nameWithTitle" class="form-label">Name</label>
                                            <input type="text" id="nameWithTitle" name="name" class="form-control"
                                                placeholder="Name" required />
                                        </div>



                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="nameWithTitle" class="form-label">Link</label>
                                            <input type="text" id="nameWithTitle" name="link" class="form-control"
                                                placeholder="Link" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" id="closeButton"
                                        data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">Add Link</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script>
            $(document).ready(function() {
                $('#closeButton').on('click', function(e) {
                    $('#addBusForm')[0].reset();
                });

            });
        </script>
    @endsection
