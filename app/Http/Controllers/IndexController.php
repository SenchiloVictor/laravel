<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class IndexController extends Controller
{
	public function index()
	{
		return view('index');
	}

	public function add()
	{
		return view('articles-add');
	}
}