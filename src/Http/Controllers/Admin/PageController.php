<?php

namespace Netcore\Aven\Content\Http\Controllers\Admin;

use Netcore\Aven\Content\Models\ContentBlock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{

    public function index()
    {
        return view('aven::admin.pages.index');
    }
    
    public function contentBlockDelete($id)
    {
        $contentBlock = ContentBlock::find($id);

        $blockClass = $contentBlock->block_type;
        $blockId = $contentBlock->block_id;
        $block = new $blockClass;
        $block->find($blockId)->delete();

        $contentBlock->delete();

        return [
            'state' => 'success'
        ];
    }
}
