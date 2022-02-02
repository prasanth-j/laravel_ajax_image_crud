<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdate;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $products = Products::latest()->get();

            return DataTables::of($products)
                ->addIndexColumn()
                ->make(true);
        }

        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|unique:products',
            'product_image' => 'required|image'
        ], [
            'product_name.required' => 'Product name required.',
            'product_name.unique' => 'Product name already taken.',
            'product_image.required' => 'Product image required.',
            'product_image.image' => 'Product image invalid.'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'status' => 'error', 'errors' => $validator->errors()->toArray()]);
        } else {
            $path = 'files/products';
            $image = $request->file('product_image');
            $image_name = Str::random(10) . time() . "." . $image->getClientOriginalExtension();
            $upload = $image->storeAs($path, $image_name, 'public');

            $product = new Products;
            $product->product_name = $request->input('product_name');
            $product->product_image = $image_name;
            $query = $product->save();

            if ($query && $upload) {
                event(new ProductUpdate("Created", $product->product_name, $product->product_image));
                return response()->json(['code' => 1, 'status' => 'success', 'method' => 'store', 'msg' => 'Product added successfully.']);
            } else {
                return response()->json(['code' => 0, 'status' => 'warning', 'msg' => 'Database error! Product not saved.']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Products::find($id);

        return response()->json(['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_product_name' => 'required|unique:products,product_name,' . $request->input('edit_product_id'),
            'edit_product_image' => 'image'
        ], [
            'edit_product_name.required' => 'Product name required.',
            'edit_product_name.unique' => 'Product name already taken.',
            'edit_product_image.image' => 'Product image invalid.'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'status' => 'error', 'errors' => $validator->errors()->toArray()]);
        } else {
            $product = Products::find($request->input('edit_product_id'));
            $product->product_name = $request->input('edit_product_name');

            if ($request->hasFile('edit_product_image')) {
                // unlink('storage/files/products/' . $product->product_image);
                $oldFile = 'storage/files/products/' . $product->product_image;

                if (File::exists($oldFile)) {
                    File::delete($oldFile);
                }

                $path = 'files/products';
                $image = $request->file('edit_product_image');
                $image_name = Str::random(10) . time() . "." . $image->getClientOriginalExtension();
                $image->storeAs($path, $image_name, 'public');
                $product->product_image = $image_name;
            }

            $query = $product->update();

            if ($query) {
                event(new ProductUpdate("Updated", $product->product_name, $product->product_image));
                return response()->json(['code' => 1, 'status' => 'success', 'method' => 'update', 'msg' => 'Product updated successfully.']);
            } else {
                return response()->json(['code' => 0, 'status' => 'warning', 'msg' => 'Database error! Product not updated.']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Products::find($id);

        $image = 'storage/files/products/' . $product->product_image;
        if (File::exists($image)) {
            File::delete($image);
        }

        $query = $product->delete();

        if ($query) {
            event(new ProductUpdate("Deleted", $product->product_name, $product->product_image));
            return response()->json(['code' => 1, 'status' => 'success', 'method' => 'destroy', 'msg' => 'Product deleted successfully.']);
        } else {
            return response()->json(['code' => 0, 'status' => 'warning', 'method' => 'destroy', 'msg' => 'Database error! Product not deleted.']);
        }
    }
}
