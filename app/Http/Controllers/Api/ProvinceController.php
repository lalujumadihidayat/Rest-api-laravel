<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProvinceModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProvinceController extends Controller
{
    // 1. GET ALL DATA
    public function index()
    {
        $province = ProvinceModel::orderBy('province_id', 'ASC')->get();
        return ApiFormatter::createJson(200, 'Get Data Success', $province);
    }

    // 2. CREATE DATA (POST)
    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'code' => 'required|max:10',
                'name' => 'required'
            ], [
                'code.required' => 'Province Code is required',
                'code.max' => 'Province Code must not exceed 10 chars',
                'name.required' => 'Province Name is required'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $province = ProvinceModel::create([
                'province_code' => $params['code'],
                'province_name' => $params['name']
            ]);

            $createdProvince = ProvinceModel::find($province->province_id);
            return ApiFormatter::createJson(200, 'Create Province Success', $createdProvince);

        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // 3. GET DETAIL (GET BY ID)
    public function detail($id)
    {
        try {
            $province = ProvinceModel::find($id);

            if (!$province) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            return ApiFormatter::createJson(200, 'Get Detail Province Success', $province);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // 4. UPDATE FULL (PUT)
    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();
            $province = ProvinceModel::find($id);

            if (!$province) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            $validator = Validator::make($params, [
                'code' => 'required|max:10',
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $province->update([
                'province_code' => $params['code'],
                'province_name' => $params['name']
            ]);

            return ApiFormatter::createJson(200, 'Update Province Success', $province->fresh());

        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // 5. DELETE
    public function delete($id)
    {
        try {
            $province = ProvinceModel::find($id);

            if (!$province) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            $province->delete();
            return ApiFormatter::createJson(200, 'Delete Province Success');

        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }
    
    // 6. PATCH (UPDATE SEBAGIAN)
    public function patch(Request $request, $id)
    {
        try {
            $province = ProvinceModel::find($id);

            if (!$province) {
                return ApiFormatter::createJson(404, 'Data Not Found');
            }

            // Ambil data yang dikirim
            $params = $request->all();

            // Cek jika user mengirim 'code', update kodenya
            if (isset($params['code'])) {
                $province->province_code = $params['code'];
            }

            // Cek jika user mengirim 'name', update namanya
            if (isset($params['name'])) {
                $province->province_name = $params['name'];
            }

            $province->save(); // Simpan perubahan

            return ApiFormatter::createJson(200, 'Update Province Success', $province);

        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }
}
