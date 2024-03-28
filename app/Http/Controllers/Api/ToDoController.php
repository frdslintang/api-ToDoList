<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
    public function index()
    {
        $todos = Todo::all();

        //pengecekan apabila tidak ada data sama sekali
        if($todos->count() > 0){
            return response()->json([
                'status'=>true,
                'data'=>$todos
            ], 200);
        } else {
            return response()->json([
                'status'=>false,
                'message' => 'Data ToDos Tidak Ada'
            ], 404);
        }

    }

    public function store(Request $request)
    {
        //initial value
        $status = false;
        $message = '';

        //1. validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100|unique:todos',
            'description' => 'required|max:100'
        ],[
            'title.required' => 'Judul Harus Diisi',
            'title.max' => 'Judul ToDo Maksmial 60 Karakter',
            'title.unique' => 'Judul ToDo Sudah Ada',

            'description.required' => 'Deskripsi Harus Diisi',
            'description.max' => 'Deskripsi ToDo Maksmial 180 Karakter',
        ]);

        //2. creating sekaligus pengecekan validasinya
        if ($validator->fails()){
            $status = false;
            $message = $validator->errors();

            //3. sending response json
            return response()->json([
                'status' => $status,
                'message' => $message
            ],400);

        } else {
            $status = true;
            $message = 'Data ToDo Berhasil Ditambah';

            //simpan ke database
            $todo = new Todo();
            $todo->title = $request->title;
            $todo->description = $request->description;
            $todo->save();

            //3. sending response json jika sukses
            return response()->json([
                'status' => $status,
                'message' => $message
            ],201);

        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //menampilkan data tunggal
        $todo = Todo::find($id);
        
        if($todo != null){
        //response json kl berhasil dan tdk null
        return response()->json([
            'status' => true,
            'data'=> $todo
        ], 200);
        } else {
        //response json kl gagal dan id tdk ada
        return response()->json([
            'status' => false,
            'message' => 'Data ToDo Tidak Ada'
        ], 404);
        }

       

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //validasi data
        $validator = Validator::make($request->all(), [
            'title'=> [
                'required',
                'max:100',
                Rule::unique('todos')->ignore($id)
            ],
            'description'=> ['required','max:180']
        ], [
                //pesan yang ditampilkan
                'title.required' => 'Judul Harus Diisi',
                'title.max' => 'Judul ToDo Maksmial 60 Karakter',
                'title.unique' => 'Judul ToDo Sudah Ada',
    
                'description.required' => 'Deskripsi Harus Diisi',
                'description.max' => 'Deskripsi ToDo Maksmial 180 Karakter',
              ]);

              //kalau ada kegagalan (tdk unique / kosong)
              if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 404);
              } else {
                //kl berhasil update 
                $todo = Todo::find($id);

                //update ke database 
                $todo->title = $request->title;
                $todo->description = $request->description;
                $todo->is_done = $request->is_done;
                $todo->save();

                //kirim response kl berhasil
                return response()->json([
                    'status' => true,
                    'message' => 'Data ToDo berhasil diupdate'
                ]);
              }
              


        //update data
        //kirim json ke client
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //membuat variabel untuk mengahpus tabel todo
        $todo = Todo::destroy($id);

        if($todo){
            return response()->json([
                'status'=> true,
                'message' => 'Data Todo Berhasil Dihapus'
            ], 200);
        } else {
            return response()->json([
                'status'=> false,
                'message' => 'Data ToDo Gagal Dihapus'
            ], 404);
        }

    }
}
