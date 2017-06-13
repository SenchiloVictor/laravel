<?php 

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Gallery;
use App\Img;
use URL;
use Image as ImageManager;
use File as FileManager;

class GalleryController extends Controller
{
	private function REST(array $data = [], int $status = 200)
	{
		response()->json($data, $status)->send();
		exit;
	}

	public function create(Request $request)
	{
		$gallery = new Gallery;
		$gallery->name = addslashes(htmlspecialchars($request->input('name') ?? ''));

		if( ! $gallery->save())
			$this->REST(['errors' => $gallery->errors()], 500);
		$this->REST(['name' => stripslashes($gallery->name), 'id' => $gallery->id]);
	}

	public function delete(int $id)
	{
		if( ! $galleryModel = Gallery::where('id', $id)->first())
			$this->REST(['message' => 'invalid gallery id'], 500);
		
		$images = $galleryModel->images;
		foreach($images as $imageModel)
		{
			if( ! FileManager::delete($imageModel->path))
				$this->REST(['message' => 'can\'t delete file'], 500);
			
			if( ! FileManager::delete($imageModel->thumb_path))
				$this->REST(['message' => 'can\'t delete file'], 500);
			
			if( ! $imageModel->delete())
				$this->REST(['message' => 'can\'t delete image from database'], 500);
		}

		if( ! $galleryModel->delete())
			$this->REST(['message' => 'can\'t gallery'], 500);
		
		$this->REST();
	}

	public function list(Request $request)
	{
		$id = intval($request->input('id'));

		$galleries = [];
		if(0 > $id)
		{
			$galleryModels = Gallery::all();
			foreach($galleryModels as &$galleryModel)
			{
				$galleries[] = [
					'id' => $galleryModel->id,
					'name' => $galleryModel->name,
					'imagesCount' => $galleryModel->images->count()
				];
			}
		}
		else
		{
			if( ! $galleryModel = Gallery::where('id', $id)->first())
				$this->REST(['message' => 'invalid gellery id'], 500);
			
			$galleries[] = [
				'id' => $galleryModel->id,
				'name' => $galleryModel->name,
				'imagesCount' => 0
			];
		}



		$this->REST(['galleries' => $galleries]);
	}

	public function getImg(Request $request)
	{
		$imageId = intval($request->input('id'));

		if( ! $galleryId = strripos(URL::previous(), '/'))
			$this->REST(['message' => 'invalid gallery id'], 500);
			
		if( ! $galleryId = intval(substr(URL::previous(), $galleryId + 1)))
			$this->REST(['message' => 'invalid gallery id'], 500);

		if( ! Gallery::where('id', $galleryId))
			$this->REST(['message' => 'invalid gallery id'], 500);
		
		$images = [];
		if(0 > $imageId)
		{
			$imageModels = Img::where('gallery__id', $galleryId)->get();
			foreach($imageModels as &$imageModel)
			{
				$images[] = [
					'url' => $imageModel->url,
					'thumb_url' => $imageModel->thumb_url,
					'id'  => $imageModel->id
				];
			}
		}
		else
		{
			if( ! $imageModel = Img::where(['gallery__id' => $galleryId, 'id' => $imageId])->first())
				$this->REST(['message' => 'invalid image id'], 500);

			$images[] = [
				'url' => $imageModel->url,
				'thumb_url' => $imageModel->thumb_url,
				'id'  => $imageModel->id
			];
		}

		$this->REST(['images' => $images]);
	}

	public function addImg(Request $request)
	{
		if( ! $image = $request->file('image'))
			$this->REST(['message' => 'image can\'t be empty'], 500);
		
		if( ! $id = strripos(URL::previous(), '/'))
			$this->REST(['message' => 'invalid gallery id'], 500);
			
		if( ! $id = intval(substr(URL::previous(), $id + 1)))
			$this->REST(['message' => 'invalid gallery id'], 500);

		if( ! Gallery::where('id', $id))
			$this->REST(['message' => 'invalid gallery id'], 500);

		$filename  = md5(time(). '.' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

		if( ! is_dir(public_path('uploaded/')))
			mkdir(public_path('uploaded/'));
		if( ! is_dir(public_path('uploaded/images/')))
			mkdir(public_path('uploaded/images/'));
		if( ! is_dir(public_path('uploaded/images/thumbs/')))
			mkdir(public_path('uploaded/images/thumbs/'));

		if( ! in_array($image->getClientMimeType(), ['image/jpeg', 'image/png']))
			$this->REST(['errors' => ['image' => 'Invalid mime type ' . $image->getClientMimeType()]], 500);

		$path = public_path('uploaded/images/' . $filename);
		$thumb_path = public_path('uploaded/images/thumbs/' . $filename);

		ImageManager::make($image->getRealPath())->fit(1920, 1080)->save($path);
		ImageManager::make($image->getRealPath())->fit(400, 300)->save($thumb_path);
	
		$imgModel = new Img;
		$imgModel->url  = 'uploaded/images/' . $filename;
		$imgModel->path = $path;
		$imgModel->thumb_url  = 'uploaded/images/thumbs/' . $filename;
		$imgModel->thumb_path = $thumb_path;
		$imgModel->gallery__id = $id;

		if( ! $imgModel->save())
			$this->REST($imgModel->errors(), 500);

		$this->REST(['url' => $imgModel->url, 'id' => $imgModel->id]);
	}

	public function removeImg()
	{

	}
}