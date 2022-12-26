<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\Usr;
use App\Models\Family;
use App\Models\So;
use App\Models\Des;
use App\Models\File;
use App\Models\Group;
use App\Models\Log;
use App\Models\Tool;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ToolController extends Controller
{

    public function history(Request $request) {
        return response()->json(Log::with('tool','user')->orderBy('created_at', 'desc')->limit(1000)->get());
    }

    public function index(Request $request) {
        return response()->json(
            Tool::all()->map(static function(Tool $tool) {
                return [
                    'id' => $tool->id,
                    'item' => $tool->item,
                    'measurement' => $tool->measurement,
                    'des' => $tool->des,
                    'group' => $tool->group,
                    'family' => $tool->family,
                    'brand' => $tool->brand,
                    'device' => $tool->device,
                    'serial_number' => $tool->serial_number,
                    'calibration_expiration' => $tool->calibration_expiration,
                    'has_validation' => $tool->has_validation
                ];
            })
        );
    }

    public function show(Request $request, Tool $tool) {
        return response()->json([
            'id' => $tool->id,
            'item' => $tool->item,
            'measurement' => $tool->measurement,
            'des' => $tool->des,
            'group' => $tool->group,
            'family' => $tool->family,
            'brand' => $tool->brand,
            'device' => $tool->device,
            'serial_number' => $tool->serial_number,
            'calibration_expiration' => $tool->calibration_expiration,
            'has_validation' => $tool->has_validation,
            'main_localization' => $tool->main_localization,
            'shelf_localization' => $tool->shelf_localization,
            'shelf' => $tool->shelf,
            'user' => $tool->user,
            'min_stock' => $tool->min_stock,
            'quantity' => $tool->quantity,
            'dispatchable' => $tool->dispatchable,
            'comments' => $tool->comments,
            'files' => $tool->files->map(static function(File $file) {
                return $file->path;
            })
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $tool = $this->createTool($request);
            foreach ($request->documents as $document) {
                $tool->files()->create([
                    'path' => $document
                ]);
            }
            $request->user()->logs()->create([
                'tool_id' => $tool->id,
                'comment' => 'Se inserto registro',
                'type'=> 'insert',
                'after' => json_encode($this->getValues($tool->toArray(), $tool))
            ]);
            DB::commit();
            return response()->json(
                $tool
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, Tool $tool) {
        DB::beginTransaction();
        try {
            $request->user()->logs()->create([
                'tool_id' => $tool->id,
                'comment' => 'Se elimino registro',
                'type'=> 'delete',
            ]);
            $tool->deleteOrFail();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
        DB::commit();
        return response()->json($tool);
    }

    public function update(Request $request, Tool $tool) {
        DB::beginTransaction();
        try {
            $des = $this->getDes($request->des);
            $group = $this->getGroup($request->group);
            $family = $this->getFamily($request->family);
            $brand = $this->getBrand($request->brand);
            $device = $this->getDevice($request->device);
            $oldTool = json_encode($this->getValues($tool->toArray(), $tool));
            if ($request->main_localization !== $tool->main_localization) {
                $tool->update([ 'quantity' => $tool->quantity - $request->movingQuantity ]);
                $request->quantity = $request->movingQuantity;
                $tool = $this->createTool($request);
                $request->user()->logs()->create([
                    'tool_id' => $tool->id,
                    'comment' => 'Se traspaso registro',
                    'type'=> 'update',
                    'before' => $oldTool,
                    'after' => json_encode($this->getValues($tool->toArray(), $tool))
                ]);
            } else {
                $tool->update([
                    'des_id' => $des->id ?? null,
                    'group_id' => $group->id ?? null,
                    'family_id' => $family->id ?? null,
                    'brand_id' => $brand->id ?? null,
                    'device_id' => $device->id ?? null,
                    'serial_number' => $request->serial,
                    'size' => $request->size,
                    'calibration_expiration' => $request->has_validation ? $request->calibration_expiration : null,
                    'has_validation' => $request->has_validation,
                    'main_localization' => $request->main_localization,
                    'shelf_localization' => $request->shelf_localization,
                    'shelf' => $request->shelf,
                    'measurement' => $request->measurement,
                    'min_stock' => $request->min_stock,
                    'quantity' => $request->quantity,
                    'comments' => $request->comments,
                    'dispatchable' => $request->dispatchable
                ]);
                $oldValues = $tool->getChanges();
                if (count($oldValues) > 0) {
                    $request->user()->logs()->create([
                        'tool_id' => $tool->id,
                        'comment' => 'Se edito registro',
                        'type'=> 'update',
                        'before' => $oldTool,
                        'after' => json_encode($this->getValues($oldValues, $tool->refresh()))
                    ]);
                }
            }
            DB::commit();
            return response()->json(
                $tool
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    private function createTool(Request $request) {
        $des = $this->getDes($request->des);
        $group = $this->getGroup($request->group);
        $family = $this->getFamily($request->family);
        $brand = $this->getBrand($request->brand);
        $device = $this->getDevice($request->device);
        $tool = $request->user()->tools()->create([
            'des_id' => $des->id ?? null,
            'group_id' => $group->id ?? null,
            'family_id' => $family->id ?? null,
            'brand_id' => $brand->id ?? null,
            'device' => $device->id ?? null,
            'serial_number' => $request->serial,
            'size' => $request->size,
            'calibration_expiration' => $request->has_validation ? $request->calibration_expiration : null,
            'has_validation' => $request->has_validation,
            'main_localization' => $request->main_localization,
            'shelf_localization' => $request->shelf_localization,
            'shelf' => $request->shelf,
            'measurement' => $request->measurement,
            'min_stock' => $request->min_stock,
            'quantity' => $request->quantity,
            'comments' => $request->comments,
            'dispatchable' => $request->dispatchable
        ]);
        $tool->update([
            'item' => sprintf('AAA%04d', $tool->id)
        ]);
        return $tool->refresh();
    }

    private function getValues($values, Tool $tool) {
//        dd($values, $tool);
        $specialAttributes = ['des_id' => 'des','group_id' => 'group','family_id' => 'family','brand_id' => 'brand','so_id' => 'so'];
        $names = ['item' => 'Item','des_id' => 'Descripcion','group_id' => 'Sub Grupo','family_id' => 'Familia','brand_id' => 'Marca',
            'device' => 'N.Dispositivo','serial_number' => 'Numero de serie','calibration_expiration' => 'Expiracion de calibracion','dispatchable' => 'Despachable',
            'has_validation' => 'Sujeto a validacion', 'main_localization' => 'Localizacion principal', 'shelf_localization' => 'Localizacion de estante', 'shelf' => 'Estante',
            'measurement' => 'Medida', 'min_stock' => 'Stock minimo', 'quantity' => 'Cantidad', 'comments' => 'Comentarios'];
        $data = array();
        foreach (array_keys($values) as $key) {
            if (array_key_exists($key, $specialAttributes)) {
                $data[$names[$key]] = $tool[$specialAttributes[$key]]->name ?? '';
            } else if(array_key_exists($key, $names)){
                $data[$names[$key]] = $values[$key];
            }
        }
        return $data;
    }

    public function showTool(Tool $tool): array {
        return [
            'id' => $tool->id,
            'item' => $tool->item,
            'des' => $tool->des,
            'measurement' => $tool->measurement,
            'group' => $tool->group,
            'family' => $tool->family,
            'brand' => $tool->brand,
            'device' => $tool->device,
            'serial_number' => $tool->serial_number,
            'calibration_expiration' => $tool->calibration_expiration,
            'has_validation' => $tool->has_validation,
            'main_localization' => $tool->main_localization,
            'shelf_localization' => $tool->shelf_localization,
            'shelf' => $tool->shelf,
            'user' => $tool->user,
            'min_stock' => $tool->min_stock,
            'quantity' => $tool->quantity
        ];
    }

    public function search(Request $request) {
        $especialKeys = ['des','group','brand','family','device','user'];
        $filters = $request->keys();
        $query = Tool::query();
        foreach($filters as $filter) {
            if (in_array($filter, $especialKeys, true)) {
                $value = is_null($request[$filter]) ? null : $request[$filter]['id'];
                if ($value !== 0) {
                    $query = $query->where("{$filter}_id", is_null($request[$filter]) ? null : $request[$filter]['id']);
                }
            }
            else if (!in_array($filter, $especialKeys, true)){
                $query = $query->where(Str::snake($filter), 'like', "%$request[$filter]%");
            }
        }
        $data = $query->get()->map(function(Tool $tool) {
            return $this->showTool($tool);
        });
        return response()->json($data);
    }

    private function getDes($data)
    {
        if (is_null($data)) {
            return null;
        }
        if (is_array($data)) {
            return Des::find($data['id']);
        }
        return Des::where('name', $data)->firstOrCreate([
            'name' => $data
        ]);
    }

    private function getGroup($data)
    {
        if (is_null($data)) {
            return null;
        }
        if (is_array($data)) {
            return Group::find($data['id']);
        }
        return Group::where('name', $data)->firstOrCreate([
            'name' => $data
        ]);
    }

    private function getFamily($data)
    {
        if (is_null($data)) {
            return null;
        }
        if (is_array($data)) {
            return Family::find($data['id']);
        }
        return Family::where('name', $data)->firstOrCreate([
            'name' => $data
        ]);
    }

    private function getBrand($data)
    {
        if (is_null($data)) {
            return null;
        }
        if (is_array($data)) {
            return Brand::find($data['id']);
        }
        return Brand::where('name', $data)->firstOrCreate([
            'name' => $data
        ]);
    }

    private function getDevice($data)
    {
        if (is_null($data)) {
            return null;
        }
        if (is_array($data)) {
            return Device::find($data['id']);
        }
        return Device::where('name', $data)->firstOrCreate([
            'name' => $data
        ]);
    }

}
