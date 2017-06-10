@if (!empty($strategy))
<div class="col-md-6" id="home-container">
    <div id="home-strategy">
        <img src="{{ asset('/img/field-large-h.png') }}" />
        @for ($i = 0; $i < 11; $i++)
            <div class="player-container {{ strtolower($strategy[$i]['position']) }}" style="left:{{ $strategy[$i]['left'] }}%;top:{{ $strategy[$i]['top'] }}%;">
                {{ $strategy[$i]['number'] }}
                <div class="status">
                    @if ($strategy[$i]['retiring'])
                    <span class="fa fa-user-times" style="color:#f00;"></span>
                    @endif
                </div>
            </div>
        @endfor
        @if (!empty($overlay))
        <a href="{{ route('strategy') }}"><div class="overlay"></div></a>
        @endif
    </div>
</div>
@endif