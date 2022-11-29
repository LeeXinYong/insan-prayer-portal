<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SortableController extends Controller
{
    protected function resourceAbilityMap(): array
    {
        return [
            ...parent::resourceAbilityMap(),
            "arrange" => "arrange",
            "rearrange" => "arrange",
        ];
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return [
            ...parent::resourceMethodsWithoutModels(),
            "arrange",
            "rearrange",
        ];
    }

    protected string $model;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->model = Str::replaceFirst("App\Http\Controllers\\", "", Str::replaceLast("Controller","", get_called_class()));
    }

    protected function getBaseModel()
    {
        return app("App\\Models\\" . $this->model);
    }

    protected function getBaseModelDataTable()
    {
        return app("App\\DataTables\\" . $this->model . "sDataTable");
    }

    protected function getBaseModelName(): string
    {
        return strtolower($this->model);
    }

    /**
     * Show the form for arranging the specified resource.
     *
     * @return View
     */
    public function arrange(): View
    {
        return view("pages.sortable.index", ["items" => $this->getSortableItems(), "model" => $this->getBaseModelName(), "sortableItemTemplate" => $this->getSortableItemTemplate()]);
    }

    protected function getSortableItems(): iterable
    {
        return $this->getBaseModel()::where("status", 1)->orderBy("order")->get();
    }

    protected static function getSortableItemTemplate(): \Closure
    {
        return function ($item) {
            return view("pages.sortable.item", ["item" => $item]);
        };
    }

    /**
     * Update the order of specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function rearrange(Request $request): JsonResponse
    {
        if(!isset($request->new_order)) {
            if(trans()->has($this->getBaseModelName() . ".message.fail_arrange")) {
                return response()->json(["errors" => __($this->getBaseModelName() . ".message.fail_arrange")], 422);
            } else {
                return response()->json(["errors" => __("general.message.fail_arrange")], 422);
            }
        } else {
            $new_order = array_map(function($item) {
                return $item["id"];
            }, json_decode($request->new_order, true)[0]);
            // Update the order of the items according to the new order
            foreach ($new_order as $order => $id) {
                $this->getBaseModel()::query()->where("id", $id)->update(["order" => $order, 'updated_at' => DB::raw('updated_at')]);
            }
            // Update the order of the items that are not in the new order according to the item's name field
            foreach (array("title", "name") as $field) {
                if(Schema::hasColumn((new ($this->getBaseModel()))->getTable(), $field)) {
                    foreach ($this->getBaseModel()::query()->whereNotIn("id", $new_order)->orderBy($field)->pluck("id")->toArray() as $order => $id) {
                        $this->getBaseModel()::query()->where("id", $id)->update(["order" => $order + count($new_order), 'updated_at' => DB::raw('updated_at')]);
                    }
                    break;
                }
            }

            $output = array();

            if(trans()->has($this->getBaseModelName() . ".message.success_arrange")) {
                $output["success"] = __($this->getBaseModelName() . ".message.success_arrange");
                $output["button"] = __($this->getBaseModelName() . ".button.view_listing");
            } else {
                $output["success"] = __("general.message.success_arrange");
                $output["button"] = __("general.button.ok");
            }

            if(Route::has($this->getBaseModelName() . ".index")) {
                $output["redirect"] = route(strtolower($this->model) . ".index");
            } else {
                $output["redirect"] = redirect()->back()->getTargetUrl();
            }

            return response()->json($output);
        }
    }
}
