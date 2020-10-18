<div class="row">

    <div class="col-md-3">
        <div class="box with-border">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Connections
                    
                    <a href="{{ url(config('admin.route.prefix').'/lake-redis/connection') }}">
                        <small>
                            <code>Setting</code>
                        </small>
                    </a>
                </h3>

                <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    @foreach($connections as $name => $connection)
                        @if(!empty($connection['host']))
                        <li>
                            <a href=" {{ route('lake-redis-index', ['conn' => $name]) }}">
                                <i class="fa fa-database"></i> {{ $name }}  &nbsp;&nbsp;<small>[{{ $connection['host'].':'.$connection['port'] }}]</small>
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <!-- /.box-body -->
        </div>

    </div>

    <div class="col-md-9">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Connection tip</h3> 

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            
            <div class="box-body">
                <div class="text-center" style="padding: 150px 20px;">
                    Pleace select one connection.
                </div>
            </div>
        </div>
    </div>

</div>

