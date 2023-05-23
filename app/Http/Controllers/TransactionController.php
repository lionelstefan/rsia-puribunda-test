<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Firebase\FirestoreRepository;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transaction/index');
    }

    public function _get(FirestoreRepository $fs, $id = '')
	{
		$_query = 'N';
		$_docs = $fs->get('transaction', $id, $_query);  

		$returnData = collect($_docs)->map(function ($val, $key) {
			/**
			 * Merge array id and data
			 */
			$data = $val->data();
			$id = $val->id();   
			$arrNewIdTransaction = ['id_tx' =>  $id];
			$newData = array_merge($data, $arrNewIdTransaction);  

			if (array_key_exists('user_id', $newData)) {
				$temp = $newData;
				unset($temp['user_id']);
				$user_id = $val->data()['user_id']->id();
				$temp['user_id'] = $user_id;
				return $temp;
			}

			return $newData;
		});

		return $returnData;
	}

    public function _getById(FirestoreRepository $fs, $id = '')
	{
		/**
		 * get tx data by id user
		 */
		$user_ref = app('firebase.firestore')
			->database()
			->collection('user')
			->document($id);

		$collections = app('firebase.firestore')
			->database()
			->collection('transaction');

		$query = $collections->where('user_id', '==', $user_ref);
		$getTxData = $query->documents()->rows();

		$return = collect($getTxData)
			->map(function($item) {
				return $item->data();
			});

		return $return;
	}

    /**
	 * Get data by api
	 */
	public function getDataByIdTxApi(FirestoreRepository $fs, $id)
	{
		$all = $this->_getById($fs, $id);

		$data = $all->sortByDesc(function($item) {
			return array_key_exists('created_date', $item) ? strtotime($item['created_date']) : null;
		})->groupBy(function($item, $key) {
			if (array_key_exists('created_date', $item)) 
			{
				$start_date = explode(" ", $item['created_date']);
				$formatted_date = date('d F Y', strtotime($start_date[0]));
				return $formatted_date;
			}
		});


		$earnt = $all->filter(function($item) {
			return $item['tx_type'] === 'GP';
		})->sortByDesc(function($item) {
			return array_key_exists('created_date', $item) ? strtotime($item['created_date']) : null;
		})->groupBy(function($item, $key) {
			if (array_key_exists('created_date', $item)) 
			{
				$start_date = explode(" ", $item['created_date']);
				$formatted_date = date('d F Y', strtotime($start_date[0]));
				return $formatted_date;
			}
		});


		$spent = $all->filter(function($item) {
			return $item['tx_type'] === 'RP';
		})->sortByDesc(function($item) {
			return array_key_exists('created_date', $item) ? strtotime($item['created_date']) : null;
		})->groupBy(function($item, $key) {
			if (array_key_exists('created_date', $item)) 
			{
				$start_date = explode(" ", $item['created_date']);
				$formatted_date = date('d F Y', strtotime($start_date[0]));
				return $formatted_date;
			}
		});

		$return = [
			"all" => [],
			"earnt" => [],
			"spent" => []
		];

		foreach ($data->toArray() as $key => $val)
		{
			$new = [
				"title" => $key,
				"data" => $val
			];

			array_push($return["all"], $new);
		}

		foreach ($earnt->toArray() as $key => $val)
		{
			$new = [
				"title" => $key,
				"data" => $val
			];

			array_push($return["earnt"], $new);
		}

		foreach ($spent->toArray() as $key => $val)
		{
			$new = [
				"title" => $key,
				"data" => $val
			];

			array_push($return["spent"], $new);
		}

		return json_encode($return);
	}

    public function getDataTxApi(FirestoreRepository $fs)
	{
		$data = $this->_get($fs);
		return json_encode($data);
	}
	/**
	 * end of data by api
	 */

    public function getDataTransaction(FirestoreRepository $fs, $id = '')
    {
        /**
		 * return pake collection atau pake type biasa bisa karena data yang di return sudah bersih
		 * Include
		 * draw
		 * total data
		 * total filter
		 * data clean
		 */
		$data_transaction = $this->_get($fs);

		return datatables($data_transaction)
			->addIndexColumn()			
			->make(true);
    }
}
