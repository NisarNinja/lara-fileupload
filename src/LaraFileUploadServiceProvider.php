<?php

namespace Easoblue\LaraFileUpload;

use Illuminate\Support\ServiceProvider;
use Easoblue\LaraFileUpload\Facades\FileUploadFacade;
use Illuminate\Foundation\AliasLoader;

class LaraFileUploadServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */

    public function boot()
    {

        // if(config('lara-response.helpers')){
        //     $helper = __DIR__.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'lara-response.php';
        //     require_once $helper;
        // }

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // $this->mergeConfigFrom(__DIR__.'/../config/lara-fileupload.php', 'lara-fileupload');

        $loader = AliasLoader::getInstance();

        $this->app->bind('FileUpload', FileUpload::class);
        $loader->alias('FileUpload', FileUploadFacade::class);
    }
}
