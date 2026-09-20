<?php

namespace App\Livewire\Education;

use App\Models\EducationArticle;
use Livewire\Component;
use Livewire\WithPagination;

class EducationManager extends Component
{
    use WithPagination;

    public $isModalOpen = false;
    public $isDeleteModalOpen = false;

    public $editingId = null;
    public $title = '';
    public $category = 'general';
    public $content = '';
    public $video_url = '';
    public $is_published = true;
    public $articleToDelete = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'category' => 'required|string|in:diagnosis,medication,general',
        'content' => 'required|string',
        'video_url' => 'nullable|url|max:255',
        'is_published' => 'boolean',
    ];

    public function create()
    {
        $this->reset(['editingId', 'title', 'category', 'content', 'video_url', 'is_published']);
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function edit(EducationArticle $article)
    {
        $this->editingId = $article->id;
        $this->title = $article->title;
        $this->category = $article->category;
        $this->content = $article->content;
        $this->video_url = $article->video_url;
        $this->is_published = $article->is_published;
        
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        if ($this->editingId) {
            $article = EducationArticle::where('practice_id', auth()->user()->practice_id)->findOrFail($this->editingId);
            $article->update($validatedData);
            session()->flash('message', 'Article updated successfully.');
        } else {
            $validatedData['practice_id'] = auth()->user()->practice_id;
            EducationArticle::create($validatedData);
            session()->flash('message', 'Article created successfully.');
        }

        $this->isModalOpen = false;
    }

    public function confirmDelete(EducationArticle $article)
    {
        $this->articleToDelete = $article->id;
        $this->isDeleteModalOpen = true;
    }

    public function delete()
    {
        if ($this->articleToDelete) {
            EducationArticle::where('practice_id', auth()->user()->practice_id)->findOrFail($this->articleToDelete)->delete();
            session()->flash('message', 'Article deleted successfully.');
        }
        $this->isDeleteModalOpen = false;
        $this->articleToDelete = null;
    }

    public function togglePublish(EducationArticle $article)
    {
        if ($article->practice_id === auth()->user()->practice_id) {
            $article->update(['is_published' => !$article->is_published]);
        }
    }

    public function render()
    {
        $articles = EducationArticle::where('practice_id', auth()->user()->practice_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.education.education-manager', [
            'articles' => $articles
        ])->layout('components.layouts.app.workspace');
    }
}
