<?php


class Home extends Controller
{
	#[Route(method: 'GET')]
	public function index(Request $request)
	{
		return json([
			'msg' => 'Initial Set-Up Complete'
		]);
	}
}
