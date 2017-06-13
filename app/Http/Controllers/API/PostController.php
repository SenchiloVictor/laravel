<?php 

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Articles\Readability\Readability;
use Image as ImageManager;
use File as FileManager;
use App\Post;
use App\Tag;
use App\PostTags;
use App\Image;
use DB;

/**
 * @class PostController
 */
class PostController extends Controller
{
	/**
	 * @return json response for REST API
	 */
	private function REST(array $data = [], int $status = 200)
	{
		response()->json($data, $status)->send();
		exit;
	}

	/**
	 * REST API for create/update post
	 * @method POST
	 * @url /api/post/create
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request, Post $postModel = null)
	{
		if(null === $postModel)
			$postModel = new Post;

		$postModel->title = addslashes(htmlentities($request->input('title') ?? ''));
		$postModel->post  = $text = addslashes(htmlentities($request->input('post') ?? ''));
		$postModel->score = ceil((new Readability)->easeScore($text));

		if($image = $request->file('image'))
		{
			if($postModel->image) {
				if( ! FileManager::delete($postModel->image->path))
					$this->REST(['message' => 'can\'t delete file'], 500);
				$postModel->image->delete();
			}

			$filename  = md5(time(). '.' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

			if( ! is_dir(public_path('uploaded/')))
				mkdir(public_path('uploaded/'));
			if( ! is_dir(public_path('uploaded/images/')))
				mkdir(public_path('uploaded/images/'));
			if( ! is_dir(public_path('uploaded/images/thumbs/')))
				mkdir(public_path('uploaded/images/thumbs/'));

			if( ! in_array($image->getClientMimeType(), ['image/jpeg', 'image/png']))
				$this->REST(['errors' => ['image' => 'Invalid mime type']], 500);

			$path = public_path('uploaded/images/' . $filename);
			$thumb_path = public_path('uploaded/images/thumbs/' . $filename);

			ImageManager::make($image->getRealPath())->fit(1920, 1080)->save($path);
			ImageManager::make($image->getRealPath())->fit(400, 300)->save($thumb_path);
		
			$imageModel = new Image;
			$imageModel->url  = 'uploaded/images/' . $filename;
			$imageModel->path = $path;
			$imageModel->thumb_url  = 'uploaded/images/thumbs/' . $filename;
			$imageModel->thumb_path  = $thumb_path;

			if( ! $imageModel->save())
				$this->REST($imageModel->errors(), 500);

			$postModel->image__id = $imageModel->id;
		}

		if( ! $postModel->save())
			$this->REST($postModel->errors(), 500);

		$this->REST(['message' => 'success', 'id' => $postModel->id]);
	}

	/**
	 * REST API for read posts
	 * @method GET
	 * @url /api/post/read<postId:int/page:int/limit:int/&tags:string=1,2,...>
	 * @return \Illuminate\Http\Response
	 */
	public function read(Request $request, int $id = null)
	{
		if(null === $id)
		{
			$page = intval($request->input('page')) ?? 1;
			$limit = intval($request->input('limit')) ?? 2;
			$tags = $request->input('tags') ?? null;
			
			if(null !== $tags)
			{
				$tags = explode(',', $tags);
				$tags = array_filter($tags, function($id){
					if(is_numeric($id))
						return true;
				});
				$tags = array_map('intval', $tags);
			}

			if( !! $tags)
			{
				$postModels = Post::orderBy('id', 'desc')
					->leftJoin('post_tags', 'posts.id', '=', 'post_tags.post__id')
					->whereIn('post_tags.tag__id', $tags)
					->select('posts.*')
					->skip($page * $limit - $limit)
					->paginate($limit);
			}
			else
				$postModels = Post::orderBy('id', 'desc')->skip($page * $limit - $limit)->paginate($limit);
		}
		else
			$postModels = Post::where('id', $id)->get();

		if(empty($postModels))
			$this->REST(['posts' => []], 500);

		$posts = [];
		foreach($postModels as &$postModel) {
			$posts[] = [
				'id' => $postModel->id,
				'title' => stripslashes($postModel->title),
				'post' => stripslashes($postModel->post),
				'image' => url($postModel->image->url),
				'tags' => (function() use(&$postModel) {
					$tags = [];
					foreach($postModel->tags as &$modelTag) {
						$tags[] = [
							'id' => $modelTag->tag->id,
							'name' => $modelTag->tag->tag
						];
					}
					return $tags;
				})()
			];
		}

		$this->REST(['posts' => $posts]);
	}

	/**
	 * REST API for update post
	 * @method POST
	 * @url /api/post/update<int:post_id>
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, int $id = null)
	{
		if( ! $postModel = Post::where('id', $id)->first())
			$this->REST(['error' => 'post not foud'], 500);

		$this->create($request, $postModel);
	}

	/**
	 * REST API for delete post
	 * @method DELETE
	 * @url /api/post/delete<int:post_id>
	 * @return \Illuminate\Http\Response
	 */
	public function delete(int $id)
	{
		if( ! $postModel = Post::where('id', $id)->first())
			$this->REST(['error' => 'post not foud'], 500);

		if($postModel->image) {
			if( ! FileManager::delete($postModel->image->path))
				$this->REST(['message' => 'can\'t delete file'], 500);
			
			if( ! FileManager::delete($postModel->image->thumb_path))
				$this->REST(['message' => 'can\'t delete file'], 500);
			
			if( ! $postModel->image->delete())
				$this->REST(['message' => 'can\'t delete image from database'], 500);
		}

		if( ! $postModel->delete())
			$this->REST(['message' => 'can\'t delete post database'], 500);

		$this->REST(['status' => 'success']);
	}

	/**
	 * REST API for add tag to post
	 * @method POST
	 * @url /api/post/addTag/<int:tagId>/<int:postId>
	 * @return \Illuminate\Http\Response
	 */
	public function addTag(int $tagId, int $postId)
	{
		if( ! Tag::where('id', $tagId)->first())
			$this->REST(['message' => 'invalid tag id'], 500);
		
		if( ! Post::where('id', $postId)->first())
			$this->REST(['message' => 'invalid post id'], 500);
		
		if(PostTags::where(['tag__id' => $tagId, 'post__id' => $postId])->first())
			$this->REST(['message' => 'tag already added to post'], 500);

		$postTags = new PostTags;
		$postTags->tag__id = $tagId;
		$postTags->post__id = $postId;

		if( ! $postTags->save())
			$this->REST($postTags->errors(), 500);

		$this->REST(['message' => 'success']);
	}

	/**
	 * REST API for delete tag from post
	 * @method DELETE
	 * @url /api/post/deleteTag/<int:tagId>/<int:postId>
	 * @return \Illuminate\Http\Response
	 */
	public function deleteTag(int $tagId, int $postId)
	{
		if( ! $postTag = PostTags::where(['tag__id' => $tagId, 'post__id' => $postId])->first())
			$this->REST(['message' => 'invalid tag id or post id'], 500);
		
		if( ! $postTag->delete())
			$this->REST(['message' => 'can\'t delete tag from post'], 500);

		$this->REST(['status' => 'success']);
	}
}