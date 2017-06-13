<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Gallery extends Model
{
	protected $table = 'galleries';

	public static $rules = [
		'name' => 'required|max:255',
	];

	protected $fillable = [
		'id', 'name'
	];

	private $errors = [];

	public function validate()
	{
		$data = [
			'name' => $this->name
		];

		
		($validator = Validator::make($data, self::$rules))->passes();
		$this->errors = $validator->errors()->messages();

		return empty($this->errors);
	}

	public function images()
	{
		return $this->hasMany('App\Img', 'gallery__id', 'id');
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
