<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserController extends Controller
{

    //function register
    public function register(Request $request){

        //validasi apa saja inputannya
        //yg wajib id, name, email, pass

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|max:255|confirmed'
        ],[
            //pesan yg ditampilkan
            'name.required' => 'Nama Tidak Boleh Kosong',
            'name.max' => 'Panjang Karakter Nama Maksimum 255',
            'email.required' => 'Email Harus Diisi',
            'email.email'=>'Format Email Tidak Valid',
            'email.unique' => 'Email Sudah Terdaftar',
            'email.max' => 'Panjang Karakter Maks 255',
            'password.required' => 'Password harus diisi',
            'password.max' => 'Panjang Karakter Password Maks 255',
            'password.confirmed' => 'Password Tidak Sama'
        ]);

        //pengecekan dilakukan
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 404);
        } else {
            //simpan user baru ke db
           // kiri sesuaikan kolom db, kanan sesuaikan nama form inputan
           $user = new User();
           $user-> name = $request->name;
           $user-> email = $request->email;
           $user-> password = Hash::make($request->password);
           //save all
           $user->save();

           //kirim response json
           return response()->json([
            'status' => true,
            'message' => 'Registrasi Berhasil'
           ], 201);
        }
    
    }

    //function login
    public function login(Request $request ){
        //BUAT VALIDASI
        //BUAT PESAN GAGAL DAN BERHASIL UNTUK VALIDASI
        //PENGECEKAN GAGAL DAN BERHASIL
        //RESPONSE JSON GAGAL DAN BERHASIL

        $validator = Validator::make($request->all(), [
            'username' => 'required|email',
            'password' => 'required',
        ], [
            //pesan untuk validator
            'username.required' => 'Username Tidak Boleh Kosong',
            'name.email' => 'Username menggunakan format email',
            'password.required' => 'Password Harus Diisi',
        ]);

        //pengecekan
        if($validator->fails()){
            //gagal

            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 404);
        } else {

            //ada pengecekan auth
            if(Auth::attempt(['email'=>$request->username, 'password'=>$request->password])){
                //kl valid 
                //buatkan token sanctum
                $user = auth()->user();
                $token = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                        'status' => true,
                        'message' => 'Login Berhasil',
                        'token' => $token
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Login Gagal'
                   ], 400);
            }
            
        }

    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
