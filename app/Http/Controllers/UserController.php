<?php

namespace App\Http\Controllers;

use App\Http\Middleware\UserMiddleware;
use App\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Psy\debug;

class UserController extends Controller
{
    public function __construct() {

    }


    public function editprofile() {
	$this->middleware(UserMiddleware::class);
        return view('showUser');
    }

    public function applyedit(Request $request) {
        if( !Auth::check() || Auth::user()->auth_level !== 2 )
        {
            return response()->json([
                "success" => false,
                "error_message" => "Operazione non consentita."
            ]);
        }
        $nome=$request->input('nome','');
        $cognome=$request->input('cognome','');
        $residenza=$request->input('residenza','');
        $datanascita=$request->input('datanascita','');
        $occupazione=$request->input('occupazione','');

        if( $nome !== '' && $cognome !== '' && $residenza!='' && $datanascita!='' && $occupazione!=''){

            $datiutente=array(
                'nome'=>$nome,
                'cognome'=>$cognome,
                'residenza'=>$residenza,
                'datanascita'=>$datanascita,
                'occupazione'=>$occupazione);
            $updateOk = User::updateCurrentUser($datiutente);

            if( !$updateOk )
            {
                return response()->json([
                    "success" => false,
                    "error_message" => "Aggiornamento dei dati non riuscito"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Aggiornamento dei dati effettuato"
            ]);

        }else{
            return response()->json([
                "success" => false,
                "error_message" => "Riempi tutti i campi"
            ]);
        }
    }

    public function applyaccessedit(Request $request) {
        if( !Auth::check() || Auth::user()->auth_level !== 2 )
        {
            return response()->json([
                "success" => false,
                "error_message" => "Operazione non consentita."
            ]);
        }
        $email=$request->input('email','');
        $password=$request->input('password','');

        if( $email !== '' && $password){

            $datiutente=array(
                'email'=>$email,
                'password'=>$password);
            $updateOk = User::updateCurrentUserAccess($datiutente);

            if( !$updateOk )
            {
                return response()->json([
                    "success" => false,
                    "error_message" => "Aggiornamento dei dati non riuscito"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Aggiornamento dei dati effettuato"
            ]);

        }else{
            return response()->json([
                "success" => false,
                "error_message" => "Riempi tutti i campi"
            ]);
        }
    }

    public function updateUserStaff(Request $request){

        if( !Auth::check() || Auth::user()->auth_level !== 4 )
        {
            return response()->json([
                "success" => false,
                "error_message" => "Operazione non consentita."
            ]);
        }

        $nome = $request->input('nome', '');
        $cognome = $request->input('cognome', '');
        $id_utente = $request->input('id', '');
        $password=$request->input('password','');

        if( $nome !== '' && $cognome !== '' && $id_utente !== ''){
            //caso in cui devo modificare i campi senza modificare la password
            $data = array('nome'=>$nome,'cognome'=>$cognome, 'id'=>$id_utente,'password'=>$password);
            $updateOk = User::updateUserStaff($data);

            if( !$updateOk )
            {
                return response()->json([
                    "success" => false,
                    "error_message" => "Aggiornamento dei dati non riuscito"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Aggiornamento dei dati effettuato"
            ]);

        }else{
            return response()->json([
                "success" => false,
                "error_message" => "Riempi tutti i campi"
            ]);
        }
    }

    public function deletemyaccount(Request $request)
    {
        $utente = User::find(Auth::user()->id);
        $utente->delete();

        return redirect()->route('catalogo')->with('messages',[['title'=>'Account Eliminato','type'=>'success','message'=>'Hai eliminato correttamente il tuo account']]);;
    }
}
