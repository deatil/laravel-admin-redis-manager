@extends('lake-redis-manager::layout')

@section('page')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Set data</h3> 
        <small></small>
    </div>


    <form class="form-horizontal">

        <div class="box-body">

            <div class="form-group">
                <label for="inputKey1" class="col-sm-2 control-label">Key1</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control key1" id="inputKey1" placeholder="key" value="{{ $params['key1'] ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label for="inputKey2" class="col-sm-2 control-label">Key2</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control key2" id="inputKey2" placeholder="key" value="{{ $params['key2'] ?? '' }}">
                </div>
            </div>

            <div class="form-group tool-storekey hidden">
                <label for="inputStorekey" class="col-sm-2 control-label">Storekey</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control storekey" id="inputAction" placeholder="storekey" value="{{ $params['storekey'] ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>

                <div class="col-sm-10">
                    <select style="width: 120px" class="action" name="action">
                        <option value="sdiff" @if(isset($params['action']) && $params['action'] == 'sdiff')selected="selected"@endif>差集</option>
                        <option value="sinter" @if(isset($params['action']) && $params['action'] == 'sinter')selected="selected"@endif>交集</option>
                        <option value="sunion" @if(isset($params['action']) && $params['action'] == 'sunion')selected="selected"@endif>并集</option>
                        <option value="sdiffstore" @if(isset($params['action']) && $params['action'] == 'sdiffstore')selected="selected"@endif>差集并存储</option>
                        <option value="sinterstore" @if(isset($params['action']) && $params['action'] == 'sinterstore')selected="selected"@endif>交集并存储</option>
                        <option value="sunionstore" @if(isset($params['action']) && $params['action'] == 'sunionstore')selected="selected"@endif>并集并存储</option>
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
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($data as $member)
                            <tr>
                                <td>{{ $member }}</td>
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
        $('.tool-set-data').addClass('active');
        
        $('select').select2();
        
        $('select.action').change(function() {
            var action = $(this).find('option:selected').val();
            if (action == 'sdiffstore' 
                || action == 'sinterstore'
                || action == 'sunionstore'
            ) {
                $('.tool-storekey').removeClass('hidden');
            } else {
                $('.tool-storekey').addClass('hidden');
            }
        });
        
        $('.zsethot-submit').on('click', function (event) {
            event.preventDefault();

            var key1 = $('input.key1').val();
            var key2 = $('input.key2').val();
            var storekey = $('input.storekey').val();
            var action = $('select.action option:selected').val();
            
            if (action == 'sdiffstore' 
                || action == 'sinterstore'
                || action == 'sunionstore'
            ) {
                var params = {
                    key1: key1,
                    key2: key2,
                    storekey: storekey,
                    action: action,
                    _token: LA.token
                };

                $.ajax({
                    url: '{{ route('lake-redis-set-data-store') }}',
                    type: 'POST',
                    data: params,
                    success: function(result) {
                        toastr.success('Push success.');
                        $.pjax.reload('#pjax-container');
                    }
                });
            } else {
                var url = '{{ route("lake-redis-set-data") }}?key1=' + key1 
                    + '&key2=' + key2 
                    + '&storekey=' + storekey 
                    + '&action=' + action;
                
                location.href = url;
            }
        });
        
    });

</script>

@endsection
