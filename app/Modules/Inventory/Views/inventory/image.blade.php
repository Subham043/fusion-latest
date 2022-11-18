@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

	<style>
		/*.member-preview {
  				width: 192px;
  				height: 192px;
  				position: relative;
  				border-radius: 100%;
  				border: 6px solid #F8F8F8;
  				box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
			}*/
		.member-preview > div {
  				/*width: 100%;*/
  				height: 300px;
  				/*border-radius: 100%;*/
  				background-size: cover;
  				background-repeat: no-repeat;
  				background-position: center;
			}
		.img-delete {
  				display: inline-block;
  				width: 34px;
  				height: 34px;
      				margin: 0;
    				padding: 0;
  				cursor: pointer;
  				font-weight: normal;
  				transition: all 0.2s ease-in-out;
			}

		.remove_icon{
 				padding: 5px;
 				color: red;
 				font-weight: 900;
   				top: 10px;
  				left: 0;
  				right: 0;
			}
	</style>

	{!! Form::close() !!}
{!! Form::open(array('url' => route('inventory.store_image', $id), 'onsubmit'=>'this.form.submit()', 'enctype'=>'multipart/form-data')) !!}
    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.inventory_form') }}
        </h1>
        <div class="pull-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content" style="min-height:140px;">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body row">
	
			<div class="col-lg-12">
                        <div class="form-group">
                            <label class="">Image: </label>
                           <!--- {!! Form::file('image', null, ['id' => 'image', 'class' => 'form-control' , 'multiple'=>'null' ]) !!}--->
				<input type="file" name="image[]" id="imageUpload" class="form-control" multiple>
                        </div>
			</div>

			<div class="col-md-12">
                    		<div class="member-preview row">
            				<div class="imagePreview col-md-4">
            				</div>
       				 </div>  
                	</div>


			
                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
	@if(count($itemLookupImage)>0)
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body row">
						@foreach($itemLookupImage as $itemLookupImage)
						<div class="col-lg-3 mb-3 box box-alert" style="width:25%;">
							<div class="box-body" style="text-align:right;">
							<a href="{{route('inventory.delete_image', $itemLookupImage->id)}}" class="btn btn-danger mb-1">Delete</a>
							<img src="{{asset('assets/upload/inventory/'.$itemLookupImage->image)}}" style="widht:100%;object-fit:contain;" />
							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</section>
	@endif


<script type="text/javascript">
    function readURL(input) {
    if (input.files && input.files[0]) {
        var filesAmount = input.files.length;
        console.log(filesAmount);
        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var d = $('.imagePreview:first').clone()
                d.css('background-image', 'url('+e.target.result +')');
                d.html('<span class="img-delete"><b class="remove_icon">X</b></span>');
                d.hide();
                d.fadeIn(650);
                $('.member-preview').append($(d));
                $(".img-delete").click(function(){
                  $(this).parent(".imagePreview").remove();
                });
            }

            reader.readAsDataURL(input.files[i]);
        }
    }
}
$("#imageUpload").change(function() {
    readURL(this);
});
</script>


@stop


