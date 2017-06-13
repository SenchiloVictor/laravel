@extends('layouts.main')

@section('content')
<h2>API Points</h2>
<div class="row">
	<div class="col-md-6 col-sm-12">
		<h3>Create post</h3>
		<p>
			<code>@method POST</code>
			<br>
			<code>@url /api/post/create</code>
			<br>
			<code>@post {title:string, post:text, image:file}</code>
			<br>
			<code>@response json {status:200|500, errors:[]}</code>
			<br>
		</p>
		<h3>Read post</h3>
		<p>
			<code>@method GET</code>
			<br>
			<code>@url /api/post/read&lt;/&lt;int:postId&gt;&gt;</code>
			<br>
			<code>@get {page:int, limit:int, tags:&lt;tagId&gt;,&lt;tagId&gt;,...}</code>
			<br>
			<code>@response json {status:200|500, posts:[], errors:[]}</code>
			<br>
		</p>
		<h3>Update post</h3>
		<p>
			<code>@method POST</code>
			<br>
			<code>@url /api/post/update/&lt;int:postId&gt;</code>
			<br>
			<code>@post {title:string, post:text, image:file}</code>
			<br>
			<code>@response json {status:200|500, id:&lt;int:postId&gt;errors:[]}</code>
		</p>
		<h3>Delete post</h3>
		<p>
			<code>@method DELETE</code>
			<br>
			<code>@url /api/post/delete/&lt;int:postId&gt;</code>
			<br>
			<code>@response json {status:200|500, errors:[]}</code>
		</p>
		<h3>Add tag to post</h3>
		<p>
			<code>@method POST</code>
			<br>
			<code>@url /api/post/addTag/&lt;int:tagId&gt;/&lt;int:postId&gt;</code>
			<br>
			<code>@response json {status:200|500, errors:[]}</code>
		</p>
		<h3>Delete tag from post</h3>
		<p>
			<code>@method DELETE</code>
			<br>
			<code>@url /api/post/deleteTag/&lt;int:tagId&gt;/&lt;int:postId&gt;</code>
			<br>
			<code>@response json {status:200|500, errors:[]}</code>
		</p>
	</div>
	<div class="col-md-6 col-sm-12">
		<h3>Create tag</h3>
		<p>
			<code>@method POST</code>
			<br>
			<code>@url /api/tag/create</code>
			<br>
			<code>@post {tag:string}</code>
			<br>
			<code>@response json {status:200|500, id:&lt;int:postId&gt; errors:[]}</code>
		</p>
		<h3>Read tags</h3>
		<p>
			<code>@method GET</code>
			<br>
			<code>@url /api/tag/read/&lt;int:id&gt;</code>
			<br>
			<code>@get {page:int, limit:int}</code> 
			<br>
			<code>@response json {status:200|500, tags:[], errors:[]}</code>
		</p>
		<h3>Update tag</h3>
		<p>
			<code>@method POST</code>
			<br>
			<code>@url /api/tag/update/&lt;int:id&gt;</code>
			<br>
			<code>@post {tag:string}</code>
			<br>
			<code>@response json {status:200|500, errors:[]}</code>
		</p>
		<h3>Delete tag</h3>
		<p>
			<code>@method DELETE</code>
			<br>
			<code>@url /api/tag/delete/&lt;int:id&gt;</code>
			<br>
			<code>@response json {status:200|500, errors:[]}</code>
		</p>
	</div>
</div>
@endsection