@extends('layouts.base')
@section('title', 'Active Tickets')
@section('main', 'Ticket Chat')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/css/pages/app-chat.css" />


@endsection
@section('content')

    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="app-chat card overflow-hidden">
                <div class="row g-0">
                    <!-- Chat History -->
                    <div class="col app-chat-history bg-body">
                        <div class="chat-history-wrapper">
                            <div class="chat-history-header border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex overflow-hidden align-items-center">
                                        <i class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2"
                                            data-bs-toggle="sidebar" data-overlay data-target="#app-chat-contacts"></i>
                                        <div class="flex-shrink-0 avatar">
                                            <img src="{{ $findUser->image != '' ? $findUser->image : asset('Placeholder_image.png') }}"
                                                alt="Avatar" class="rounded-circle" data-bs-toggle="sidebar" data-overlay
                                                data-target="#app-chat-sidebar-right" />
                                        </div>
                                        <div class="chat-contact-info flex-grow-1 ms-2">
                                            <h6 class="m-0">{{ $findUser->name }}</h6>
                                            {{-- <small class="user-status text-muted">NextJS developer</small> --}}
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">close</a>
                                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                            <div class="modal-content deleteModal verifymodal">
                                                <div class="modal-header">
                                                    <div class="modal-title" id="modalCenterTitle">Are you
                                                        sure you want to close
                                                        this ticket?
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="body">After closing this ticket user cannot
                                                        send message in this ticket</div>
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
                                                                href="{{ route('dashboard-report-close-report', $ticket->id) }}">Close</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-history-body bg-body" id="chat-history-body">
                                <ul class="list-unstyled chat-history" id="list-unstyled">
                                    @foreach ($conversation as $message)
                                        @if ($message->from == 0)
                                            <li class="chat-message chat-message-right">
                                                <div class="d-flex overflow-hidden">
                                                    <div class="chat-message-wrapper flex-grow-1">
                                                        <div class="chat-message-text">
                                                            <p class="mb-0">{{ $message->message }}</p>
                                                        </div>
                                                        <div class="text-end text-muted mt-1">
                                                            {{--                                                            <i class="ti ti-checks ti-xs me-1 text-success"></i> --}}
                                                            <small>{{ date('d/m/Y, h:i:s', $message->time) }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="user-avatar flex-shrink-0 ms-3">
                                                        <div class="avatar avatar-sm">
                                                            <img src="/assets/img/avatars/1.png" alt="Avatar"
                                                                class="rounded-circle" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @else
                                            <li class="chat-message">
                                                <div class="d-flex overflow-hidden">
                                                    <div class="user-avatar flex-shrink-0 me-3">
                                                        <div class="avatar avatar-sm">
                                                            <img src="{{ $findUser->image != '' ? $findUser->image : asset('Placeholder_image.png') }}"
                                                                alt="Avatar" class="rounded-circle" />
                                                        </div>
                                                    </div>
                                                    <div class="chat-message-wrapper flex-grow-1">
                                                        <div class="chat-message-text">
                                                            <p class="mb-0">{{ $message->message }}</p>
                                                        </div>
                                                        <div class="text-muted mt-1">
                                                            <small>{{ date('d/m/Y, h:i:s', $message->time) }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <div class="chat-history-footer shadow-sm">
                                <form class=" d-flex justify-content-between align-items-center"
                                    id="messageForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $findUser->id }}">
                                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                    <input class="form-control message-input border-0 me-3 shadow-none"
                                        placeholder="Type your message here" name="message" />
                                    <div class="message-actions d-flex align-items-center">

                                        <button type="submit" class="btn btn-primary d-flex send-msg-btn" id="sendMessage">
                                            <i class="ti ti-send me-md-1 me-0" id="sendicon"></i>
                                            <span class="align-middle" id="sending">Send</span>


                                            <span class="align-middle spinner-border text-dark" style="display: none"
                                                id="loader" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Chat History -->
                    <!-- Sidebar Right -->
                    {{-- <div class="col app-chat-sidebar-right app-sidebar overflow-hidden" id="app-chat-sidebar-right">
                        <div
                            class="sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-4 pt-5">
                            <div class="avatar avatar-xl ">
                                <img src="{{ $ticket->user->image != '' ? $ticket->user->image : asset('Placeholder_image.png') }}" alt="Avatar" class="rounded-circle" />
                            </div>
                            <h6 class="mt-2 mb-0">{{$ticket->user->name }}</h6>
                            <span>{{ $ticket->user->user_name }}</span>
                            <i class="ti ti-x ti-sm cursor-pointer close-sidebar d-block" data-bs-toggle="sidebar"
                                data-overlay data-target="#app-chat-sidebar-right"></i>
                        </div>
                        <div class="sidebar-body px-4 pb-4">
                            <div class="my-4">
                                <p class="text-muted text-uppercase">About</p>
                                <p class="mb-0 mt-3">
                                    {{ $ticket->user->bio }}
                                </p>
                            </div>
                            <div class="my-4">
                                <p class="text-muted text-uppercase">Personal Information</p>
                                <ul class="list-unstyled d-grid gap-2 mt-3">
                                    <li class="d-flex align-items-center">
                                        <i class="ti ti-mail"></i>
                                        <span class="align-middle ms-2">{{ $ticket->user->email }}</span>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="ti ti-map-pin"></i>
                                        <span class="align-middle ms-2">{{ $ticket->user->location }}</span>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div> --}}
                    <!-- /Sidebar Right -->

                    <div class="app-overlay"></div>
                </div>
            </div>
        </div>
        <!-- / Content -->
    @endsection

    @section('script')


        <!-- Page JS -->
        <script src="/assets/js/app-chat.js"></script>
        <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
        <script>
            function scrollToBottom() {
                var chatHistory = document.getElementById('chat-history-body');
                chatHistory.scrollTop = chatHistory.scrollHeight;

            }
            $(document).ready(function() {
                $(document).on('submit', '#messageForm', function(e) {
                    e.preventDefault();
                    var loader = $('#loader');
                    var sending = $('#sending');
                    var sendicon = $('#sendicon');

                    loader.show()
                    sendicon.hide();
                    sending.hide();

                    var formData = new FormData(this);
                    $('.message-input').val('');

                    $.ajax({
                        type: "POST",
                        url: '{{ route('dashboard-report-send-message') }}',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            loader.hide()
                            sendicon.show();
                            sending.show();
                            console.log(response);

                            var newMessage = `
                                <li class="chat-message chat-message-right">
                                    <div class="d-flex overflow-hidden">
                                        <div class="chat-message-wrapper flex-grow-1">
                                            <div class="chat-message-text">
                                                <p class="mb-0">${response.message}</p>

                                            </div> 
                                            
                                        </div>
                                        <div class="user-avatar flex-shrink-0 ms-3">
                                            <div class="avatar avatar-sm">
                                                <img src="/assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                `;

                            $('#list-unstyled').append(newMessage);
                            scrollToBottom();
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            });
        </script>
    @endsection
