@props([
    'label',
    'value' => null,
    'type' => null,  
    'limit' => 80,   
    'col' => 'col-md-3',  // default column size
    'user_class' => null
])

@php
    // Convert booleans and nulls into readable form
    if (is_bool($value)) {
        $value = $value ? 'Yes' : 'No';
    } elseif (is_null($value)) {
        $value = '';
    }

    // Auto-detect type if not explicitly given
    
    if (!$type) {
        if (is_array($value)) {
            $type = 'badge';
        } elseif (is_string($value) && strlen($value) > $limit) {
            $type = 'textarea';
        } else {
            $type = 'input';
        }
    }

    // Convert comma-separated values to array for badge type
    if ($type === 'badge' && is_string($value)) {
        $value = array_filter(array_map('trim', explode(',', $value)));
    }
@endphp

@if(!empty($value))
    <div class="{{ $col }} mb-3">
        <div class="info-label">{{ $label }}</div>        
        @switch($type)
                @case('textarea')
                    <p class="small text-muted mb-0">{{ $value }}</p>
                    
                @break
                @case('tag')
                    <div class="badge rounded-pill {{$user_class}} p-2 text-uppercase px-3 __web-inspector-hide-shortcut__">{{ $value }}</div>                    
                @break    
                @case('badge')
                    <div>
                        @foreach((array)$value as $v)
                            <span class="badge bg-primary me-1">{{ $v }}</span>
                        @endforeach
                    </div>
                @break
            @case('image')
                <div class="row">
                    @foreach((array)$value as $img)
                        <div class="col-6 col-md-3 mb-2">
                            <img src="{{ $img }}" class="img-fluid rounded {{ $class }}" alt="Image">
                        </div>
                    @endforeach
                </div>
                @break

            @default
                <div class="fw-semibold text-break">{{ $value }}</div>                
        @endswitch
    </div>
@endif
