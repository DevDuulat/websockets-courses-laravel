<?php
namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;


class CategoryCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function broadcastOn()
    {
        return new Channel('categories');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->category->id,
            'name' => $this->category->name,
        ];
    }

    public function broadcastAs()
    {
        return 'category.created';
    }
}
