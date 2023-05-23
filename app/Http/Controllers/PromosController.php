<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Firebase\FirestoreRepository;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class PromosController extends Controller
{

	public function _get(FirestoreRepository $fs, $id = '')
	{
		$_query = 'Y';
		$_docs = $fs->get('promos', $id, $_query);

		$returnData = collect($_docs)->map(function ($val, $key) {
			/**
			 * Merge array id and data
			 */
			$data = $val->data();
			$id = $val->id();
			$arrNewIdPromo = ['id_promo' =>  $id];
			$newData = array_merge($data, $arrNewIdPromo);

			if (array_key_exists('venue_key', $newData)) {
				$temp = $newData;
				unset($temp['venue_key']);
				$venue_key = $val->data()['venue_key']->id();
				$temp['venue_key'] = $venue_key;
				return $temp;
			}

			return $newData;
		});

		return $returnData;
	}

	public function _getById(FirestoreRepository $fs, $id = '')
	{
		/**
		 * get data by id event
		 */
		$getPromoData 			= $fs->get('promos', $id);
		$data_promo 			= $getPromoData->data();
		$data_promo['id_promo'] = $getPromoData->id();

		/**
		 * if type document field reference type, unset and change key to be id
		 */
		if (array_key_exists('venue_key', $data_promo)) {
			unset($data_promo['venue_key']);
			$venue_key = $getPromoData->data()['venue_key']->id();
			$data_promo['venue_key'] = $venue_key;
		}

		if (array_key_exists('start_date', $data_promo)) {
			$convertDateStart = date('d-M-Y', strtotime($data_promo['start_date']));
			$convertTimeStart = date('H:i', strtotime($data_promo['start_date']));
			$data_promo['sDate'] = $convertDateStart;
			$data_promo['sTime'] = $convertTimeStart;
			unset($data_promo['start_date']);
		}

		if (array_key_exists('end_date', $data_promo)) {
			$convertDateEnd = date('d-M-Y', strtotime($data_promo['end_date']));
			$convertTimeEnd = date('H:i', strtotime($data_promo['end_date']));
			$data_promo['eDate'] = $convertDateEnd;
			$data_promo['eTime'] = $convertTimeEnd;
			unset($data_promo['end_date']);
		}

		return $data_promo;
	}

		/**
	 * Get data by api
	 */
	public function getDataByIdPromoApi(FirestoreRepository $fs, $id)
	{
		$data = $this->_getById($fs, $id);
		return json_encode($data);
	}

	public function getDataPromoApi(FirestoreRepository $fs)
	{
		$data = $this->_get($fs);
		$data = $data->sortByDesc(function($item) {
			return strtotime($item['start_date']);
		});

		$return = [];
		foreach ($data->toArray() as $key => $val)
		{
			array_push($return, $val);
		}

		return json_encode($return);
	}

	/**
	 * end of data by api
	 */

	// public function new(FirestoreRepository $fs, Request $request)
	// {
	// 	$payload = json_decode($request->getContent(), TRUE);
	// 	$fs->create('promos', $payload);
	// 	return json_encode([
	// 		'success' => true,
	// 		'message' => 'PROMO_CREATED'
	// 	]);
	// }

	// public function edit(FirestoreRepository $fs, Request $request, $id)
	// {
	// 	$payload = json_decode($request->getContent(), TRUE);
	// 	$fs->update('promos', $id, $payload);
	// 	return json_encode([
	// 		'success' => true,
	// 		'message' => 'PROMO_UPDATED'
	// 	]);
	// }

	// public function remove(FirestoreRepository $fs, Request $request, $id)
	// {
	// 	$payload = json_decode($request->getContent(), TRUE);
	// 	$fs->delete('promos', $id);
	// 	return json_encode([
	// 		'success' => true,
	// 		'message' => 'PROMO_DELETED'
	// 	]);
	// }

	public function index(FirestoreRepository $fs, $id = '')
	{
		$_docs = $fs->get('venue_location', $id);
		$venue_location = collect($_docs)->map(function ($val, $key) {
			return [
				'key' 	=> $val->id(),
				'data'	=> $val->data()
			];
		});

		return View::make("promo/add")->with(array('dataVenue' => $venue_location));
	}

	public function listPromoServerSide()
	{
		return view('promo/index');
	}

	public function getDataPromo(FirestoreRepository $fs, $id = '')
	{		
		/**
		 * return pake collection atau pake type biasa bisa karena data yang di return sudah bersih
		 * Include
		 * draw
		 * total data
		 * total filter
		 * data clean
		 */

		$data_promo = $this->_get($fs);

		return datatables($data_promo)
			->addIndexColumn()
			->addColumn('action', function ($data_promo) {
				return $this->_getActionColumn($data_promo);
			})
			->rawColumns(['action'])
			->make(true);
	}

	protected function _getActionColumn($data)
	{
		$editUrl = route('editPromoPage', $data['id_promo']);
		$deleteUrl = route('deletePromodata', $data['id_promo']);

		$btn = "<a href='$editUrl' class='edit btn btn-primary btn-sm'>Edit</a>";
		$returnBtn = $btn . ' ' . '<a href="' . $deleteUrl . '" class="edit btn btn-danger btn-sm delete">Delete <i class="fa fa-trash"></i></a>';

		return $returnBtn;
		// '<a href="' . $deleteUrl . '" onClick="confirm(\'Are you sure ?\')" class="edit btn btn-danger btn-sm">Delete <i class="fa fa-trash"></i></a>';

	}

	public function editPromoIndex(FirestoreRepository $fs, $id)
	{

		$data_promo = $this->_getById($fs, $id);
		/**
		 * get data venue by id
		 */
		$getSelectedVenue = $fs->get('venue_location', $data_promo['venue_key']);
		$arrValVenue = [
			'id_val_venue'	=>	$getSelectedVenue->id(),
			'dataValVenue'	=>	$getSelectedVenue->data()
		];

		$getAllVenue = $fs->get('venue_location', $id = '');
		$all_venue_location = collect($getAllVenue)->map(function ($val, $key) {
			return [
				'key' 				=> $val->id(),
				'data_all_venue'	=> $val->data()
			];
		});

		return View::make("promo/edit")
			->with(array(
				'dataPromo' 		=> $data_promo,
				'dataValueVenue' 	=> $arrValVenue,
				'dataAllVenue'		=> $all_venue_location				
			));
	}

	public function editPromo(FirestoreRepository $fs, Request $request)
	{
		//if ($request->isMethod('post')) {
		// $id_collection		= $request->input('id_promo');
		// $title 				= $request->input('title');
		// $venueId 			= $request->input('venue_location');
		// $start_date 		= $request->input('sDate');
		// $end_date 			= $request->input('eDate');
		// $start_time 		= $request->input('sTime');
		// $end_time 			= $request->input('eTime');
		// $desc 				= $request->input('desc');
		// $file_upload 		= $request->file('file');

		$id_collection		= $request->id_promo;
		$title 				= $request->title;
		$venueId 			= $request->venue_location;
		$start_date 		= $request->sDate;
		$end_date 			= $request->eDate;
		$start_time 		= $request->sTime;
		$end_time 			= $request->eTime;
		$desc 				= $request->desc;
		$file_upload 		= $request->file('file');

		//Validator from laravel
		$rulesValidator = array(
			'title'				=> 'required',
			'venue_location'	=> 'required',
			'sDate'				=> 'required',
			'eDate'				=> 'required',
			'sTime'				=> 'required',
			'eTime'				=> 'required',
			'desc'				=> 'required',
			'file'				=> 'required|mimes:jpg,png|max:2000'
		);

		$messageValidator = array(
			'title.required'			=> 'The Title field is required',
			'venue_location.required'	=> 'The Venue field is required',
			'sDate.required'			=> 'The Start Date field is required',
			'eDate.required'			=> 'The End Date is required',
			'sTime.required'			=> 'The Start Time field is required',
			'eTime.required'			=> 'The End Time field is required',
			'desc.required'				=> 'The Description field is required',
			'file.required'				=> 'The File field is required',
			'file.mimes'				=> 'File Extension: Img/Png only',
			'file.max'					=> 'Max File Size: 2 Mb!'
		);

		$validator = Validator::make($request->all(), $rulesValidator, $messageValidator);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()->all()
			], 400);
		}

		//logic upload file to cloud firebase storage
		$firebase_storage_path = 'imgs/promo/';
		$localfolder = public_path('firebase-temp-uploads') . '/';
		$imageName = $file_upload->getClientOriginalName();

		if ($file_upload->move($localfolder, $imageName)) {
			$uploadedfile = fopen($localfolder . $imageName, 'r');
			//execute upload to storage
			app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path .  $imageName]);
			//will remove from local laravel folder  
			unlink($localfolder . $imageName);

			$bucketName = 'https://firebasestorage.googleapis.com/v0/b/dragon-sea-view.appspot.com/o/' . urlencode($firebase_storage_path) . $imageName . '?alt=media';
			$arrUpdate = array(
				'title' 		=> $title,
				'venue_key'		=> app('firebase.firestore')->database()->document('venue_location' . '/' . $venueId),
				'img_path'		=> $bucketName,
				'start_date'	=> $start_date . ' ' . $start_time,
				'end_date'		=> $end_date . ' ' . $end_time,
				'desc'			=> $desc
			);

			$fs->update('promos', $id_collection, $arrUpdate);

			return response()->json([
				'success' => true,
				'url'=>url('/listPromo')
			], 200);

			// 	return Redirect::to('listPromo')->withSuccess('Edit Promo Successfull!');
			// } else {
			// 	return Redirect::to('listPromo')->withFail('Edit Promo Failed!');
		}
		//}
	}

	public function createPromo(FirestoreRepository $fs, Request $request)
	{
		// if ($request->isMethod('post')) {
		// $title 				= $request->input('title');
		// $venueId 			= $request->input('venue_location');
		// $start_date 		= $request->input('sDate');
		// $end_date 			= $request->input('eDate');
		// $start_time 		= $request->input('sTime');
		// $end_time 			= $request->input('eTime');
		// $desc 				= $request->input('desc');
		// $file_upload 		= $request->file('file');

		$title 				= $request->title;
		$venueId 			= $request->venue_location;
		$start_date 		= $request->sDate;
		$end_date 			= $request->eDate;
		$start_time 		= $request->sTime;
		$end_time 			= $request->eTime;
		$desc 				= $request->desc;
		$file_upload 		= $request->file('file');

		//Validator from laravel
		$rulesValidator = array(
			'title'				=> 'required',
			'venue_location'	=> 'required',
			'sDate'				=> 'required',
			'eDate'				=> 'required',
			'sTime'				=> 'required',
			'eTime'				=> 'required',
			'desc'				=> 'required',
			'file'				=> 'required|mimes:jpg,png|max:2000'
		);

		$messageValidator = array(
			'title.required'			=> 'The Title field is required',
			'venue_location.required'	=> 'The Venue field is required',
			'sDate.required'			=> 'The Start Date field is required',
			'eDate.required'			=> 'The End Date is required',
			'sTime.required'			=> 'The Start Time field is required',
			'eTime.required'			=> 'The End Time field is required',
			'desc.required'				=> 'The Description field is required',
			'file.required'				=> 'The File field is required',
			'file.mimes'				=> 'File Extension: Img/Png only',
			'file.max'					=> 'Max File Size: 2 Mb!'
		);

		$validator = Validator::make($request->all(), $rulesValidator, $messageValidator);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()->all()
			], 400);
		}

		//logic upload file to cloud firebase storage
		$firebase_storage_path = 'imgs/promo/';
		$localfolder = public_path('firebase-temp-uploads') . '/';
		$imageName = $file_upload->getClientOriginalName();

		if ($file_upload->move($localfolder, $imageName)) {
			$uploadedfile = fopen($localfolder . $imageName, 'r');
			//execute upload to storage
			app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path .  $imageName]);
			//will remove from local laravel folder  
			unlink($localfolder . $imageName);

			$bucketName = 'gs://dragon-sea-view.appspot.com/' . $firebase_storage_path . $imageName;
			$arrInsert = array(
				'title' 		=> $title,
				'venue_key'		=> app('firebase.firestore')->database()->document('venue_location' . '/' . $venueId),
				'img_path'		=> $bucketName,
				'start_date'	=> $start_date . ' ' . $start_time,
				'end_date'		=> $end_date . ' ' . $end_time,
				'desc'			=> $desc,
				'is_deleted'	=> '0'
			);

			$fs->create('promos', $arrInsert);

			return response()->json([
				'success' => true,
				'url'=>url('/listPromo')
			], 200);			
			// 	return Redirect::to('addPromo')->withSuccess('Add Promo Successfull!');
			// } else {
			// 	return Redirect::to('addPromo')->withFail('Add Promo Failed!');
		}
		//}
	}

	public function softDeletePromo(FirestoreRepository $fs, $id)
	{
		$arrDelete = array(
			'is_deleted'	=> '1'
		);

		$fs->update('promos', $id, $arrDelete);
		return response()->json([
			'success' => true,
			'url'=>url('/listPromo')
		], 200);
		//return Redirect::to('listPromo')->withSuccess('Delete Promo Successfull!');
	}
}
