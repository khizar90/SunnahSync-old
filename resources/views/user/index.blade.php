@extends('layouts.base')
@section('title', 'Users')
@section('main', 'Accounts Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span>Session</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">21,459</h4>
                                        <span class="text-success">(+29%)</span>
                                    </div>
                                    <span>Total Users</span>
                                </div>
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="ti ti-user ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span>Paid Users</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">4,567</h4>
                                        <span class="text-success">(+18%)</span>
                                    </div>
                                    <span>Last week analytics </span>
                                </div>
                                <span class="badge bg-label-danger rounded p-2">
                                    <i class="ti ti-user-plus ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span>Active Users</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">19,860</h4>
                                        <span class="text-danger">(-14%)</span>
                                    </div>
                                    <span>Last week analytics</span>
                                </div>
                                <span class="badge bg-label-success rounded p-2">
                                    <i class="ti ti-user-check ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span>Pending Users</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">237</h4>
                                        <span class="text-success">(+42%)</span>
                                    </div>
                                    <span>Last week analytics</span>
                                </div>
                                <span class="badge bg-label-warning rounded p-2">
                                    <i class="ti ti-user-exclamation ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Users List Table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-3">Users List</h5>
                        <div class="">
                            {{-- <button class="btn btn-primary btn-sm" id="clearFiltersBtn">Clear Filter</button> --}}
                        </div>
                    </div>



                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session()->get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif



                    {{-- <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                        <div class="col-md-4 user_status">
                            <h6>Account Status</h6>

                            <select id="FilterTransaction" class="form-select text-capitalize">
                                <option value="">All</option>
                                <option value="Pending" class="text-capitalize"
                                    @if ($accountStatus === 'pending') selected @endif>Pending</option>
                                <option value="Approved" class="text-capitalize"
                                    @if ($accountStatus === 'approved') selected @endif>Approved</option>
                            </select>
                        </div>

                        <div class="col-md-4 user_role">
                            <h6>Devices</h6>
                            <select id="UserRole" class="form-select text-capitalize">
                                <option value="">All</option>
                                <option value="iOS" @if ($userDevice === 'ios') selected @endif>iOS</option>
                                <option value="android" @if ($userDevice === 'android') selected @endif>Android</option>
                            </select>
                        </div>

                        <div class="col-md-4 user_plan">
                            <h6>Category</h6>

                            <select id="FilterCategory" class="form-select text-capitalize">
                                <option value="">All</option>
                                <option value="Ophthalmology" @if ($category === 'ophthalmology') selected @endif>
                                    Ophthalmology</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">


                        <div class="row me-2">
                            <div class="col-md-2">
                                <div class="me-3">
                                    <div class="dataTables_length" id="DataTables_Table_0_length"><label>

                                            <select name="entries" id="entries" aria-controls="" class="form-select">
                                                <option value="20">20
                                                </option>
                                                <option value="50">50
                                                </option>
                                                <option value="100">
                                                    100</option>
                                                <option value="500">
                                                    500</option>
                                                <option value="1000">
                                                    1000</option>
                                                <option value="all">
                                                    All</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div
                                    class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label class="user_search">
                                            <input type="text" class="form-control" id="searchInput"
                                                placeholder="Search.." value="" aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                    <div class="dt-buttons btn-group flex-wrap">
                                        <div class="btn-group">
                                            <button class="btn btn-secondary buttons-collection btn-label-secondary mx-3"
                                                data-bs-toggle="modal" data-bs-target="#modalContainer" type="button">
                                                <span><i class="ti ti-screen-share me-1 ti-xs"></i>Export</span>
                                                <span class="dt-down-arrow"></span>
                                            </button>
                                        </div>
                                        <button class="btn btn-secondary btn-primary" tabindex="0"
                                            aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvasAddUser"><span><i
                                                    class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                                                    class="d-none d-sm-inline-block">Add New User</span></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table border-top dataTable" id="usersTable">
                            <thead>
                                <tr>

                                    <th>User</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>platform</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr class="odd">

                                        <td class="sorting_1">
                                            <div class="d-flex justify-content-start align-items-center user-name">
                                                @if ($user->image)
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><img
                                                                src="{{ asset($user->image != '' ? $user->image : 'user.png') }}"
                                                                alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><span
                                                                class="avatar-initial rounded-circle bg-label-danger">
                                                                {{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                        </div>
                                                    </div>
                                                @endif



                                                <div class="d-flex flex-column"><a href=""
                                                        class="text-body text-truncate"><span
                                                            class="fw-semibold user-name-text">{{ $user->name }}</span></a><small
                                                        class="text-muted">&#64;{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="user_name">{{ $user->phone }}</td>


                                        <td class="user-category">{{ $user->type }}</td>

                                        <td class="user-category">{{ $user->platform ?: 'No platform yet' }}</td>







                                        <td class="" style="">
                                            <div class="d-flex align-items-center">
                                                <a href="" class="text-body delete-record"><i
                                                        class="ti ti-eye"></i></a>

                                                <a href="" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $user->id }}"
                                                    class="text-body delete-record">
                                                    <i class="ti ti-trash x`ti-sm mx-2"></i>
                                                </a>




                                            </div>


                                            <div class="modal fade" data-bs-backdrop='static'
                                                id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content deleteModal verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you
                                                                sure you want to delete
                                                                this account?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">After delete this account user cannot
                                                                access anything in application</div>
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
                                                                        href="{{ url('admin/user/delete', $user->id) }}">Delete</a>
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
                                        aria-live="polite">Showing {{ $users->firstItem() }} to
                                        {{ $users->lastItem() }}
                                        of
                                        {{ $users->total() }} entries</div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="paginationLinks">
                                        {{-- <h1>{{ @json($data) }}</h1> --}}
                                        @if ($users->hasPages())
                                            {{ $users->links('pagination::bootstrap-4') }}
                                        @endif


                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Offcanvas to add new user -->

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
                    aria-labelledby="offcanvasAddUserLabel">
                    <div class="offcanvas-header">
                        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add User</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                        <form>

                            <div class="mb-3">
                                <label class="form-label" for="add-user-company">User Name</label>
                                <input type="text" class="form-control" placeholder="JohnDoe9" aria-label="jdoe1"
                                    name="user_name" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="add-user-fullname">Full Name</label>
                                <input type="text" class="form-control" placeholder="John Doe" name="name"
                                    aria-label="John Doe" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="add-user-email">Email</label>
                                <input type="text" class="form-control" placeholder="john.doe@example.com"
                                    aria-label="john.doe@example.com" name="email" />
                            </div>


                            <div class="mb-3">
                                <label class="form-label" for="add-user-company">Doctor ID</label>
                                <input type="text" class="form-control" placeholder="Doctor ID" aria-label="jdoe1"
                                    name="doctor_id" />
                            </div>


                            <div class="mb-3">
                                <label class="form-label" for="add-user-company">Password</label>
                                <input type="password" class="form-control" placeholder="password" aria-label="jdoe1"
                                    name="password" />
                            </div>

                            <button type="submit" class="btn btn-primary me-sm-3 me-1 ">Create</button>

                        </form>
                    </div>
                </div>


                <div class="modal fade" id="modalContainer" data-bs-backdrop='static' tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content verifymodal">
                            <div class="modal-header">
                                <div class="modal-title" id="modalCenterTitle">Are you sure you want to export all users
                                    in CSV formart?</div>

                            </div>
                            <div class="modal-body">
                                <div class="body"> After clicking on export button users list will export in CSV format
                                </div>
                            </div>




                            <hr class="hr">
                            <div class="container">
                                <div class="row">
                                    <div class="first">
                                        <a class="btn" data-bs-dismiss="modal" style="color: #a8aaae ">Cancel</a>
                                    </div>
                                    <div class="second">
                                        <button type="submit" class="btn" onclick="dismissModal()">Export</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    @endsection

    @section('script')

    @endsection
