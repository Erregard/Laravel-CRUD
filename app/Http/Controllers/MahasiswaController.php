<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $mahasiswas = Mahasiswa::latest()->paginate(1);
        return view('mahasiswa.index', compact('mahasiswas'));
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('mahasiswa.create');
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'nim'     => 'required',
            'nama'   => 'required'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/mahasiswas', $image->hashName());

        $mahasiswa = Mahasiswa::create([
            'image'     => $image->hashName(),
            'nim'     => $request->nim,
            'nama'   => $request->nama
        ]);

        if($mahasiswa){
            //redirect dengan pesan sukses
            return redirect()->route('mahasiswa.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('mahasiswa.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }
    
    /**
     * edit
     *
     * @param  mixed $mahasiswa
     * @return void
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.edit', compact('mahasiswa'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $mahasiswa
     * @return void
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $this->validate($request, [
            'nama'     => 'required',
            'nim'   => 'required'
        ]);

        //get data Mahasiswa by ID
        $mahasiswa = Mahasiswa::findOrFail($mahasiswa->id);

        if($request->file('image') == "") {

            $mahasiswa->update([
                'nama'     => $request->nama,
                'nim'   => $request->nim
            ]);

        } else {

            //hapus old image
            Storage::disk('local')->delete('public/mahasiswas/'.$mahasiswa->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/mahasiswas', $image->hashName());

            $mahasiswa->update([
                'image'     => $image->hashName(),
                'nama'     => $request->nama,
                'nim'   => $request->nim
            ]);
            
        }

        if($mahasiswa){
            //redirect dengan pesan sukses
            return redirect()->route('mahasiswa.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('mahasiswa.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }
    
    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();

        if($mahasiswa){
            //redirect dengan pesan sukses
            return redirect()->route('mahasiswa.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('mahasiswa.index')->with(['error' => 'Data Gagal Dihapus!']);
        }
    }

}
