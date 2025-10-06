<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Basic;
use App\DataTables\BasicDataTable;
use App\Http\Requests\Backend\BasicStoreRequest;
use App\Facades\FileUpload;
use App\Http\Requests\Backend\BasicUpdateRequest;
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
        return view('backend.basic.index');
    }

    /**
     * Category Ajax
     *
     * @param BasicDataTable $basicDataTable
     * @return JsonResponse
     */
    public function ajax(BasicDataTable $basicDataTable): JsonResponse
    {
        return $basicDataTable->ajax();
    }

    /**
     * Category store request
     *
     * @param BasicStoreRequest $request
     * @return JsonResponse
     */
    public function store(BasicStoreRequest $request) : JsonResponse
    {
        try{
            $entity = app(Basic::class);
            $this->storeOrUpdate($entity, $request);
            return Response::success('Basic created successfully');
        }catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }

    /**
     * Store or update basic
     *
     * @param $entity
     * @param Request $request
     * @return Basic
     */
    public function storeOrUpdate($entity, Request $request) : Basic
    {
        $original = $entity->replicate();

        $entity->name = $request->name;

        if($request->image_removed && $entity->image){
            FileUpload::removeFile($entity->image_url);
            $entity->image = null;
        }
        if($request->hasFile('image')){
            $entity->image = FileUpload::upload(Basic::FOLDER_NAME, $request->file('image'));
        }

        if (!$entity->isDirty()) {
            throw new HttpException(422, 'Nothing has been changed.');
        }
        $entity->save();

        return $entity;
    }

    public function edit(Basic $brand) : JsonResponse
    {
        return Response::success('Fetched!',$brand);
    }

    /**
     * Update Basic
     *
     * @param Basic $basic
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Basic $basic, BasicUpdateRequest $request) : JsonResponse
    {
        try{
            $this->storeOrUpdate($basic, $request);
            return Response::success('Basic updated successfully');
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
     * Active Inactive basic
     *
     * @param Basic $basic
     * @return JsonResponse
     */
    public function activeInactive(Basic $basic) : JsonResponse
    {
        try{
            if($basic->status == 1){
                $basic->update([
                    'status' => 0
                ]);
                $message = "Successfuly, deactive the ".$basic->name;
            }else{
                $basic->update([
                    'status' => 1
                ]);
                $message = "Successfuly, active the ".$basic->name;
            }
            return Response::success($message);
        }catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }

    /**
     * Delete basic
     *
     * @param Basic $basic
     * @return JsonResponse
     */
    public function destroy(Basic $basic) : JsonResponse
    {
        try{
            if($basic->image){
                FileUpload::removeFile($basic->image_url);
            }
            $basic->delete();
            return Response::success('Basic deleted successfully');
        }catch(Exception $ex){
            return Response::error('Something went wrong.', 500);
        }
    }
}
