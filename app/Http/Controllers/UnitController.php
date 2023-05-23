<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{

    public function list()
    {
        return view('unit/index');
    }

    public function create()
    {
        return view()->make("unit/add");
    }

    public function addNewUnit()
    {
        $name = request()->name;
        $unit = new Unit();
        $unit->name = $name;
        $unit->save();
        return response()->json([
            'success' => true,
            'url' => url('/list-unit')
        ], 200);
    }

    public function getAllUnit()
    {
        $units = Unit::all();

        return datatables($units->toArray())
            ->addIndexColumn()
            ->addColumn('action', function ($data_event) {
                return $this->_getActionColumn($data_event);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    protected function _getActionColumn($data)
    {
        $editUrl = route('unit.edit', $data['id']);
        $deleteUrl = route('unit.delete', $data['id']);

        $btn = "<a href='$editUrl' class='edit btn btn-primary btn-sm'>Edit</a>";
        $returnBtn = $btn . ' ' . '<a href="' . $deleteUrl . '" class="edit btn btn-danger btn-sm delete">Delete <i class="fa fa-trash"></i></a>';

        return $returnBtn;
        // "<a onclick='$asd' class='edit btn btn-danger btn-sm'>Delete <i class='fa fa-trash'></i></a>"

    }
    
    public function deleteUnit($id)
    {
        Unit::find($id)->delete($id);

        return response()->json([
            'success' => true,
            'url' => url('/list-unit')
        ], 200);
    }

    public function editUnit($id)
    {
        $unit = Unit::find($id)->first();
        return view()->make("unit/edit")
            ->with(array(
                'unit'=> $unit,
            ));
    }

    public function confirmEditUnit()
    {
        $id = request()->id;
        $name = request()->name;

        $unit = Unit::find($id);
        $unit->name = $name;
        $unit->save();

        return response()->json([
            'success' => true,
            'url' => url('/list-unit')
        ], 200);
    }
}
