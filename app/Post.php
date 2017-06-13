<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Post extends Model
{
	public static $rules = [
		'title'   => 'required|max:255',
		'post'    => 'required',
		'score'   => 'required'
	];

	protected $fillable = [
		'id', 'title', 'post', 'image__id', 'score'
	];

	private $errors = [];

	public function validate()
	{
		$data = [
			'title' => $this->title,
			'post'  => $this->post,
			'score' => $this->score
		];

		
		($validator = Validator::make($data, self::$rules))->passes();
		$this->errors = $validator->errors()->messages();

		return empty($this->errors);
	}

	public function image()
	{
		return $this->hasOne('App\Image', 'id', 'image__id');
	}

	public function tags()
	{
		return $this->hasMany('App\PostTags', 'post__id', 'id');
	}

	public function errors()
	{
		return $this->errors;
	}

	public function save(array $options = [])
	{
		if($this->validate())
			return parent::save($options);
		return false;
	}
}
