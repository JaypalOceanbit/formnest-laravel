<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Models\MasterTemplate;
use App\Models\MasterTemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MasterTemplateController extends BaseController
{
    /**
     * List all master templates with optional search, category filter, and status filter.
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
        $query = MasterTemplate::with('category');

        // Search by name or description
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category_id
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        // Filter by is_featured
        if ($request->has('is_featured') && $request->input('is_featured') !== '' && $request->input('is_featured') !== null) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Filter by status (1 = Active, 0 = Inactive)
        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $query->where('status', (int) $request->input('status'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, ['id', 'name', 'category_id', 'sort_order', 'is_featured', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->ordered();
        }

        // Pagination or Full List
        if ($request->boolean('paginate') || $request->filled('per_page')) {
            $perPage = (int) $request->input('per_page', 10);
            $items = $query->paginate($perPage);

            $this->setSuccessMessage('Master templates retrieved successfully.');
            $this->addPaginationDatainSuccess($items);
            return $this->sendSuccessResult();
        }

        $items = $query->get();

        $this->setSuccessMessage('Master templates retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $items);
        $this->addSuccessResultKeyValue('count', $items->count());

        return $this->sendSuccessResult();
    }

    /**
     * Get detail of a single master template by id or slug.
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
            $this->setFailMessage('Template ID or slug is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $query = MasterTemplate::with('category');

        $template = null;
        if (!empty($targetId)) {
            $template = $query->find($targetId);
        } elseif (!empty($slug)) {
            $template = $query->where('slug', $slug)->first();
        }

        if (!$template) {
            $this->setFailMessage('Master template not found.');
            return $this->sendFailResultWithCode(404);
        }

        $this->setSuccessMessage('Master template retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $template);

        return $this->sendSuccessResult();
    }

    /**
     * Create / Add a new master template.
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

        // Convert string JSON if passed as JSON string
        if (isset($input['json']) && is_string($input['json'])) {
            $decoded = json_decode($input['json'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input['json'] = $decoded;
            }
        }

        $validator = Validator::make($input, [
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200',
            'category_id' => 'required|integer|exists:master_template_categories,id',
            'json' => 'required|array',
            'description' => 'nullable|string',
            'preview_image' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ], [
            'name.required' => 'The template name is required.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'json.required' => 'The form JSON configuration is required.',
            'json.array' => 'The form JSON must be a valid JSON object or array.',
        ]);

        if ($validator->fails()) {
            $this->setFailMessage('Validation error.');
            return $this->sendValidationError($validator->errors());
        }

        $slug = !empty($input['slug']) ? Str::slug($input['slug']) : Str::slug($input['name']);

        $template = MasterTemplate::create([
            'name' => trim($input['name']),
            'slug' => $slug,
            'description' => $input['description'] ?? null,
            'category_id' => (int) $input['category_id'],
            'json' => $input['json'],
            'preview_image' => $input['preview_image'] ?? null,
            'is_featured' => isset($input['is_featured']) ? (bool) $input['is_featured'] : false,
            'sort_order' => isset($input['sort_order']) ? (int) $input['sort_order'] : 0,
            'status' => isset($input['status']) ? (int) $input['status'] : 1,
        ]);

        $this->setSuccessMessage('Master template created successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $template->load('category'));

        return $this->sendSuccessResult();
    }

    /**
     * Update an existing master template.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Master template ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $template = MasterTemplate::find($targetId);

        if (!$template) {
            $this->setFailMessage('Master template not found.');
            return $this->sendFailResultWithCode(404);
        }

        $input = $request->all();

        // Convert string JSON if passed as JSON string
        if (isset($input['json']) && is_string($input['json'])) {
            $decoded = json_decode($input['json'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input['json'] = $decoded;
            }
        }

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:200',
            'slug' => 'nullable|string|max:200',
            'category_id' => 'sometimes|required|integer|exists:master_template_categories,id',
            'json' => 'sometimes|required|array',
            'description' => 'nullable|string',
            'preview_image' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ], [
            'category_id.exists' => 'The selected category does not exist.',
            'json.array' => 'The form JSON must be a valid JSON object or array.',
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
        } elseif (isset($input['name']) && empty($template->slug)) {
            $dataToUpdate['slug'] = Str::slug($input['name']);
        }
        if (array_key_exists('category_id', $input)) {
            $dataToUpdate['category_id'] = (int) $input['category_id'];
        }
        if (array_key_exists('json', $input)) {
            $dataToUpdate['json'] = $input['json'];
        }
        if (array_key_exists('description', $input)) {
            $dataToUpdate['description'] = $input['description'];
        }
        if (array_key_exists('preview_image', $input)) {
            $dataToUpdate['preview_image'] = $input['preview_image'];
        }
        if (isset($input['is_featured'])) {
            $dataToUpdate['is_featured'] = (bool) $input['is_featured'];
        }
        if (isset($input['sort_order'])) {
            $dataToUpdate['sort_order'] = (int) $input['sort_order'];
        }
        if (isset($input['status'])) {
            $dataToUpdate['status'] = (int) $input['status'];
        }

        $template->update($dataToUpdate);

        $this->setSuccessMessage('Master template updated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $template->fresh()->load('category'));

        return $this->sendSuccessResult();
    }

    /**
     * Delete a master template.
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
            $this->setFailMessage('Master template ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $template = MasterTemplate::find($targetId);

        if (!$template) {
            $this->setFailMessage('Master template not found.');
            return $this->sendFailResultWithCode(404);
        }

        $template->delete();

        $this->setSuccessMessage('Master template deleted successfully.');
        $this->addSuccessResultKeyValue('deleted_id', $targetId);

        return $this->sendSuccessResult();
    }

    /**
     * Duplicate / Clone an existing master template.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Master template ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $template = MasterTemplate::find($targetId);

        if (!$template) {
            $this->setFailMessage('Master template not found.');
            return $this->sendFailResultWithCode(404);
        }

        $cloneName = $request->input('name') ?: ($template->name . ' (Copy)');
        $cloneSlug = Str::slug($cloneName) . '-' . time();

        $cloned = MasterTemplate::create([
            'name' => $cloneName,
            'slug' => $cloneSlug,
            'description' => $template->description,
            'category_id' => $request->input('category_id') ?: $template->category_id,
            'json' => $template->json,
            'preview_image' => $template->preview_image,
            'is_featured' => false,
            'sort_order' => $template->sort_order + 1,
            'status' => 1,
        ]);

        $this->setSuccessMessage('Master template duplicated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $cloned->load('category'));

        return $this->sendSuccessResult();
    }

    /**
     * Toggle or update status of a master template.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $id = null)
    {
        $targetId = $id ?? $request->input('id');

        if (empty($targetId)) {
            $this->setFailMessage('Master template ID is required.');
            return $this->sendFailResultWithCode(ResponseCodes::FAIL);
        }

        $template = MasterTemplate::find($targetId);

        if (!$template) {
            $this->setFailMessage('Master template not found.');
            return $this->sendFailResultWithCode(404);
        }

        if ($request->has('status')) {
            $newStatus = (int) $request->input('status');
        } else {
            $newStatus = $template->status == 1 ? 0 : 1;
        }

        $template->status = $newStatus;
        $template->save();

        $this->setSuccessMessage('Master template status updated successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $template);

        return $this->sendSuccessResult();
    }
}
