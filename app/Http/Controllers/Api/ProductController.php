<?php

namespace App\Http\Controllers\Api;

//import model Product
use App\Models\Product;

use App\Http\Controllers\Controller;

//import resource ProductResource
use App\Http\Resources\ProductResource;

//import Http request
use Illuminate\Http\Request;

//import facade Validator dan Storage
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all products
        $products = Product::latest()->paginate(5);

        //return collection of products as a resource
        return new ProductResource(true, 'List Data Products', $products);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'       => 'required',
            'description' => 'required',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create product
        $product = Product::create([
            'image' => 'products/' . $image->hashName(),
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
        ]);

        //return response
        return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', $product);
    }

    /**
     * show
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        //find product by id
        $product = Product::find($id);

        //check if product not found
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        //return product as a resource
        return new ProductResource(true, 'Detail Data Product', $product);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find product by ID
        $product = Product::find($id);

        //check if product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {
            //delete old image
            Storage::delete('public/products/' . basename($product->image));

            //upload image
            $image = $request->file('image');
            $imagePath = $image->store('public/products');

            //update product with new image
            $product->update([
                'image'       => str_replace('public/', '', $imagePath),
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
            ]);
        } else {
            //update product without image
            $product->update([
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
            ]);
        }

        //return response
        return new ProductResource(true, 'Data Product Berhasil Diubah!', $product);
    }

    /**
     * destroy
     *
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find product by ID
        $product = Product::find($id);

        //check if product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        //delete image
        Storage::delete('public/products/' . basename($product->image));

        //delete product
        $product->delete();

        //return response
        return new ProductResource(true, 'Data Product Berhasil Dihapus!', null);
    }
}
