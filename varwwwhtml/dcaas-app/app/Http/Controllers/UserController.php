<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;

class UserController extends Controller
{
    /**
     * Display a listing of the resource. gfgdf
     */
    public function index()
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

    /**
     *
     */
    public function registrar(User $user)
    {
        return RespuestaAPI::exito('registrar');
    }

    /**
     *
     */
    public function ver(User $user)
    {
        return RespuestaAPI::exito('ver');
    }

    /**
     *
     */
    public function login(User $user)
    {
        return RespuestaAPI::exito('login');
    }
}
