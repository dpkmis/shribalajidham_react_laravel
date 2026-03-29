@if(!empty($breadcrumbs = \App\Services\Breadcrumbs::get()))
    <!--breadcrumb-->
    @php        
        $titles = array_map(fn($crumb) => $crumb['title'], $breadcrumbs);
    @endphp
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">{{ last($titles) }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    @foreach($breadcrumbs as $crumb)
                        @if ($loop->last)
                            <li class="breadcrumb-item"><a href="javascript:;">{{ $crumb['title'] }}</a></li>
                        @else
                            <li class="breadcrumb-item text-secondary" aria-current="page">
                                <a href="{{ $crumb['url'] ?? '#' }}">{{ $crumb['title'] }}</a>
                            </li>
                        @endif    
                    @endforeach
                </ol>
            </nav>
        </div>      
    </div>
    <!--end breadcrumb-->
@endif