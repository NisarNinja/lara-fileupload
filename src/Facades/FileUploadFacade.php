<?php

namespace Easoblue\LaraFileUpload\Facades;

use Illuminate\Support\Facades\Facade;

class FileUploadFacade extends Facade {
   protected static function getFacadeAccessor() { 
   	return 'FileUpload'; 
   }
 }