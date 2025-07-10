<?php
// app/Service/CoreQueryBuilder.php
namespace App\Services;

use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CoreQueryBuilder
{
    protected $modelClass;
    protected $resourceClass;
    protected $description;
    protected $allowedFilters;
    protected $allowedIncludes;
    protected $defaultSort;
    protected $allowedSorts;
    protected $perPage;

    public function __construct(Model $modelInstance, $filters, $includes, $sort, $sorts)
    {
        $this->modelClass = new $modelInstance;
        $this->resourceClass = $this->modelClass::getResourceClass();
        $this->description = strtoupper($this->modelClass->getTable() . ' , core query builder.');
        $this->allowedFilters = $filters;
        $this->allowedIncludes = $includes;
        $this->defaultSort = $sort;
        $this->allowedSorts = $sorts;
    }

    public function create(array $data)
    {
        try {
            $validator = Validator::make($data, $this->modelClass::createRules());
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validatedData = $validator->validated();
            $modelInstance = new $this->modelClass($validatedData);

            if ($modelInstance->save()) {
                return (new $this->resourceClass($modelInstance))
                        ->additional(['throwable' => ['status' => 'success', 'message' => 'Resource successfully created.']]);
            } else {
                throw new \Exception("Unable to create resource.");
            }
        } catch (ValidationException $ve) {
            return (new $this->resourceClass(null))
                ->additional([
                    'throwable' => [
                        'status' => 'validation_error',
                        'message' => $ve->errors()
                    ]
                ])->response()->setStatusCode(422);
        } catch (\Throwable $th) {
            return (new $this->resourceClass(null))
                ->additional([
                    'throwable' => [
                        'status' => 'danger',
                        'message' => $th->getMessage()
                    ]
                ])->response()->setStatusCode(500);
        }
    }

    public function read($query, bool $collection = true)
    {
        $queryBuilder = QueryBuilder::for($query)
            ->allowedFilters($this->allowedFilters)
            ->allowedIncludes(is_array($this->allowedIncludes) ? $this->allowedIncludes : [])
            ->defaultSort($this->defaultSort)
            ->allowedSorts($this->allowedSorts);

        // Check if 'created_between' filter is provided in the request
        if (request()->has('filter.created_between')) {
            // Parse the start and end dates from the request
            $dateFilter = request()->input('filter.created_between');
            $startDate = $dateFilter['start'];
            $endDate = $dateFilter['end'];

            // Apply the 'created_between' scope to filter by date range
            $queryBuilder->createdBetween(['start' => $startDate, 'end' => $endDate]);
        }

        if ($collection) {
            return $this->resourceClass::collection($queryBuilder->jsonPaginate());
        } else {
            $modelInstance = $queryBuilder->firstOrFail();
            return new $this->resourceClass($modelInstance);
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $validator = Validator::make($data, $this->modelClass::updateRules($id));
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validatedData = $validator->validated();
            $modelInstance = $this->modelClass->findOrFail($id);
            $modelInstance->fill($validatedData);

            if ($modelInstance->save()) {
                return (new $this->resourceClass($modelInstance))
                        ->additional(['throwable' => ['status' => 'success', 'message' => 'Resource successfully updated.']]);
            } else {
                throw new \Exception("Unable to update resource.");
            }
        } catch (ValidationException $ve) {
            return (new $this->resourceClass(null))
                ->additional([
                    'throwable' => [
                        'status' => 'validation_error',
                        'message' => $ve->errors()
                    ]
                ])->response()->setStatusCode(422);
        } catch (\Throwable $th) {
            return (new $this->resourceClass(null))
                ->additional([
                    'throwable' => [
                        'status' => 'danger',
                        'message' => $th->getMessage()
                    ]
                ])->response()->setStatusCode(500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $modelInstance = $this->modelClass->findOrFail($id);

            if ($modelInstance->delete()) {
                return (new $this->resourceClass($modelInstance))
                    ->additional(['throwable' => ['status' => 'success', 'message' => 'Resource successfully deleted.']]);
            } else {
                return (new $this->resourceClass(null))
                    ->additional(['throwable' => ['status' => 'warning', 'message' => 'Resource not deleted.']]);
            }
        } catch (\Throwable $th) {
            return (new $this->resourceClass(null))
                ->additional(['throwable' => ['status' => 'danger', 'message' => 'Error: ' . $th->getMessage()]]);
        }
    }
}
