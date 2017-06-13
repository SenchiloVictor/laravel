<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Img extends Model
{
	public static $rules = [
		'url'  => 'required|max:255',
		'path' => 'required|max:255',
		'thumb_url'  => 'required|max:255',
		'thumb_path' => 'required|max:255',
		'gallery__id' => 'required|int'
	];

	protected $fillable = [
		'id', 'path', 'url', 'thumb_path', 'thumb_url', 'gallery__id'
	];

	private $errors = [];

	public function validate()
	{
		$data = [
			'path' => $this->path,
			'url'  => $this->url,
			'thumb_path' =>  $this->thumb_path,
			'thumb_url'  => $this->thumb_url,
			'gallery__id' => $this->gallery__id
		];

		($validator = Validator::make($data, self::$rules))->passes();
		$this->errors = $validator->errors()->messages();

		return empty($this->errors);
	}

	public function save(array $options = [])
	{
		if($this->validate())
			return parent::save($options);
		return false;
	}

	public function errors()
	{
		return $this->errors;
	}
}
