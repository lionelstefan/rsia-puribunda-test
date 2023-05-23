<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Firebase\FirestoreRepository;
use App\Services\Notification\FCMRepository;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
	public function _get(FirestoreRepository $fs, $id = '')
	{
		$_query = 'Y';
		$_docs = $fs->get('events', $id, $_query);

		$returnData = collect($_docs)->map(function ($val, $key) {
			/**
			 * Merge array id and data
			 */
			$data = $val->data();
			$id = $val->id();
			$arrNewIdEvent = ['id_event' =>  $id];
			$newData = array_merge($data, $arrNewIdEvent);

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
		$_query = 'Y';
		$getEventData 			= $fs->get('events', $id, $_query);
		$data_event 			= $getEventData->data();
		$data_event['id_event'] = $getEventData->id();

		/**
		 * if type document field reference type, unset and change key to be id
		 */
		if (array_key_exists('venue_key', $data_event)) {
			unset($data_event['venue_key']);
			$venue_key = $getEventData->data()['venue_key']->id();
			$data_event['venue_key'] = $venue_key;
		}

		if (array_key_exists('start_date', $data_event)) {
			$convertDateStart = date('d-M-Y', strtotime($data_event['start_date']));
			$convertTimeStart = date('H:i', strtotime($data_event['start_date']));
			$data_event['sDate'] = $convertDateStart;
			$data_event['sTime'] = $convertTimeStart;
			unset($data_event['start_date']);
		}

		if (array_key_exists('end_date', $data_event)) {
			$convertDateEnd = date('d-M-Y', strtotime($data_event['end_date']));
			$convertTimeEnd = date('H:i', strtotime($data_event['end_date']));
			$data_event['eDate'] = $convertDateEnd;
			$data_event['eTime'] = $convertTimeEnd;
			unset($data_event['end_date']);
		}

		return $data_event;
	}

	/**
	 * Get data by api
	 */
	public function getDataByIdEventApi(FirestoreRepository $fs, $id)
	{
		$data = $this->_getById($fs, $id);
		return json_encode($data);
	}

	public function getDataEventApi(FirestoreRepository $fs)
	{
		$data = $this->_get($fs);
		$data = $data->sortByDesc(function($item) {
			return strtotime($item['start_date']);
		});
		$data = $data->groupBy(function($item, $key) {
			$start_date = explode(" ", $item['start_date']);
			$start_date = str_replace("-", " ", $start_date);
			return $start_date[0];
		});

		$return = [];
		foreach ($data->toArray() as $key => $val)
		{
			$new = [
				"title" => $key,
				"data" => $val
			];

			array_push($return, $new);
		}

		return json_encode($return);
	}

	/**
	 * end of data by api
	 */

	// public function new(FirestoreRepository $fs, Request $request)
	// {
	// 	$payload = json_decode($request->getContent(), TRUE);
	// 	$fs->create('events', $payload);
	// 	return json_encode([
	// 		'success' => true,
	// 		'message' => 'EVENT_CREATED'
	// 	]);
	// }

	// public function edit(FirestoreRepository $fs, Request $request, $id)
	// {
	// 	$payload = json_decode($request->getContent(), TRUE);
	// 	$fs->update('events', $id, $payload);
	// 	return json_encode([
	// 		'success' => true,
	// 		'message' => 'EVENT_UPDATED'
	// 	]);
	// }

	// public function remove(FirestoreRepository $fs, Request $request, $id)
	// {
	// 	$payload = json_decode($request->getContent(), TRUE);
	// 	$fs->delete('events', $id);
	// 	return json_encode([
	// 		'success' => true,
	// 		'message' => 'EVENT_DELETED'
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

		return View::make("event/add")->with(array('dataVenue' => $venue_location));
	}

	public function editEventIndex(FirestoreRepository $fs, $id)
	{
		/** 
		 * get data event by id
		 * */
		$data_event = $this->_getById($fs, $id);
		/**
		 * get data venue by id
		 */
		$getSelectedVenue = $fs->get('venue_location', $data_event['venue_key']);
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

		return View::make("event/edit")
			->with(array(
				'dataEvent' 		=> $data_event,
				'dataValueVenue' 	=> $arrValVenue,
				'dataAllVenue'		=> $all_venue_location
			));
	}

	public function createEvent(FirestoreRepository $fs, Request $request)
	{
		// if ($request->isMethod('post')) {					
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
		$firebase_storage_path = 'imgs/event/';
		$localfolder = public_path('firebase-temp-uploads') . '/';
		$imageName = $file_upload->getClientOriginalName();

		if ($file_upload->move($localfolder, $imageName)) {
			$uploadedfile = fopen($localfolder . $imageName, 'r');
			//execute upload to storage
			app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path .  $imageName]);
			//will remove from local laravel folder  
			unlink($localfolder . $imageName);

			$bucketName = 'https://firebasestorage.googleapis.com/v0/b/dragon-sea-view.appspot.com/o/' . urlencode($firebase_storage_path) . $imageName . '?alt=media';
			$arrInsert = array(
				'title' 		=> $title,
				'venue_key'		=> app('firebase.firestore')->database()->document('venue_location' . '/' . $venueId),
				'img_path'		=> $bucketName,
				'start_date'	=> $start_date . ' ' . $start_time,
				'end_date'		=> $end_date . ' ' . $end_time,
				'desc'			=> $desc,
				'is_deleted'	=> '0'
			);

			$create = $fs->create('events', $arrInsert);
			$event_id = $create->id();

			$fcm = new FCMRepository();
			$fcm->sendToMultipleDevices([
				'title' => $title,
				'body' => $desc,
				'type' => 'event',
				'id' => $event_id
			]);

			// return Redirect::to('addEvent')->withSuccess('Add Event Successfull!');
			return response()->json([
				'success' => true,
				'url' => url('/listEvent')
			], 200);
		}
		// else {
		// 	return Redirect::to('addEvent')->withFail('Add Event Failed!');
		// }
		//}
	}

	public function editEvent(FirestoreRepository $fs, Request $request)
	{
		//if ($request->isMethod('post')) {
		// $id_collection		= $request->input('id_event');
		// $title 				= $request->input('title');
		// $venueId 			= $request->input('venue_location');
		// $start_date 		= $request->input('sDate');
		// $end_date 			= $request->input('eDate');
		// $start_time 		= $request->input('sTime');
		// $end_time 			= $request->input('eTime');
		// $desc 				= $request->input('desc');
		// $file_upload 		= $request->file('file');

		$id_collection		= $request->id_event;
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
		$firebase_storage_path = 'imgs/event/';
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

			$fs->update('events', $id_collection, $arrUpdate);

			return response()->json([
				'success' => true,
				'url' => url('/listEvent')
			], 200);

			// 	return Redirect::to('listEvent')->withSuccess('Edit Event Successfull!');
			// } else {
			// 	return Redirect::to('listEvent')->withFail('Edit Event Failed!');
			// }
			//}
		}
	}

	public function softDeleteEvent(FirestoreRepository $fs, $id)
	{
		$arrDelete = array(
			'is_deleted'	=> '1'
		);

		$fs->update('events', $id, $arrDelete);
		return response()->json([
			'success' => true,
			'url'=>url('/listEvent')
		], 200);
		// return Redirect::to('listEvent')->withSuccess('Delete Event Successfull!');
	}

	public function listEventServerSide()
	{
		// $fcm = new FCMRepository();

		// dummy send notif
		// $fcm->sendToMultipleDevices([
		// 	'title' => 'test notif',
		// 	'body' => 'body test',
		// 	'type' => 'promo',
		// 	'id' => 'asd'
		// ]);
		return view('event/index');
	}

	public function getDataEvent(FirestoreRepository $fs, $id = '')
	{		

		/**
		 * return pake collection atau pake type biasa bisa karena data yang di return sudah bersih
		 * Include
		 * draw
		 * total data
		 * total filter
		 * data clean
		 */
		$data_event = $this->_get($fs);

		return datatables($data_event)
			->addIndexColumn()
			->addColumn('action', function ($data_event) {
				return $this->_getActionColumn($data_event);
			})
			->rawColumns(['action'])
			->make(true);
	}

	protected function _getActionColumn($data)
	{
		$editUrl = route('editEventPage', $data['id_event']);
		$deleteUrl = route('deleteEventdata', $data['id_event']);

		$btn = "<a href='$editUrl' class='edit btn btn-primary btn-sm'>Edit</a>";
		$returnBtn = $btn . ' ' . '<a href="' . $deleteUrl . '" class="edit btn btn-danger btn-sm delete">Delete <i class="fa fa-trash"></i></a>';

		return $returnBtn;
		// "<a onclick='$asd' class='edit btn btn-danger btn-sm'>Delete <i class='fa fa-trash'></i></a>"

	}
}
