<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Tag extends Model
{
	public static $rules = [
		'tag'   => 'required|max:255',
	];

	protected $fillable = [
		'id', 'tag'
	];

	private $errors = [];

	public function validate()
	{
		$data = [
			'tag' => $this->tag
		];

		
		($validator = Validator::make($data, self::$rules))->passes();
		$this->errors = $validator->errors()->messages();

		return empty($this->errors);
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