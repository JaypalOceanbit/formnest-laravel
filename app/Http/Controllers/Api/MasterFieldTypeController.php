<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Models\MasterFieldType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MasterFieldTypeController extends BaseController
{
    /**
     * List all master field types with optional search, category filter, and status filter.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request)
    {
        $query = MasterFieldType::query();

        // Search by name, type_key, or description
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('type_key', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by status (1 = Active, 0 = Inactive)
        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $query->where('status', (int) $request->input('status'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, ['id', 'name', 'type_key', 'sort_order', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->ordered();
        }

        // Pagination or Full List
        if ($request->boolean('paginate') || $request->filled('per_page')) {
            $perPage = (int) $request->input('per_page', 15);
            $items = $query->paginate($perPage);

            $this->setSuccessMessage('Master field types retrieved successfully.');
            $this->addPaginationDatainSuccess($items);
            return $this->sendSuccessResult();
        }

        $items = $query->get();

        $this->setSuccessMessage('Master field types retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $items);
        $this->addSuccessResultKeyValue('count', $items->count());

        return $this->sendSuccessResult();
    }

    /**
     * Get detail of a single field type by id or type_key.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request, $id = null)
    {
        return $this->show($request, $id);
    }

    public function show(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');
        $typeKey = $request->input('type_key');

        if (empty($targetId) && empty($typeKey)) {
            $this->setFailMessage('Field type ID or type_key is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $fieldType = null;
        if (!empty($targetId)) {
            $fieldType = MasterFieldType::find($targetId);
        } elseif (!empty($typeKey)) {
            $fieldType = MasterFieldType::where('type_key', $typeKey)->first();
        }

        if (!$fieldType) {
            $this->setFailMessage('Master field type not found.');
            return $this->sendFailResultWithCode(404);
        }

        $this->setSuccessMessage('Master field type retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $fieldType);

        return $this->sendSuccessResult();
    }

    /**
     * Create / Add a new master field type.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        return $this->store($request);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        // Convert string JSON if passed
        if (isset($input['default_config']) && is_string($input['default_config'])) {
            $decoded = json_decode($input['default_config'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input['default_config'] = $decoded;
            }
        }

        $validator = Validator::make($input, [
            'name' => 'required|string|max:100',
            'type_key' => 'required|string|max:50|regex:/^[a-zA-Z0-9_\-]+$/|unique:master_field_types,type_key',
            'category' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'default_config' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ], [
            'name.required' => 'The field name is required.',
            'type_key.required' => 'The field type key/slug is required.',
            'type_key.regex' => 'The type key must only contain letters, numbers, hyphens, and underscores.',
            'type_key.unique' => 'This field type key already exists.',
        ]);

        if ($validator->fails()) {
            $this->setFailMessage('Validation error.');
            return $this->sendValidationError($validator->errors());
        }

        $fieldType = MasterFieldType::create([
            'name' => trim($input['name']),
            'type_key' => strtolower(trim($input['type_key'])),
            'category' => $input['category'] ?? 'basic',
            'icon' => $input['icon'] ?? null,
            'description' => $input['description'] ?? null,
            'default_config' => $input['default_config'] ?? null,
            'sort_order' => isset($input['sort_order']) ? (int) $input['sort_order'] : 0,
            'status' => isset($input['status']) ? (int) $input['status'] : 1,
        ]);

        $this->setSuccessMessage('Master field type created successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $fieldType);

        return $this->sendSuccessResult();
    }

    /**
     * Update an existing master field type.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Master field type ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $fieldType = MasterFieldType::find($targetId);

        if (!$fieldType) {
            $this->setFailMessage('Master field type not found.');
            return $this->sendFailResultWithCode(404);
        }

        $input = $request->all();

        // Convert string JSON if passed
        if (isset($input['default_config']) && is_string($input['default_config'])) {
            $decoded = json_decode($input['default_config'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input['default_config'] = $decoded;
            }
        }

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:100',
            'type_key' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('master_field_types', 'type_key')->ignore($fieldType->id),
            ],
            'category' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'default_config' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            $this->setFailMessage('Validation error.');
            return $this->sendValidationError($validator->errors());
        }

        $dataToUpdate = [];
        if (isset($input['name'])) {
            $dataToUpdate['name'] = trim($input['name']);
        }
        if (isset($input['type_key'])) {
            $dataToUpdate['type_key'] = strtolower(trim($input['type_key']));
        }
        if (array_key_exists('category', $input)) {
            $dataToUpdate['category'] = $input['category'];
        }
        if (array_key_exists('icon', $input)) {
            $dataToUpdate['icon'] = $input['icon'];
        }
        if (array_key_exists('description', $input)) {
            $dataToUpdate['description'] = $input['description'];
        }
        if (array_key_exists('default_config', $input)) {
            $dataToUpdate['default_config'] = $input['default_config'];
        }
        if (isset($input['sort_order'])) {
            $dataToUpdate['sort_order'] = (int) $input['sort_order'];
        }
        if (isset($input['status'])) {
            $dataToUpdate['status'] = (int) $input['status'];
        }

        $fieldType->update($dataToUpdate);

        $this->setSuccessMessage('Master field type updated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $fieldType->fresh());

        return $this->sendSuccessResult();
    }

    /**
     * Delete a master field type.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id = null)
    {
        return $this->destroy($request, $id);
    }

    public function destroy(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Master field type ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $fieldType = MasterFieldType::find($targetId);

        if (!$fieldType) {
            $this->setFailMessage('Master field type not found.');
            return $this->sendFailResultWithCode(404);
        }

        $fieldType->delete();

        $this->setSuccessMessage('Master field type deleted successfully.');
        $this->addSuccessResultKeyValue('deleted_id', $targetId);

        return $this->sendSuccessResult();
    }

    /**
     * Toggle or update status of a field type.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Master field type ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $fieldType = MasterFieldType::find($targetId);

        if (!$fieldType) {
            $this->setFailMessage('Master field type not found.');
            return $this->sendFailResultWithCode(404);
        }

        if ($request->has('status')) {
            $newStatus = (int) $request->input('status');
        } else {
            $newStatus = $fieldType->status == 1 ? 0 : 1;
        }

        $fieldType->status = $newStatus;
        $fieldType->save();

        $this->setSuccessMessage('Master field type status updated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $fieldType);

        return $this->sendSuccessResult();
    }
}
