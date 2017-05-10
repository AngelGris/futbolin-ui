@extends('layouts.inner')

@section('content-inner')
<div class="col-md-6" id="home-container">
    <div id="home-strategy">
        <img src="{{ asset('/img/field-large-h.png') }}" />
        @for ($i = 0; $i < 11; $i++)
            @if (empty($formation[$i]))
            <div class="player-container" style="left:{{ $strategy[$i]['left'] }}%;top:{{ $strategy[$i]['top'] }}%;"></div>
            @else
            <div class="player-container {{ strtolower($players[$formation[$i]]['position']) }}" style="left:{{ $strategy[$i]['left'] }}%;top:{{ $strategy[$i]['top'] }}%;">{{ $players[$formation[$i]]['number'] }}</div>
            @endif
        @endfor
        <a href="{{ route('strategy') }}"><div class="overlay"></div></a>
    </div>
</div>
@endsection
