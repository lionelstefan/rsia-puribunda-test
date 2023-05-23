<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Firebase\FirestoreRepository;

class UserController extends Controller
{
	public function get($user_uuid = '')
	{
		$user_collection = app('firebase.firestore')
			->database()
			->collection('user');
		$query = $user_collection->where('user_uuid', '=', $user_uuid);
		$_docs = $query->documents()->rows()[0];
		
		return json_encode([
			"data" => $_docs->data(),
			"info" => [
				"created_at" => $_docs->createTime()->nanoSeconds(),
				"updated_at" => $_docs->updateTime()->nanoSeconds()
			]
		]);

	}

	public function updateProfile(FirestoreRepository $fs, Request $request, $user_uuid)
	{
		$bodyRequest = json_decode($request->getContent(), TRUE);

		$user_collection = app('firebase.firestore')
			->database()
			->collection('user');
		$query = $user_collection->where('user_uuid', '=', $user_uuid);
		$_docs = $query->documents()->rows()[0];
		$user_docs = $_docs->data();
		
		$user_docs['user_name'] = $bodyRequest['user_name'];

		$fs->update('user', $_docs->id(), $user_docs);

		return json_encode([
			'success' => true,
			'message' => 'USER_UPDATED',
			'error_code' => '0000'
		]);
	}

	public function _get_tier($totalPoint)
	{
		$tier = '';
		//auto select tier based by point
		if ($totalPoint >= 0 && $totalPoint <= 1000)
		{
			$tier = 'Water Dragon';
		}

		if ($totalPoint > 1000 && $totalPoint <= 2000)
		{
			$tier = 'Fire Dragon';
		}

		if ($totalPoint > 2000 && $totalPoint <= 3000)
		{
			$tier = 'Gold Dragon';
		}

		if ($totalPoint > 3000 && $totalPoint <= 4000)
		{
			$tier = 'King Dragon';
		}

		if ($totalPoint > 4000)
		{
			$tier = 'Blue Eyes White Dragon';
		}

		return $tier;
	}

	public function redeemPoint(FirestoreRepository $fs, Request $request)
	{
		$body = json_decode($request->getcontent(), true);
		$user_uuid = $body['user_uuid'];
		$redeemed_point = $body['redeemed_point'];

		$user_collection = app('firebase.firestore')
			->database()
			->collection('user');
		$query = $user_collection->where('user_uuid', '=', $user_uuid);
		$_docs = $query->documents()->rows()[0];
		$user_docs = $_docs->data();
		
		$new_point = $user_docs['point'] - $redeemed_point;

		if ($new_point < 0)
		{
			return json_encode([
				'success' => false,
				'message' => 'INVALID_POINT_REDEEM',
				'error_code' => '0000'
			]);
		}

		$user_docs['point'] = $new_point; 
		$user_docs['tier'] = $this->_get_tier($user_docs['point']);

		$fs->update('user', $_docs->id(), $user_docs);

		return json_encode([
			'success' => true,
			'message' => 'POIN_REDEEMED',
			'error_code' => '0000'
		]);
	}

	public function addPoint(FirestoreRepository $fs, Request $request)
	{
		$body = json_decode($request->getcontent(), true);
		$user_uuid = $body['user_uuid'];
		$nominal_transaction = $body['nominal_transaction'];

		(int)$point = 0.1 * ($nominal_transaction / 1000);

		$user_collection = app('firebase.firestore')
			->database()
			->collection('user');
		$query = $user_collection->where('user_uuid', '=', $user_uuid);
		$_docs = $query->documents()->rows()[0];
		$user_docs = $_docs->data();
	
		$user_docs['point'] = $user_docs['point'] + $point;
		$user_docs['tier'] = $this->_get_tier($user_docs['point']);

		$fs->update('user', $_docs->id(), $user_docs);

		return json_encode([
			'success' => true,
			'message' => 'POINT_ADDED',
			'error_code' => '0000'
		]);
	}
	
}
