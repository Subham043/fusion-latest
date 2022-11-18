<style>
.image-slider .slick-prev, .image-slider .slick-next{
position: absolute;
}
.image-slider .slick-prev{
    top: 50%;
    left: 0;
    z-index: 10;
}
.image-slider .slick-next{
    bottom: 46%;
    right: 0;
    z-index: 10;
}

.slick-initialized .slick-slide{
height: 100%;
}

.image-slider.slick-slider{
max-height: 300px;
}

.image-slider img{
height:200px;
object-fit:contain;
margin: auto;
}

</style>
<div class="modal fade" id="modal-mail-invoice-{{$inv->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Image Viewer</h4>
            </div>
            <div class="modal-body">
			@if($inv->inventoryImage->count()>0)
			<div class="image-slider">
				@foreach($inv->inventoryImage as $itemLookupImage)
  				<div><img src="{{asset('assets/upload/inventory/'.$itemLookupImage->image)}}" /></div>
				@endforeach
			</div>
			@else

			<p>There are no images attatched to this inventory, kindly add it to view it.</p>
			@endif
            </div>
        </div>
    </div>
</div>
