<div class="w-100 d-flex flex-center" data-kt-indicator="on" @isset($id) id="{{ $id }}" @endisset>
    <span class="indicator-label w-100">
        {{ $slot }}
    </span>
    <span class="indicator-progress w-100">
        <span class="d-inline-flex flex-center w-100">
            @include('pages.common-components.loader.loader')
        </span>
    </span>
</div>
