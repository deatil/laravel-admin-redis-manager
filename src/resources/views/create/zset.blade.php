@extends('lake-redis-manager::layout')

@section('page')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Create</h3> <small></small>
        </div>


        <form class="form-horizontal" method="post" action="{{ route('lake-redis-store-key') }}" pjax-container>

            <div class="box-body">

                <div class="form-group">
                    <label for="inputKey" class="col-sm-2 control-label">Key</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputKey" placeholder="key" name="key">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputExpire" class="col-sm-2 control-label">Expires</label>

                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="ttl" id="inputExpire" min="-1" value="-1">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Member</label>

                    <div class="col-sm-10">
                        <input class="form-control" name="member">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Score</label>

                    <div class="col-sm-10">
                        <input class="form-control" name="score">
                    </div>
                </div>

                {{ csrf_field() }}
                <input type="hidden" name="conn" value="{{ $conn }}">
                <input type="hidden" name="type" value="zset">
                <input type="hidden" name="redirect" value="1">
            </div>

            <div class="box-footer">
                <button type="reset" class="btn btn-default pull-right">Reset</button>
                <button class="btn btn-info col-sm-offset-2">Submit</button>
            </div>

        </form>

    </div>
    <!-- /.box-body -->

@endsection