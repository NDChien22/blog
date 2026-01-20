<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentCategory;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use Intervention\Image\Colors\Rgb\Channels\Red;

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

    public function createPost(Request $request)
    {
        $request->validate([
            "title" => 'required|unique:posts,title',
            'content' => 'required',
            'category' => 'required|exists:categories,id',
            'featured_image' => 'required|mimes:jpg,jpeg,png|max:1024',
        ]);

        //Create Post
        if ($request->hasFile('featured_image')) {
            $path = 'images/posts/';
            $file = $request->file('featured_image');
            $filename = $file->getClientOriginalName();
            $new_filename = time() . '_' . $filename;

            // Upload File
            $upload = $file->move(public_path($path), $new_filename);

            if ($upload) {
                //Generate Resized Image and Thumbnail
                $resize_path = $path . 'resized/';
                if (!File::isDirectory($resize_path)) {
                    File::makeDirectory($resize_path, 0755, true, true);
                }

                //Thumbnail (Aspect Ratio:1)
                Image::read($path . $new_filename)->resize(250, 250)->save($resize_path . 'thumb_' . $new_filename);
                //Resized Image (Aspect Ratio:1.6)
                Image::read($path . $new_filename)->resize(512, 320)->save($resize_path . 'resized_' . $new_filename);

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

                if ($saved) {
                    return response()->json(['status' => 1, 'message' => 'Post has been created successfully.']);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Failed to create new post.']);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong in uploading featured image.']);
            }
        }
    }

    //All Posts
    public function allPosts(Request $request)
    {
        $data = [
            'pageTitle' => 'Posts',

        ];

        return view('back.pages.posts', $data);
    }

    //Edit Post
    public function editPost(Request $request, $id = null)
    {
        $post = Post::findOrFail($id);

        $categories_html = '';
        $pcategories = ParentCategory::whereHas('children')->orderBy('name', 'asc')->get();
        $categories = Category::where('parent', 0)->orderBy('name', 'asc')->get();

        if (count($pcategories) > 0) {
            foreach ($pcategories as $item) {
                $categories_html .= '<optgroup label="' . $item->name . '">';
                foreach ($item->children as $category) {
                    $selected = $category->id == $post->category ? 'selected' : '';
                    $categories_html .= '<option value="' . $category->id . '" ' . $selected . '>' . $category->name . '</option>';
                }
                $categories_html .= '</optgroup>';
            }
        }

        if (count($categories) > 0) {
            foreach ($categories as $item) {
                $selected = $item->id == $post->category ? 'selected' : '';
                $categories_html .= '<option value="' . $item->id . '" ' . $selected . '>' . $item->name . '</option>';
            }
        }

        $data = [
            'pageTitle' => 'Edit Post',
            'post' => $post,
            'categories_html' => $categories_html,
        ];

        return view('back.pages.edit-post', $data);
    }

    //Update Post
    public function updatePost(Request $request)
    {
        $post = Post::findOrFail($request->post_id);
        $featured_image = $post->featured_image;

        $request->validate([
            "title" => 'required|unique:posts,title,' . $post->id,
            'content' => 'required',
            'category' => 'required|exists:categories,id',
            'featured_image' => 'nullable|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('featured_image')) {
            $old_featured_image = $post->featured_image;
            $path = 'images/posts/';
            $file = $request->file('featured_image');
            $filename = $file->getClientOriginalName();
            $new_filename = time() . '_' . $filename;

            // Upload File
            $upload = $file->move(public_path($path), $new_filename);

            if ($upload) {
                //Generate Resized Image and Thumbnail
                $resize_path = $path . 'resized/';
                if (!File::isDirectory($resize_path)) {
                    File::makeDirectory($resize_path, 0755, true, true);
                }

                //Thumbnail (Aspect Ratio:1)
                Image::read($path . $new_filename)->resize(250, 250)->save($resize_path . 'thumb_' . $new_filename);
                //Resized Image (Aspect Ratio:1.6)
                Image::read($path . $new_filename)->resize(512, 320)->save($resize_path . 'resized_' . $new_filename);

                //Delete old featured image
                if ($old_featured_image != null & File::exists(public_path($path . $old_featured_image))) {
                    File::delete(public_path($path . $old_featured_image));

                    //Delete old resized images
                    if (File::exists(public_path($resize_path . 'resized_' . $old_featured_image))) {
                        File::delete(public_path($resize_path . 'resized_' . $old_featured_image));
                    }

                    //Delete old thumbnail image
                    if (File::exists(public_path($resize_path . 'thumb_' . $old_featured_image))) {
                        File::delete(public_path($resize_path . 'thumb_' . $old_featured_image));
                    }
                }

                $featured_image_name = $new_filename;
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong in uploading featured image.']);
            }
        }

        //Update Post
        $post->author_id = Auth::id();
        $post->category = $request->category;
        $post->title = $request->title;
        $post->slug = null;
        $post->content = $request->content;
        $post->featured_image = $featured_image_name;
        $post->tags = $request->tags;
        $post->meta_keywords = $request->meta_keywords;
        $post->meta_description = $request->meta_description;
        $post->visibility = $request->visibility;
        $updated = $post->save();

        if ($updated) {
            return response()->json(['status' => 1, 'message' => 'Post has been updated successfully.']);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed to update the post.']);
        }
    }
}
