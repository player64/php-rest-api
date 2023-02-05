<?php

namespace Api\Controllers;

use Api\Models\Entities\FilmEntity;
use Api\Models\FilmModel;
use Api\Models\ModelException;

class FilmController extends Controller
{

    function __construct(\PDO $db)
    {
        $this->model = new FilmModel($db);
    }

    public function create(): ControllerResponse {
        $request = $this->parse_json_request();

        try {



            $model_entity_type = FilmEntity::class;
            $entity = $this->get_entity_from_request($request, $model_entity_type);
            $data = $this->model->create($entity);

            return new ControllerResponse([
                'message' => 'Successfully created',
                'data' => $data
            ], 200);

        } catch(ModelException $e) {
            return new ControllerResponse([
                'error' => $e->getMessage(),
            ], 400);
        } catch (ControllerException $e) {
        }
    }


}