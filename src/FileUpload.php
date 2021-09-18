<?php

namespace Easoblue\LaraFileUpload;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUpload{

	/**
	* $file an instance of UploadedFile
	*
	* @var Illuminate\Http\UploadedFile
	*/
 protected $file;

 /**
	* $image an instance of Intervention Image
	*
	* @var Intervention\Image\Facades\Image
	*/
 protected $image;

 protected $returnUrl = 'relative';

 protected $relativePrefix = '/storage';

 protected $fullUrl;

 /**
	* $imageMimes full list of image mime types
	*
	* @var array
	*/

 protected static $imageMimes = [
		 'image/bmp',
		 'image/cmu-raster',
		 'image/fif',
		 'image/florian',
		 'image/g3fax',
		 'image/gif',
		 'image/ief',
		 'image/jpeg',
		 'image/jutvision',
		 'image/naplps',
		 'image/pict',
		 'image/pjpeg',
		 'image/png',
		 'image/jfif',
		 'image/svg+xml',
		 'image/tiff',
		 'image/vasa',
		 'image/vnd.dwg',
		 'image/vnd.fpx',
		 'image/vnd.net-fpx',
		 'image/vnd.rn-realflash',
		 'image/vnd.rn-realpix',
		 'image/vnd.wap.wbmp',
		 'image/vnd.xiff',
		 'image/webp',
		 'image/x-cmu-raster',
		 'image/x-dwg',
		 'image/x-icon',
		 'image/x-jg',
		 'image/x-jps',
		 'image/x-niff',
		 'image/x-pcx',
		 'image/x-pict',
		 'image/x-portable-anymap',
		 'image/x-portable-bitmap',
		 'image/x-portable-graymap',
		 'image/x-portable-pixmap',
		 'image/x-quicktime',
		 'image/x-rgb',
		 'image/x-tiff',
		 'image/x-windows-bmp',
		 'image/x-xbitmap',
		 'image/x-xbm',
		 'image/x-xpixmap',
		 'image/x-xwd',
		 'image/x-xwindowdump',
		 'image/xbm',
		 'image/xpm',
 ];

public function __construct(){
	$this->fullUrl = url('/');
}

 /**
	* Custom constructor
	*
	* @param Illuminate\Http\UploadedFile $file
	* @return Easoblue\LaraFileUpload\FileUpload
	*/
 public static function make($file)
 {
	return (new FileUpload())->setFile($file);
 }

	/**
	* Custom constructor
	*
	* @return
	*/
 public function makeImage()
 {

		 $this->file = Image::make($this->file);

		 return $this;
 }

 /**
	* Sets the $file property
	*
	* @param Illuminate\Http\UploadedFile $file
	* @return Easoblue\LaraFileUpload\FileUpload $this
	*/
 public function setFile($file)
 {
		 $this->file = $file;
		 return $this;
 } 

 /**
	* Set the return path
	*
	* @param String $returnUrl
	* @return Easoblue\LaraFileUpload\FileUpload $this
	*/
 public function returnUrl($returnUrl)
 {
		 $this->returnUrl = $returnUrl;
		 return $this;
 }

 /**
	* Set the relativePrefix
	*
	* @param String $relativePrefix
	* @return Easoblue\LaraFileUpload\FileUpload $this
	*/
 public function relativePrefix($relativePrefix)
 {
		 $this->relativePrefix = $relativePrefix;
		 return $this;
 }

 /**
	* Set the fullUrl
	*
	* @param String $fullUrl
	* @return Easoblue\LaraFileUpload\FileUpload $this
	*/
 public function fullUrl($fullUrl)
 {
		 $this->fullUrl = $fullUrl;
		 return $this;
 }

 /**
	* Create new or return an existing Intervention Image object based on
	* $this->file
	*
	* @return Image
	*/
 protected function getImage()
 {
		 if (!$this->image) {
				 // if (self::isImage($this->file)) {
						 return Image::make($this->file);
				 // }
		 }

		 return $this->image;
 }

 /**
	* Uploads a file under a hashname
	*
	* @param string $path path to upload to
	* @param string $storage
	* @return string uploaded file's path
	*/
 public function upload($path = '', $storage = 'public')
 {
		 return $this->uploadAs('', $path, $storage);
 }

