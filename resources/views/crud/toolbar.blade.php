
@if (
    (isset($label) && strlen($label)) ||
    (isset($buttons_left) && count($buttons_left)) ||
    (isset($buttons_right) && count($buttons_right))
    )
    <div style="margin-left:0;" class="btn-toolbar" role="toolbar">
        <div class="pull-left">
            <h3>資料列表：{!! $label !!}</h3>
        </div>
        @if (isset($label) && strlen($label))
        <div class="pull-left">
            <h2>{!! $label !!}</h2>
        </div>
        @endif
        @if (isset($buttons_left) && count($buttons_left))
        <div class="pull-left">
            @foreach ($buttons_left as $button) {!! $button !!}
            @endforeach
        </div>
        @endif
        @if (isset($buttons_right) && count($buttons_right))
        <div class="pull-right">
            @foreach ($buttons_right as $button) {!! $button !!}
            @endforeach
        </div>
        @endif
    </div>
@endif
