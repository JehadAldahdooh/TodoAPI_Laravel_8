<?php

namespace App\Http\Controllers;
use App\Models\Todo;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Interfaces\TodoRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class TodoController extends Controller {
    protected $user;  //will be initialized Using request dependency injection 
    private TodoRepositoryInterface $todoRepository;

    public function __construct(TodoRepositoryInterface $todoRepository) {
        $this->middleware('auth', ['except' => ['signup', 'signin']]);
        $this->todoRepository = $todoRepository;
        //$this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
    * Display a listing of the todo task [status parameter is an optional].
    * output: JsonResponse with the data
    */
    public function index( Request $request ):JsonResponse {
        $status = $request->input( 'status' );
        $this->user = $request->attributes->get('user');                 
        return response()->json([
            'data' => $this->todoRepository->show($status,$this->user)
        ]);
    }

    /**
    * Create a new todo task item in DB.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store( Request $request ): JsonResponse  {
        $rules = array(
            'name'=>'required',
            'status' => 'required|in:NotStarted,OnGoing,Completed',
        );
        $validator = Validator::make( $request->all(), $rules );
        if ( $validator->fails() ) {
            return  $validator->messages();
        }
        $this->user = $request->attributes->get('user');             
        return $this->todoRepository->create($request->all(),$this->user);       
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  $id
    * @return \Illuminate\Http\Response
    */
    public function update( Request $request,  $id ) {

        $rules = array(
            'name'=>'required',
            'status' => 'required|in:NotStarted,OnGoing,Completed',
        );

        $validator = Validator::make( $request->all(), $rules );
        if ( $validator->fails() ) {
            return  $validator->messages();
        }
        
        $this->user = $request->attributes->get('user');  
        return $this->todoRepository->update($id,$request->all(),$this->user);
        
    }

    /**
    * Remove the specified todo task from DB.
    *
    * @param  $id
    * @return \Illuminate\Http\Response
    */
    public function remove(Request $request, $id ) {
        $this->user = $request->attributes->get('user');             
        return $this->todoRepository->destroy($id,$this->user );
    }

}
