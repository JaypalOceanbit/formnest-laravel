<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Models\MasterTemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MasterTemplateCategoryController extends BaseController
{
    /**
     * List all master template categories with optional search and status filter.
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
        $query = MasterTemplateCategory::withCount('templates');

        // Search by name or description
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status (1 = Active, 0 = Inactive)
        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $query->where('status', (int) $request->input('status'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, ['id', 'name', 'sort_order', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->ordered();
        }

        // Pagination or Full List
        if ($request->boolean('paginate') || $request->filled('per_page')) {
            $perPage = (int) $request->input('per_page', 15);
            $items = $query->paginate($perPage);

            $this->setSuccessMessage('Master template categories retrieved successfully.');
            $this->addPaginationDatainSuccess($items);
            return $this->sendSuccessResult();
        }

        $items = $query->get();

        $this->setSuccessMessage('Master template categories retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $items);
        $this->addSuccessResultKeyValue('count', $items->count());

        return $this->sendSuccessResult();
    }

    /**
     * Get detail of a single category by id or slug.
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
        $slug = $request->input('slug');

        if (empty($targetId) && empty($slug)) {
            $this->setFailMessage('Category ID or slug is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $query = MasterTemplateCategory::with(['templates' => function ($q) {
            $q->active()->ordered();
        }]);

        $category = null;
        if (!empty($targetId)) {
            $category = $query->find($targetId);
        } elseif (!empty($slug)) {
            $category = $query->where('slug', $slug)->first();
        }

        if (!$category) {
            $this->setFailMessage('Master template category not found.');
            return $this->sendFailResultWithCode(404);
        }

        $this->setSuccessMessage('Master template category retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $category);

        return $this->sendSuccessResult();
    }

    /**
     * Create / Add a new master template category.
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

        $validator = Validator::make($input, [
            'name' => 'required|string|max:150|unique:master_template_categories,name',
            'slug' => 'nullable|string|max:150|unique:master_template_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ], [
            'name.required' => 'The category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'slug.unique' => 'A category with this slug already exists.',
        ]);

        if ($validator->fails()) {
            $this->setFailMessage('Validation error.');
            return $this->sendValidationError($validator->errors());
        }

        $slug = !empty($input['slug']) ? Str::slug($input['slug']) : Str::slug($input['name']);

        $category = MasterTemplateCategory::create([
            'name' => trim($input['name']),
            'slug' => $slug,
            'description' => $input['description'] ?? null,
            'icon' => $input['icon'] ?? null,
            'sort_order' => isset($input['sort_order']) ? (int) $input['sort_order'] : 0,
            'status' => isset($input['status']) ? (int) $input['status'] : 1,
        ]);

        $this->setSuccessMessage('Master template category created successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $category);

        return $this->sendSuccessResult();
    }

    /**
     * Update an existing category.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Category ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $category = MasterTemplateCategory::find($targetId);

        if (!$category) {
            $this->setFailMessage('Master template category not found.');
            return $this->sendFailResultWithCode(404);
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
                Rule::unique('master_template_categories', 'name')->ignore($category->id),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('master_template_categories', 'slug')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
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
        if (isset($input['slug'])) {
            $dataToUpdate['slug'] = Str::slug($input['slug']);
        } elseif (isset($input['name']) && empty($category->slug)) {
            $dataToUpdate['slug'] = Str::slug($input['name']);
        }
        if (array_key_exists('description', $input)) {
            $dataToUpdate['description'] = $input['description'];
        }
        if (array_key_exists('icon', $input)) {
            $dataToUpdate['icon'] = $input['icon'];
        }
        if (isset($input['sort_order'])) {
            $dataToUpdate['sort_order'] = (int) $input['sort_order'];
        }
        if (isset($input['status'])) {
            $dataToUpdate['status'] = (int) $input['status'];
        }

        $category->update($dataToUpdate);

        $this->setSuccessMessage('Master template category updated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $category->fresh());

        return $this->sendSuccessResult();
    }

    /**
     * Delete a category.
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
            $this->setFailMessage('Category ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $category = MasterTemplateCategory::find($targetId);

        if (!$category) {
            $this->setFailMessage('Master template category not found.');
            return $this->sendFailResultWithCode(404);
        }

        // Check if there are attached templates and cascade is not explicitly forced
        $templatesCount = $category->templates()->count();
        if ($templatesCount > 0 && !$request->boolean('force')) {
            $this->setFailMessage("Cannot delete category because it has {$templatesCount} attached template(s). Pass force=true to cascade delete.");
            $this->addFailResultKeyValue('templates_count', $templatesCount);
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $category->delete();

        $this->setSuccessMessage('Master template category deleted successfully.');
        $this->addSuccessResultKeyValue('deleted_id', $targetId);

        return $this->sendSuccessResult();
    }

    /**
     * Toggle or update status of a category.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Category ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $category = MasterTemplateCategory::find($targetId);

        if (!$category) {
            $this->setFailMessage('Master template category not found.');
            return $this->sendFailResultWithCode(404);
        }

        if ($request->has('status')) {
            $newStatus = (int) $request->input('status');
        } else {
            $newStatus = $category->status == 1 ? 0 : 1;
        }

        $category->status = $newStatus;
        $category->save();

        $this->setSuccessMessage('Master template category status updated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $category);

        return $this->sendSuccessResult();
    }
}
