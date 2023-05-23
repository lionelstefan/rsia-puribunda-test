<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;

class KaryawanController extends Controller
{
    public function list()
    {
        return view('karyawan/index');
    }

    public function create()
    {
        $unit = Unit::all()->toArray();
        $jabatan = Jabatan::all()->toArray();
        return view()->make("karyawan/add")
            ->with([
                'unit' => $unit,
                'jabatan' => $jabatan
            ]);
    }

    public function addNewKaryawan()
    {
        $karyawan = new User();

        $check = User::where('username', request()->username)->first();

        if (!$check)
        {
            $karyawan->name = request()->name;
            $karyawan->username = request()->username;
            $karyawan->unit_id = request()->unit;
            
            $new_jabatan = request()->jabatan;
            foreach ($new_jabatan as $key => $j)
            {
                $chk = Jabatan::where('id', $j)->first();
                if (!$chk)
                {
                    $jabatan = new Jabatan();
                    $jabatan->name = $j;
                    $jabatan->save();
                    $new_jabatan[$key] = $jabatan->id;
                }
            }

            $karyawan->jabatan = json_encode($new_jabatan);
            $karyawan->password = request()->password;
            $karyawan->save();
            return response()->json([
                'success' => true,
                'url' => url('/list-karyawan')
            ], 200);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'username exists'
            ]
        ], 400);

    }

    public function getAllKaryawan()
    {
        $karyawans = collect(User::all())->map(function($items) {
            $items->jabatan = '';
            if ($items->jabatan !== null)
            {
                $temp = json_decode($items->jabatan, true);
                $items->jabatan = join(", ",
                    collect($temp)->map(function($item) {
                        return Jabatan::where('id', $item)->first()->name;
                    })->toArray()
                );
            }

            $items->unit_id = Unit::where('id', $items->unit_id)->first() ?
                Unit::where('id', $items->unit_id)->first()->name : '' ;

            return $items;
        })->toArray();

        return datatables($karyawans)
            ->addIndexColumn()
            ->addColumn('action', function ($data_event) {
                return $this->_getActionColumn($data_event);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    protected function _getActionColumn($data)
    {
        $editUrl = route('karyawan.edit', $data['id']);
        $deleteUrl = route('karyawan.delete', $data['id']);

        $btn = "<a href='$editUrl' class='edit btn btn-primary btn-sm'>Edit</a>";
        $returnBtn = $btn . ' ' . '<a href="' . $deleteUrl . '" class="edit btn btn-danger btn-sm delete">Delete <i class="fa fa-trash"></i></a>';

        return $returnBtn;
        // "<a onclick='$asd' class='edit btn btn-danger btn-sm'>Delete <i class='fa fa-trash'></i></a>"

    }

    public function deleteKaryawan($id)
    {
        User::where('id', $id)->delete($id);

        return response()->json([
            'success' => true,
            'url' => url('/list-karyawan')
        ], 200);
    }

    public function editKaryawan($id)
    {
        $karyawan = User::where('id', $id)->first();
        $karyawan->jabatan = json_encode($karyawan->jabatan);
        $unit = Unit::all()->toArray();
        $jabatan = Jabatan::all()->toArray();

        return view()->make("karyawan/edit")
        ->with([
            'karyawan' => $karyawan->toArray(),
            'unit' => $unit,
            'jabatan' => $jabatan
        ]);
    }

    public function confirmEditKaryawan()
    {
        $id = request()->id;
        $name = request()->name;

        $karyawan = User::where('id', $id)->first();
        $karyawan->name = $name;
        $karyawan->username = request()->username;
        $karyawan->unit_id = request()->unit;

        $new_jabatan = request()->jabatan;
        foreach ($new_jabatan as $key => $j) {
            $chk = Jabatan::where('id', $j)->first();
            if (!$chk) {
                $jabatan = new Jabatan();
                $jabatan->name = $j;
                $jabatan->save();
                $new_jabatan[$key] = $jabatan->id;
            }
        }

        $karyawan->jabatan = json_encode(request()->jabatan);
        $karyawan->password = request()->password;
        $karyawan->save();

        return response()->json([
            'success' => true,
            'url' => url('/list-karyawan')
        ], 200);
    }
}
