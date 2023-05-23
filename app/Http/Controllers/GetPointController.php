<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Firebase\FirestoreRepository;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class GetPointController extends Controller
{
    public function index()
    {
        return view('frontend/getPoint');
    }

    public function getPointCreate(FirestoreRepository $fs, Request $request)
    {
        $member_id                  = $request->member_id;
        $current_point_member       = $request->current_point_member;
        $bill_number                = $request->bill_number;        
        $total_get_point            = $request->total_get_point;

        //Validator from laravel
        $rulesValidator = array(
            'member_id'             => 'required',
            'current_point_member'  => 'required|numeric|min:1',
            'bill_number'           => 'required',
            'total_bill'            => 'required',
            'total_get_point'       => 'required|numeric|lte:current_point_member',
        );

        $messageValidator = array(
            'member_id.required'                => 'Member Id field is required',
            'current_point_member.required'     => 'Current Point Member field is required',
            'bill_number.required'              => 'Bill Number field is required',
            'total_bill.required'               => 'Total Bill field is required',                     
            'total_get_point.required'          => 'Total Redeem field is required',    
            'total_get_point.gt'                => 'Member point insufficient',
        );

        $validator = Validator::make($request->all(), $rulesValidator, $messageValidator);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $finalPointMember = bcadd($current_point_member, $total_get_point);
        //get tier member
        $userController = new UserController;
        $getTierMember = $userController->_get_tier($finalPointMember);

        $arrUpdateUser = array(
            'point'         =>  (int)$finalPointMember,
            'tier'          =>  $getTierMember 
        );

        $fs->update('user', $member_id, $arrUpdateUser); 

        $arrInsertTx = array(
            'no_bill'           => $bill_number,
            'total_point'       => (int)$total_get_point,
            'tx_type'           => 'GP',
            'user_id'           => app('firebase.firestore')->database()->document('user' . '/' . $member_id),
            'created_date'      => date('Y-m-d H:i:s')               
        );

        $fs->create('transaction', $arrInsertTx);
        
        return response()->json([
            'success' => true
        ], 200);
    }

        //INI BISA DIJADIIN 1 FUNCTION AJA GLOBAL SEPERTINYA HMMMMMMMM
        public function checkMemberExist(FirestoreRepository $fs, Request $request)
        {
            $member_id      = $request->id; 
            $_query = 'Y';       
            $checkIdExist   = $fs->get('user', $member_id, $_query);        
            $data_event 	= $checkIdExist->data();
            if(!empty($data_event))
            {
                return response()->json([
                    'match'     => true,
                    'data'      => $data_event,
                    'message'   => 'Data Match!'                
                ], 200);
            }else{
                return response()->json([
                    'match'     => false,
                    'message'   => 'Data Not Found!'                
                ], 400);
            }
        }
}
