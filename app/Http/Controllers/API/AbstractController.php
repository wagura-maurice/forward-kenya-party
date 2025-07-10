<?php
// app/Http/Controllers/API/AbstractController.php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\CoreQueryBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractController extends Controller
{
    protected $modelInstance;
    protected $queryBuilder;

    public function __construct()
    {
        $this->modelInstance = $this->getModelInstance();
        $this->queryBuilder = new CoreQueryBuilder(
            $this->modelInstance,
            $this->getAllowedFilters(),
            $this->getAllowedIncludes(),
            $this->getDefaultSort(),
            $this->getAllowedSorts()
        );
    }

    protected function getModelInstance(): Model
    {
        $modelClass = $this->getModel();
        return new $modelClass;
    }

    public function index(Request $request)
    {
        return $this->queryBuilder->read($this->modelInstance->newQuery(), true);
    }

    public function store()
    {
        $storeRequestClass = $this->modelInstance::getRequestClass();
        $storeRequest = app($storeRequestClass);
        $validatedData = $storeRequest->validated();
        return $this->queryBuilder->create($validatedData);
    }

    public function show(Request $request, $id)
    {
        return $this->queryBuilder->read($this->modelInstance->newQuery()->whereKey($id), false);
    }

    public function update($id)
    {
        $updateRequestClass = $this->modelInstance::getRequestClass();
        $updateRequest = app($updateRequestClass);
        $validatedData = $updateRequest->validated();

        return $this->queryBuilder->update($validatedData, $id);
    }

    public function destroy($id)
    {
        return $this->queryBuilder->destroy($id);
    }

    abstract protected function getModel(): string;
    abstract protected function getAllowedFilters(): array;
    abstract protected function getAllowedIncludes(): array;
    abstract protected function getDefaultSort(): string;
    abstract protected function getAllowedSorts(): array;
}
