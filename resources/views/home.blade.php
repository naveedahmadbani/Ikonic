@extends('layouts.app')

@section('content')

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="{{ asset('js/helper.js') }}?v={{ time() }}" defer></script>
  <script src="{{ asset('js/main.js') }}?v={{ time() }}" defer></script>

  <div class="container">
    <x-dashboard />
    <!-- <x-network_connections /> -->
    <div class="row justify-content-center mt-5">
      <div class="col-12">
        <div class="card shadow  text-white bg-dark">
          <div class="card-header">Coding Challenge - Network connections</div>
          <div class="card-body">
            <div class="btn-group w-100 mb-3" role="group" aria-label="Basic radio toggle button group">
              <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
              <label class="btn btn-outline-primary" for="btnradio1" id="get_suggestions_btn" onclick="data('suggestions')">Suggestions ()</label>

              <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
              <label class="btn btn-outline-primary" for="btnradio2" id="get_sent_requests_btn" onclick="data('sent_requests')">Sent Requests ()</label>

              <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
              <label class="btn btn-outline-primary" for="btnradio3" id="get_received_requests_btn" onclick="data('received')">Received
                Requests()</label>

              <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off">
              <label class="btn btn-outline-primary" for="btnradio4" id="get_connections_btn" onclick="data('connections')">Connections ()</label>
            </div>
            <hr>
            <div id="contents" style="display:none">
            </div>
            <div id="loading" style="display:none">
              <div id="connections_in_common_skeleton">
                <br>
                <span class="fw-bold text-white">Loading Skeletons</span>
                <div class="px-2">
                  @for ($i = 0; $i < 10; $i++)
                    <x-skeleton />
                  @endfor
                </div>
              </div>
            </div>
            <div id="commoncontents" style="display:none">
            </div>
            <input type="hidden" id="sugestion_load" value="10">
           
<script type="text/javascript">
    $(document).ready(function() { 
      $.ajax({
        url: "{{route('getData')}}",
        type: "get",
        data: {
            type: 'suggestions',
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          // $('#commoncontents').hide();
          $('#contents').show();
          $('#contents').html(response);
        },
    });
  });
  function data(d_type){
    $('#loading').show();
    $.ajax({
        url: "{{route('getData')}}",
        type: "get",
        data: {
            type: d_type,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          $('#commoncontents').hide();
          $('#loading').hide();
          $('#contents').show();
          $('#contents').html(response);
          $('#sugestion_load').val(10);
        },
    });
  }
  function loadMoreF(d_type){
    let skip = Number($('#sugestion_load').val());
    $.ajax({
        url: "{{route('getData')}}",
        type: "get",
        data: {
            type: d_type,
            skip: skip,
            get: 10,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          $('#sugestion_load').val(Number(skip)+10);
          $('#load_more_connections_in_common_').remove();
          $('#load_more_connections_in_common_1').remove();
          $('#contents').append(response);
        },
    });
  }
  function sendFrndRequest(user_id)
  {
    $.ajax({
        url: "{{route('send_frnd_request')}}",
        type: "get",
        data: {
          user_id: user_id,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          if(response){
            data('suggestions');
          }
        },
    });
  } 
  function aceptFrndRequest(user_id)
  {
    $.ajax({
        url: "{{route('accept_frnd_request')}}",
        type: "get",
        data: {
          user_id: user_id,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          if(response){
            data('received');
          }
        },
    });
  } 
  function withdrawRequest(user_id)
  {
    $.ajax({
        url: "{{route('withdraw_frnd_request')}}",
        type: "get",
        data: {
          user_id: user_id,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          if(response){
            data('sent_requests');
          }
        },
    });
  } 
  function removeFriend(user_id)
  {
    $.ajax({
        url: "{{route('remove_connection')}}",
        type: "get",
        data: {
          user_id: user_id,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          if(response){
            data('connections');
          }
        },
    });
  } 
  function commonFrnd(user_id)
  {
    $.ajax({
        url: "{{route('common_frnd')}}",
        type: "get",
        data: {
          user_id: user_id,
            _token: '{{csrf_token()}}'
        },
        success: function success(response) {
          if(response){
            $('#commoncontents').show();
            $('#commoncontents').html(response);
        }
        },
    });
  } 
</script>
@endsection
