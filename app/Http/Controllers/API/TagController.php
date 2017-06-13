<?php 

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Articles\Readability\Readability;
use App\Tag;
use App\PostTags;

/**
* @class TagController
*/
class TagController extends Controller
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
	* REST API for create/update tag
	* @method POST
	* @url /api/tag/create
	* @return \Illuminate\Http\Response
	*/
	public function create(Request $request, Tag $tagModel = null)
	{
		if(null === $tagModel)
			$tagModel = new Tag;
		$tagModel->tag = addslashes(htmlspecialchars($request->input('tag') ?? ''));

		if( ! $tagModel->save())
			$this->REST($tagModel->errors(), 500);

		$this->REST(['message' => 'success', 'id' => $tagModel->id]);
	}

	/**
	* REST API for read tag
	* @method GET
	* @url /api/tag/read/<params>
	* @return \Illuminate\Http\Response
	*/
	public function read(Request $request, int $id = null)
	{
		if(null === $id)
		{
			$page = $request->input('page') ?? 1;
			$limit = $request->input('limit') ?? 2;
			$tagModels = Tag::orderBy('id', 'desc')->skip($page * $limit - $limit)->paginate($limit);
		}
		else
			$tagModels = Tag::where('id', $id)->get();
		
		if(empty($tagModels))
			$this->REST(['tags' => []]);
		
		$tags = [];
		foreach($tagModels as &$tagModel)
		{
			$tags[] = [
				'id'  => $tagModel->id,
				'tag' => $tagModel->tag
			];
		}

		$this->REST(['tags' => $tags]);
	}

	/**
	* REST API for update tag
	* @method POST
	* @url /api/tag/update/<int:id>
	* @return \Illuminate\Http\Response
	*/
	public function update(Request $request, int $id = null)
	{
		if( ! $tagModel = Tag::where('id', $id)->first())
			$this->REST(['invalid tag id'], 500);

		$this->create($request, $tagModel);
	}

	/**
	* REST API for delete tag
	* @method DELETE
	* @url /api/tag/delete/<id>
	* @return \Illuminate\Http\Response
	*/
	public function delete(int $id)
	{
		if( ! $tagModel = Tag::where('id', $id)->first())
			$this->REST(['message' => 'invalid tag id'], 500);

		if($postsTagsModels = PostTags::where('tag__id', $id)->get())
		{
			foreach($postsTagsModels as &$postsTagsModel)
			{
				if( ! $postsTagsModel->delete())
					$this->REST(['message' => 'can\'t delete tag from post'], 500);
			}
		}

		if( ! $tagModel->delete())
			$this->REST(['message' => 'can\'t delete tag'], 500);

		$this->REST(['status' => 'success']);
	}
}