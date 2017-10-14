@if (!empty($strategy))
<div class="col-md-6" id="home-container">
    <div id="home-strategy">
        <img src="{{ asset('/img/field-large-h.png') }}" />
        @for ($i = 0; $i < 11; $i++)
            <div class="player-container {{ strtolower($strategy[$i]['position']) }}" style="left:{{ $strategy[$i]['left'] }}%;top:{{ $strategy[$i]['top'] }}%;">
                {{ $strategy[$i]['number'] }}
                @if (isset($strategy[$i]['retiring']))
                <div class="status">
                    @if ($strategy[$i]['retiring'])
                    <span class="fa fa-user-times" style="color:#f00;"></span>
                    @endif
                    @if ($strategy[$i]['cards'])
                    <span class="fa fa-square" style="color:#ff0;"></span>
                    @endif
                    @if ($strategy[$i]['suspended'])
                    <span class="fa fa-square" style="color:#f00;"></span>
                    @endif
                    @if ($strategy[$i]['recovery'])
                    <span class="fa fa-medkit" style="color:#f00;"></span>
                    @endif
                    @if ($strategy[$i]['upgraded'])
                    <span class="fa fa-arrow-circle-up" style="color:#080;"></span>
                    @endif
                    @if ($strategy[$i]['tired'])
                    <span class="fa fa-arrow-down" style="color:#f00;"></span>
                    @endif
                </div>
                @endif
            </div>
        @endfor
        @if (!empty($overlay))
        <a href="{{ route('strategy') }}"><div class="overlay"></div></a>
        @endif
    </div>
</div>
@endif