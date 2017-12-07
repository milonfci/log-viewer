<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Log Viewer by SRMILON</title>

    <!-- Bootstrap CDN-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

    {{--Datatable CDN--}}
    <link rel="stylesheet"
          href="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.css">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body style="padding:22px;">

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Laravel Log Viewer</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown active">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">{{$current_file}} <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @foreach($files as $file)
                            <li class="{{($current_file == $file)?'llv-active':''}}"><a
                                        href="?file={{ base64_encode($file) }}">{{$file}}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a target="_blank" href="http://www.srmilon.com"><i>by</i> <span style="color:blue;">SRMILON</span></a>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container-fluid" style="margin-top:50px;">
    <div class="row">
        <div class="col-sm-12 col-md-12 table-container">
            @if ($log_data === null)
                <div>
                    Log file size exceeded (More than 60 MB), please download the file.
                </div>
            @else
                <table id="table-log" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Date Time</th>
                        <th>Type</th>
                        <th>Env</th>
                        <th>Content</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($log_data as $key => $log)
                        <tr data-display="stack{{$key}}">
                            <td class="date">{{$log['date_time']}}</td>
                            <td class="text-{{$log['label_class']}}"><span
                                        class="glyphicon glyphicon-{{$log['label_img']}}-sign"
                                        aria-hidden="true"></span> &nbsp;{{$log['label']}}</td>
                            <td class="text">{{$log['context']}}</td>
                            <td class="text">
                                @if ($log['stack_data']) <a class="pull-right expand btn btn-default btn-xs"
                                                       data-display="stack{{$key}}"><span
                                            class="glyphicon glyphicon-eye-open"></span></a>@endif
                                {{$log['text']}}
                                @if (isset($log['in_file'])) <br/>{{$log['in_file']}}@endif
                                @if ($log['stack_data'])
                                    <div class="stack" id="stack{{$key}}"
                                         style="display: none; white-space: pre-wrap;">{{ trim($log['stack_data']) }}
                                    </div>@endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
            <div class="text-center">
                @if($current_file)
                    <a class="btn btn-default" href="?dload={{ base64_encode($current_file) }}"><span
                                class="glyphicon glyphicon-download-alt"></span>
                        Download Log File</a>
                    <a class="btn btn-danger" id="del-log-file" href="?delete={{ base64_encode($current_file) }}"><span
                                class="glyphicon glyphicon-trash"></span> Delete Log File</a>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- jQuery  and Javascript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('.table-container tr').on('click', function (event) {
            $('#' + $(this).data('display')).toggle();
        });

        $('#del-log-file').click(function (event) {
            return confirm('Are you sure?');
        });

        $('#table-log').DataTable({
            "order": [1, 'desc'],
            "stateSave": true,
            "stateSaveCallback": function (settings, data) {
                window.localStorage.setItem("dt", JSON.stringify(data));
            },
            "stateLoadCallback": function (settings) {
                var data = JSON.parse(window.localStorage.getItem("dt"));
                if (data) data.start = 0;
                return data;
            }
        });
    });
</script>
</body>
</html>