<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use App\Models\ParentCategory;
use Illuminate\Support\Facades\File;

class Posts extends Component
{
    use WithPagination;

    public $perPage = 3;
    public $categories_html = '';

    public $search = null;
    public $author = null;
    public $category = null;
    public $visibility = null;
    public $sortBy = 'desc';
    public $post_visibilities;

    protected $queryString = [
        'search' => ['except' => ''],
        'author' => ['except' => ''],
        'category' => ['except' => ''],
        'visibility' => ['except' => ''],
        'sortBy' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'deletePostAction'
    ];

    public function updatedSearch(){
        $this->resetPage();
    }

    public function updatedAuthor(){
        $this->resetPage();
    }

    public function updatedCategory(){
        $this->resetPage();
    }

    public function updatedVisibility(){
        $this->resetPage();
        $this->post_visibilities = $this->visibility == 'public' ? 1 : 0;
    }

    public function mount(){
        $this->author = Auth::user()->type == "superAdmin" ? Auth::user()->id : '';
        $this->post_visibilities = $this->visibility == 'public' ? 1 : 0;
        //Prepare Categories selection
        $categories_html = '';

        $pcategories = ParentCategory::whereHas('children', function($q){
            $q->whereHas('posts');
        })->orderby('name', 'asc')->get();

        $categories = Category::whereHas('posts')->where('parent', 0)->orderBy('name', 'asc')->get();

        if( count($pcategories) > 0 ){
            foreach( $pcategories as $item){
               $categories_html .= '<optgroup label="'.$item->name.'">';
                    foreach( $item->children as $category ){
                        if( $category->posts->count() > 0){
                            $categories_html .= '<option value="'.$category->id.'">'.$category->name.'</option>';
                        }
                    }

               $categories_html .= '</optgroup>';
            }
        }

        if( count($categories) > 0 ){
            foreach( $categories as $item ){
                $categories_html .= '<option value="'.$item->id.'">'.$item->name.'</option>';
            }
        }
        $this->categories_html = $categories_html;
    }

    public function deletePost($id){
        $this->dispatch('deletePost', ['id' => $id]);
    }

    public function deletePostAction($id){
        $post = Post::findOrFail($id);
        $path = "images/posts/";
        $resize_ppath = $path.'resized/';
        $old_featured_image = $post->featured_image;

        //delete featured image
        if($old_featured_image != '' && File::exists(public_path($path.$old_featured_image))){
            File::delete(public_path($path.$old_featured_image));

            //delete resize image
            if(File::exists(public_path($resize_ppath.'resized_'.$old_featured_image))){
                File::delete(public_path($resize_ppath.'resized_'.$old_featured_image));
            }

            //delete thumbnail image
            if(File::exists(public_path($resize_ppath.'thumb_'.$old_featured_image))){
                File::delete(public_path($resize_ppath.'thumb_'.$old_featured_image));
            }
        }

        //delete post
        $delete = $post->delete();

        if($delete){
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Post deleted successfully!']);
        }else{
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Something went wrong!']);
        }
    }

    public function render()
    {
        return view('livewire.admin.posts',[
            'posts' => Auth::user()->type == 'superAdmin' ? 
                        Post::search(trim($this->search))
                        ->when( $this->author, function($query){
                            $query->where('author_id', $this->author);
                        })
                        ->when( $this->category, function($query){
                            $query->where('category', $this->category);
                        })
                        ->when( $this->visibility, function($query){
                            $query->where('visibility', $this->post_visibilities);
                        })
                        ->when( $this->sortBy, function($query){
                            $query->orderBy('id', $this->sortBy);
                        })
                        ->paginate($this->perPage) : 

                        Post::search(trim($this->search))
                        ->when( $this->author, function($query){
                            $query->where('author_id', $this->author);
                        })
                        ->when( $this->category, function($query){
                            $query->where('category', $this->category);
                        })
                        ->when( $this->visibility, function($query){
                            $query->where('visibility', $this->post_visibilities);
                        })
                        ->when( $this->sortBy, function($query){
                            $query->orderBy('id', $this->sortBy);
                        })
                        ->where('author_id', Auth::id())->paginate($this->perPage),
        ]);
    }
}
