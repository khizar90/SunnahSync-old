@extends('layouts.base')
@section('title', 'Quran')
@section('main', 'Quran Management')
@section('link')
    <link rel="stylesheet" href="/panel-v2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/panel-v2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />

@endsection
@section('content')

    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center h-px">
                        <form class="w-px-500 border rounded p-3 p-md-5" method="POST" action="{{ Request::url() }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    {{ session()->get('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session()->has('edit'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    {{ session()->get('edit') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session()->has('delete'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    {{ session()->get('delete') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <h3 class="mb-4 ">Add Para</h3>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Name</label>
                                <div class="col-sm-12">
                                    <input type="text" required name="name" id="" class="form-control"
                                        placeholder="Name" />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Name in Arabic</label>
                                <div class="col-sm-12">
                                    <input type="text" required name="arabic" id="" class="form-control"
                                        placeholder="Name in Arabic" />
                                </div>
                            </div>

                            <div class="row mb-3 form-password-toggle">
                                <label class="col-sm-12 col-form-label" for="form-alignment-password">Para PDF</label>
                                <div class="col-sm-12">
                                    <input class="form-control" required name="pdf" type="file" id="formFile" accept="application/pdf">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->
    @endsection

    @section('script')

        <script src="/panel-v2/assets/js/app-user-list.js"></script>

    @endsection
