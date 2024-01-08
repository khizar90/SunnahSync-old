@extends('layouts.base')
@section('title', 'Posts')
@section('main', 'Posts Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-profile.css" />
@endsection
@section('content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session()->get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('edit'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session()->get('edit') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('delete'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session()->get('delete') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row mb-2">
                @foreach ($posts as $post)
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card postCard">
                            @if ($post->type == 'video')
                                <div class="card-img-top videoPost">
                                    <a href="{{ $post->media[0] }}" target="_blank">

                                        <i class="ti ti-player-play ti-lg"></i>
                                    </a>
                                </div>
                            @else
                                <img class="card-img-top" src="{{ asset($post->media) }}" alt="Card image cap" />
                            @endif

                            <div class="card-body">
                                <div>
                                    <p class="card-text">
                                        <span class="fw-bold">Caption:
                                            <span class="caption fw-light">
                                                {{ $post->caption }}
                                            </span>
                                        </span>
                                    </p>
                                </div>
                                <a href="" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $post->id }}">
                                    <i class="ti ti-trash ti-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="deleteModal{{ $post->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                            <div class="modal-content deleteModal verifymodal">
                                <div class="modal-header">
                                    <div class="modal-title" id="modalCenterTitle">Are you
                                        sure you want to delete
                                        this post?
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="body">After delete this post user cannot
                                        see this post in application</div>
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
                                                href="{{ route('dashboard-delete-post', $post->id) }}">Delete</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div id="paginationContainer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="">
                        <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing
                            {{ $posts->firstItem() }} to {{ $posts->lastItem() }}
                            of
                            {{ $posts->total() }} entries</div>
                    </div>
                    <div class="">
                        <div class="dataTables_paginate paging_simple_numbers" id="paginationLinks">
                            @if ($posts->hasPages())
                                {{ $posts->links('pagination::bootstrap-4') }}
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
    @endsection
