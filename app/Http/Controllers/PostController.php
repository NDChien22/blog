<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentCategory;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
class PostController extends Controller
{
    public function addPost(Request $request)
    {
        $categories_html = '';
        $pcategories = ParentCategory::whereHas('children')->orderBy('name', 'asc')->get();
        $categories = Category::where('parent', 0)->orderBy('name', 'asc')->get();

        if (count($pcategories) > 0) {
            foreach ($pcategories as $item) {
                $categories_html .= '<optgroup label="' . $item->name . '">';
                foreach ($item->children as $category) {
                    $categories_html .= '<option value="' . $category->id . '">' . $category->name . '</option>';
                }
                $categories_html .= '</optgroup>';
            }
        }

        if (count($categories) > 0) {
            foreach ($categories as $item) {
                $categories_html .= '<option value="' . $item->id . '">' . $item->name . '</option>';
            }
        }

        $data = [
            'pageTitle' => 'Add New Post',
            'categories_html' => $categories_html,
        ];

        return view('back.pages.add-post', $data);
    }

    public function createPost(Request $request){
        $request->validate([
            "title" => 'required|unique:posts,title',
            'content' => 'required',
            'category' =>'required|exists:categories,id',
            'featured_image' => 'required|mimes:jpg,jpeg,png|max:1024',
        ]);

        //Create Post
        if($request->hasFile('featured_image')){
            $path = 'images/posts/';
            $file = $request->file('featured_image');
            $filename = $file->getClientOriginalName();
            $new_filename = time().'_'.$filename;

            // Upload File
            $upload = $file->move(public_path($path), $new_filename);

            if($upload){
                //Generate Resized Image and Thumbnail
                $resize_path = $path.'resized/';
                if(!File::isDirectory($resize_path)){
                    File::makeDirectory($resize_path, 0755, true, true);
                }

                //Thumbnail (Aspect Ratio:1)
                Image::read($path.$new_filename)->resize(250, 250)->save($resize_path.'thumb_'.$new_filename);
                //Resized Image (Aspect Ratio:1.6)
                Image::read($path.$new_filename)->resize(512, 320)->save($resize_path.'resized_'.$new_filename);
                
                $post = new Post();
                $post->author_id = Auth::id();
                $post->category = $request->category;
                $post->title = $request->title;
                $post->content = $request->content;
                $post->featured_image = $new_filename;
                $post->tags = $request->tags;
                $post->meta_keywords = $request->meta_keywords;
                $post->meta_description = $request->meta_description;
                $post->visibility = $request->visibility;
                $saved = $post->save();

                if($saved){
                    return response()->json(['status'=>1, 'message'=>'Post has been created successfully.']);
                }else{
                    return response()->json(['status'=>0, 'message'=>'Failed to create new post.']);
                }
            }else{
                return response()->json(['status'=>0, 'message'=>'Something went wrong in uploading featured image.']);
            }
        }
    }
}