 /**
	* Uploads a file under a speciifed name
	*
	* @param string $filename filename to upload the file under
	* @param string $path path to upload to
	* @param string $storage
	* @return string uploaded file's path
	*/
 public function uploadAs($filename, $path = '', $storage = 'public')
 {

		 $filename = $filename ?: $this->file->hashName();
		 if ($this->image) {
				 $imagePath = "{$path}/{$filename}";

				 if (!Storage::disk($storage)->exists($path)) {
						 Storage::disk($storage)->makeDirectory($path);
				 }

				 $this->image->save(Storage::disk($storage)->path($imagePath));

				 return $this->getStoredUrl($path,$filename);

		 } else {
				 $this->file->storeAs($path, $filename, $storage);

				 return $this->getStoredUrl($path,$filename);
		 }

		 
 }

 /**
	* Replace an old file with a new one
	*
	* @param string $oldFilePath path of the file to replace
	* @param string $path path to upload the new file to
	* @param string $storage
	* @return string uploaded file's path
	*/
 public function replace($oldFilePath, $path = '', $storage = 'public')
 {
		 return $this->replaceAs($oldFilePath, '', $path, $storage);
 }

 /**
	* Replace an old file with a new one and store it under a specified name
	*
	* @param string $oldFilePath path of the file to be replaced
	* @param string $newFilename filename to upload the new file under
	* @param string $path path to upload the new file to
	* @param string $storage
	* @return string uploaded file's path
	*/
 public function replaceAs($oldFilePath, $newFilename, $path = '', $storage = 'public')
 {
		 self::delete($oldFilePath, $storage);

		 return $this->uploadAs($newFilename, $path, $storage);
 }

 /**
	* Downsize an image if it's bigger than the dimensions provided
	*
	* @param integer $maxWidth maximum allowed width
	* @param integer $maxHeight maximum allowed height
	* @return Easoblue\LaraFileUpload\FileUpload $this
	*/
 public function resize($maxWidth, $maxHeight)
 {
		 if ($this->image = $this->getImage()) {
				 // Downsize the image if it's bigger than desired
				 if ($this->image->width() > $maxWidth || $this->image->height() > $maxHeight) {
						 $this->image->resize($maxWidth, $maxHeight, function ($constraint) {
								 $constraint->aspectRatio();
						 });
				 }
		 }

		 return $this;
 }

 /**
	* Crop and resize an image to fit the the specified dimensions
	*
	* @param integer $width
	* @param integer $height
	* @param boolean $dontUpsize don't upsize the image if it's smaller than
	* the provided width and height
	* @return void
	*/
 public function fit($width, $height, $dontUpsize = false)
 {
		 if ($this->image = $this->getImage()) {
				 $this->image->fit($width, $height, function ($constraint) use ($dontUpsize) {
						 if ($dontUpsize) {
								 $constraint->upsize();
						 }
				 });
		 }

		 return $this;
 }

 /**
	* Delete a file from the specified storage
	*
	* @param string $filePath relative path to the file to delete
	* @param string $storage
	* @return void
	*/
 public static function delete($filePath, $storage = 'public')
 {
		 if (Storage::disk($storage)->exists($filePath)) {
				 Storage::disk($storage)->delete($filePath);
		 }
 }

 /**
	* Determine if a file is an image base on its MIME type
	*
	* @param string $file a path to a file or an UploadedFile instance
	* @param string $storage
	* @return boolean
	*/
 public static function isImage($file, $storage = 'public')
 {
		 if (is_string($file)) {
				 if (Storage::disk($storage)->exists($file)) {
						 return in_array(
								 Storage::disk($storage)->mimeType($file),
								 FileUploader::$imageMimes
						 );
				 } else {
						 return false;
				 }
		 } else {
				 return in_array($file->getMimeType(), FileUploader::$imageMimes);
		 }
 }


 private function getStoredUrl($path,$filename){

 	 if($this->returnUrl == 'relative'){
	 	return ($this->relativePrefix ? $this->relativePrefix.'/' : '').$path.'/'.$filename;
	 }elseif($this->returnUrl == 'filename'){
	 	return $filename;
	 }elseif($this->returnUrl == 'full'){
	 	return $fullUrl.'/'.$path.'/'.$filename;
	 }

 }


}
