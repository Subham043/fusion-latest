@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css" integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.inventory') }}
        </h1>
        <div class="pull-right">
            <a href="{{ route('inventory.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
        </div>
        <div class="clearfix"></div>
    </section>


    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                                <th>{!! Sortable::link('category', 'Category') !!}</th>
				<!--<th>{!! Sortable::link('sub-category', 'Sub-Category') !!}</th>-->
				<th>{!! Sortable::link('color', 'Color') !!}</th>
				<th>{!! Sortable::link('style', 'Style') !!}</th>
				<th>{!! Sortable::link('location', 'Location') !!}</th>
                                <th>{!! Sortable::link('price', trans('fi.price')) !!}</th>
                                <th>{!! Sortable::link('total', 'Quantity') !!}</th>
                                <th>{!! Sortable::link('image', 'View Image') !!}</th>
                               <th>{{ trans('fi.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($itemLookups as $itemLookup)
                                <tr>
                                    <td><span  data-toggle="tooltip" data-placement="bottom" title="{{ $itemLookup->description }}">{{ $itemLookup->name }}</span></td>
                                    <td>{{ $itemLookup->category }}</td>
				    <!--<td>{{ $itemLookup->getAttribute('sub-category') }}</td>-->
				    <td>{{ $itemLookup->color }}</td>
 				    <td>{{ $itemLookup->style }}</td>
					<td>{{ $itemLookup->location }}</td>
                                    <td>{{ $itemLookup->formatted_price }}</td>
                                    <td>{{ $itemLookup->total }}</td>
                                    @if($itemLookup->inventoryImage->count() > 0)
                                    <td><a href="#" type="button" data-toggle="modal" data-target="#modal-mail-invoice-{{$itemLookup->id}}">
                                    <i class="fa fa-eye" style="font-size: 20px;" aria-hidden="true"></i> View</a></td>
                                    @else
                                          <td></td>        
                                    @endif
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('inventory.edit', [$itemLookup->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
						<li><a href="{{ route('inventory.add_image', [$itemLookup->id]) }}"><i class="fa fa-edit"></i> Add Image</a></li>
						<li></li>
                                               <li><a href="{{ route('inventory.delete', [$itemLookup->id]) }}" onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li>
                                            </ul>

                                        </div>
                                    </td>
					@include('inventory._modal_image_viewer',['inv'=>$itemLookup])
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="pull-right">
                    {!! $itemLookups->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(function(){
$('.image-slider').slick({
infinite: true,
  slidesToShow: 1,
  slidesToScroll: 1,
centerMode: true,
//variableWidth: true,
//adaptiveHeight: true,
lazyLoad: 'ondemand',
autoplay: true,
});
});
</script>
@stop
