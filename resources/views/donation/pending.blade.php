@extends('layouts.base')
@section('title', 'Donations')
@section('main', 'Donation Management')
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
                            Donation Request
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
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>amount</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donations as $donation)
                                    <tr class="odd">
                                        <td>{{ $donation->category->name }}</td>
                                        <td>{{ $donation->user->name }}</td>
                                        <td>{{ $donation->user->email }}</td>
                                        <td>{{ $donation->amount }}$</td>
                                        <td>{{ $donation->created_at }}</td>
                                        <td>

                                            <button class="badge bg-label-secondary btn" data-bs-toggle="modal"
                                                data-bs-target="#verifyModal{{ $donation->id }}" text-capitalized="">Pending

                                            </button>


                                            <div class="modal fade" id="verifyModal{{ $donation->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you
                                                                sure you want to approve
                                                                this donation?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">If you will approve this donation after
                                                                that
                                                                this user will see this donation</div>
                                                        </div>
                                                        <hr class="hr">

                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="first reject">
                                                                    <a href="" class="btn"
                                                                        data-bs-target="#reject{{ $donation->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-dismiss="modal">Reject</a>
                                                                </div>
                                                                <div class="second">
                                                                    <a class="btn text-center" href="{{ route('dashboard-donation-donation-approve' , $donation->id) }}">APPROVED</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" data-bs-backdrop='static'
                                                id="reject{{ $donation->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content verifymodal deleteModal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you
                                                                sure you want to Reject
                                                                this donation?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">
                                                                <form
                                                                    action="{{ route('dashboard-donation-donation-reject' ,$donation->id) }}"
                                                                    id="addBusForm" method="GET">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <input type="hidden" name="user_id" value="{{ $donation->user_id }}">
                                                                        <input type="hidden" name="donation_id" value="{{ $donation->id }}">
                                                                        <div class="col mb-3">
                                                                            <label for="nameWithTitle"
                                                                                class="form-label">Reason</label>
                                                                            <textarea rows="5" 
                                                                                name="reason" class="form-control"
                                                                                required></textarea>
                                                                        </div>

                                                                        <hr class="hr">

                                                                        <div class="container">
                                                                            <div class="row">
                                                                                <div class="first">
                                                                                    <a href=""
                                                                                        class="btn"data-bs-dismiss="modal">cancel</a>
                                                                                </div>
                                                                                <div class="second">
                                                                                    <button class="btn text-center" type="submit">REJECT</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                   

                                                                </form>
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
            </div>
        </div>
    @endsection
