<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Todo;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function createTodo(Request $request)
    {
        $msg = [];
        $rules = [
            'title' => 'required|min:3'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['http' => Response::HTTP_INTERNAL_SERVER_ERROR,'error' => $validator->messages()];
        }

        $title = $request->input('title');
        $todo = new Todo();
        $todo->title = $title;
        $todo->save();

        return ['http' => Response::HTTP_CREATED, 'success' => 'Tarefa Inserida com sucesso!'];
    }

    public function readAllTodos()
    {
        $todos = Todo::simplePaginate(2);
        if (count($todos->items()) > 0) {
            return ['http' => Response::HTTP_OK, 'list' => $todos->items(), 'current_page' => $todos->currentPage()];
        }

        return ['http' => Response::HTTP_NO_CONTENT, 'error' => 'Nenhuma tarefa encontrada'];
    }

    public function readTodo($id)
    {
        $todo = Todo::find($id);
        if ($todo) {
            return ['http' => Response::HTTP_OK, 'Todo' => $todo];
        }

        return ['http' => Response::HTTP_BAD_GATEWAY,'error' => "A tarefa {$id} não existe."];
    }

    public function updateTodo($id, Request $request)
    {
        $msg = [];
        $rules = [
            'title' => 'min:3',
            'done' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['http' => Response::HTTP_INTERNAL_SERVER_ERROR,'error' => $validator->messages()];
        }

        $title = $request->input('title');
        $done = $request->input('done');

        $todo = Todo::find($id);
        if ($todo) {
            if ($title) {
                $todo->title = $title;
            }

            if ($done !== null) {
                $todo->done = $done;
            }

            $todo->save();
            return ['http' => Response::HTTP_OK,'success' => 'Tarefa editada com sucesso!'];
        }

        return ['http' => Response::HTTP_INTERNAL_SERVER_ERROR,'error' => "Tarefa {$id} não existe, logo não pode ser atualizada!"];
    }

    public function deleteTodo($id)
    {
        $todo = Todo::find($id);
        $response = $todo->delete();
        if ($response) {
            return ['http' => Response::HTTP_OK, 'success' => 'Tarefa excluida com sucesso!'];
        }

        return ['http' => Response::HTTP_INTERNAL_SERVER_ERROR,'error' => ''];
    }
}
