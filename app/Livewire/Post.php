<?php

namespace App\Livewire;

use App\Models\Post as ModelsPost;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Post extends Component
{
    use WithFileUploads;

    public $title, $content, $thumbnail, $postId, $updatePost = false, $addPost = false;

    public $rules = [
        'title' => 'required',
        'content' => 'required',
    ];

    public function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->thumbnail = '';
    }

    public function render()
    {
        $posts = ModelsPost::latest()->paginate(10);

        return view('livewire.post', compact('posts'));
    }

    public function create()
    {
        $this->resetFields();
        $this->addPost = true;
        $this->updatePost = false;
    }

    public function store()
    {
        $this->validate();

        try {
            // save image on public folder
            $image = $this->thumbnail;
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);

            ModelsPost::create([
                'user_id' => Auth::user()->id,
                'title' => $this->title,
                'content' => $this->content,
                'thumbnail' => $filename
            ]);

            session()->flash('success', 'Post Created Successfully');

            $this->resetFields();
            $this->addPost = false;
        } catch (\Exception $ex) {
            session()->flash('error', $ex->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $post = ModelsPost::find($id);

            if (!$post) {
                session()->flash('error', 'Post Not Found');
            } else {
                $this->title = $post->title;
                $this->content = $post->content;
                $this->thumbnail = $post->thumbnail;
                $this->postId = $post->id;
                $this->updatePost = true;
                $this->addPost = false;
            }
        } catch (\Exception $ex) {
            session()->flash('error', $ex->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $post = ModelsPost::find($this->postId);

            if (!$post) {
                session()->flash('error', 'Post Not Found');
            } else {
                // save image on public folder
                $image = $this->thumbnail;
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);

                $post->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'thumbnail' => $filename
                ]);

                session()->flash('success', 'Post Updated Successfully');

                $this->resetFields();
                $this->updatePost = false;
            }
        } catch (\Exception $ex) {
            session()->flash('error', $ex->getMessage());
        }
    }

    public function cancel()
    {
        $this->addPost = false;
        $this->updatePost = false;
        $this->resetFields();
    }

    public function delete($id)
    {
        try {
            ModelsPost::find($id)->delete();
            session()->flash('success', 'Post Deleted Successfully');
        } catch (\Exception $ex) {
            session()->flash('error', $ex->getMessage());
        }
    }
}
