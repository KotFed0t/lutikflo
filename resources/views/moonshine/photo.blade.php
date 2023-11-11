{{--@dd($product->images)--}}
{{--<input type="file">--}}

{{--<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>--}}
{{--<script>--}}
{{--    axios.get('/get-test').then(response => {--}}
{{--        alert(response.data)--}}
{{--    }).catch(err => {--}}
{{--        alert(err.data)--}}
{{--    })--}}
{{--</script>--}}

<img src="{{ config('app_url').'/storage/'.$product->main_img }}" style="width: 100px; height: 100px; border-radius: 10px">

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>


<input type="file" name="before_crop_image" id="before_crop_image" accept="image/*"/>


<div id="imageModel" class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" hidden>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Обрежьте изображение</h5>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="image_demo" style="width:350px; margin-top:30px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary crop_image">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
            }
        });

        $image_crop = $('#image_demo').croppie({
            enableExif: true,
            viewport: {
                width: 200,
                height: 200,
                type: 'square' //circle
            },
            boundary: {
                width: 300,
                height: 300
            }
        });

        $('#before_crop_image').on('change', function () {
            var reader = new FileReader();
            reader.onload = function (event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                }).then(function () {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
            $('#imageModel').attr('hidden', false);

        });

        $('.crop_image').click(function (event) {
            let crop_points = $image_crop.croppie('get').points;
            let x = crop_points[0]
            let y = crop_points[1]
            let width = crop_points[2] - crop_points[0]
            let height = crop_points[3] - crop_points[1]
            let formData = new FormData;
            formData.append('image', $('#before_crop_image').prop('files')[0])
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'))
            formData.append('x', x)
            formData.append('y', y)
            formData.append('width', width)
            formData.append('height', height)
            formData.append('product_id', {{ $product->id }})

            $.ajax({
                url: "{{ route('crop-and-store-image') }}",
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    $('#imageModel').attr('hidden', true);
                    alert('Crop image has been uploaded');
                    console.log(data)
                }
            });
        });

        $('.btn-close').click(function (event) {
            $('#imageModel').attr('hidden', true);
        });
    });
</script>
