<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class GalleryController extends Controller
{
	public function show()
	{
		return view('gallery-show');
	}

	public function list()
	{
		return view('gallery-list');
	}
}