<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
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
        return view('product.index');
    }

    /**
     * Fetch a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetch()
    {
        $products = Products::latest()->get();

        return DataTables::of($products)
            ->addIndexColumn()
            ->make(true);
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
        $validator = \Validator::make($request->all(), [
            'product_name' => 'required|unique:products',
            'product_image' => 'required|image'
        ], [
            'product_name.required' => 'Product name required.',
            'product_name.unique' => 'Product name already taken.',
            'product_image.required' => 'Product image required.',
            'product_image.image' => 'Product image invalid.'
        ]);

        if (!$validator->passes()) {
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
                return response()->json(['code' => 1, 'status' => 'success', 'msg' => 'Product added successfully.']);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
