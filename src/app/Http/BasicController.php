<?php

namespace VivekMistry\LaravelCodeTransformer\app\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Brand;
use App\DataTables\BrandDataTable;
use App\Http\Requests\Backend\BrandStoreRequest;
use App\Facades\FileUpload;
use App\Http\Requests\Backend\BrandUpdateRequest;
use Exception;

class BasicController extends Controller
{
    /**
     * Load View
     *
     * @return View
     */
    public function index() : View
    {
        return view('backend.brand.index');
    }

    /**
     * Category Ajax
     *
     * @param BrandDataTable $brandDataTable
     * @return JsonResponse
     */
    public function ajax(BrandDataTable $brandDataTable): JsonResponse
    {
        return $brandDataTable->ajax();
    }

    /**
     * Category store request
     *
     * @param BrandStoreRequest $request
     * @return JsonResponse
     */
    public function store(BrandStoreRequest $request) : JsonResponse
    {
        try{
            $entity = app(Brand::class);
            $this->storeOrUpdate($entity, $request);
            return Response::success('Brand created successfully');
        }catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }

    /**
     * Store or update brand
     *
     * @param $entity
     * @param Request $request
     * @return Brand
     */
    public function storeOrUpdate($entity, Request $request) : Brand
    {
        $original = $entity->replicate();

        $entity->name = $request->name;

        if($request->image_removed && $entity->image){
            FileUpload::removeFile($entity->image_url);
            $entity->image = null;
        }
        if($request->hasFile('image')){
            $entity->image = FileUpload::upload(Brand::FOLDER_NAME, $request->file('image'));
        }

        if (!$entity->isDirty()) {
            throw new HttpException(422, 'Nothing has been changed.');
        }
        $entity->save();

        return $entity;
    }

    public function edit(Brand $brand) : JsonResponse
    {
        return Response::success('Fetched!',$brand);
    }

    /**
     * Update brand
     *
     * @param Brand $brand
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Brand $brand, BrandUpdateRequest $request) : JsonResponse
    {
        try{
            $this->storeOrUpdate($brand, $request);
            return Response::success('Brand updated successfully');
        }catch (HttpException $ex) {
            if ($ex->getStatusCode() === 422) {
                return Response::error($ex->getMessage(), 422); // Or use a different method if you have it
            }
            return Response::error('Something went wrong.', 500);
        } catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }

    /**
     * Active Inactive brand
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function activeInactive(Brand $brand) : JsonResponse
    {
        try{
            if($brand->status == 1){
                $brand->update([
                    'status' => 0
                ]);
                $message = "Successfuly, deactive the ".$brand->name;
            }else{
                $brand->update([
                    'status' => 1
                ]);
                $message = "Successfuly, active the ".$brand->name;
            }
            return Response::success($message);
        }catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }

    /**
     * Delete brand
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand) : JsonResponse
    {
        try{
            if($brand->image){
                FileUpload::removeFile($brand->image_url);
            }
            $brand->delete();
            return Response::success('Brand deleted successfully');
        }catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }
}
