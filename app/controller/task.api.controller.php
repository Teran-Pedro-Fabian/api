<?php
    require_once 'app/controllers/api.controller.php';
    require_once 'app/helpers/auth.api.helper.php';
    require_once 'app/models/task.model.php';

    class TaskApiController extends ApiController {
        private $model;
        private $authHelper;

        function __construct() {
            parent::__construct();
            $this->model = new TaskModel();
            $this->authHelper = new AuthHelper();
        }

        function get($params = []) {
            $user = $this->authHelper->currentUser();
            if(!$user) {
                $this->view->response('Unauthorized', 401);
                return;
            }

            if($user->role!='ADMIN') {
                $this->view->response('Forbidden', 403);
                return;
            }

            if (empty($params)){
                $tareas = $this->model->getTasks();
                $this->view->response($tareas, 200);
            } else {
                $tarea = $this->model->getTask($params[':ID']);
                if(!empty($tarea)) {
                    if($params[':subrecurso']) {
                        switch ($params[':subrecurso']) {
                            case 'titulo':
                                $this->view->response($tarea->titulo, 200);
                                break;
                            case 'descripcion':
                                $this->view->response($tarea->descripcion, 200);
                                break;
                                
                            default:
                            $this->view->response(
                                'La tarea no contiene '.$params[':subrecurso'].'.'
                                , 404);
                                break;
                        }
                    } else
                        $this->view->response($tarea, 200);
                } else {
                    $this->view->response(
                        'La tarea con el id='.$params[':ID'].' no existe.'
                        , 404);
                }
            }
        }

        function delete($params = []) {
            $id = $params[':ID'];
            $tarea = $this->model->getTask($id);

            if($tarea) {
                $this->model->deleteTask($id);
                $this->view->response('La tarea con id='.$id.' ha sido borrada.', 200);
            } else {
                $this->view->response('La tarea con id='.$id.' no existe.', 404);
            }
        }

        function create($params = []) {
            $body = $this->getData();

            $titulo = $body->titulo;
            $descripcion = $body->descripcion;
            $prioridad = $body->prioridad;

            if (empty($titulo) || empty($prioridad)) {
                $this->view->response("Complete los datos", 400);
            } else {
                $id = $this->model->insertTask($titulo, $descripcion, $prioridad);

                // en una API REST es buena práctica es devolver el recurso creado
                $tarea = $this->model->getTask($id);
                $this->view->response($tarea, 201);
            }
    
        }

        function update($params = []) {
            $id = $params[':ID'];
            $tarea = $this->model->getTask($id);

            if($tarea) {
                $body = $this->getData();
                $titulo = $body->titulo;
                $descripcion = $body->descripcion;
                $prioridad = $body->prioridad;
                $this->model->updateTaskData($id, $titulo, $descripcion, $prioridad);

                $this->view->response('La tarea con id='.$id.' ha sido modificada.', 200);
            } else {
                $this->view->response('La tarea con id='.$id.' no existe.', 404);
            }
        }
    }
