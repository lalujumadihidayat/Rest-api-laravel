<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistrictModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class DistrictController extends Controller
{
    // i. GET ALL DISTRICT
    public function index()
    {
        $data = DistrictModel::all();
        return ApiFormatter::createJson(200, 'Get All District Success', $data);
    }

    // ii. GET DISTRICT BY CITY ID (Fitur Khusus)
    public function getByCity($city_id)
    {
        try {
            $data = DistrictModel::where('city_id', $city_id)->get();

            if ($data->isEmpty()) {
                 return ApiFormatter::createJson(404, 'Data Not Found for this City');
            }

            return ApiFormatter::createJson(200, 'Get District by City Success', $data);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // iii. CREATE DISTRICT
    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'city_id'     => 'required|numeric',
                'district_name' => 'required|min:3'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $district = DistrictModel::create([
                'city_id'     => $params['city_id'],
                'district_name' => $params['district_name']
            ]);

            return ApiFormatter::createJson(200, 'Create District Success', $district);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // iv. GET DETAIL DISTRICT
    public function detail($id)
    {
        try {
            $data = DistrictModel::find($id);

            if (!$data) return ApiFormatter::createJson(404, 'Data Not Found');

            return ApiFormatter::createJson(200, 'Get Detail District Success', $data);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // v. UPDATE DISTRICT
    public function update(Request $request, $id)
    {
        try {
            $data = DistrictModel::find($id);
            if (!$data) return ApiFormatter::createJson(404, 'Data Not Found');

            $params = $request->all();

            $validator = Validator::make($params, [
                'city_id'     => 'required|numeric',
                'district_name' => 'required'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $data->update([
                'city_id'     => $params['city_id'],
                'district_name' => $params['district_name']
            ]);

            return ApiFormatter::createJson(200, 'Update District Success', $data->fresh());
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // vi. DELETE DISTRICT
    public function delete($id)
    {
        try {
            $data = DistrictModel::find($id);
            if (!$data) return ApiFormatter::createJson(404, 'Data Not Found');

            $data->delete();
            return ApiFormatter::createJson(200, 'Delete District Success');
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }
}