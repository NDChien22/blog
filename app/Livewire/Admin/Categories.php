<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ParentCategory;
use App\Models\Category;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;
    public $isUpdateParentCategoryMode = false;
    public $pcategory_id, $pcategory_name;

    public $isUpdateCategoryMode = false;
    public $category_id, $parent = 0, $category_name; 

    public $pcategoriesPerPage = 2;
    public $categoriesPerPage = 3;

    protected $listeners = [
        'updateParentCategoryOrdering',
        'updateCategoryOrdering',
        'deleteParentCategoryAction',
        'deleteCategoryAction',
    ];

    public function addParentCategory()
    {
        $this->pcategory_id = null;
        $this->pcategory_name = null;
        $this->isUpdateParentCategoryMode = false;
        $this->showParentCategoryModalForm();
    }

    public function createParentCategory()
    {
        $this->validate([
            'pcategory_name' => 'required|unique:parent_categories,name',
        ], [
            'pcategory_name.required' => 'Parent category field is required.',
            'pcategory_name.unique' => 'Parent category name already exists.',
        ]);

        //Store new parent category
        $pcategory = new ParentCategory();
        $pcategory->name = $this->pcategory_name;
        $saved = $pcategory->save();

        if ($saved) {
            $this->hideParentCategoryModalForm();
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Parent category created successfully.']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Failed to create parent category.']);
        }
    }

    public function editParentCategory($id)
    {
        $pcategory = ParentCategory::findOrFail($id);
        $this->pcategory_id = $pcategory->id;
        $this->pcategory_name = $pcategory->name;
        $this->isUpdateParentCategoryMode = true;
        $this->showParentCategoryModalForm();
    }

    public function updateParentCategory()
    {
        $pcategory = ParentCategory::findOrFail($this->pcategory_id);

        $this->validate([
            'pcategory_name' => 'required|unique:parent_categories,name,' . $pcategory->id,
        ], [
            'pcategory_name.required' => 'Parent category field is required.',
            'pcategory_name.unique' => 'Parent category name already exists.',
        ]);

        //Update parent category
        $pcategory->name = $this->pcategory_name;
        $pcategory->slug = null;
        $updated = $pcategory->save();
        if ($updated) {
            $this->hideParentCategoryModalForm();
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Parent category updated successfully.']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Failed to update parent category.']);
        }
    }

    public function deleteParentCategory($id)
    {
        $this->dispatch('deleteParentCategory', id: $id);
    }

    public function deleteParentCategoryAction($id)
    {
        $pcategory = ParentCategory::findOrFail($id);

        //check if parent category as children
        if( $pcategory->children()->count() > 0 ){
            foreach( $pcategory->children as $category){
                //Release a category
                Category::where('id', $category->id)->update(['parent' => 0]);
            }
        }
        //delete parent category
        $deleted = $pcategory->delete();

        if ($deleted) {
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Parent category deleted successfully.']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Failed to delete parent category.']);
        }
    }

    public function updateParentCategoryOrdering($positions)
    {
        foreach ($positions as $positon) {
            $index = $positon[0];
            $new_positon = $positon[1];
            ParentCategory::where('id', $index)->update([
                'ordering' => $new_positon,
            ]);
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Parent categories reordered successfully.']);
        }
    }

    public function addCategory()
    {
        $this->category_id = null;
        $this->parent = 0;
        $this->category_name = null;
        $this->isUpdateCategoryMode = false;
        $this->showCategoryModalForm();
    }

    public function createCategory(){
        $this->validate([
            'category_name' => 'required|unique:categories,name',
        ], [
            'category_name.required' => 'Category field is required.',
            'category_name.unique' => 'Category name already exists.',
        ]);

        //Store new category
        $category = new Category();
        $category->name = $this->category_name;
        $category->parent = $this->parent;
        $saved = $category->save();

        if ($saved) {
            $this->hideCategoryModalForm();
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Category created successfully.']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Failed to create category.']);
        }
    }

    public function editCategory($id){
        $category = Category::findOrFail($id);
        $this->category_id = $category->id;
        $this->category_name = $category->name;
        $this->parent = $category->parent;
        $this->isUpdateCategoryMode = true;
        $this->showCategoryModalForm();
    }

    public function updateCategory(){
        $category = Category::findOrFail($this->category_id);

        $this->validate([
            'category_name' => 'required|unique:categories,name,' . $category->id,
        ], [
            'category_name.required' => 'Category field is required.',
            'category_name.unique' => 'Category name already exists.',
        ]);

        //Update category
        $category->name = $this->category_name;
        $category->parent = $this->parent;
        $category->slug = null;
        $updated = $category->save();
        if ($updated) {
            $this->hideCategoryModalForm();
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Category updated successfully.']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Failed to update category.']);
        }
    }

    public function updateCategoryOrdering($positions)
    {
        foreach ($positions as $positon) {
            $index = $positon[0];
            $new_positon = $positon[1];
            Category::where('id', $index)->update([
                'ordering' => $new_positon,
            ]);
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Categories reordered successfully.']);
        }
    }

    public function deleteCategory($id){
        $this->dispatch('deleteCategory', id: $id);
    }

    public function deleteCategoryAction($id)
    {
        $category = Category::findOrFail($id);

        //check if category has posts

        //delete category
        $deleted = $category->delete();

        if ($deleted) {
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Category deleted successfully.']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'Failed to delete category.']);
        }
    }

    public function showParentCategoryModalForm()
    {
        $this->resetErrorBag();
        $this->dispatch('showParentCategoryModalForm');
    }

    public function hideParentCategoryModalForm()
    {
        $this->dispatch('hideParentCategoryModalForm');
        $this->isUpdateParentCategoryMode = false;
        $this->pcategory_id = $this->pcategory_name = null;
    }

    public function showCategoryModalForm(){
        $this->resetErrorBag();
        $this->dispatch('showCategoryModalForm');
    }

    public function hideCategoryModalForm(){
        $this->dispatch('hideCategoryModalForm');
        $this->isUpdateCategoryMode = false;
        $this->category_id = $this->category_name = null;
        $this->parent = 0;
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'pcategories' => ParentCategory::orderBy('ordering', 'ASC')->paginate($this->pcategoriesPerPage, ['*'], 'pcat_page'),
            'categories' => Category::orderBy('ordering', 'ASC')->paginate($this->categoriesPerPage, ['*'], 'cat_page'),
        ]);
    }
}
