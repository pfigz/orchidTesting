<?php

namespace App\Orchid\Screens;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
// use Orchid\Attachment\File;

class PostEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Creating a new post';

    /**
     * Display header description
     *
     * @var string
     */
    public $description = 'Blog posts';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Post $post): array
    {
        $this->exists = $post->exists;

        if($this->exists){
            $this->name = 'Edit post';
        }

        $post->load('attachment');

        return [
            'post' => $post
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Create post')
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->exists),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('post.title')
                    ->title('Title')
                    ->placeholder('Attractive but mysterious title')
                    ->help('Specify a short descriptive title for this post.'),

                Cropper::make('post.hero')
                    ->title('Large web banner image, generally in the front and center')
                    ->width(1000)
                    ->height(500)
                    ->targetRelativeUrl(),
            
                TextArea::make('post.description')
                    ->title('Description')
                    ->rows(3)
                    ->maxlength(200)
                    ->placeholder('Brief description for preview'),
                
                Relation::make('post.author')
                    ->title('Author')
                    ->fromModel(User::class, 'name'),

                Quill::make('post.body')
                    ->title('Main text'),
                    
                Upload::make('post.attachment')
                    ->title('All Media') 
                    
                    
            ])
        ];
    }

    /**
     * Create or update a post
     * 
     * @param Post      $post
     * @param Request   $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Post $post, Request $request)
    {
        // $arr = implode("\n", $request->input('post.attachment', []));
        $file = $request->file('file');
        // $fileName = $file->getClientOriginalName();
        var_dump($file);
        exit;
        $post->fill($request->get('post'))->save();
        
        $post->attachment()->syncWithoutDetaching(
            $request->input('post.attachment', [])
        );

        
        // $request->file('post.attachment')->storeAs('m:/samplemusic/uploadedmusic', time().'.png');

        Storage::disk('custom')->put('example.attachment', 'post.attachment');

        Alert::info('You have successfully created a post.');

        return redirect()->route('platform.post.list');
    }

    /**
     * Remove a post
     *
     * @param Post $post
     * 
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Post $post)
    {
        $post->delete();

        Alert::info('You have successfully deleted the post.');

        return redirect()->route('platform.post.list');
    }

}
