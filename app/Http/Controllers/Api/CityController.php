<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CityController extends Controller
{
    // i. GET ALL CITY
    public function index()
    {
        $data = CityModel::all();
        return ApiFormatter::createJson(200, 'Get All City Success', $data);
    }

    // ii. GET ALL CITY BY PROVINCE ID (Fitur Khusus)
    public function getByProvince($province_id)
    {
        try {
            $data = CityModel::where('province_id', $province_id)->get();

            if ($data->isEmpty()) {
                 return ApiFormatter::createJson(404, 'Data Not Found for this Province');
            }

            return ApiFormatter::createJson(200, 'Get City by Province Success', $data);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // iii. CREATE CITY
    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, [
                'province_id' => 'required|numeric', // Pastikan ID Provinsi ada dan angka
                'city_name'   => 'required|min:3'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $city = CityModel::create([
                'province_id' => $params['province_id'],
                'city_name'   => $params['city_name']
            ]);

            return ApiFormatter::createJson(200, 'Create City Success', $city);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // iv. GET DETAIL CITY
    public function detail($id)
    {
        try {
            $data = CityModel::find($id);

            if (!$data) return ApiFormatter::createJson(404, 'Data Not Found');

            return ApiFormatter::createJson(200, 'Get Detail City Success', $data);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // v. UPDATE CITY (PUT)
    public function update(Request $request, $id)
    {
        try {
            $data = CityModel::find($id);
            if (!$data) return ApiFormatter::createJson(404, 'Data Not Found');

            $params = $request->all();

            $validator = Validator::make($params, [
                'province_id' => 'required|numeric',
                'city_name'   => 'required'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $data->update([
                'province_id' => $params['province_id'],
                'city_name'   => $params['city_name']
            ]);

            return ApiFormatter::createJson(200, 'Update City Success', $data->fresh());
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // vi. DELETE CITY
    public function delete($id)
    {
        try {
            $data = CityModel::find($id);
            if (!$data) return ApiFormatter::createJson(404, 'Data Not Found');

            $data->delete();
            return ApiFormatter::createJson(200, 'Delete City Success');
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }
}