<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\StoreImage;
use App\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipesController extends Controller
{
    use StoreImage;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = is_numeric($request->input('page')) && $request->input('page') == (int) $request->input('page') ? $request->input('page') : 1;

        $items = \Cache::tags(['recipes', $request->user()->id])
            ->remember($page, $this->hourlyCacheTime, function() use ($request) {
                return $request
                    ->user()
                    ->recipes()
                    ->orderByDesc('created_at')
                    ->paginate();
            });

        return response()
            ->json($items);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:1|max:255',
            'image' => 'nullable|image|max:4194304',
            'ingredients' => 'required|array|max:20',
            'ingredients.*' => 'required|string|max:64',
            'recipe' => 'required|string|min:1|max:2048'
        ]);

        $data = $request->except('image', 'ingredients');
        $data['ingredients'] = array_values(
            $request->input('ingredients')
        );

        if($request->hasFile('image')) {
            $data['image'] = $this->storeImage(
                $request->file('image')
            );
        }

        $item = $request
            ->user()
            ->recipes()
            ->create($data);

        \Cache::tags(['recipes', $request->user()->id])
            ->flush();

        return response()
            ->json($item, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'name' => 'nullable|string|min:1|max:255',
            'image' => 'nullable|image|max:4194304',
            'ingredients' => 'nullable|array|max:20',
            'ingredients.*' => 'string|max:64',
            'recipe' => 'nullable|string|min:1|max:2048'
        ]);

        /** @var Recipe $item */
        $item = $request
            ->user()
            ->recipes()
            ->findOrFail($id);

        // Except not valid fields
        $data = collect($request->except('image'))
            ->filter(function($i) {
                return !is_null($i);
            })
            ->toArray();

        // Store image if exists
        if($request->hasFile('image')) {
            $data['image'] = $this->storeImage(
                $request->file('image')
            );
        }

        // Create recipe
        $item->update($data);

        // Flush all recipe cache for user
        \Cache::tags(['recipes', $request->user()->id])
            ->flush();

        return response()
            ->json($item);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, int $id)
    {
        $item = $request
            ->user()
            ->recipes()
            ->findOrFail($id);

        return response()
            ->json($item);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, int $id)
    {
        /** @var Recipe $item */
        $item = $request
            ->user()
            ->recipes()
            ->findOrFail($id);

        // Delete image if exists
        if(is_string($item->image) && !empty($item->image) && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')
                ->delete($item->image);
        }

        // Delete recipe
        $item->delete();

        return response()
            ->json([], 204);
    }
}