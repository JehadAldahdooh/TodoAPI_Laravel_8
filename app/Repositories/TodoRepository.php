<?php

namespace App\Repositories;

use App\Interfaces\TodoRepositoryInterface;
use App\Models\Todo;

class TodoRepository implements TodoRepositoryInterface {

    public function show( $status, $user ) {
        $cols = [ 'id', 'name', 'description', 'status', 'created_at', 'updated_at' ];
        $tasks = $user->todo();
        if ( $status ) {
            $tasks->where( 'status', $status );
        }
        return $tasks->get( $cols )->toArray();
    }

    public function create( array $taskDetails, $user ) {
        try {
            $new_task = $user->todo()->create( $taskDetails );
        } catch( Throwable $e ) {
            return response()->json( [
                'status' => false,
                'message' => $e->getMessage()
            ] );
        }
        if ( $new_task->exists ) {
            return response()->json( [
                'message' => 'ok',
                'task' => $new_task,
            ], 200 );
        } else {
            return response()->json( [
                'status'=>false,
                'message'=>'failed'
            ], 500 );
        }
    }

    public function destroy( $taskId, $user ) {
        try {
            $task = $user->todo()->find( $taskId );

            //$task = Todo::find( $id );
            if ( $task ) {
                $task->delete();
                return response()->json( [
                    'message'=>'successfully deleted',
                    'task'=> $task
                ], 200 );
            } else {
                return response()->json( [
                    'message'=>'failed: task id is not found'
                ], 500 );
            }

        } catch( Throwable $e ) {
            return $e->getMessage();
        }
    }

    public function update( $taskId, array $taskDetails, $user ) {
        $task = $user->todo()->find( $taskId );
        switch( $task ) {
            case( true ):
            if ( $task->update( $taskDetails ) ) {
                return response()->json( [
                    'status' => 'success',
                    'task' => $task,
                ] );
            } else {
                return response()->json( [
                    'task'=>$task,
                    'message'=>'failed'
                ], 500 );
            }
            break;
            case( false ):
            return response()->json( [
                'message'=>'task is not found'
            ], 500 );
            break;
            default:
            return response()->json( [
                'message'=>'something went wrong'
            ], 500 );
        }
    }

}
