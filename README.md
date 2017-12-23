# Laravel Log Viewer

The package will help to view laravel logs easily.

### Prerequisites

```
PHP >= 5.3.0
```

### Installing

Install the package via [Composer](https://getcomposer.org/download) by following command:

```
composer require srmilon/log-viewer
```

Once package installation done, register the service provider in `config/app.php` in the `providers array` like bellow:

```
providers' => [
    ...
    Srmilon\LogViewer\LogServiceProvider::class
  ]
```

Then add folloiwng route in your routes file:

```
Route::get('/log', '\Srmilon\LogViewer\LogViewerController@index');
```

Now you can browse your laravel website logs using `{your-website-url}/log`.

For example:

```
http://www.example.com/log
```

## Enjoy!
