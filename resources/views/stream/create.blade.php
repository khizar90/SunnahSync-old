@extends('layouts.base')
@section('title', 'Stream')
@section('main', 'Stream Management')
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
                            <h3 class="mb-4 ">Live Stream</h3>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Title</label>
                                <div class="col-sm-12">
                                    <input type="text" required name="title" id="" class="form-control"
                                        placeholder="Title" />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Link</label>
                                <div class="col-sm-12">
                                    <input type="text" required name="link" id="" class="form-control"
                                        placeholder="Link" />
                                </div>
                            </div>

                            <div class="row mb-3 form-password-toggle">
                                <label class="col-sm-12 col-form-label" for="form-alignment-password">Stream Image</label>
                                <div class="col-sm-12">
                                    <input class="form-control" required name="image" type="file" id="formFile" accept="image/*">
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
