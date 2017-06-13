<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class PostTags extends Model
{
	public static $rules = [
		'tag__id'  => 'required|int',
		'post__id' => 'required|int'
	];

	protected $fillable = [
		'id', 'tag__id', 'post__id'
	];

	private $errors = [];

	public function validate()
	{
		$data = [
			'tag__id' => $this->tag__id,
			'post__id'  => $this->post__id
		];

		
		($validator = Validator::make($data, self::$rules))->passes();
		$this->errors = $validator->errors()->messages();

		return empty($this->errors);
	}

	public function tag()
	{
		return $this->hasOne('App\Tag', 'id', 'tag__id');
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
