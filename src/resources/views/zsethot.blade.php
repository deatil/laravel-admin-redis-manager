@extends('lake-redis-manager::layout')

@section('page')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">ZSet hot</h3> 
        <small></small>
    </div>


    <form class="form-horizontal">

        <div class="box-body">

            <div class="form-group">
                <label for="inputKey" class="col-sm-2 control-label">Key</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control key" id="inputKey" placeholder="key" value="{{ $key ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>

                <div class="col-sm-10">
                    <select style="width: 120px" class="order" name="order">
                        <option value="asc" selected="selected">顺序</option>
                        <option value="desc">倒叙</option>
                    </select>
            
                    <button type="button" class="btn btn-primary zsethot-submit">Submit</button>
                </div>
            </div>

            <hr>

            <div class="form-group">

                <label class="col-sm-2 control-label">Members</label>

                <div class="col-sm-10">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>member</th>
                            <th>score</th>
                            <th width="80px;">action</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($data as $member => $score)
                            <tr>
                                <td>{{ $member }}</td>
                                <td>
                                    <a class="zset-member" data-type="textarea" data-pk="{{ $member }}" data-url="{{ route('lake-redis-update-key', ['type' => 'zset', 'conn' => $conn, 'key' => $key]) }}">{{ $score }}</a></td>
                                <td>
                                    <a href="#" class="text-red remove-key" data-member="{{ $member }}"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    
                    @if (empty($data))
                        <div class="text-center" style="padding: 20px;">
                            Empty list or set.
                        </div>
                    @endif
                    
                </div>
            </div>

        </div>

    </form>

</div>
<!-- /.box-body -->

<script>

    $(function () {
        $('.tool-zset-hot').addClass('active');
        
        $('select').select2();
        
        $('.zsethot-submit').on('click', function (event) {
            event.preventDefault();

            var key = $('input.key').val();
            var order = $('select.order option:selected').val();
            
            var url = '{{ route("lake-redis-zset-hot") }}?key=' + key + '&order=' + order;
            
            location.href = url;
        });
        
        $('.remove-key').on('click', function (e) {
            e.preventDefault();
            var key = $('input.key').val();
            var member = $(this).data('member');

            swal({
                    title: "Remove from list ?",
                    type: "error",
                    showCancelButton: true
                })
                .then(function(){

                    var params = {
                        key: key,
                        member: member,
                        connection: "{{ $conn }}",
                        type: 'zset',
                        _token: LA.token
                    };

                    $.ajax({
                        url: '{{ route('lake-redis-remove-item') }}',
                        type: 'DELETE',
                        data: params,
                        success: function(result) {
                            toastr.success('List item removed');
                            $.pjax.reload('#pjax-container');
                        }
                    });
                });
        });
        
        $('.zset-member').editable();
        
    });

</script>

@endsection
