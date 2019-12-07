<?php

namespace App\Http\Controllers;

use App\Models\LinhaTeorica;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorController extends AbstractController
{

    public function store(Request $request)
    {
        if ($id = base64_decode($request->id)) {
            $this->_model = $this->_model->find($id);
            $user = User::find($id);
        } else {
            $user = new User();
        }

        if (!empty($request->password)) {
            if ($request->password !== $request->password_confirmation ) {
                return redirect()->back();
            } else {
                $user->password =  bcrypt($request->password);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->nu_telefone = $request->nu_telefone;
        $user->nu_celular = $request->nu_celular;
        $user->save();

        $this->_model->user_id = $user->id;
        $this->_model->linha_id = $request->linha_id;
        $this->_model->nu_crp = str_replace('/', '', $request->nu_crp);
        $this->_model->save();

        return redirect($this->_redirectSave);
    }

    public function create()
    {
        $aDados = $this->_recuperarDados();
        $aDados['model'] = $this->_model;
        $aDados['linhas'] = LinhaTeorica::all()->sortBy('tx_nome');

        return view("{$this->_dirView}.formulario", $aDados);
    }

    public function edit($id)
    {
        $aDados = $this->_recuperarDados();
        $aDados['model'] = $this->_model->find(base64_decode($id));

        $aDados['model']->name = $aDados['model']->user->name;
        $aDados['model']->email = $aDados['model']->user->email;
        $aDados['model']->username = $aDados['model']->user->username;
        $aDados['model']->nu_telefone = $aDados['model']->user->nu_telefone;
        $aDados['model']->nu_celular = $aDados['model']->user->nu_celular;

        $aDados['linhas'] = LinhaTeorica::all()->sortBy('tx_nome');

        return view("{$this->_dirView}.formulario", $aDados);
    }

    public function searchCrp($crp)
    {
        $registro = Supervisor::where('nu_crp', $crp)->first();
        if (empty($registro)) {
            $return = [
                "type" => "success",
                "msg"  => "Nenhuma informação"
            ];
        } else {
            $return = [
                "type" => "error",
                "msg"  => "Número de CRP em uso!"
            ];
        }

        return $return;
    }
}
