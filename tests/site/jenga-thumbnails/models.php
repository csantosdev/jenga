<?php
namespace JengaThumbnails\Models;

class Image {

	public $filename = [f\CharField];
	public $thumbnails = [f\ManyToMany, 'model' => JengaThumbnails\Models\Thumbnail];
}

class Thumbnail {

	public $image = [f\ForeignKey, 'model' => 'JengaThumbnails\Models\Image'];
}