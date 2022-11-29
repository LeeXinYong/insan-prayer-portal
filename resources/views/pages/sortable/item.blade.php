<div class="d-flex align-items-start justify-content-start gap-4">
    @if($item->img_url)
        <img src="{{ $item->img_url }}" alt="{{ $item->title ?? $item->name }}" class="mh-50px mw-100px">
    @endif
    <span class="fw-bold text-dark-75 fs-5">
        {{ $item->title ?? $item->name }}
    </span>
</div>

