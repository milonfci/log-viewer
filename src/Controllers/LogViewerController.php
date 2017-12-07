<?php

namespace Srmilon\LogViewer;

use App\Http\Controllers\Controller;

class LogViewerController extends Controller
{
    protected $request;

    public function __construct()
    {
        $this->request = app('request');
    }

    public function index()
    {
        // Set log file
        $logFile = $this->request->input('file');
        if ($logFile) {
            LogViewer::setLogFile(base64_decode($this->request->input('file')));
        }

        // Download log file
        $downloadFileName = $this->request->input('dload');
        if ($downloadFileName) {
            return $this->download(LogViewer::getLogFilePath(base64_decode($downloadFileName)));
        }

        // Delete log file
        $deleteFile = $this->request->get('delete');
        if ($this->request->has('delete')) {
            app('files')->delete(LogViewer::getLogFilePath(base64_decode($deleteFile)));
            return $this->redirect($this->request->url());
        }

        return app('view')->make('log-viewer::log', [
            'files' => LogViewer::getFiles(true),
            'log_data' => LogViewer::getAllLogs(),
            'current_file' => LogViewer::getCurrentLogFileName()
        ]);
    }


    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }
        return app('redirect')->to($to);
    }

    private function download($data)
    {
        if (function_exists('response')) {
            return response()->download($data);
        }
        // For laravel 4.2
        return app('\Illuminate\Support\Facades\Response')->download($data);
    }

}
