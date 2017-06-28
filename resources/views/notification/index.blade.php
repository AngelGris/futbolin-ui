@extends('layouts.inner')

@section('javascript-inner')
<script type="text/javascript">
$(function() {
    $('.notification').click(function() {
        $(this).removeClass('unread');
        $('.notification').removeClass('selected');
        $(this).addClass('selected');

        $.ajax({
            'method': 'GET',
            'url': '/notificacion/' + $(this).data('id'),
            'dataType': 'json'
        }).done(function(data) {
            $('.unread-count').text(data.unread);
            $('#reader-title').text(data.title);
            $('#reader-message').html(data.message);
        });
    });
});
</script>
@endsection

@section('content-inner')
<div class="messagepanel">
    <div class="messagecontent">
        <div class="messageleft">
            <ul class="msglist">
                @foreach($_notifications as $notification)
                <li class="notification{{ ($notification['read_on'] == NULL) ? ' unread' : '' }}" data-id="{{ $notification['id'] }}">
                    <div class="summary">
                        <h4>{{ $notification['title'] }}</h4>
                        <span class="date"><small>{{ $notification['published'] }}</small></span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div><!--messageleft-->
        <div class="messageright">
            <div class="messageview">
                <h1 id="reader-title" class="subject"></h1>
                <div id="reader-message" class="msgbody">
                </div><!--msgbody-->
            </div>
        </div><!--messageright-->
    </div><!--messagecontent-->
</div><!--messagepanel-->
@endsection
