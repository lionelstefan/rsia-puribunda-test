<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function list()
    {
        return view('jabatan/index');
    }

    public function create()
    {
        return view()->make("jabatan/add");
    }

    public function addNewJabatan()
    {
        $name = request()->name;
        $jabatan = new Jabatan();
        $jabatan->name = $name;
        $jabatan->save();
        return response()->json([
            'success' => true,
            'url' => url('/list-jabatan')
        ], 200);
    }

    public function getAllJabatan()
    {
        $jabatans = Jabatan::all();

        return datatables($jabatans->toArray())
            ->addIndexColumn()
            ->addColumn('action', function ($data_event) {
                return $this->_getActionColumn($data_event);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    protected function _getActionColumn($data)
    {
        $editUrl = route('jabatan.edit', $data['id']);
        $deleteUrl = route('jabatan.delete', $data['id']);

        $btn = "<a href='$editUrl' class='edit btn btn-primary btn-sm'>Edit</a>";
        $returnBtn = $btn . ' ' . '<a href="' . $deleteUrl . '" class="edit btn btn-danger btn-sm delete">Delete <i class="fa fa-trash"></i></a>';

        return $returnBtn;
        // "<a onclick='$asd' class='edit btn btn-danger btn-sm'>Delete <i class='fa fa-trash'></i></a>"

    }

    public function deleteJabatan($id)
    {
        Jabatan::find($id)->delete($id);

        return response()->json([
            'success' => true,
            'url' => url('/list-jabatan')
        ], 200);
    }

    public function editJabatan($id)
    {
        $jabatan = Jabatan::find($id)->first();
        return view()->make("jabatan/edit")
        ->with(array(
            'jabatan' => $jabatan,
        ));
    }

    public function confirmEditJabatan()
    {
        $id = request()->id;
        $name = request()->name;

        $jabatan = Jabatan::find($id);
        $jabatan->name = $name;
        $jabatan->save();

        return response()->json([
            'success' => true,
            'url' => url('/list-jabatan')
        ], 200);
    }
}
